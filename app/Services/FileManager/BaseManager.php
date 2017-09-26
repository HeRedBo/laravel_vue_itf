<?php

namespace App\Services\FileManager;

use Carbon\Carbon;
use Dflydev\ApacheMimeTypes\PhpRepository;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
/**
 * 文件管理基本服
 */
class BaseManager 
{

    protected $disk;
    protected $mimeDetect;
    

    public function __construct(PhpRepository $mimeDetect)
    {
        $this->disk = Storage::disk(config('filesystems.default', 'public'));
        $this->mimeDetect = $mimeDetect;
    }


    /**
     * Return files and directories within a folder
     *
     * @param string $folder
     * @return array of [
     *     'folder' => 'path to current folder',
     *     'folderName' => 'name of just current folder',
     *     'breadCrumbs' => breadcrumb array of [ $path => $foldername ]
     *     'folders' => array of [ $path => $foldername] of each subfolder
     *     'files' => array of file details on each file in folder
     * ]
     */

    public function folderInfo($folder)
    {
        $folder = $this->cleanFolder($folder);

        $breadcrumbs = $this->breadcrumbs($folder);
        $slice       = array_slice($breadcrumbs, -1);
        $folderName  = current($slice);
        $breadcrumbs = array_slice($breadcrumbs, 0, -1);

        $subfolders = [];
        foreach (array_unique($this->disk->directories($folder)) as $subfolder) {
            $subfolders["/$subfolder"] = basename($subfolder);
        }

        $files = [];
        foreach ($this->disk->files($folder) as $path) {
            $files[] = $this->fileDetails($path);
        }

        return compact(
            'folder',
            'folderName',
            'breadcrumbs',
            'subfolders',
            'files'
        );
    }

    /**
     * Sanitize the folder name
     */
    protected function cleanFolder($folder)
    {
        return '/' . trim(str_replace('..', '', $folder), '/');
    }


    /**
     * 返回当前目录路径
     */
    protected function breadcrumbs($folder)
    {
        $folder = trim($folder, '/');
        $crumbs = ['/' => 'root'];

        if (empty($folder)) {
            return $crumbs;
        }

        $folders = explode('/', $folder);
        $build = '';
        foreach ($folders as $folder) {
            $build .= '/'.$folder;
            $crumbs[$build] = $folder;
        }

        return $crumbs;
    }

     /**
     * 返回文件详细信息数组
     */
    protected function fileDetails($path)
    { 
        $path = '/' . ltrim($path, '/');

        return [
            'name'     => basename($path),
            'fullPath' => $path,
            'webPath'  => $this->fileWebpath($path),
            'mimeType' => $this->fileMimeType($path),
            'size'     => $this->fileSize($path),
            'modified' => $this->fileModified($path),
        ];
    }

    
    /**
     * 返回文件完整的web路径
     */
    public function fileWebpath($path)
    { 
        $path = rtrim(config('blog.uploads.webpath'), '/') . '/' .ltrim($path, '/');
        return url($path);
    }

    /**
     * 返回文件MIME类型
     */
    public function fileMimeType($path)
    {
        return $this->mimeDetect->findType(
            pathinfo($path, PATHINFO_EXTENSION)
        );
    }

    /**
     * 返回文件大小
     */
    public function fileSize($path)
    {
        return $this->disk->size($path);
    }

    /**
     * 返回最后修改时间
     */
    public function fileModified($path)
    {
        return Carbon::createFromTimestamp(
            $this->disk->lastModified($path)
        );
    }


    /**
     * 创建新目录
     */
    public function createDirectory($folder)
    {
        $folder = $this->cleanFolder($folder);

        if ($this->disk->exists($folder)) {
            throw new UploadException("Folder '$folder' already exists.");
        }

        return $this->disk->makeDirectory($folder);
    }

    /**
     * 删除目录
     */
    public function deleteDirectory($folder)
    {
        $folder = $this->cleanFolder($folder);

        $filesFolders = array_merge(
            $this->disk->directories($folder),
            $this->disk->files($folder)
        );
        if (! empty($filesFolders)) {
            return false;
        }

        return $this->disk->deleteDirectory($folder);
    }


    /**
     * 保存文件
     */
    public function saveFile($path, $content)
    {
        $path = $this->cleanFolder($path);

        if ($this->disk->exists($path)) {
            return "File already exists.";
        }

        return $this->disk->put($path, $content);
    }

     /**
     * Handle the file upload.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @param string                                              $dir
     * @param string                                              $name
     *
     * @return array|bool
     */
    public function storeFile(UploadedFile $file, $dir = '', $name = '')
    {
        $hashName = emnty($name) 
                    ? str_ireplace('.jpeg', '.jpg', $file->hashName()) 
                    : $name;
        $mime = $file->getMimeType();
        $realPath = $this->disk->putFileAs($dir, $file, $hashName);
        return [
            'success'       => true,
            'filename'      => $hashName,
            'original_name' => $file->getClientOriginalName(),
            'mime'          => $mime,
            'size'          => human_filesize($file->getClientSize()),
            'real_path'     => $realPath,
            'relative_url'  => public_path('files')."/$realPath",
            //'url'           => asset(public_path('files')."/$realPath"),
            'url'           =>$this->disk->url($realPath),
        ];
    }

    /**
     * 保存base64 图片文件
     *
     * @param string $base64_image_content base图片文件
     * @param string $path 图片存放路径
     * @return void  
     */
    public function saveBase64File($base64_image_content, $path = 'images')
    {
        $new_file = '';
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)) {
            $type     = $result[2];
            $name     = date('YmdHis') . microtime(true) . rand(100, 999) . '.' . $type;
            $new_file = $path . '/' . $name;
            $contents = base64_decode(str_replace($result[1], '', $base64_image_content));
            $res      = $this->disk->put($new_file,$contents); 
        }
        return $new_file;
    }

    /**
    * 删除文件
    */
   
   /**
    *  删除文件
    * @param  [type] $path [description]
    * @return [type]       [description]
    */
    public function deleteFile($path)
    {
        $path = $this->cleanFolder($path);
        if (! $this->disk->exists($path)) {
            return "File does not exist.";
        }
        return $this->disk->delete($path);
    }

    

}