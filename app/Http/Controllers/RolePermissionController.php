<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RolePermissionController extends Controller
{
    /**
     * Display the role and permission management page.
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        $users = User::with('roles')->get();
        
        return view('role-permission.index', compact('roles', 'permissions', 'users'));
    }

    /**
     * Store a new role.
     */
    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil dibuat',
            'role' => $role
        ]);
    }

    /**
     * Update a role.
     */
    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'description' => 'nullable|string',
        ]);

        $role = Role::findOrFail($id);
        $role->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil diupdate',
            'role' => $role
        ]);
    }

    /**
     * Delete a role.
     */
    public function deleteRole($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil dihapus'
        ]);
    }

    /**
     * Store a new permission.
     */
    public function storePermission(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'description' => 'nullable|string',
        ]);

        $permission = Permission::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permission berhasil dibuat',
            'permission' => $permission
        ]);
    }

    /**
     * Update a permission.
     */
    public function updatePermission(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $id,
            'description' => 'nullable|string',
        ]);

        $permission = Permission::findOrFail($id);
        $permission->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permission berhasil diupdate',
            'permission' => $permission
        ]);
    }

    /**
     * Delete a permission.
     */
    public function deletePermission($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Permission berhasil dihapus'
        ]);
    }

    /**
     * Assign permissions to a role.
     */
    public function assignPermissionsToRole(Request $request, $roleId)
    {
        $request->validate([
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role = Role::findOrFail($roleId);
        $role->permissions()->sync($request->permission_ids);

        return response()->json([
            'success' => true,
            'message' => 'Permissions berhasil diassign ke role',
            'role' => $role->load('permissions')
        ]);
    }

    /**
     * Assign roles to a user.
     */
    public function assignRolesToUser(Request $request, $userId)
    {
        $request->validate([
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id',
        ]);

        $user = User::findOrFail($userId);
        $user->roles()->sync($request->role_ids);

        return response()->json([
            'success' => true,
            'message' => 'Roles berhasil diassign ke user',
            'user' => $user->load('roles')
        ]);
    }

    /**
     * Get role with permissions.
     */
    public function getRole($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        return response()->json($role);
    }

    /**
     * Get user with roles.
     */
    public function getUser($id)
    {
        $user = User::with('roles')->findOrFail($id);
        return response()->json($user);
    }

    /**
     * Get all permissions (for API).
     */
    public function getPermissions()
    {
        $permissions = Permission::all();
        return response()->json($permissions);
    }

    /**
     * Get all roles (for API).
     */
    public function getRoles()
    {
        $roles = Role::all();
        return response()->json($roles);
    }

    /**
     * Get permission by id.
     */
    public function getPermission($id)
    {
        $permission = Permission::findOrFail($id);
        return response()->json($permission);
    }
}

