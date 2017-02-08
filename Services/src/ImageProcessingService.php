<?php

namespace LOOP\Imaging\Services\src;

use LOOP\Imaging\Services\ImageProcessingServiceInterface;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

/**
 * Class ImageProcessingService
 * @package LOOP\Imaging\Services\src
 */
class ImageProcessingService implements ImageProcessingServiceInterface
{

    protected $intervention;
    protected $disk;

    protected $availableSettings = [
        'maintain_aspect_ratio'     => TRUE,
        'prevent_upsizing'          => TRUE,
        'crop'                      => FALSE,
        'extension'                 => 'jpg',
        'quality'                   => 90
    ];


    /**
     * @param ImageManager $imageManager
     */
    public function __construct( ImageManager $imageManager )
    {
        $this->intervention = $imageManager;
        $this->disk = Storage::disk( config( 'imaging.local_disk_name', 'local' ) );
    }


    /**
     * @param $b64StringOrURL
     * @param $destinationFolder
     * @return bool|string
     */
    public function createImageFromB64StringOrURL( $b64StringOrURL, $destinationFolder )
    {
        $filename = md5( $b64StringOrURL  ).'.jpg';
        $image = $this->intervention->make( $b64StringOrURL );

        $folderPath = rtrim( $destinationFolder, '/' ) ;
        $pathWithFile = $folderPath . '/' . $filename;

        if ( !$this->disk->exists( $folderPath ) ) $this->disk->makeDirectory( $folderPath, 0775);

        try
        {
            $result = $image->save( $pathWithFile, 100 );
        } catch( \Exception $e )
        {
            $result = FALSE;
        }

        return $result ? $pathWithFile : FALSE;
    }


    /**
     * @param $sourceImage
     * @param $destinationFolder
     * @param array $sizes
     * @param array $extraSettings
     * @return array
     */
    public function resizeOrCropImageToSizes( $sourceImage, $destinationFolder, array $sizes, array $extraSettings = [] )
    {
        $resizedImages = [];

        if ( $this->disk->exists( $sourceImage ) )
        {
            // Now we have to do it like this because the intervention library needs the full path.
            $image = $this->intervention->make( $sourceImage );

            if ( $image )
            {
                $settings = $this->parseSettings( $extraSettings );

                foreach( $sizes as $size )
                {
                    $workWithThisImage = clone $image;

                    $sizesArray = explode( 'x', $size );

                    if ( count( $sizesArray ) == 2 )
                    {
                        $width = $sizesArray[0];
                        $height = $sizesArray[1];

                        $width = $width ? $width : NULL;
                        $height = $height ? $height : NULL;

                        $crop = @$settings['crop'];
                        $maintainAspectRatio = @$settings['maintain_aspect_ratio'];
                        $preventUpsizing = @$settings['prevent_upsizing'];

                        if ( !$crop )
                        {
                            $workWithThisImage->resize( $width, $height, function ($constraint) use ( $maintainAspectRatio, $preventUpsizing )
                            {
                                if ( $maintainAspectRatio ) $constraint->aspectRatio();
                                if ( $preventUpsizing ) $constraint->upsize();
                            });
                        } else
                        {
                            $workWithThisImage->fit( $width, $height, function ($constraint) use ( $preventUpsizing ) {
                                if ( $preventUpsizing ) $constraint->upsize();
                            });
                        }

                        $folderName = '_'.$size;

                        $pathInfo = pathinfo( $sourceImage );
                        $filename = @$pathInfo['filename'];

                        $extension = @$settings['extension'];
                        $extension = is_null( $extension ) ? $pathInfo['extension'] : $extension;
                        $quality = (int)@$settings['quality'];

                        $saveInFolder = rtrim( $destinationFolder, '/' ) . '/' . $folderName;

                        if ( !$this->disk->exists( $saveInFolder ) ) $this->disk->makeDirectory( $saveInFolder );

                        // Once again: intervention is not able to save the image if we don't provide the full path.
                        $partialNewPath = $saveInFolder . '/' . $filename . '.' . $extension;
                        //$fullNewPath = storage_path( $partialNewPath );

                        $workWithThisImage->save( $partialNewPath, $quality);

                        // Return the base path.
                        $resizedImages[ $folderName ] = $partialNewPath;
                    }
                }
            }
        }

        return $resizedImages;
    }


    /**
     * @param array $extraSettings
     * @return array
     */
    protected function parseSettings( array $extraSettings )
    {
        $parsedSettings = [];
        foreach( $this->availableSettings as $availableSetting => $defaultValue )
        {
            if ( array_key_exists( $availableSetting, $extraSettings ) ) $parsedSettings[ $availableSetting ] = $extraSettings[ $availableSetting ];
            else $parsedSettings[ $availableSetting ] = $defaultValue;
        }

        return $parsedSettings;
    }

}