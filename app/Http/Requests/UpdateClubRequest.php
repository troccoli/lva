<?php

namespace LVA\Http\Requests;

use Illuminate\Support\Facades\Auth;

/**
 * Class UpdateClubRequest
 *
 * @package LVA\Http\Requests
 */
class UpdateClubRequest extends Request
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
            'club' => 'required|unique:clubs,club,' . $this->input('id')
        ];
    }
}
