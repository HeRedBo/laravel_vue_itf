<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\AdminRepository;
use App\Models\Admin\Admin;
use Exception;
use Illuminate\Support\Facades\DB;

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
     * @param array $data
     * @return array|void
     * @author Red-Bo
     */
    public function createAdminData(array $data)
    {
        try
        {
            DB::beginTransaction();
            $admin = $this->model;
            // 设置字段默认值
            foreach(array_keys($this->fields) as $field)
            {
                $admin->$field = empty($data[$field]) ? $this->fields[$field] : $data[$field];
            }
            $admin->password = bcrypt($data['password']);
            unset($admin->roles);
            unset($admin->venues); 
            // 保存用户信息
            $admin->save();
            $roles  = $data['roles'];
            $venues = $data['venues'];
            if(!empty($roles))
            {
                $admin->giveRoleTo($roles);
            }

            if(!empty($venues))
            {
                 $admin->giveVenueTo($venues);
            }

            DB::commit();
            return success('数据创建成功');
        }
        catch (Exception $e)
        {
            DB::rollBack();
            logResult('【管理员数据创建失败】'. $e->__toString(),'error');
            return error($e->getMessage());
        }
    }
    
    public  function  getAdminInfo($id)
    {
        $user_model = $this->model;
        $user = $user_model->with('roles')
                            ->with('venues')
                            ->find($id);
        $roles_res = $venues_res = [];
       if($user)
       {
           $roles = $user->roles;
           $venues = $user->venues;
        //    foreach ($roles as $role)
        //    {
        //        $roles_res[] = ['label' => $role->name, 'value' => $role->id];
        //        $roleStr[]   = $role->name;

        //    }
        //    foreach ($venues as $venue)
        //    {
        //        $venues_res[] = ['label' => $venue->name, 'value' => $venue->id];
        //        $venueStr[] = $venue->name;
        //    }
       }
       $user = $user->toArray();
       $user['roles'] = array_column($roles->toArray(),'id');
       //$user['rolesStr'] = $id == 1 ? '管理员' : (!empty($roleStr) ? implode(',', $roleStr) : '未分配');
       $user['venues'] = array_column($venues->toArray(),'id');
       //$user['venuesStr'] = (!empty($venues_res)) ? implode(',', $venueStr) : '未分配';
       return success('数据获取成功', $user);
    }
    
    /**
     * @param  integer   $id record id
     * @param array $data  request data
     * @return array|void   返回操作结果
     * @author Red-Bo
     */
    public function updateAdminData(array $data,$id)
    {
        
        try 
        {
            $admin = $this->find($id);
            if($admin)
            {
                DB::beginTransaction();
                $old_picture = $admin->picture;
                foreach(array_keys($this->fields) as $field)
                {
                    if($field == 'picture')
                    {
                        if(strrpos($data[$field],'http:') !== false) {
                            continue;
                        }
                    }
                    $admin->$field = empty($data[$field]) ? $this->fields[$field] : $data[$field];
                }
                unset($admin->roles);
                unset($admin->venues);
                if($data['password'] != '')
                {
                    $admin->password = bcrypt($data['password']);
                }

                if($old_picture != $data['picture'])
                {
                    // 删除旧图
                    $manager = app('uploader');
                    $manager->deleteFile($old_picture);
                }
                // 保存用户信息
                $admin->save();
                
                $roles  = $data['roles'];
                $venues = $data['venues'];
                if(!empty($roles))
                {
                    $admin->giveRoleTo($roles);
                }
                if(!empty($venues))
                {
                    $admin->giveVenueTo($venues);
                }
                DB::commit();
                return success('数据修改成功');
            }
            else
            {
                return errror('数据不存在');
            }
        }
        catch (\Exception $e)
        {
            var_dump($e->__toString());
            DB::rollBack();
            logResult('【管理员数据创建失败】'. $e->__toString(),'error');
            return error($e->getMessage());
        }
       
    }


    public function checkUserName($username, $id)
    {
        $where = [
            'username'=>$username,
        ];
        if($id > 0) {
            $where[] = ['id','!=', $id];
        }
        return  $this->findWhere($where)->toArray();
    }
}
