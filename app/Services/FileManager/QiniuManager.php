<?php
namespace App\Services\FileManager;

use Carbon\Carbon;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Intervention\Image\Facades\Image;
use Illuminate\Http\File;

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
     * @param string  $dir
     * @param string  $name
     * @param array  $thumb
     * @return array|bool
     */
    public function storeFile(UploadedFile $file, $dir = '', $name = '', $thumb = [])
    {
        $extension =$file->getClientOriginalExtension();
        $fileName  = date('YmdHis') . microtime(true). rand(109, 999) . '.' .$extension;
        $hashName  = empty($name) ?$fileName : $name;
        $mime      = $file->getMimeType();

        // 保存文件
        $realPath = $this->disk->putFileAs($dir, $file, $hashName);
        // 处理文件截图 保存一张那个 200 * 200 的缩略图 有利于减少服务器获取开销
        $img_url = $this->fileWebPath($realPath);
        $thumb_result = [];
        if($thumb)
        {
            foreach ($thumb as $k =>  $v)
            {
                $thumb_name =  'thumb_'. $k . $hashName;
                // 图片先保存到本地
                $thumb_img =  Image::make($img_url)->resize(200, 200)->save($thumb_name);
                if($thumb_img)
                {
                    // 图片上传到七牛云
                    $pubic_img_path = public_path($thumb_name);
                    $thumbRealPath = $this->disk->putFileAs($dir.'/thumb',  $thumb_img_obj = new File($pubic_img_path), $thumb_name);
                    $thumb_result[$k] =  [
                        'real_path'     => $thumbRealPath,
                        'url'           =>$this->fileWebPath($thumbRealPath),
                    ];
                    @unlink($pubic_img_path);
                }
            }

        }

        $result =  [
            'success'       => true,
            'filename'      => $hashName,
            'original_name' => $file->getClientOriginalName(),
            'mime'          => $mime,
            'size'          => human_filesize($file->getClientSize()),
            'real_path'     => $realPath,
            'url'           =>$this->fileWebPath($realPath),
        ];

        if($thumb_result)
        {
            $result['thumb'] = $thumb_result;
        }
        return $result;
    }
    
}