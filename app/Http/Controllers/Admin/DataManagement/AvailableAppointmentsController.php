<?php

namespace App\Http\Controllers\Admin\DataManagement;

use App\Http\Requests\StoreAvailableAppointmentRequest as StoreRequest;
use App\Http\Requests\UpdateAvailableAppointmentRequest as UpdateRequest;
use App\Http\Controllers\Controller;

use Laracasts\Flash\Flash;

use App\Models\AvailableAppointment;
use App\Models\Fixture;
use App\Models\Role;

/**
 * Class AvailableAppointmentsController
 *
 * @package App\Http\Controllers\Admin\DataManagement
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

        return redirect('admin/data-management/available-appointments');
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

        return redirect('admin/data-management/available-appointments');
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

        return redirect('admin/data-management/available-appointments');
    }

}
