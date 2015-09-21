<?php
namespace S12g\ImageAttachments\Drivers;

use League\Flysystem\Adapter\Local as LocalFS;
use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;
use Qiniu\Storage\UploadManager;
use Qiniu\Auth;
use Exception;

class Qiniu implements DriverInterface
{
    public $name = 'qiniu';
    protected $config;
    
    public function __construct($config)
    {
        $this->config = $config;
    }
    
    public function saveImage($tmpFile)
    {
        $upManager = new UploadManager();
        $auth = new Auth($this->config['accessToken'], $this->config['secretToken']);
        $token = $auth->uploadToken($this->config['bucketName']);
        $uploadName = date('Y/m/d/').Str::lower(Str::quickRandom()).'.jpg';
        list($ret, $error) = $upManager->put($token, $uploadName, file_get_contents($tmpFile));
        if (!$ret){
            throw new Exception($error->message());
        }
        return rtrim($this->config['baseUrl'], '/').'/'.$uploadName;
    }
    
    public function getConfigItems()
    {
    
    }
}