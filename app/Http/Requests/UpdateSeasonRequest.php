<?php

namespace LVA\Http\Requests;

use Illuminate\Support\Facades\Auth;

/**
 * Class UpdateSeasonRequest
 *
 * @package LVA\Http\Requests
 */
class UpdateSeasonRequest extends Request
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
            'season' => 'required|unique:seasons,season,' . $this->input('id')
        ];
    }
}
