<?php
namespace App\Services\FileManager;

use Carbon\Carbon;

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
    
}