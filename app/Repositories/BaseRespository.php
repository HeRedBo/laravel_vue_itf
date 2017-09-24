<?php

namespace  App\Repositories;

trait BaseRepository
{
    /**
     * Get number of records
     * @return mixed
     */
    public function  getNumber()
    {
        return $this->model->count();
    }

    public function updateColumn($id, $input)
    {
        $this->model = $this->getRowByPK($id);
        foreach ($input as $k => $v) {
            $this->model->{$k} = $v;
        }
        return $this->model->save();
    }

    /**
     * destroy a model
     *
     * @param $id
     * @return mixed
     */
    public  function  destroy($id)
    {
        return  $this->getRowByPK($id)->delete();
    }


    /**
     * Get the
     * @param $id
     * @return mixed
     */
    public  function  getRowByPK($id)
    {
        return $this->model->findOrFail($id);
    }


    /**
     * Get all the record
     * @return mixed
     */
    public  function  all()
    {
        return $this->model->get();
    }

    public  function  page($pageSize = 10 , $sort = 'desc',$sortColumn = 'created_at' )
    {
        return $this->model->orderBy($sortColumn, $sort)->paginate($pageSize)->toArray();
    }


    public  function  store($input)
    {
        return $this->save($this->model, $input);
    }

    /**
     *  Update a record by id
     */
    public  function  update($id, $input)
    {
        $this->model = $this->getRowByPK($id);
        return $this->save($this->model, $input);
    }
    /**
     * Save the input's data
     *
     * @param $model
     * @param $input
     * @return mixed
     */
    public  function save($model, $input)
    {
        $model->fill($input);
        $model->save();
        return $model;
    }
}