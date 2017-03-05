<?php

namespace LVA\Http\Requests;

use Illuminate\Support\Facades\Auth;

/**
 * Class UpdateFixtureRequest
 *
 * @package LVA\Http\Requests
 */
class UpdateFixtureRequest extends Request
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
            'division_id'  =>
                'required|' .
                'exists:divisions,id|' .
                'unique:fixtures,division_id,' . $this->input('id') . ',id' .
                ',home_team_id,' . $this->input('home_team_id') .
                ',away_team_id,' . $this->input('away_team_id'),
            'match_number' => 'required|unique:fixtures,match_number,' . $this->input('id') . ',id,division_id,' . $this->input('division_id'),
            'match_date'   => 'required',
            'warm_up_time' => 'required',
            'start_time'   => 'required',
            'home_team_id' => 'required|exists:teams,id',
            'away_team_id' => 'required|exists:teams,id|different:home_team_id',
            'venue_id'     => 'required|exists:venues,id',
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'away_team_id.different' => 'The away team cannot be the same as the home team.',
            'division_id.unique'     => 'The fixture for these two teams have already been added in this division.',
            'match_number.unique'    => 'There is already a match with the same number in this division.',
        ];
    }
}
