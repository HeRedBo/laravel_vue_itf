<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Admin\Permission;
use Illuminate\Support\Facades\Auth;
use App\Repositories\StudentRepository;
use App\Repositories\CardRepository;
use App\Repositories\ClassesRepository;
use App\Repositories\VenueRepositoryEloquent;

class IndexController extends ApiController
{
    
    protected $student;
    protected $cards;
    protected $classes;
    protected $venues;
    
    
	public function __construct(
	    StudentRepository $student,
        CardRepository $cards,
        ClassesRepository $classes,
        VenueRepositoryEloquent $venues
    )
	{
		parent::__construct();
		$this->student = $student;
		$this->cards = $cards;
		$this->classes = $classes;
		$this->venues = $venues;
	}
	/**
	 * index
	 */
	public function Index()
	{
	    $user = Auth::guard('admin')->user();
        $permissionsArr = [];
        if($user)
        {
            $permissions = Permission::all();
            foreach ($permissions as $key => $val) {
                if ($user->hasPermission($val->name)) {
                    array_push($permissionsArr, $val->name);
                }
            }
        }
        # dd($permissionsArr);
		return view('admin.index',[
		    'user' => $user,
            'permissions' => json_encode($permissionsArr),
        ]);
	}


	public function menu() 
	{
		$adminMenuData = Request::get('adminMenuData');
		return $this->response->withData($adminMenuData);
	}

	/**
	 * 校验请求路由权限
	 * @return bool
	 */
	public function checkAcl()
	{
		$path        = Request::get('path');
        $routeName   = implode('.', explode('/',$path));
        $routeName = ltrim($routeName,'.');
        $permission  = Permission::where('name',$routeName)->first();
        $check = true;
        if($permission)
        {
            $check  = Gate::check($routeName);
        }
        $res['status']= $check;
        return $this->response->withData($res);
	}
    
	
	public  function statistics()
    {
        $students = $this->student->getNumber();
        $cards    = $this->cards->getNumber();
        $classes  = $this->classes->getNumber();
        $venues  = $this->venues->getNumber();
        $data = compact('students', 'cards', 'classes','venues');
        return $this->response->withData($data);
    }
}

