<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli-Allard <giulio@troccoli.it>
 * Date: 21/01/2017
 * Time: 15:02
 */

namespace App\Services\Contracts;

interface StatusContract
{
    public function getNextStepStatus($status);

    public function getStatusCode($status);

    public function getStatusProcessedLines($status);

    public function setStatusProcessedLines(&$status, $lines);
}