<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli-Allard <giulio@troccoli.it>
 * Date: 10/09/2016
 * Time: 16:22
 */

namespace App\Services\Contracts;

use App\Models\UploadJob;
use Illuminate\Http\UploadedFile;

interface InteractiveUploadContract
{
    /**
     * @param UploadedFile $file
     *
     * @return mixed
     */
    public function createJob(UploadedFile $file);

    /**
     * @param UploadJob $job
     *
     * @return mixed
     */
    public function processJob(UploadJob $job);
}