<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\FileManager\BaseManager;
use Intervention\Image\Facades\Image; // laravel图片裁切们门面

class UploadController extends ApiController
{
    protected $manager;

    public function __construct()
    {
        parent::__construct();
        $this->manager = app('uploader');
    }

     /**
     * Show page of files / subfolders
     */
    public function index(Request $request)
    {
        //$folder = $request->get('folder');
        //$folder = public_path('files');
        //$data = $this->manager->folderInfo($folder);
        return view('admin.upload');
    }
    
    public  function uploadImg(Request $request)
    {
        $file = $request->file('files');
        $allowed_extensions = ['png','jpg','gif','jpeg'];
        if($file->getClientOriginalExtension()
            && !in_array($file->getClientOriginalExtension(), $allowed_extensions))
        {
            return  'error|You may only upload png ,gif, jpg, or jpeg.';
        }
        $destinationPath = "files/images/";
        $extension =$file->getClientOriginalExtension();
        $res = $this->manager->storeFile($file, $destinationPath);
        dd($res);
    }
    
    public  function  fileDetail()
    {
        $path = 'files/images/062OMR4KgXOFFoZMiCaRpy6g0xApf0VDRoZFADdE.jpg';
        $imgInfo = $this->manager->fileDetails($path);
        dd($imgInfo);
    }
    
    
    
    public  function getFileList()
    {
        $path = 'files';// public_path('files/files');
        $list = $this->manager->getFileList($path);
        dd($list);
    }
    
    public function uploadAvatar(Request $request)
    {
        $file = $request->file('avatar');
        $need_thumb = $request->get('thumb',0) ?: 1;
        $allowed_extensions = ['png','jpg','gif','jpeg'];
        if($file->getClientOriginalExtension()
            && !in_array($file->getClientOriginalExtension(), $allowed_extensions))
        {
            return $this->response->withUnprocessableEntity('文件格式有误');
        }
        $destinationPath = "files/avatar";
        // 设置需要返回的缩略图规格
        $img_thumb = [
            [200, 200]
        ];
        $res = $this->manager->storeFile($file, $destinationPath,'',$img_thumb);
        return $this->response->withData($res);
    }
}
