<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli <giulio@troccoli.it>
 * Date: 11/09/2016
 * Time: 12:49
 */

namespace App\Validators;

use Illuminate\Http\UploadedFile;
use League\Csv\Reader;

class CustomValidators
{
    public function requiredHeaders($attribute, $value, $parameters, $validator)
    {
        if ($value instanceof UploadedFile) {
            $csv = Reader::createFromPath($value->getRealPath());
            $headers = $csv->fetchOne();

            if ($parameters == array_intersect($parameters, $headers)) {
                return true;
            }
        }

        return false;
    }

    public function requiredHeadersMessage($message, $attribute, $rule, $parameters)
    {
        $lastHeader = '"' . array_pop($parameters) . '"';
        if (empty($parameters)) {
            $headers = $lastHeader;
        } else {
            $headers = '"' . implode('", "', $parameters) . '" and ' . $lastHeader;
        }

        return str_replace(':headers', $headers, $message);
    }
}