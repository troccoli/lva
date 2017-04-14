<?php

namespace LVA\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;

/**
 * Class UpdateAvailableAppointmentRequest
 *
 * @package LVA\Http\Requests
 */
class UpdateAvailableAppointmentRequest extends Request
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
            'fixture_id' => 'unique:available_appointments,fixture_id,' . $this->input('id') . ',id,role_id,' . $this->input('role_id'),
            'role_id'    => 'unique:available_appointments,role_id,' . $this->input('id') . ',id,fixture_id,' . $this->input('fixture_id'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function formatErrors(Validator $validator)
    {
        return [[
            'Appointment already added.',
        ]];
    }
}
