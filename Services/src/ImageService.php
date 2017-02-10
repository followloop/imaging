<?php

namespace LOOP\Imaging\Services\src;

use LOOP\Imaging\Events\ImageWasCreated;
use LOOP\Imaging\Events\ImageWasProcessed;
use LOOP\Imaging\Events\ImageWasRemoved;
use LOOP\Imaging\Models\Image;
use LOOP\Imaging\Services\ImageProcessingServiceInterface;
use LOOP\Imaging\Services\ImageServiceInterface;
use LOOP\Imaging\Services\Validation\ImageValidatorInterface;


/**
 * Class ImageService
 * @package LOOP\Imaging\Services\src
 */
class ImageService implements ImageServiceInterface
{
    protected $imageValidator;
    protected $imageProcessingService;


    /**
     * @param ImageValidatorInterface $imageValidatorInterface
     * @param ImageProcessingServiceInterface $imageProcessingServiceInterface
     */
    public function __construct(
        ImageValidatorInterface $imageValidatorInterface,
        ImageProcessingServiceInterface $imageProcessingServiceInterface
    )
    {
        $this->imageValidator = $imageValidatorInterface;
        $this->imageProcessingService = $imageProcessingServiceInterface;
    }


    /**
     * @param $imageB64OrUploadedFile
     * @param array $options
     * @return bool
     */
    public function createImage( $imageB64OrUploadedFile, array $options = [] )
    {
        $path = @$options['path'];
        $sizes = @$options['sizes'];
        $sizes = is_array( $sizes ) ? $sizes : [];

        $imagePath = $this->imageProcessingService->createImageFromB64StringOrURL( $imageB64OrUploadedFile, $path );

        if ( $imagePath )
        {
            $filename = basename( $imagePath );

            $data = [
                'path' => $imagePath,
                'filename' => $filename,
                'processed' => FALSE,
                'type' => @$options['type'],
            ];

            if ( $this->imageValidator->with( $data )->passes( ImageValidatorInterface::IMAGE_CREATION ) )
            {
                $image = Image::create( $data );

                event( new ImageWasCreated( $image, $sizes ) );

                return $image;
            }

            return $this->imageValidator->errors();
        }

        return FALSE;

    }


    /**
     * @param $imageId
     * @param bool|TRUE $skipValidation
     * @return array|bool
     */
    public function removeImage( $imageId, $skipValidation = TRUE )
    {
        $data = [
            'id' => $imageId
        ];

        if ( $skipValidation || $this->imageValidator->with( $data )->passes( ImageValidatorInterface::EXISTS_BY_ID ) )
        {
            $image = Image::find( $imageId );
            $image->delete();

            event( new ImageWasRemoved( $imageId ) );

            return TRUE;
        }

        return $this->imageValidator->errors();
    }


    /**
     * @param Image $image
     * @param array $sizes
     * @return Image
     */
    public function processImage( Image $image, array $sizes = [] )
    {
        if ( !$image->processed )
        {
             $finalThumbs = [];
            $destinationPath = dirname( $image->path );
            foreach( $sizes as $sizeKey => $size )
            {
                $thumbs = $this->imageProcessingService->resizeOrCropImageToSizes( $image->path, $destinationPath, [ $size ] );

                if ( $thumbs && !empty( $thumbs ) )
                {
                    $thumb =  array_first( $thumbs );
                    $finalThumbs[ $sizeKey ] = $thumb;
                }
            }

            // Update the file.
            $update = [
                'thumbnails' => json_encode( $finalThumbs ),
                'processed' => TRUE
            ];

            $image->update( $update );

            foreach( $update as $key => $value ) $image->{$key} = $value;

            event( new ImageWasProcessed( $image ) );

        }

        return $image;

    }


}