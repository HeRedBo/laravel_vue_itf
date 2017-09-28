<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\AdminRepository;
use App\Models\Admin\Admin;
use Exception;


/**
 * Class AdminRepositoryEloquent
 * @package namespace App\Repositories;
 */
class AdminRepositoryEloquent extends BaseRepository implements AdminRepository
{
    /**
     * model fields 
     * @var array 
     */
    protected  $fields = [
        'username' => '',
        'name'     => '',
        'phone'    => '',
        'email'    => '',
        'picture'  => '',
        'roles'    => [],
        'venues'   => [],
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Admin::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     *  create admin data 
     * @param  array  $data [description]
     * @return  
     */
    public function createAdminData(array $data)
    {
        try 
        {
            // 设置字段默认值
            $input = [];
            foreach(array_keys($this->fields) as $field) 
            {
                $input[$field] = empty($data[$field]) ? $this->fields[$field] : $data[$field];
            }
            
            $input['password'] = bcrypt($data['password']);
            unset($input['roles']);
            unset($input['venues']);
            // 上传用户图片处理
            if(checkBase64Image($data['picture']))
            {
                // 处理图片逻辑写在这里
                $input['picture'] = upBase64Img($data['picture'],'admin/avatar');
            }
            // 保存用户信息
            $this->save($input);
  
            $roles = $data['roles'];
            $venues = $data['venues'];
            
            if(!empty($roles))
            {
                //$this->giveRoleTo($roles);
            }
            if(!empty($venues))
            {
                //$this->giveVenueTo($venues);
            }

            return success('数据创建成功');
        
        } 
        catch (Exception $e) 
        {
            logResult('【管理员数据创建失败】'. $e->__toString(),'error');
            return error($e->getMessage());
        }  
    }
}
