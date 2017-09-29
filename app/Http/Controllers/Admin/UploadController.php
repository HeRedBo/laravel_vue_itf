<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\FileManager\BaseManager;

class UploadController extends Controller
{
    protected $manager;

    public function __construct(BaseManager $manager)
    {
        $this->manager = $manager;
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
        $fileName = date('ymdHis') . microtime(true). rand(10, 99) . '.' .$extension;
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
}
