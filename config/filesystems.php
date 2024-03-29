<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => public_path('files'),
            'url' => env('APP_URL').'/files',
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'bucket' => env('AWS_BUCKET'),
        ],

        'qiniu' => [
            'driver' => 'qiniu',
            'domain' => env('QINIU_DOMAIN'), // 七牛云链接域名          
            //你的七牛域名，支持 http 和 https，也可以不带协议，默认 http
            'access_key'    => env('QINIU_ACCESS_KEY'),                          //AccessKey
            'secret_key' => env('QINIU_SECRET_KEY'),                             //SecretKey
            'bucket' => env('QINIU_BUCKET'),                                 //Bucket名字
        ],
        'cosv5' => [
          'driver' => 'cosv5',
          'region'          => env('COSV5_REGION', 'ap-guangzhou'),
          'credentials'     => [
              'appId'     => env('COSV5_APP_ID'),
              'secretId'  => env('COSV5_SECRET_ID'),
              'secretKey' => env('COSV5_SECRET_KEY'),
          ],
          'timeout'         => env('COSV5_TIMEOUT', 60),
          'connect_timeout' => env('COSV5_CONNECT_TIMEOUT', 60),
          'bucket'          => env('COSV5_BUCKET'),
          'cdn'             => env('COSV5_CDN'),
          'scheme'          => env('COSV5_SCHEME', 'https'),
          'read_from_cdn'   => env('COSV5_READ_FROM_CDN', false),
          'cdn_key'         => env('COSV5_CDN_KEY'),
        ],
        


    ],

];
