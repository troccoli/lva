<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli-Allard <giulio@troccoli.it>
 * Date: 26/09/2016
 * Time: 18:33
 */

namespace App\Http\Controllers\Api\v1;


use App\Http\Controllers\Controller;
use App\Models\UploadJob;
use Illuminate\Support\Facades\Input;

class UploadApiController extends Controller
{
    public function getUploadStatus()
    {
        $jobId = Input::get('job', null);
        if (null === $jobId) {
            return response([
                'error'   => true,
                'message' => 'Job parameter missing',
            ]);
        }
        $uploadJob = UploadJob::find($jobId);

        if (is_null($uploadJob)) {
            return response([
                'error'   => true,
                'message' => 'Job not found',
            ]);
        }

        return response()->json([
            'error'   => false,
            'message' => 'Job found',
            'status'  => $uploadJob->status,
        ]);
    }
}