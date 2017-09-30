<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Role extends Model implements Transformable
{

    protected  $table = "admin_roles";
    use TransformableTrait;

    protected $dates = ['created_at', 'updated_at'];

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'display_name',
    ];


    /**
     * 属于该身份的用户。
     */
    public function users()
    {
        return $this->belongsToMany(Admin::class,'admin_user_role','role_id','user_id');
    }


    /**
     * 属于该角色的权限
     *
     * @return void
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission','role_id','permission_id');
    }

    
    public function getTreeData()
    {
        $data = [];
        $permission = new permission();
        $list = $permission->query()
            ->orderBy('order_num','ASC')
            ->get()
            ->toArray();

        foreach ($list as $k => $v) 
        {
            if($v['parent_id'] == 0) 
            {
                $data[$k]['id'] = $v['id'];
                $data[$k]['parent'] = '#';
                $data[$k]['text'] = $v['display_name'];
                $data[$k]['state'] = ['opened' => true];
            } 
            else
            {
                $data[$k]['id'] = $v['id'];
                $data[$k]['parent']= $v['parent_id'];
                $data[$k]['text'] = $v['display_name'];

            }

       
            $isExists = $this->permissions->contains('id',$v['id']);
            if($isExists)
            {
                $data[$k]['state'] = ['opened' => true, 'selected' => true];
            }

        }

        return $data;
    }

}
