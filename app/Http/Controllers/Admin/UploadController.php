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
        $folder = public_path('files');
        $data = $this->manager->folderInfo($folder);
        return view('admin.upload.index', $data);
    }
}
