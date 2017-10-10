<?php
namespace App\Services\FileManager;

use Carbon\Carbon;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class QiniuManager extends BaseManager
{
    
    
    public function fileWebPath($path)
    {
        return $this->disk->downloadUrl($path);
    }
    
    /**
     * Get the file's size by the path.
     *
     * @param $path
     * @return mixed
     */
    public function fileSize($path)
    {
        return human_filesize($this->disk->size($path));
    }
    
    /**
     * Get the file's last modified time by the path.
     *
     * @param $path
     * @return string
     */
    public function fileModified($path)
    {
        return Carbon::createFromTimestamp(
            substr($this->disk->lastModified($path), 0, 10)
        )->toDateTimeString();
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
        $extension =$file->getClientOriginalExtension();
        //$extension = str_ireplace('jpeg', 'jpg', $extension);
        //$fileName = date('ymdHis') . microtime(true). rand(10, 99) . '.' .$extension;
        $fileName = date('YmdHis') . microtime(true). rand(109, 999) . '.' .$extension;
        //str_ireplace('.jpeg', '.jpg', $fileName)
        $hashName = empty($name)
            ?$fileName
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
            'url'           =>$this->fileWebPath($realPath),
        ];
    }
    
}