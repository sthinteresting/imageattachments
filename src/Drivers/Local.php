<?php
namespace S12g\ImageAttachments\Drivers;

use League\Flysystem\Adapter\Local as LocalFS;
use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use Illuminate\Support\Str;

class Local implements DriverInterface
{
    public $name = 'local';
    
    public function saveImage($tmpFile)
    {
        $dir = date('Ym/d');
        $urlGenerator = app('Flarum\Http\UrlGeneratorInterface');
        $mount = new MountManager([
            'source' => new Filesystem(new LocalFS(pathinfo($tmpFile, PATHINFO_DIRNAME))),
            'target' => new Filesystem(new LocalFS(public_path('assets/uploads'))),
        ]);
        $uploadName = Str::lower(Str::quickRandom()) . '.jpg';
        $mount->move("source://".pathinfo($tmpFile, PATHINFO_BASENAME), "target://$dir/$uploadName");
        return $urlGenerator->toAsset('uploads/'.$dir.'/'.$uploadName);
    }
    
    public function getConfigItems()
    {
    
    }
}