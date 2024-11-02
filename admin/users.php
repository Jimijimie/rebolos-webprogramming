<?php
    session_start();
    require_once '../includes/head.php';

    // Check if user is logged in and is admin
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
                                <button class="btn btn-primary brand-bg-color" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                    Add New User
                                </button>
                            </div>
                            <h4 class="page-title">User Management</h4>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="usersTable" class="table table-centered table-nowrap mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-start">ID</th>
                                                <th>Name</th>
                                                <th>Username</th>
                                                <th>Role</th>
                                                <th class="text-center">Status</th>
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
            const usersTable = $('#usersTable').DataTable({
                ajax: {
                    url: 'user-actions.php',
                    type: 'GET'
                },
                columns: [
                    { data: 'id', className: 'text-start' },
                    { 
                        data: null,
                        render: function(data) {
                            return `${data.first_name} ${data.last_name}`;
                        }
                    },
                    { data: 'username' },
                    { data: 'role' },
                    { 
                        data: 'is_active',
                        className: 'text-center',
                        render: data => `<span class="badge ${data ? 'bg-success' : 'bg-danger'}">${data ? 'Active' : 'Inactive'}</span>`
                    },
                    {
                        data: null,
                        className: 'text-center',
                        render: function(data) {
                            return `
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-info edit-user" data-id="${data.id}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning toggle-status" data-id="${data.id}">
                                        <i class="bi bi-toggle-on"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-user" data-id="${data.id}">
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
