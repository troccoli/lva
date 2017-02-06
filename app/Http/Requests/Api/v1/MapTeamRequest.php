<?php


namespace App\Http\Requests\Api\v1;


use App\Http\Requests\Request;

/**
 * Class MapTeamRequest
 *
 * @package App\Http\Requests\Api\v1
 */
class MapTeamRequest extends Request
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