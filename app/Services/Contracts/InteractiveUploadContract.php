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
     * @return int
     */
    public function createJob(UploadedFile $file);

    /**
     * @param UploadJob $job
     * @return mixed
     */
    public function processJob(UploadJob $job);

    /**
     * @param UploadJob $job
     * @param array $newStatus
     * @return mixed
     */
    public function updateStatus(UploadJob $job, array $newStatus);
}