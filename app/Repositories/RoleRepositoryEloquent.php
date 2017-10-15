<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\RoleRepository;
use App\Models\Admin\Role;
use App\Models\Admin\Permission;
use App\Validators\RoleValidator;
use Illuminate\Http\Request;

/**
 * Class RoleRepositoryEloquent
 * @package namespace App\Repositories;
 */
class RoleRepositoryEloquent extends BaseRepository implements RoleRepository
{
    protected $fields = [
        'name' => '',
        'display_name' => '',
        'description' => '',
    ];

    protected $fieldSearchable = [
        'name'=>'like',
    ];
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Role::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }


    /**
     *  get Role Access Control List
     *
     * @param int  $orleId
     * @return void
     */
    public function getAcl($roleId)
    {
        $role = $this->find($roleId);
        $data = [];
        if($role)
        {
            $data['tree'] = $role->getTreeData($roleId);
        }

        return success('数据获取成功',$data);
    }

    public function setAcl(Request $request)
    {
        $id = $request->get('id');
        $role = Role::find((int) $id);
        $role->permissions()->sync($request->get('permission',[]));
        return success('权限设置成功');
    }

    public function  checkRoleName($name, $id)
    {
        $where = [
            'name'=>$name,
        ];
        if($id > 0) {
            $where[] = ['id','!=', $id];
        }
        return  $this->findWhere($where)->toArray();
    }


     /**
     * 道馆数据更新
     * @param array $data 需要更新的数据
     * @param int   $id
     * @author Red-Bo
     */
    public  function updateRoleData(array $data, $id)
    {
        $role = $this->find($id);
        if($role)
        {
           
            // 设置字段默认值
            foreach(array_keys($this->fields) as $field)
            {
                
                $role->$field = empty($data[$field]) ? $this->fields[$field] : $data[$field];
            }
            $role->save();
            return success('数据更新成功');
        }
        return error('记录不存在，请检查');
    }
}
