<?php

namespace LVA\Http\Requests;

use Illuminate\Support\Facades\Auth;

/**
 * Class StoreDivisionRequest
 *
 * @package LVA\Http\Requests
 */
class StoreDivisionRequest extends Request
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
