<?php namespace S12g\ImageAttachments;

use Flarum\Api\Actions\Action;
use Flarum\Api\Request;
use Illuminate\Contracts\Bus\Dispatcher;
use Zend\Diactoros\Response\JsonResponse;
use Tobscure\JsonApi\Document;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;
use Illuminate\Support\Str;
use Flarum\Core;

class UploadAction implements Action {
    /**
     * @var Dispatcher $bus
     */
    protected $bus;
    
    /**
     * @var FilesystemInterface $uploadDir
     */
    protected $uploadDir;
    
    /**
     * Upload image attachments
     * @param Dispatcher $bus
     * @param FilesystemInterface $uploadDir This will set in extention bootstrap class
     */
    public function __construct(Dispatcher $bus, FilesystemInterface $uploadDir)
    {
        $this->bus = $bus;
        $this->uploadDir = $uploadDir;
    }
    
    /**
     * Handle upload requests
     * @param Request $request
     * @todo Add upload event
     */
    public function handle(Request $request)
    {
        $images = $request->http->getUploadedFiles()['images'];
        $results = [];
        $fs = new \S12g\ImageAttachments\Drivers\Local();
        foreach($images as $image_key => $image) {
            $tmpFile = tempnam(sys_get_temp_dir(), 'image');
            $image->moveTo($tmpFile);
            $results['img_'.$image_key] = $fs->saveImage($tmpFile);
        }
        return new JsonResponse($results);
    }
}