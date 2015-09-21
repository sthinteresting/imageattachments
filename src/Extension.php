<?php namespace S12g\ImageAttachments;

use Flarum\Support\Extension as BaseExtension;
use Illuminate\Events\Dispatcher;
use Flarum\Events\RegisterApiRoutes;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class Extension extends BaseExtension
{
    public function listen(Dispatcher $events)
    {
        // add client assets
        $events->subscribe('S12g\ImageAttachments\Listeners\AddClientAssets');
        // register upload api
        $events->listen(RegisterApiRoutes::class, function (RegisterApiRoutes $event) {
            $event->post(
                '/s12g/image_attachments',
                's12g.imageattachments.upload',
                'S12g\ImageAttachments\UploadAction'
            );
        });
        // register image provider
        $uploadsFilesystem = function () {
            return new Filesystem(new Local(public_path('assets/uploads')));
        };
        $this->app->when('S12g\ImageAttachments\UploadAction')
            ->needs('League\Flysystem\FilesystemInterface')
            ->give($uploadsFilesystem);
    }
}
