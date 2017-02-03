<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;

/**
 * Class UpdateDivisionRequest
 *
 * @package App\Http\Requests
 */
class UpdateDivisionRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'season_id' => 'required|exists:seasons,id',
            'division'  => 'required|unique:divisions,division,NULL,id,season_id,' . $this->input('season_id'),
        ];
    }
}
