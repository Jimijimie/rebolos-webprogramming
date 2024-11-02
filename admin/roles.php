<?php
    session_start();
    require_once '../includes/head.php';

    if(!isset($_SESSION['account']) || !$_SESSION['account']['is_admin']){
        header('location: ../account/login.php');
        exit;
    }
?>
<body id="page-top">
    <div class="wrapper">
        <?php 
            require_once '../includes/topnav.php';
            require_once '../includes/sidebar.php';
        ?>
        
        <div class="content-page">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box">
                            <div class="page-title-right d-flex align-items-center">
                                <button class="btn btn-primary brand-bg-color" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                                    Add New Role
                                </button>
                            </div>
                            <h4 class="page-title">Role Management</h4>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="rolesTable" class="table table-centered table-nowrap mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-start">ID</th>
                                                <th>Role Name</th>
                                                <th>Description</th>
                                                <th class="text-center">Users Count</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once '../includes/footer.php'; ?>

    <script>
        $(document).ready(function() {
            const rolesTable = $('#rolesTable').DataTable({
                ajax: {
                    url: 'role-actions.php',
                    type: 'GET'
                },
                columns: [
                    { data: 'id', className: 'text-start' },
                    { data: 'name' },
                    { data: 'description' },
                    { 
                        data: 'users_count',
                        className: 'text-center',
                        render: data => `<span class="badge bg-info">${data}</span>`
                    },
                    {
                        data: null,
                        className: 'text-center',
                        render: function(data) {
                            return `
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-info edit-role" data-id="${data.id}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-primary permissions-role" data-id="${data.id}">
                                        <i class="bi bi-key"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-role" data-id="${data.id}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
                responsive: true,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
                pageLength: 10,
                ordering: false
            });
        });

        // Handle permission button click
        $(document).on('click', '.permissions-role', function() {
            const roleId = $(this).data('id');
            $('#role_id').val(roleId);
            
            // Load permissions for this role
            $.ajax({
                url: 'permission-actions.php',
                type: 'GET',
                success: function(response) {
                    const permissions = JSON.parse(response).data;
                    let html = '';
                    
                    permissions.forEach(permission => {
                        html += `
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" 
                                    name="permission_ids[]" value="${permission.id}" 
                                    id="permission_${permission.id}">
                                <label class="form-check-label" for="permission_${permission.id}">
                                    ${permission.name}
                                    <small class="text-muted d-block">${permission.description}</small>
                                </label>
                            </div>
                        `;
                    });
                    
                    $('.permission-list').html(html);
                    $('#rolePermissionsModal').modal('show');
                }
            });
        });

        // Handle permission form submission
        $('#rolePermissionsForm').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'assign_permissions');
            
            $.ajax({
                url: 'role-actions.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    const result = JSON.parse(response);
                    if(result.success) {
                        $('#rolePermissionsModal').modal('hide');
                        rolesTable.ajax.reload();
                        alert('Permissions updated successfully!');
                    } else {
                        alert('Failed to update permissions');
                    }
                }
            });
        });
    </script>

    <!-- Role Permissions Modal -->
    <div class="modal fade" id="rolePermissionsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Manage Role Permissions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="rolePermissionsForm">
                    <div class="modal-body">
                        <input type="hidden" name="role_id" id="role_id">
                        <div class="row">
                            <div class="col-12">
                                <div class="permission-list">
                                    <!-- Permissions will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Permissions</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
