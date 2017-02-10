<?php

namespace LOOP\Imaging\Listeners\Traits;

use Illuminate\Support\Facades\Storage;
use LOOP\Imaging\Models\Image;

/**
 * Class RemovesLocalImage
 * @package LOOP\Imaging\Listeners\Traits
 */
trait RemovesLocalImage
{
    
    /**
     * @param Image $image
     */
    public function removeLocalImage( Image $image )
    {
        if ( $image->cloud )
        {
            $localDisk = Storage::disk( config( 'imaging.local_disk_name', 'local' ) );

            // Delete all thumbnails.
            foreach( $image->thumbnails( NULL, TRUE ) as $thumb )
            {
                $folderName = dirname( $thumb );
                $localDisk->deleteDirectory( $folderName );
            }

            // Also remove the main pic.
            $localDisk->delete( $image->path );
        }
    }
}