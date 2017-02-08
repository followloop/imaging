<?php

namespace LOOP\Imaging\Services\src;

use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use LOOP\Imaging\Events\ImageWasCreated;
use LOOP\Imaging\Events\ImageWasProcessed;
use LOOP\Imaging\Events\ImageWasRemoved;
use LOOP\Imaging\Models\Image;
use LOOP\Imaging\Repositories\ImageRepositoryInterface;
use LOOP\Imaging\Services\ImageProcessingServiceInterface;
use LOOP\Imaging\Services\ImageServiceInterface;
use LOOP\Imaging\Services\Validation\ImageValidatorInterface;

/**
 * Class ImagesService
 * @package LOOP\Imaging\Services\src
 */
class ImagesService implements ImageServiceInterface
{
    protected $imageRepository;
    protected $imageValidator;
    protected $imageProcessingService;

    /**
     * @param ImageRepositoryInterface $imageRepositoryInterface
     * @param ImageValidatorInterface $imageValidatorInterface
     * @param ImageProcessingServiceInterface $imageProcessingServiceInterface
     */
    public function __construct(
        ImageRepositoryInterface $imageRepositoryInterface,
        ImageValidatorInterface $imageValidatorInterface ,
        ImageProcessingServiceInterface $imageProcessingServiceInterface
    )
    {
        $this->imageRepository = $imageRepositoryInterface;
        $this->imagesValidator = $imageValidatorInterface;
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
        $disk = @$options['disk'];
        $sizes = is_array( $sizes ) ? $sizes : [];

        //
        if ( $disk ) $this->imageProcessingService->setDisk( $disk );
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

            if ( $this->imagesValidator->with( $data )->passes( ImageValidatorInterface::IMAGE_CREATION ) )
            {
                $image = $this->imageRepository->create( $data );

                event( new ImageWasCreated( $image, $sizes ) );

                return $image;
            }

            return $this->imagesValidator->errors();
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

        if ( $skipValidation || $this->imagesValidator->with( $data )->passes( ImageValidatorInterface::EXISTS_BY_ID ) )
        {
            event( new ImageWasRemoved( $imageId ) );

            $this->imageRepository->delete( $imageId );

            return TRUE;
        }

        return $this->imagesValidator->errors();
    }


    /**
     * @param Image $image
     * @param array $sizes
     * @return Image
     */
    public function processImageAndMoveThemToCloudDisk( Image $image, array $sizes = [] )
    {
        if ( !$image->processed )
        {
            $localDiskName = config( 'imaging.local_disk_name');
            $cloudDiskName = config( 'imaging.cloud_disk_name');

            $localDisk = Storage::disk( $localDiskName );
            $cloudDisk = NULL;

            $path = rtrim( dirname( $image->path ), '/');

            if ( !$localDisk->exists( $path ) ) $localDisk->makeDirectory( $path );

            if ( $localDiskName != $cloudDiskName )
            {
                // Copy file to the cloud.
                $cloudDisk = Storage::disk( $cloudDiskName );
                $cloudDisk->putFile( $image->path, new File( $image->path ), basename( $image->path ) );
            }

            $finalThumbs = [];
            foreach( $sizes as $sizeKey => $size )
            {
                $thumbs = $this->imageProcessingService->resizeOrCropImageToSizes( $image->path, $path, [ $size ] );

                if ( $thumbs && !empty( $thumbs ) )
                {
                    $thumb =  array_first( $thumbs );
                    $finalThumbs[ $sizeKey ] = $thumb;

                    // And move them to the cloud.
                    if ( !is_null( $cloudDisk ) ) $cloudDisk->putFile( $thumb, new File( $thumb ), basename( $thumb ) );
                }
            }

            // Update the file.
            $update = [
                'thumbnails' => json_encode( $finalThumbs ),
                'processed' => TRUE
            ];

            $this->imageRepository->updateBy( $update, $image->id );

            foreach( $update as $key => $value ) $image->{$key} = $value;

            if ( !is_null( $cloudDisk ) && $localDisk->exists( $path ) ) $localDisk->deleteDirectory( $path );

            event( new ImageWasProcessed( $image ) );

        }

        return $image;

        /*
        $disk = Storage::disk('local');

        $path = rtrim( dirname( $image->path ), '/');

        if ( $disk->exists( $path ) ) $disk->makeDirectory( $path );

        // Copy file to the cloud.
        $this->cloudStorageService->copyLocalFileToCloudStorage( $image->path, $image->path, FALSE, FALSE );

        $finalThumbs = [];
        foreach( $sizes as $sizeKey => $size )
        {
            $thumbs = $this->imageProcessingService->resizeOrCropImageToSizes( $image->path, $path, [ $size ] );

            if ( $thumbs && !empty( $thumbs ) )
            {
                $thumb =  array_first( $thumbs );
                $finalThumbs[ $sizeKey ] = $thumb;

                // And move them to the cloud.
                $this->cloudStorageService->copyLocalFileToCloudStorage( $thumb, $thumb, FALSE, FALSE );
            }
        }

        // Update the file.
        $update = [
            'thumbnails' => json_encode( $finalThumbs ),
            'processed' => TRUE
        ];

        $this->imageRepository->updateBy( $update, $image->id );

        foreach( $update as $key => $value ) $image->{$key} = $value;

        if ( $disk->exists( $path ) ) $disk->deleteDirectory( $path );

        return $image;
        */
    }

}