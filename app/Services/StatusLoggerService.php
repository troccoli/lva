<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli <giulio@troccoli.it>
 * Date: 11/09/2016
 * Time: 15:29
 */

namespace App\Services;


use App\Services\Interfaces\StatusLoggerInterface;
use Illuminate\Http\UploadedFile;
use League\Flysystem\FileExistsException;
use Symfony\Component\HttpFoundation\File\File;

class StatusLoggerService implements StatusLoggerInterface
{
    const STATUS_DIR = 'app/files/';

    /**
     * @inheritdoc
     */
    public function createStatusFile($filename)
    {
        $fileInfo = new \SplFileInfo(storage_path(self::STATUS_DIR) . $filename);

        if ($fileInfo->isFile()) {
            throw new FileExistsException(storage_path(self::STATUS_DIR) . $filename);
        }

        return $fileInfo->openFile('w+', false);
    }

    /**
     * @inheritdoc
     */
    public function getStatus(\SplFileObject $statusFile)
    {
        // TODO: Implement getStatus() method.
    }

    /**
     * @inheritdoc
     */
    public function setStatus(\SplFileObject $statusFile, $newStatus)
    {
        // TODO: Implement setStatus() method.
    }

    /**
     * @inheritdoc
     */
    public function statusFileExist()
    {
        // TODO: Implement statusFileExist() method.
    }

}