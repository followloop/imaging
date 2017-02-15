<?php

namespace LOOP\Imaging\Services\src;

use Illuminate\Support\Facades\Storage;
use LOOP\Imaging\Events\ImageWasCreated;
use LOOP\Imaging\Events\ImageWasProcessed;
use LOOP\Imaging\Events\ImageWasDeleted;
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
     * @param $imageId
     * @return array
     */
    public function findImageById( $imageId )
    {
        $data = [
            'id' => $imageId
        ];

        if ( $this->imageValidator->with( $data )->passes( ImageValidatorInterface::EXISTS_BY_ID ) )
        {
            $image = Image::find( $imageId );

            return $image;
        }

        return $this->imageValidator->errors();
    }


    /**
     * @param mixed $imageB64OrUploadedFile
     * @param array $options
     * @return array|bool
     */
    public function createImage( $imageB64OrUploadedFile, array $options = [] )
    {
        $path = @$options['path'];

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

                event( new ImageWasCreated( $image, $options ) );

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
    public function deleteImage( $imageId, $skipValidation = TRUE )
    {
        $data = [
            'id' => $imageId
        ];

        if ( $skipValidation || $this->imageValidator->with( $data )->passes( ImageValidatorInterface::EXISTS_BY_ID ) )
        {
            $image = Image::find( $imageId );
            $image->delete();

            event( new ImageWasDeleted( $imageId ) );

            return TRUE;
        }

        return $this->imageValidator->errors();
    }


    /**
     * @param mixed $imageIdOrImage
     * @param array $options
     * @return array
     */
    public function processImage( $imageIdOrImage, array $options = [] )
    {
        $image = $this->getImageFromImageOrImageId( $imageIdOrImage );

        if ( !is_array( $image ) && !$image->processed )
        {
            $sizes = @$options['sizes'];
            $sizes = is_array( $sizes ) ? $sizes : [];

            $finalThumbs = [];
            $destinationPath = dirname( $image->path );
            foreach( $sizes as $sizeKey => $size )
            {
                $thumbs = $this->imageProcessingService->resizeOrCropImageToSizes( $image->path, $destinationPath, [ $size ], $options );

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


    /**
     * @param $imageIdOrImage
     * @return array|bool
     */
    public function destroyImage( $imageIdOrImage )
    {
        $isImage = $imageIdOrImage instanceof Image;

        $data = [
            'id' => $isImage ? $imageIdOrImage : $imageIdOrImage->id
        ];

        if ( $isImage || $this->imageValidator->with( $data )->passes( ImageValidatorInterface::EXISTS_BY_ID_EVEN_DELETED ) )
        {
            $image = $isImage ? $imageIdOrImage : Image::find( $imageIdOrImage )->withTrashed();

            // Remove from local disk.
            $this->removeImageFromDisk( $image, config( 'imaging.local_disk_name', 'local' ) );

            // If it's in the cloud then remove it from the cloud too.
            if ( $image->cloud )
            {
                $this->removeImageFromDisk( $image, config( 'imaging.cloud_disk_name' ) );
            }

            // Destroy the entry.
            $image->forceDelete();

            return TRUE;
        }

        return $this->imageValidator->errors();
    }


    /*******************************************************************************************************************
     *******************************************************************************************************************
     ******************************************************************************************************************/
    /**
     * @param Image $image
     * @param $diskName
     */
    protected function removeImageFromDisk( Image $image, $diskName )
    {
        if ( $diskName )
        {
            $disk = Storage::disk( $diskName );

            // Delete all thumbnails locally.
            foreach( $image->thumbnails( NULL, TRUE ) as $thumb )
            {
                $folderName = dirname( $thumb );
                if ( $disk->exists( $folderName ) ) $disk->deleteDirectory( $folderName );
            }

            // Also remove the main pic.
            if ( $disk->exists( $image->path ) ) $disk->delete( $image->path );
        }
    }

    /**
     * @param $imageOrImageId
     * @return array
     */
    protected function getImageFromImageOrImageId( $imageOrImageId )
    {
        $isImage = $imageOrImageId instanceof Image;

        return $isImage ? $imageOrImageId : $this->findImageById( $imageOrImageId->id );

    }

}