<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli <giulio@troccoli.it>
 * Date: 10/09/2016
 * Time: 16:22
 */

namespace App\Services\Interfaces;


use Illuminate\Http\UploadedFile;

interface InteractiveUploadInterface
{
    /**
     * @param UploadedFile $file
     * @return int
     */
    public function createJob(UploadedFile $file);
}