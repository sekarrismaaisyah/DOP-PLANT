@extends('layouts.master')

@section('title', 'Role & Permission Management')

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .card {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .nav-tabs .nav-link {
        color: #6b7280;
        border: none;
        border-bottom: 2px solid transparent;
    }
    
    .nav-tabs .nav-link.active {
        color: #3b82f6;
        border-bottom-color: #3b82f6;
        background: transparent;
    }
    
    .table-responsive {
        border-radius: 8px;
    }
    
    .badge-role {
        background-color: #3b82f6;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
    }
    
    .badge-permission {
        background-color: #10b981;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        margin: 2px;
    }
    
    .btn-action {
        padding: 4px 12px;
        font-size: 12px;
    }
</style>
@endsection

@section('content')
<x-page-title title="Role & Permission Management" pagetitle="Kelola Roles, Permissions, dan User Assignments" />

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs nav-primary" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" data-bs-toggle="tab" href="#roles" role="tab" aria-selected="true">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class="material-icons-outlined">group</i></div>
                                <div class="tab-title ms-3">Roles</div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" href="#permissions" role="tab" aria-selected="false">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class="material-icons-outlined">lock</i></div>
                                <div class="tab-title ms-3">Permissions</div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" href="#users" role="tab" aria-selected="false">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class="material-icons-outlined">people</i></div>
                                <div class="tab-title ms-3">User Management</div>
                            </div>
                        </a>
                    </li>
                </ul>
                
                <div class="tab-content py-3">
                    <!-- Roles Tab -->
                    <div class="tab-pane fade show active" id="roles" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Daftar Roles</h5>
                            <button type="button" class="btn btn-primary btn-sm" onclick="openRoleModal()">
                                <i class="material-icons-outlined">add</i> Tambah Role
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama</th>
                                        <th>Slug</th>
                                        <th>Description</th>
                                        <th>Permissions</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="rolesTableBody">
                                    @foreach($roles as $role)
                                    <tr>
                                        <td>{{ $role->id }}</td>
                                        <td><strong>{{ $role->name }}</strong></td>
                                        <td><code>{{ $role->slug }}</code></td>
                                        <td>{{ $role->description ?? '-' }}</td>
                                        <td>
                                            @foreach($role->permissions as $permission)
                                                <span class="badge-permission">{{ $permission->name }}</span>
                                            @endforeach
                                            @if($role->permissions->isEmpty())
                                                <span class="text-muted">No permissions</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info btn-action" onclick="editRole({{ $role->id }})">
                                                <i class="material-icons-outlined" style="font-size: 16px;">edit</i>
                                            </button>
                                            <button class="btn btn-sm btn-warning btn-action" onclick="manageRolePermissions({{ $role->id }})">
                                                <i class="material-icons-outlined" style="font-size: 16px;">settings</i>
                                            </button>
                                            <button class="btn btn-sm btn-danger btn-action" onclick="deleteRole({{ $role->id }})">
                                                <i class="material-icons-outlined" style="font-size: 16px;">delete</i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Permissions Tab -->
                    <div class="tab-pane fade" id="permissions" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Daftar Permissions</h5>
                            <button type="button" class="btn btn-primary btn-sm" onclick="openPermissionModal()">
                                <i class="material-icons-outlined">add</i> Tambah Permission
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama</th>
                                        <th>Slug</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="permissionsTableBody">
                                    @foreach($permissions as $permission)
                                    <tr>
                                        <td>{{ $permission->id }}</td>
                                        <td><strong>{{ $permission->name }}</strong></td>
                                        <td><code>{{ $permission->slug }}</code></td>
                                        <td>{{ $permission->description ?? '-' }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-info btn-action" onclick="editPermission({{ $permission->id }})">
                                                <i class="material-icons-outlined" style="font-size: 16px;">edit</i>
                                            </button>
                                            <button class="btn btn-sm btn-danger btn-action" onclick="deletePermission({{ $permission->id }})">
                                                <i class="material-icons-outlined" style="font-size: 16px;">delete</i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Users Tab -->
                    <div class="tab-pane fade" id="users" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Daftar Users</h5>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Roles</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="usersTableBody">
                                    @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td><strong>{{ $user->name }}</strong></td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @foreach($user->roles as $role)
                                                <span class="badge-role">{{ $role->name }}</span>
                                            @endforeach
                                            @if($user->roles->isEmpty())
                                                <span class="text-muted">No roles</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-warning btn-action" onclick="manageUserRoles({{ $user->id }})">
                                                <i class="material-icons-outlined" style="font-size: 16px;">settings</i> Manage Roles
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Role Modal -->
<div class="modal fade" id="roleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleModalTitle">Tambah Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="roleForm">
                    <input type="hidden" id="roleId" name="id">
                    <div class="mb-3">
                        <label for="roleName" class="form-label">Nama Role <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="roleName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="roleDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="roleDescription" name="description" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveRole()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Permission Modal -->
<div class="modal fade" id="permissionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="permissionModalTitle">Tambah Permission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="permissionForm">
                    <input type="hidden" id="permissionId" name="id">
                    <div class="mb-3">
                        <label for="permissionName" class="form-label">Nama Permission <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="permissionName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="permissionDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="permissionDescription" name="description" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="savePermission()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Role Permissions Modal -->
<div class="modal fade" id="rolePermissionsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manage Permissions untuk Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Role:</strong> <span id="rolePermissionsRoleName"></span></p>
                <div class="mb-3">
                    <label class="form-label">Pilih Permissions:</label>
                    <div id="rolePermissionsList" style="max-height: 400px; overflow-y: auto;">
                        <!-- Permissions will be loaded here -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveRolePermissions()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- User Roles Modal -->
<div class="modal fade" id="userRolesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manage Roles untuk User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>User:</strong> <span id="userRolesUserName"></span></p>
                <div class="mb-3">
                    <label class="form-label">Pilih Roles:</label>
                    <div id="userRolesList" style="max-height: 400px; overflow-y: auto;">
                        <!-- Roles will be loaded here -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveUserRoles()">Simpan</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
let currentRoleId = null;
let currentUserId = null;

// Role Functions
function openRoleModal(id = null) {
    currentRoleId = id;
    const modal = new bootstrap.Modal(document.getElementById('roleModal'));
    const title = document.getElementById('roleModalTitle');
    const form = document.getElementById('roleForm');
    
    if (id) {
        title.textContent = 'Edit Role';
        fetch(`/role-permission/role/${id}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('roleId').value = data.id;
                document.getElementById('roleName').value = data.name;
                document.getElementById('roleDescription').value = data.description || '';
            });
    } else {
        title.textContent = 'Tambah Role';
        form.reset();
        document.getElementById('roleId').value = '';
    }
    
    modal.show();
}

function saveRole() {
    const form = document.getElementById('roleForm');
    const formData = new FormData(form);
    const id = document.getElementById('roleId').value;
    const url = id ? `/role-permission/role/${id}` : '/role-permission/role';
    const method = id ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            name: document.getElementById('roleName').value,
            description: document.getElementById('roleDescription').value
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Success', data.message, 'success').then(() => location.reload());
        } else {
            Swal.fire('Error', data.message || 'Terjadi kesalahan', 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error', 'Terjadi kesalahan', 'error');
    });
}

function editRole(id) {
    openRoleModal(id);
}

function deleteRole(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Role akan dihapus secara permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/role-permission/role/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Deleted!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message || 'Terjadi kesalahan', 'error');
                }
            });
        }
    });
}

function manageRolePermissions(id) {
    currentRoleId = id;
    Promise.all([
        fetch(`/role-permission/role/${id}`).then(res => res.json()),
        fetch('/role-permission/permissions').then(res => res.json())
    ])
    .then(([role, permissions]) => {
        document.getElementById('rolePermissionsRoleName').textContent = role.name;
        
        const container = document.getElementById('rolePermissionsList');
        container.innerHTML = '';
        
        const rolePermissionIds = (role.permissions || []).map(p => p.id);
        
        permissions.forEach(permission => {
            const checked = rolePermissionIds.includes(permission.id) ? 'checked' : '';
            const div = document.createElement('div');
            div.className = 'form-check mb-2';
            div.innerHTML = `
                <input class="form-check-input" type="checkbox" value="${permission.id}" id="perm_${permission.id}" ${checked}>
                <label class="form-check-label" for="perm_${permission.id}">
                    <strong>${permission.name}</strong> <code>${permission.slug}</code>
                    ${permission.description ? `<br><small class="text-muted">${permission.description}</small>` : ''}
                </label>
            `;
            container.appendChild(div);
        });
        
        new bootstrap.Modal(document.getElementById('rolePermissionsModal')).show();
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Gagal memuat data', 'error');
    });
}

function saveRolePermissions() {
    const checkboxes = document.querySelectorAll('#rolePermissionsList input[type="checkbox"]:checked');
    const permissionIds = Array.from(checkboxes).map(cb => parseInt(cb.value));
    
    fetch(`/role-permission/role/${currentRoleId}/permissions`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ permission_ids: permissionIds })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Success', data.message, 'success').then(() => {
                bootstrap.Modal.getInstance(document.getElementById('rolePermissionsModal')).hide();
                location.reload();
            });
        } else {
            Swal.fire('Error', data.message || 'Terjadi kesalahan', 'error');
        }
    });
}

// Permission Functions
function openPermissionModal(id = null) {
    const modal = new bootstrap.Modal(document.getElementById('permissionModal'));
    const title = document.getElementById('permissionModalTitle');
    const form = document.getElementById('permissionForm');
    
    if (id) {
        title.textContent = 'Edit Permission';
        fetch(`/role-permission/permission/${id}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('permissionId').value = data.id;
                document.getElementById('permissionName').value = data.name;
                document.getElementById('permissionDescription').value = data.description || '';
            });
    } else {
        title.textContent = 'Tambah Permission';
        form.reset();
        document.getElementById('permissionId').value = '';
    }
    
    modal.show();
}

function savePermission() {
    const id = document.getElementById('permissionId').value;
    const url = id ? `/role-permission/permission/${id}` : '/role-permission/permission';
    const method = id ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            name: document.getElementById('permissionName').value,
            description: document.getElementById('permissionDescription').value
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Success', data.message, 'success').then(() => location.reload());
        } else {
            Swal.fire('Error', data.message || 'Terjadi kesalahan', 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error', 'Terjadi kesalahan', 'error');
    });
}

function editPermission(id) {
    openPermissionModal(id);
}

function deletePermission(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Permission akan dihapus secara permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/role-permission/permission/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Deleted!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message || 'Terjadi kesalahan', 'error');
                }
            });
        }
    });
}

// User Functions
function manageUserRoles(id) {
    currentUserId = id;
    Promise.all([
        fetch(`/role-permission/user/${id}`).then(res => res.json()),
        fetch('/role-permission/roles').then(res => res.json())
    ])
    .then(([user, roles]) => {
        document.getElementById('userRolesUserName').textContent = `${user.name} (${user.email})`;
        
        const container = document.getElementById('userRolesList');
        container.innerHTML = '';
        
        const userRoleIds = (user.roles || []).map(r => r.id);
        
        roles.forEach(role => {
            const checked = userRoleIds.includes(role.id) ? 'checked' : '';
            const div = document.createElement('div');
            div.className = 'form-check mb-2';
            div.innerHTML = `
                <input class="form-check-input" type="checkbox" value="${role.id}" id="role_${role.id}" ${checked}>
                <label class="form-check-label" for="role_${role.id}">
                    <strong>${role.name}</strong> <code>${role.slug}</code>
                    ${role.description ? `<br><small class="text-muted">${role.description}</small>` : ''}
                </label>
            `;
            container.appendChild(div);
        });
        
        new bootstrap.Modal(document.getElementById('userRolesModal')).show();
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Gagal memuat data', 'error');
    });
}

function saveUserRoles() {
    const checkboxes = document.querySelectorAll('#userRolesList input[type="checkbox"]:checked');
    const roleIds = Array.from(checkboxes).map(cb => parseInt(cb.value));
    
    fetch(`/role-permission/user/${currentUserId}/roles`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ role_ids: roleIds })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Success', data.message, 'success').then(() => {
                bootstrap.Modal.getInstance(document.getElementById('userRolesModal')).hide();
                location.reload();
            });
        } else {
            Swal.fire('Error', data.message || 'Terjadi kesalahan', 'error');
        }
    });
}
</script>
@endsection

