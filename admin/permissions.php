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
                                <button class="btn btn-primary brand-bg-color" data-bs-toggle="modal" data-bs-target="#addPermissionModal">
                                    Add New Permission
                                </button>
                            </div>
                            <h4 class="page-title">Permissions Management</h4>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="permissionsTable" class="table table-centered table-nowrap mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-start">ID</th>
                                                <th>Permission Name</th>
                                                <th>Description</th>
                                                <th class="text-center">Roles Count</th>
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

    <!-- Add Permission Modal -->
    <div class="modal fade" id="addPermissionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Permission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addPermissionForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Permission Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label>Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Permission</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php require_once '../includes/footer.php'; ?>

    <script>
        $(document).ready(function() {
            const permissionsTable = $('#permissionsTable').DataTable({
                ajax: {
                    url: 'permission-actions.php',
                    type: 'GET'
                },
                columns: [
                    { data: 'id', className: 'text-start' },
                    { data: 'name' },
                    { data: 'description' },
                    { 
                        data: 'roles_count',
                        className: 'text-center',
                        render: data => `<span class="badge bg-info">${data}</span>`
                    },
                    {
                        data: null,
                        className: 'text-center',
                        render: function(data) {
                            return `
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-info edit-permission" data-id="${data.id}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-permission" data-id="${data.id}">
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
    </script>
</body>
</html>
