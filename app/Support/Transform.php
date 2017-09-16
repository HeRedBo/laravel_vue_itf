<?php

namespace App\Support;

use League\Fractal\Manager;
use Illuminate\Database\Eloquent\Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Serializer\DataArraySerializer;
use League\Fractal\Resource\Item as FractalItem;
use League\Fractal\TransformerAbstract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection as FractalCollection;

use App\Transformers\EmptyTransformer;

class Transform
{
    /**
     * Fractal manager
     * @var \League\fractal\Manager
     */
    private $fractal;

    public function __construct(Manager $fractal)
    {
        $this->fractal = $fractal;
        if(request()->has('include'))
        {
            $this->fractal->parseExcludes(request()->query('include'));
        }
        $this->fractal->setSerializer(new DataArraySerializer());
    }

    public function  collection($data, TransformerAbstract $transformer = null)
    {
        $transformer  = $transformer  ?: $this->fetchDefaultTransformer($data);
        $collection   = new FractalCollection($data, $transformer);
        if($data instanceof  LengthAwarePaginator)
        {
            $collection->setPaginator(new IlluminatePaginatorAdapter($data));
        }
        return $this->fractal->createData($collection)->toArray();
    }

    public  function  item($data, TransformerAbstract $transformer = null)
    {
        $transformer = $transformer ?: $this->fetchDefaultTransformer($data);
        return $this->fractal->createData(
            new FractalItem($data, $transformer)
        )->toArray();
    }
    protected  function  fetchDefaultTransformer($data)
    {
        if(($data instanceof LengthAwarePaginator || $data instanceof Collection) && $data->isEmpty())
        {
            return new EmptyTransformer();
        }

        $className = $this->getClassName($data);

        if($this->hasDefaultTransformer($className))
        {
            $transformer = config('api.transformers.'. $className);
        } else
        {
            $classBaseName = class_basename($className);
            if(!class_exists($transformer = "App\\Transformers\\{$classBaseName}Transformer")) {
                throw new \Exception("No transformer for {$className}");
            }
        }
        return $transformer;
    }

    protected  function  hasDefaultTransformer($className)
    {
        return ! is_null(config('api.transformers.'. $className));
    }

    protected function getClassName($object)
    {
        if($object instanceof LengthAwarePaginator || $object instanceof Collection)
        {
            return get_class(array_first($object));
        }
        if(!is_array($object) && !is_object($object)) {
            throw  new \Exception("No transformer of \" {$object} \" found.");
        }
        return get_class($object);
    }




}