<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Admin\Admin;
use App\Models\Admin\Permission;
use App\Models\Admin\Role;
use Auth, Cache,DB;

class Menu
{
    protected $tb_admin_user_role = 'admin_user_role';
    protected $tb_admin_roles = 'admin_roles';
    protected $tb_admin_role_permission = 'admin_role_permission';
    protected $tb_admin_permissions = 'admin_permissions';
    const SHOW_OK = 1;
    const ADMIN_UID = 1;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $request->attributes->set('adminMenuData', $this->getMenu());
        return $next($request);
    }

    protected function getMenu()
    {
        Cache::forget('menus');
        $data = Cache::store(config('cache.default','file'))->get('menus', function() {

            $data   = [];
            $uid    = Auth::guard('admin')->user()->id;
            $query = DB::table($this->tb_admin_user_role);
            $where = [
                ["admin_permissions.is_show","=",self::SHOW_OK],
                ["admin_user_role.user_id","=",$uid]
            ];
            if($uid == self::ADMIN_UID)
            {
                $permissions = DB::table($this->tb_admin_permissions)
                                ->where("is_show",'=', self::SHOW_OK)
                                ->orderBy('order_num','ASC')
                                ->get();
            }
            else
            {
                $query->join($this->tb_admin_roles, $this->tb_admin_user_role.".role_id","=", $this->tb_admin_roles.'.id')
                    ->join($this->tb_admin_role_permission, $this->tb_admin_user_role.".role_id","=",$this->tb_admin_role_permission.".role_id")
                    ->join($this->tb_admin_permissions, $this->tb_admin_role_permission.".permission_id","=",$this->tb_admin_permissions.".id")

                ;
                $query->select([$this->tb_admin_permissions.".*"]);
                if($where)
                {
                    foreach ($where as $k => $v)
                    {
                        $query->where($v[0], $v[1], $v[2]);
                    }
                }

                $query->orderBy('order_num','ASC');
                $permissions = $query->get();
            }
            $permissionIds = [];
            if($permissions)
            {
                $permissions = $permissions->toArray();
                $permissionIds = array_column($permissions,'id');
            }

             $level0 = Permission::where('parent_id','0')
                                 ->orderBy('order_num','ASC')
                                 ->get()
                                 ->toArray();
            $parentIds  =  array_unique(array_column($level0,'id'));
            $subLevels  = [];
            foreach ($parentIds as $k => $parentId)
            {
                foreach ($permissions as $kk => $v)
                {
                    if($v->parent_id == $parentId)
                    {
                        $subLevels[$v->parent_id][] = $v;
                    }
                }
            }
            foreach ($level0 as $key => $val)
            {
                $subLevel = isset($subLevels[$val['id']]) ? $subLevels[$val['id']] : [];
                foreach ($subLevel as $k => $v)
                {
                    $subLevel[$k]->url = '/' .str_replace('.', '/', $v->name);
                }
                $val['url'] =  '/' .str_replace('.', '/', $val['name']);
                $data[$val['name']] = $val;
                $data[$val['name']]['children'] = [];
                if(!empty($subLevel)) {
                    $data[$val['name']]['children'] = $subLevel;
                }
                else
                {
                    if(!in_array($val['id'], $permissionIds))
                    {
                        unset( $data[$val['name']]);
                    }
                }
            }
            Cache::put('menus', $data);
            return $data;

            // $level0 = Permission::where('parent_id','0')
            //                     ->orderBy('order_num','ASC')
            //                     ->get()
            //                     ->toArray();

            // $parentIds  =  array_unique(array_column($level0,'id'));

            // $subPermissions = Permission::whereIn('parent_id',$parentIds)
            //                   ->where('is_show',1)
            //                   ->orderBy('order_num','ASC')
            //                   ->get()
            //                   ->toArray();

            // $subLevels = [];
            // foreach ($subPermissions as $key => $val)
            // {
            //     $subLevels[$val['parent_id']][] = $val;
            // }

            // foreach ($level0 as $key => $val)
            // {

            //     $subLevel = isset($subLevels[$val['id']]) ? $subLevels[$val['id']] : [];
            //     foreach ($subLevel as $k => $v)
            //     {
            //         $subLevel[$k]['url'] = '/' .str_replace('.', '/', $v['name']);
            //         if(!$user->hasPermission($v['name']) && $uid != 1) {
            //             unset($subLevel[$k]);
            //         }
            //     }

            //     $val['url'] =  '/' .str_replace('.', '/', $val['name']);

            //     $data[$val['name']] = $val;
            //     $data[$val['name']]['children'] = [];

            //     if(!empty($subLevel)) {
            //         $data[$val['name']]['children'] = $subLevel;
            //     }
            // }
            // Cache::put('menus', $data);
            // return $data;

        });
        return $data;

    }
}
