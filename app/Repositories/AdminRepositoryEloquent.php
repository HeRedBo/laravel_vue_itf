<?php
namespace App\Repositories;

use Exception;
use App\Models\Admin\Admin;
use Illuminate\Http\Request;
use App\Models\Admin\AdminLogger;
use Illuminate\Support\Facades\DB;
use App\Repositories\AdminRepository;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

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

    const DEFAULT_PAGE_SIZE = 15;

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
        $roleStr = $venueStr = [];
       if($user)
       {
           $roles = $user->roles;
           $venues = $user->venues;
           foreach ($roles as $role)
            {
                //$roles_res[] = ['label' => $role->name, 'value' => $role->id];
                $roleStr[]   = $role->name;
            }
            foreach ($venues as $venue)
            {
                //$venues_res[] = ['label' => $venue->name, 'value' => $venue->id];
             
                $venueStr[] = $venue->name;
            }
           $user = $user->toArray();
           $user['roles'] = array_column($roles->toArray(),'id');
           $user['rolesStr'] = $id == 1 ? '超级管理员' : (!empty($roleStr) ? implode(',', $roleStr) : '未分配');
           $user['venues'] = array_column($venues->toArray(),'id');
           $user['venuesStr'] = (!empty($roleStr)) ? implode(',', $venueStr) : '未分配';
           return success('数据获取成功', $user);
       }
       else {
           return error('数据不存在');
       }
       
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
                if(isset($data['password']) && $data['password'] != '')
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
                return error('数据不存在');
            }
        }
        catch (\Exception $e)
        {
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


    public function deleteUser($id)
    {
        $user = $this->find($id);
        if($user) {
            $picture = $user->picture;
            $old_picture = $user->picture;
            if($old_picture)
            {
                 // 删除用户图片
                $manager = app('uploader');
                $manager->deleteFile($old_picture);
                
            }
            $user->delete();
            return success('数据删除成功');
        } else {
            return error('数据不存在');
        }
           
    }
    
    public  function  getUserVenues($uid)
    {
        $user = $this->find($uid);
        $user_venues = [];
        if($user)
        {
            $user_venues = $user->venues->toArray();
        }
        return success('ok', $user_venues);
    }

    /**
     * 管理员操作日志数据表
     * @param  Request $reqeust 
     * @return array 
     */
    public function logger(Request $request)
    {
        try 
        {
            $admin_user_id = auth('admin')->user()->id;
            $admin_venue_ids = [];
            if($admin_user_id !== 1) 
            {
                $admin_info      = Admin::with("venues")->find($admin_user_id);
                $admin_venues    = $admin_info->venues->toArray();
                $admin_venue_ids = array_column($admin_venues, 'id');
            }
            $orderBy   = $orderBy = $request->get('orderBy')?:'id';
            $sortBy    = $request->get('sortedBy')?:'desc';
            $intro     = $request->get('intro')?:'';
            $venue_id  = $request->get('venue_id')?:0;
            $pageSize  = $request->get('pageSize') ?: self::DEFAULT_PAGE_SIZE;
            $user_name = $request->get('user_name','');
            $query     = AdminLogger::query()->with(['users']);
            
            $query->join('venues', 'admin_logger.venue_id', '=', 'venues.id')
                    ->join('admin','admin_logger.user_id', '=', 'admin.id')
                    ->select('admin_logger.*',"venues.name as venue_name");


            $where = [];
            if(!empty($venue_id))
            {
                $where[] =['admin_logger.venue_id','=', $venue_id];
            }

            if(!empty($intro))
            {
                $where[] = ['admin_logger.intro','like',"%{$intro}%"];
            }
            if($admin_venue_ids)
                $query->whereIn('admin_logger.venue_id', $admin_venue_ids);
            if($user_name) {
                $where[] = ['admin.name',"like","%{$user_name}%"];
            }
            if($where)
            {
                foreach($where as $v)
                {
                    $query->where($v[0], $v[1], $v[2]);
                }
            }
            $query->orderBy($orderBy, $sortBy);
            $data = $query->paginate($pageSize)->toArray();
        } 
        catch (Exception $e) 
        {
            logResult('[获取管理操作日志错误].'. $e->__toString(), 'error');
            return error($e->getMessage());
        }
        return success('数据获取成功',$data);

    }

    /**
     * 获取管理员学生列表数据
     *
     * @param Request $request
     *
     */
    public  function  getUserList(Request $request)
    {
        $query = Admin::query()->with(["venues","roles"]);
        
        $query->leftJoin('admin_user_role', 'admin.id','=', 'admin_user_role.user_id')
            ->leftJoin("admin_roles","admin_user_role.role_id",'=', "admin_roles.id")
            ->leftJoin("admin_venue","admin_venue.admin_id","admin.id")
            ->leftJoin("venues","admin_venue.venue_id","=","venues.id");
        
        $fields = ["admin.*"];
        $pageSize  = $request->get('pageSize') ?: self::DEFAULT_PAGE_SIZE;
        $orderBy   = $orderBy = $request->get('orderBy')?:'id';
        $sortBy    = $request->get('sortedBy')?:'desc';
        $query_name= $request->get('query_name');
        $role_id= $request->get('role_id');
        $venue_id= $request->get('venue_id');

        $or_where  = $where = $whereIn =  [];
        if(!empty($query_name))
        {
            $or_where = [
                ["admin.username","like","%{$query_name}%"],
                ["admin.name","like","%{$query_name}%"],
                ["admin.phone","like","%{$query_name}%"],
                ["admin.email","like","%{$query_name}%"],
            ];
        }

        if(!empty($role_id))
        {
            if(is_array($role_id))
            {
                $whereIn[] = ["admin_user_role.role_id", $role_id];
            }
            else
                $where[] = ["admin_user_role.roleL_id",'=', $role_id];
        }

        if(!empty($venue_id))
        {
            if(is_array($venue_id))
            {
                $whereIn[] = ["admin_venue.venue_id", $venue_id];
            }
            else
                $where[] = ["admin_venue.venue_id",'=', $venue_id];
        }
        
        if(!empty($name))
        {
            $where[] = ["admin.name"];
        }
        $where    = [];
        if($or_where)
        {
            foreach ($or_where as $v) {
                $query->orWhere($v[0], $v[1], $v[2]);
            }
        }
        if($whereIn)
        {
            foreach ($whereIn as $v) {
                $query->whereIn($v[0], $v[1]);
            }
        }
        if($where)
        {
            foreach ($where as $v) {
                $query->where($v[0], $v[1], $v[2]);
            }
        }
        $query->groupBy("admin.id");
        $query->orderBy($orderBy, $sortBy);
        return  $query->select($fields)->paginate($pageSize)->toArray();
    }
}
