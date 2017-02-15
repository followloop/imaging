<?php

namespace LOOP\Imaging;

use Illuminate\Support\ServiceProvider;
use Intervention\Image\ImageManager;
use LOOP\Imaging\Services\ImageServiceInterface;
use LOOP\Imaging\Services\ImageProcessingServiceInterface;
use LOOP\Imaging\Services\src\ImageProcessingService;
use LOOP\Imaging\Services\src\ImageService;
use LOOP\Imaging\Services\Validation\ImageValidatorInterface;
use LOOP\Imaging\Services\Validation\src\ImageValidatorLaravel;

/**
 * Class ImagingServiceProvider
 * @package LOOP\Imaging
 */
class ImagingServiceProvider extends ServiceProvider
{

    private $configPath = '/config/imaging.php';


    /**
     *
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.$this->configPath => config_path('imaging.php'),
        ], 'imaging');

        $this->loadMigrationsFrom( __DIR__.'/migrations' );
    }


    /**
     *
     */
    public function register()
    {
        // merge default config
        $this->mergeConfigFrom(
            __DIR__.$this->configPath , 'imaging'
        );

        // Bindings.
        $this->bindValidators();
        $this->bindImageProcessor();
        $this->bindImageService();

        $this->loadHelpers();

        // And commands.
        $this->registerPurgeImagesCommand();
        $this->registerResizeImageCommand();
    }


    /**
     *
     */
    private function bindImageProcessor()
    {
        // Bind the image processing service.
        $this->app->bind( ImageProcessingServiceInterface::class, function ( $app )
        {
            // create an image manager instance with favored driver
            $config = [
                'driver' => config('imaging.driver', 'gd')
            ];

            $imageManager = new ImageManager( $config );

            return new ImageProcessingService( $imageManager );
        });
    }

    /**
     *
     */
    private function bindValidators()
    {
        // Bind the image service.
        $this->app->bind( ImageValidatorInterface::class, function ( $app )
        {
            return $app->make( ImageValidatorLaravel::class );
        });
    }

    /**
     *
     */
    private function bindImageService()
    {
        // Bind the image service.
        $this->app->bind( ImageServiceInterface::class, function ( $app )
        {
            return $app->make( ImageService::class );
        });
    }

    /**
     *
     */
    private function loadHelpers()
    {
        foreach (glob(__DIR__.'/Helpers/*.php') as $filename) require_once( $filename );
    }


    /**
     *
     */
    private function registerPurgeImagesCommand()
    {
        $this->app->singleton('command.imaging.purge-deleted-images', function ($app)
        {
            return $app[ \LOOP\Imaging\Commands\PurgeDeletedImages::class ];
        });

        $this->commands('command.imaging.purge-deleted-images');
    }

    /**
     *
     */
    private function registerResizeImageCommand()
    {
        $this->app->singleton('command.imaging.resize-image', function ($app)
        {
            return $app[ \LOOP\Imaging\Commands\ResizeImage::class ];
        });

        $this->commands('command.imaging.resize-image');
    }


}