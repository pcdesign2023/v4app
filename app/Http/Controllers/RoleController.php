<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    public function ListRoles()
    {
        $roles = Role::all();
        return view('usermanagement.roles', compact('roles'));
    }
    public function update(Request $request, Role $role)
    {
        // Validate the request
        $request->validate([
            'label' => 'required|string|unique:roles,label,' . $role->id . '|max:255',
        ]);

        // Update the role
        $role->update([
            'label' => $request->label,
        ]);

        // Redirect with success message
        return redirect()->route('roles', $role)->with('success', 'Role updated successfully.');
    }
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'label' => 'required|string|unique:roles,label|max:255',
        ]);

        // Create the role
        Role::create([
            'label' => $request->label,
        ]);

        // Redirect with success message
        return redirect()->route('roles')->with('success', 'Role created successfully.');
    }

}
