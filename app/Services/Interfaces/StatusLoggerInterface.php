<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli <giulio@troccoli.it>
 * Date: 11/09/2016
 * Time: 15:30
 */

namespace App\Services\Interfaces;


interface StatusLoggerInterface
{
    /**
     * @param string $filename
     * @return mixed
     */
    public function createStatusFile($filename);

    /**
     * @param \SplFileObject $statusFile
     * @return mixed
     */
    public function getStatus(\SplFileObject $statusFile);

    /**
     * @param \SplFileObject $statusFile
     * @param $newStatus
     * @return mixed
     */
    public function setStatus(\SplFileObject $statusFile, $newStatus);

    /**
     * @return bool
     */
    public function statusFileExist();
}