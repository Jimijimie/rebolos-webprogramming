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
                <!-- Page Title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box">
                            <div class="page-title-right d-flex align-items-center">
                                <button class="btn btn-primary brand-bg-color" data-bs-toggle="modal" data-bs-target="#addAccountModal">
                                    Add New Account
                                </button>
                            </div>
                            <h4 class="page-title">Accounts Management</h4>
                        </div>
                    </div>
                </div>

                <!-- Accounts Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="accountsTable" class="table table-centered table-nowrap mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-start">ID</th>
                                                <th>Name</th>
                                                <th>Username</th>
                                                <th>Role</th>
                                                <th class="text-center">Staff</th>
                                                <th class="text-center">Admin</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Account Modal -->
    <div class="modal fade" id="addAccountModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addAccountForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>First Name</label>
                            <input type="text" class="form-control" name="first_name" required>
                        </div>
                        <div class="mb-3">
                            <label>Last Name</label>
                            <input type="text" class="form-control" name="last_name" required>
                        </div>
                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label>Role</label>
                            <select class="form-control" name="role">
                                <option value="staff">Staff</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php require_once '../includes/footer.php'; ?>

    <script>
        $(document).ready(function() {
            const accountsTable = $('#accountsTable').DataTable({
                ajax: {
                    url: 'account-actions.php',
                    type: 'GET',
                    error: function(xhr, error, thrown) {
                        console.error('DataTables error:', error);
                        console.error('Server response:', xhr.responseText);
                    }
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
                        data: 'is_staff', 
                        className: 'text-center',
                        render: data => `<span class="badge ${data ? 'bg-success' : 'bg-secondary'}">${data ? 'Yes' : 'No'}</span>`
                    },
                    { 
                        data: 'is_admin', 
                        className: 'text-center',
                        render: data => `<span class="badge ${data ? 'bg-primary' : 'bg-secondary'}">${data ? 'Yes' : 'No'}</span>`
                    },
                    {
                        data: null,
                        className: 'text-center',
                        render: function(data) {
                            return `
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-info edit-btn" data-id="${data.id}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-btn" data-id="${data.id}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
                responsive: true,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
                language: {
                    search: "",
                    searchPlaceholder: "Search accounts..."
                },
                pageLength: 10,
                ordering: false
            });

            $('#addAccountForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('action', 'add');

                $.ajax({
                    url: 'account-actions.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        const result = JSON.parse(response);
                        if(result.success) {
                            $('#addAccountModal').modal('hide');
                            accountsTable.ajax.reload();
                            alert('Account added successfully!');
                        } else {
                            alert('Failed to add account');
                        }
                    }
                });
            });
        });

        function deleteAccount(id) {
            if(confirm('Are you sure you want to delete this account?')) {
                $.ajax({
                    url: 'account-actions.php',
                    type: 'POST',
                    data: {
                        action: 'delete',
                        id: id
                    },
                    success: function(response) {
                        const result = JSON.parse(response);
                        if(result.success) {
                            $('#accountsTable').DataTable().ajax.reload();
                            alert('Account deleted successfully!');
                        } else {
                            alert('Failed to delete account');
                        }
                    }
                });
            }
        }
    </script>
</body>
</html>
