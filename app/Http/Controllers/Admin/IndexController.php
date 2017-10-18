<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request;
class IndexController extends ApiController
{
	
	public function __construnct() 
	{
		parent::__construnct();

	}
	/**
	 * index
	 */
	public function Index()
	{
		return view('admin.index');
	}


	public function menu() 
	{
		$adminMenuData = Request::get('adminMenuData');
		return $this->response->withData($adminMenuData);
	}
}

