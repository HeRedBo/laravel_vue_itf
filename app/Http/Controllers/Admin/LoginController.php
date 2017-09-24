<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use  App\Services\ApiServer\ApiResponse;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Admin\ApiController;


class LoginController extends ApiController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * LoginController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('guest:admin')->except('logout');
    }

    public  function  username()
    {
        return 'username';
    }

    protected function guard()
    {
        return Auth::guard('admin');
    }

    public function  showLoginForm()
    {
        return redirect('/admin/login');
    }

    /**
     * 重写登录
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            $this->username() => 'required',
            'password' => 'required|min:6'
        ],[
            'username.required' => '邮箱必须',
            'password.required' => '密码必须',
        ]);

        if($validator->fails())
        {
            $errors= $validator->errors()->toArray();
            return $this->response->setStatusCode(412)->withData($errors);
        }


        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }
        if ($this->attemptLogin($request)) {
            $request->session()->regenerate();
            $this->clearLoginAttempts($request);
            return $this->response->withSuccess('登录成功');
        }
        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);
        return $this->response->withError('用户名或密码错误');
    }
}
