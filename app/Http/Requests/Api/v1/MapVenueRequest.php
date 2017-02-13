<?php


namespace LVA\Http\Requests\Api\v1;


use LVA\Http\Requests\Request;

/**
 * Class MapVenueRequest
 *
 * @package LVA\Http\Requests\Api\v1
 */
class MapVenueRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Authorization is done by the auth:api middleware
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'job'     => 'required|exists:upload_jobs',
            'name'    => 'required',
            'newName' => 'required',
        ];
    }
}