<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\VenueBillRepository;
use App\Models\Admin\VenueBill;
use App\Models\Admin\VenueBillDataType;
use App\Validators\VenueBillValidator;

/**
 * Class VenueBillRepositoryEloquent
 * @package namespace App\Repositories;
 */
class VenueBillRepositoryEloquent extends BaseRepository implements VenueBillRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return VenueBill::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    
    /**
     * 保存道馆账单数据类型
     * @param object $request
     * @return bool
     */
    public function saveVenueBillDataType($request)
    {
        if($request->get('id'))
        {
            $model = VenueBillDataType::find($request->get('id'));
        } 
        else 
        {
            $model = new VenueBillDataType();
        }
        $model->type = $request->get('type');
        $model->venue_id = $request->get('venue_id');
        $model->name = $request->get('name');
        return $model->save();
    }
}
