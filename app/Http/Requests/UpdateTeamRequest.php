<?php

namespace LVA\Http\Requests;

use Illuminate\Support\Facades\Auth;

/**
 * Class UpdateTeamRequest
 *
 * @package LVA\Http\Requests
 */
class UpdateTeamRequest extends Request
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
            'club_id' => 'required|exists:clubs,id',
            'team'    => 'required|unique:teams,team,' . $this->input('id') . ',id,club_id,' . $this->input('club_id'),
        ];
    }
}
