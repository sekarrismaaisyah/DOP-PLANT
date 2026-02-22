@extends('layouts.master')

@section('title', 'Role & Permission Management')

@section('css')
<link href="{{ URL::asset('build/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .nav-tabs-custom .nav-link {
        font-weight: 500;
        color: #6b7280;
        border: none;
        border-bottom: 2px solid transparent;
        padding: 0.6rem 1rem;
    }
    .nav-tabs-custom .nav-link:hover { color: #111827; }
    .nav-tabs-custom .nav-link.active {
        color: #008cff;
        border-bottom-color: #008cff;
        background: transparent;
    }
</style>
@endsection

@section('content')
<x-page-title title="Role & Permission" pagetitle="Kelola Roles, Permissions, dan User Assignments" />

<div class="row">
    <div class="col-12">
        <div class="card rounded-4 border-0 shadow-sm">
            <div class="card-body">
                <ul class="nav nav-tabs nav-tabs-custom mb-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="roles-tab" data-bs-toggle="tab" data-bs-target="#roles" type="button" role="tab" aria-selected="true">
                            <i class="material-icons-outlined me-1">group</i> Roles
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="permissions-tab" data-bs-toggle="tab" data-bs-target="#permissions" type="button" role="tab" aria-selected="false">
                            <i class="material-icons-outlined me-1">lock</i> Permissions
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" aria-selected="false">
                            <i class="material-icons-outlined me-1">people</i> User Management
                        </button>
                    </li>
                </ul>

                <div class="tab-content py-3">
                    <!-- Roles Tab -->
                    <div class="tab-pane fade show active" id="roles" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                            <h5 class="mb-0 fw-bold">Daftar Roles</h5>
                            <button type="button" class="btn btn-primary btn-sm" onclick="openRoleModal()">
                                <i class="material-icons-outlined">add</i> Tambah Role
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table id="rolesDataTable" class="table table-bordered table-hover align-middle mb-0" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama</th>
                                        <th>Slug</th>
                                        <th>Description</th>
                                        <th>Permissions</th>
                                        <th width="140">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($roles as $role)
                                    <tr>
                                        <td>{{ $role->id }}</td>
                                        <td><strong>{{ $role->name }}</strong></td>
                                        <td><code>{{ $role->slug }}</code></td>
                                        <td>{{ $role->description ?? '-' }}</td>
                                        <td>
                                            @foreach($role->permissions as $permission)
                                                <span class="badge bg-success me-1">{{ $permission->name }}</span>
                                            @endforeach
                                            @if($role->permissions->isEmpty())
                                                <span class="text-muted">No permissions</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="editRole({{ $role->id }})" title="Edit">
                                                <i class="material-icons-outlined" style="font-size:18px">edit</i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-warning" onclick="manageRolePermissions({{ $role->id }})" title="Atur Permissions">
                                                <i class="material-icons-outlined" style="font-size:18px">settings</i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteRole({{ $role->id }})" title="Hapus">
                                                <i class="material-icons-outlined" style="font-size:18px">delete</i>
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
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                            <h5 class="mb-0 fw-bold">Daftar Permissions</h5>
                            <button type="button" class="btn btn-primary btn-sm" onclick="openPermissionModal()">
                                <i class="material-icons-outlined">add</i> Tambah Permission
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table id="permissionsDataTable" class="table table-bordered table-hover align-middle mb-0" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama</th>
                                        <th>Slug</th>
                                        <th>Description</th>
                                        <th width="120">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($permissions as $permission)
                                    <tr>
                                        <td>{{ $permission->id }}</td>
                                        <td><strong>{{ $permission->name }}</strong></td>
                                        <td><code>{{ $permission->slug }}</code></td>
                                        <td>{{ $permission->description ?? '-' }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="editPermission({{ $permission->id }})" title="Edit">
                                                <i class="material-icons-outlined" style="font-size:18px">edit</i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deletePermission({{ $permission->id }})" title="Hapus">
                                                <i class="material-icons-outlined" style="font-size:18px">delete</i>
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
                            <h5 class="mb-0 fw-bold">Daftar Users</h5>
                        </div>
                        <div class="table-responsive">
                            <table id="usersDataTable" class="table table-bordered table-hover align-middle mb-0" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Roles</th>
                                        <th width="160">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td><strong>{{ $user->name }}</strong></td>
                                        <td><code>{{ $user->email }}</code></td>
                                        <td>
                                            @foreach($user->roles as $role)
                                                <span class="badge bg-primary me-1">{{ $role->name }}</span>
                                            @endforeach
                                            @if($user->roles->isEmpty())
                                                <span class="text-muted">No roles</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-warning" onclick="manageUserRoles({{ $user->id }})" title="Atur Roles">
                                                <i class="material-icons-outlined me-1" style="font-size:18px">settings</i> Manage Roles
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
                <p class="text-muted small mb-2">Satu role bisa punya <strong>banyak permission</strong>. Centang semua permission yang ingin diberikan ke role ini.</p>
                <div class="mb-2 d-flex gap-2 flex-wrap">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="checkAllRolePermissions(true)">Pilih Semua</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="checkAllRolePermissions(false)">Hapus Semua</button>
                </div>
                <div class="mb-3">
                    <label class="form-label">Pilih Permissions (bisa lebih dari satu):</label>
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
<script src="{{ URL::asset('build/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('build/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
let currentRoleId = null;
let currentUserId = null;

var dtLang = {
    processing: "Memproses...",
    search: "Cari:",
    lengthMenu: "Tampilkan _MENU_ data",
    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
    infoFiltered: "(disaring dari _MAX_ total data)",
    paginate: { first: "Pertama", last: "Terakhir", next: "Selanjutnya", previous: "Sebelumnya" },
    emptyTable: "Tidak ada data",
    zeroRecords: "Tidak ada data yang cocok"
};

$(document).ready(function() {
    if ($.fn.DataTable && document.getElementById('rolesDataTable') && !$.fn.DataTable.isDataTable('#rolesDataTable')) {
        $('#rolesDataTable').DataTable({
            order: [[0, 'asc']],
            pageLength: 25,
            language: dtLang,
            columnDefs: [
                { orderable: false, targets: [4, 5] }
            ]
        });
    }

    $('button[data-bs-target="#permissions"]').on('shown.bs.tab', function() {
        if ($.fn.DataTable && document.getElementById('permissionsDataTable') && !$.fn.DataTable.isDataTable('#permissionsDataTable')) {
            $('#permissionsDataTable').DataTable({
                order: [[0, 'asc']],
                pageLength: 25,
                language: dtLang,
                columnDefs: [
                    { orderable: false, targets: 4 }
                ]
            });
        }
    });

    $('button[data-bs-target="#users"]').on('shown.bs.tab', function() {
        if ($.fn.DataTable && document.getElementById('usersDataTable') && !$.fn.DataTable.isDataTable('#usersDataTable')) {
            $('#usersDataTable').DataTable({
                order: [[0, 'asc']],
                pageLength: 25,
                language: dtLang,
                columnDefs: [
                    { orderable: false, targets: [3, 4] }
                ]
            });
        }
    });
});

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

function checkAllRolePermissions(checked) {
    document.querySelectorAll('#rolePermissionsList input[type="checkbox"]').forEach(cb => { cb.checked = checked; });
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

