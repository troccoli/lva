<?php

namespace LVA\Http\Controllers\Admin\DataManagement;

use Laracasts\Flash\Flash;
use LVA\Http\Controllers\Controller;
use LVA\Http\Requests\StoreAvailableAppointmentRequest as StoreRequest;
use LVA\Http\Requests\UpdateAvailableAppointmentRequest as UpdateRequest;
use LVA\Models\AvailableAppointment;
use LVA\Models\Fixture;
use LVA\Models\Role;

/**
 * Class AvailableAppointmentsController
 *
 * @package LVA\Http\Controllers\Admin\DataManagement
 */
class AvailableAppointmentsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return mixed
     */
    public function index()
    {
        $availableAppointments = AvailableAppointment::paginate(15);

        return view('admin.data-management.available-appointments.index', compact('availableAppointments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return mixed
     */
    public function create()
    {
        $fixtures = Fixture::all();
        $roles = Role::all();

        return view('admin.data-management.available-appointments.create', compact('fixtures', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     *
     * @return mixed
     */
    public function store(StoreRequest $request)
    {
        AvailableAppointment::create($request->all());

        Flash::success('Appointment added!');

        return redirect()->route('available-appointments.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return mixed
     */
    public function show($id)
    {
        $availableAppointment = AvailableAppointment::findOrFail($id);

        return view('admin.data-management.available-appointments.show', compact('availableAppointment'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return mixed
     */
    public function edit($id)
    {
        $availableAppointment = AvailableAppointment::findOrFail($id);

        $fixtures = Fixture::all();
        $roles = Role::all();

        return view('admin.data-management.available-appointments.edit',
            compact('availableAppointment', 'fixtures', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param int           $id
     *
     * @return mixed
     */
    public function update(UpdateRequest $request, $id)
    {
        /** @var AvailableAppointment $availableAppointment */
        $availableAppointment = AvailableAppointment::findOrFail($id);
        $availableAppointment->update($request->all());

        Flash::success('Appointment updated!');

        return redirect()->route('available-appointments.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return mixed
     */
    public function destroy($id)
    {
        AvailableAppointment::destroy($id);

        Flash::success('Appointment deleted!');

        return redirect()->route('available-appointments.index');
    }

}
