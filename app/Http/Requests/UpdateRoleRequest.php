<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;

/**
 * Class UpdateRoleRequest
 *
 * @package App\Http\Requests
 */
class UpdateRoleRequest extends Request
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
            'role' => 'required|unique:roles,role,' . $this->input('id'),
        ];
    }
}
