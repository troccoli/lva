<?php

namespace LVA\Http\Controllers\Admin\DataManagement;

use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use LVA\Http\Controllers\Controller;
use LVA\Models\Role;

/**
 * Class RolesController.
 */
class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return mixed
     */
    public function index()
    {
        $roles = Role::paginate(15);

        return view('admin.data-management.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return mixed
     */
    public function create()
    {
        return view('admin.data-management.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function store(Request $request)
    {
        $this->validate($request, ['role' => 'required|unique:roles']);

        Role::create($request->all());

        Flash::success('Role added!');

        return redirect()->route('roles.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function show($id)
    {
        $role = Role::findOrFail($id);

        return view('admin.data-management.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);

        return view('admin.data-management.roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, ['role' => 'required|unique:roles,role,' . $id]);

        /** @var Role $role */
        $role = Role::findOrFail($id);
        $role->update($request->all());

        Flash::success('Role updated!');

        return redirect()->route('roles.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function destroy($id)
    {
        $canBeDeleted = empty(Role::find($id)->available_appointments->toArray());
        if ($canBeDeleted) {
            Role::destroy($id);
            Flash::success('Role deleted!');
        } else {
            Flash::error('Cannot delete because they are existing appointments for this role.');
        }

        return redirect()->route('roles.index');
    }
}
