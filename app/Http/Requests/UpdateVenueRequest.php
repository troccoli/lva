<?php

namespace LVA\Http\Requests;

use Illuminate\Support\Facades\Auth;

/**
 * Class UpdateVenueRequest
 *
 * @package LVA\Http\Requests
 */
class UpdateVenueRequest extends Request
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
            'venue' => 'required|unique:venues,venue,' . $this->input('id')
        ];
    }
}
