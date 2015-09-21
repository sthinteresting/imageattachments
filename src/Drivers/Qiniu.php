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
    
    public function __construct($config) {
        
    }
    
    public function saveImage($tmpFile)
    {
        $upManager = new UploadManager();
        $auth = new Auth('a', 's');
        $token = $auth->uploadToken('b');
        $uploadName = date('Y/m/d/').Str::lower(Str::quickRandom()).'.jpg';
        list($ret, $error) = $upManager->put($token, $uploadName, file_get_contents($tmpFile));
        if (!$ret){
            throw new Exception($error->message());
        }
        return 'http://7o51ac.com1.z0.glb.clouddn.com/'.$uploadName;
    }
    
    public function getConfigItems()
    {
    
    }
}