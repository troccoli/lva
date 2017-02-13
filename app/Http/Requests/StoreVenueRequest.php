<?php

namespace LVA\Http\Requests;

use Illuminate\Support\Facades\Auth;

/**
 * Class StoreVenueRequest
 *
 * @package LVA\Http\Requests
 */
class StoreVenueRequest extends Request
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
            'venue' => 'required|unique:venues',
        ];
    }
}
