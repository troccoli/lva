<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli-Allard <giulio@troccoli.it>
 * Date: 11/09/2016
 * Time: 12:49
 */

namespace LVA\Validators;

use LVA\Services\InteractiveFixturesUploadService;
use Illuminate\Http\UploadedFile;

class CustomValidators
{
    public function requiredHeaders($attribute, $value, $parameters, $validator)
    {
        if ($value instanceof UploadedFile) {
            $handle = fopen($value->getRealPath(), 'r');
            $headers = InteractiveFixturesUploadService::readOneLine($handle);

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

    public function ukPostcode($attribute, $value, $parameters, $validator)
    {
        return \Postcode::validate($value);
    }
}