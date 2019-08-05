<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\Division;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DivisionController extends Controller
{
    public function index(Competition $competition): View
    {
        $divisions = Division::query()->inCompetition($competition)->inOrder()->get();

        return view('CRUD.divisions.index', compact('competition', 'divisions'));
    }

    public function create(Competition $competition): View
    {
        return view('CRUD.divisions.create', compact('competition'));
    }

    public function store(Request $request, Competition $competition): RedirectResponse
    {
        $this->validate($request,
            [
                'competition_id' => 'required|exists:competitions,id',
                'name'           => [
                    'required',
                    Rule::unique('divisions')->where(function (Builder $query) use ($competition): Builder {
                        return $query->where('competition_id', $competition->getId());
                    }),
                ],
                'display_order'  => [
                    'required',
                    'integer',
                    'min:1',
                    Rule::unique('divisions')->where(function (Builder $query) use ($competition): Builder {
                        return $query->where('competition_id', $competition->getId());
                    }),
                ],
            ], [
                'name.required'          => __('The name is required.'),
                'name.unique'            => __('The division already exists in this competition.'),
                'display_order.required' => __('The order is required.'),
                'display_order.integer'  => __('The order must be a positive number.'),
                'display_order.min'      => __('The order must be a positive number.'),
                'display_order.unique'   => __('The order is already used for another division.'),
            ]);

        Division::create($request->only('competition_id', 'name', 'display_order'));

        return redirect()
            ->route('divisions.index', [$competition])
            ->withToastSuccess(__('Division added!'));
    }

    public function edit(Competition $competition, Division $division): View
    {
        return view('CRUD.divisions.edit', compact('competition', 'division'));
    }

    public function update(Request $request, Competition $competition, Division $division): RedirectResponse
    {
        $this->validate($request,
            [
                'name'          => [
                    'required',
                    Rule::unique('divisions')
                        ->ignore($division)
                        ->where(function (Builder $query) use ($competition): Builder {
                            return $query->where('competition_id', $competition->getId());
                        }),
                ],
                'display_order' => [
                    'required',
                    'integer',
                    'min:1',
                    Rule::unique('divisions')
                        ->ignore($division)
                        ->where(function (Builder $query) use ($competition): Builder {
                            return $query->where('competition_id', $competition->getId());
                        }),
                ],
            ],
            [
                'name.required'          => __('The name is required.'),
                'name.unique'            => __('The division already exists in this competition.'),
                'display_order.required' => __('The order is required.'),
                'display_order.integer'  => __('The order must be a positive number.'),
                'display_order.min'      => __('The order must be a positive number.'),
                'display_order.unique'   => __('The order is already used for another division.'),
            ]);

        $division->update($request->only('name', 'display_order'));

        return redirect()
            ->route('divisions.index', [$competition])
            ->withToastSuccess(__('Division updated!'));
    }

    public function destroy(Competition $competition, Division $division): RedirectResponse
    {
        $division->delete();

        return redirect()
            ->route('divisions.index', [$competition])
            ->withToastSuccess(__('Division deleted!'));
    }
}
