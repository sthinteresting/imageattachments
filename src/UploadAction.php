<?php namespace S12g\ImageAttachments;

use Flarum\Api\Actions\Action;
use Flarum\Api\Request;
use Illuminate\Contracts\Bus\Dispatcher;
use Zend\Diactoros\Response\JsonResponse;
use Flarum\Core\Settings\SettingsRepository;
use Tobscure\JsonApi\Document;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;
use Illuminate\Support\Str;
use Flarum\Core;
use Exception;

class UploadAction implements Action {
    /**
     * @var Dispatcher $bus
     */
    protected $bus;
    
    /**
     * @var SettingsRepository $settings
     */
    protected $settings;
    
    /**
     * Upload image attachments
     * @param Dispatcher $bus
     * @param FilesystemInterface $uploadDir This will set in extention bootstrap class
     */
    public function __construct(Dispatcher $bus, SettingsRepository $settings)
    {
        $this->bus = $bus;
        $this->settings = $settings;
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
        
        $driver_name = $this->settings->get('imageattachments.driver') ?: 'local';
        $driver_list = [
            'local' => '\S12g\ImageAttachments\Drivers\Local',
            'qiniu' => '\S12g\ImageAttachments\Drivers\Qiniu'
        ];
        $driver = $driver_list[$driver_name];
        if (!$driver) {
            $driver_name = 'local';
            $driver = $driver_list['local'];
        }
        
        $config = $this->settings->get('imageattachments.'.$driver_name.'.config');
        
        $fs = new $driver($config);
        
        try {
            foreach($images as $image_key => $image) {
                $tmpFile = tempnam(sys_get_temp_dir(), 'image');
                $image->moveTo($tmpFile);
                $results['img_'.$image_key] = $fs->saveImage($tmpFile);
            }
        } catch (Exception $e) {
            throw $e;
        }
        return new JsonResponse($results);
    }
}