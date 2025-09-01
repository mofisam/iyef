<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vobab Security - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #6c757d;
            --success: #198754;
            --info: #0dcaf0;
            --warning: #ffc107;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #212529;
            --sidebar-width: 250px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fb;
            overflow-x: hidden;
        }
        
        #sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            background: linear-gradient(180deg, var(--primary) 0%, #3a56d4 100%);
            color: white;
            transition: all 0.3s;
            z-index: 1000;
        }
        
        #sidebar .sidebar-header {
            padding: 20px;
            background: rgba(0, 0, 0, 0.1);
        }
        
        #sidebar ul.components {
            padding: 20px 0;
        }
        
        #sidebar ul li a {
            padding: 15px 25px;
            display: block;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        #sidebar ul li a:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }
        
        #sidebar ul li a.active {
            color: white;
            background: rgba(255, 255, 255, 0.2);
        }
        
        #sidebar ul li a i {
            margin-right: 10px;
        }
        
        #content {
            width: calc(100% - var(--sidebar-width));
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s;
            padding: 20px;
        }
        
        .stat-card {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card i {
            font-size: 2rem;
            opacity: 0.8;
        }
        
        .dashboard-table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .dashboard-table .table thead th {
            background-color: #f8f9fa;
            border-top: none;
            font-weight: 600;
            color: var(--secondary);
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .badge-subscription {
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 500;
        }
        
        .chart-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            #sidebar {
                width: 80px;
                text-align: center;
            }
            
            #sidebar .sidebar-header h3 {
                display: none;
            }
            
            #sidebar ul li a span {
                display: none;
            }
            
            #sidebar ul li a i {
                margin-right: 0;
                font-size: 1.5rem;
            }
            
            #content {
                width: calc(100% - 80px);
                margin-left: 80px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3>Vobab Security</h3>
        </div>

        <ul class="list-unstyled components">
            <li>
                <a href="#" class="active">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="bi bi-people"></i>
                    <span>Users</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="bi bi-collection"></i>
                    <span>Projects</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="bi bi-link-45deg"></i>
                    <span>Preview Links</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="bi bi-credit-card"></i>
                    <span>Subscriptions</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="bi bi-bar-chart"></i>
                    <span>Analytics</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Content -->
    <div id="content">
        <!-- Top Navigation -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white rounded-3 mb-4 shadow-sm">
            <div class="container-fluid">
                <button type="button" id="sidebarCollapse" class="btn btn-primary">
                    <i class="bi bi-list"></i>
                </button>
                
                <div class="d-flex align-items-center">
                    <div class="dropdown ms-3">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://ui-avatars.com/api/?name=Admin+User&background=random" alt="Admin" class="user-avatar">
                            <span class="d-none d-md-inline mx-2">Admin User</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownUser">
                            <li><a class="dropdown-item" href="#">Profile</a></li>
                            <li><a class="dropdown-item" href="#">Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Admin Dashboard</h2>
            <div>
                <button class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>New User
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card bg-white p-4 rounded-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Total Users</h6>
                            <h3 class="mb-0">128</h3>
                        </div>
                        <div class="text-primary">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-success"><i class="bi bi-arrow-up me-1"></i> 12%</span>
                        <span class="text-muted small ms-2">Since last month</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card bg-white p-4 rounded-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Active Projects</h6>
                            <h3 class="mb-0">342</h3>
                        </div>
                        <div class="text-info">
                            <i class="bi bi-folder-fill"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-success"><i class="bi bi-arrow-up me-1"></i> 8%</span>
                        <span class="text-muted small ms-2">Since last month</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card bg-white p-4 rounded-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Preview Links</h6>
                            <h3 class="mb-0">1,248</h3>
                        </div>
                        <div class="text-warning">
                            <i class="bi bi-link-45deg"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-success"><i class="bi bi-arrow-up me-1"></i> 24%</span>
                        <span class="text-muted small ms-2">Since last month</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card bg-white p-4 rounded-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Monthly Revenue</h6>
                            <h3 class="mb-0">$2,489</h3>
                        </div>
                        <div class="text-success">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-success"><i class="bi bi-arrow-up me-1"></i> 5%</span>
                        <span class="text-muted small ms-2">Since last month</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="chart-container">
                    <h5 class="mb-3">User Signups</h5>
                    <canvas id="userSignupsChart" height="300"></canvas>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="chart-container">
                    <h5 class="mb-3">Subscription Plans</h5>
                    <canvas id="subscriptionPlansChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="dashboard-table mb-4">
            <div class="p-4 border-bottom">
                <h5 class="mb-0">Recent Users</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Account Type</th>
                            <th>Subscription</th>
                            <th>Projects</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name=John+Doe&background=random" class="user-avatar me-3">
                                    <div>John Doe</div>
                                </div>
                            </td>
                            <td>john@example.com</td>
                            <td>Designer</td>
                            <td>
                                <span class="badge-subscription bg-success">Premium</span>
                            </td>
                            <td>12</td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></button>
                                    <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name=Jane+Smith&background=random" class="user-avatar me-3">
                                    <div>Jane Smith</div>
                                </div>
                            </td>
                            <td>jane@example.com</td>
                            <td>Designer</td>
                            <td>
                                <span class="badge-subscription bg-info">Basic</span>
                            </td>
                            <td>7</td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></button>
                                    <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name=Robert+Johnson&background=random" class="user-avatar me-3">
                                    <div>Robert Johnson</div>
                                </div>
                            </td>
                            <td>robert@example.com</td>
                            <td>Designer</td>
                            <td>
                                <span class="badge-subscription bg-warning text-dark">Free</span>
                            </td>
                            <td>3</td>
                            <td><span class="badge bg-warning text-dark">Trial</span></td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></button>
                                    <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name=Sarah+Williams&background=random" class="user-avatar me-3">
                                    <div>Sarah Williams</div>
                                </div>
                            </td>
                            <td>sarah@example.com</td>
                            <td>Designer</td>
                            <td>
                                <span class="badge-subscription bg-info">Basic</span>
                            </td>
                            <td>9</td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></button>
                                    <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name=Michael+Brown&background=random" class="user-avatar me-3">
                                    <div>Michael Brown</div>
                                </div>
                            </td>
                            <td>michael@example.com</td>
                            <td>Designer</td>
                            <td>
                                <span class="badge-subscription bg-success">Premium</span>
                            </td>
                            <td>15</td>
                            <td><span class="badge bg-danger">Inactive</span></td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></button>
                                    <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">
                <nav aria-label="Users table navigation">
                    <ul class="pagination mb-0 justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#">Previous</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row">
            <div class="col-lg-6">
                <div class="dashboard-table">
                    <div class="p-4 border-bottom">
                        <h5 class="mb-0">Recent Projects</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Mobile App Design</h6>
                                <small class="text-muted">2 hours ago</small>
                            </div>
                            <p class="mb-1">By John Doe</p>
                            <small class="text-muted">3 assets, 5 preview links</small>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Website Redesign</h6>
                                <small class="text-muted">5 hours ago</small>
                            </div>
                            <p class="mb-1">By Jane Smith</p>
                            <small class="text-muted">12 assets, 8 preview links</small>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Brand Identity</h6>
                                <small class="text-muted">Yesterday</small>
                            </div>
                            <p class="mb-1">By Robert Johnson</p>
                            <small class="text-muted">7 assets, 3 preview links</small>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="dashboard-table">
                    <div class="p-4 border-bottom">
                        <h5 class="mb-0">Recent Preview Links</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Client Review - Homepage</h6>
                                <small class="text-muted">Active</small>
                            </div>
                            <p class="mb-1">Created by John Doe</p>
                            <small class="text-muted">12 views, expires in 3 days</small>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Final Approval</h6>
                                <small class="text-muted">Expired</small>
                            </div>
                            <p class="mb-1">Created by Jane Smith</p>
                            <small class="text-muted">23 views, expired yesterday</small>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Internal Review</h6>
                                <small class="text-muted">Active</small>
                            </div>
                            <p class="mb-1">Created by Sarah Williams</p>
                            <small class="text-muted">7 views, expires in 7 days</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle sidebar
            document.getElementById('sidebarCollapse').addEventListener('click', function() {
                const sidebar = document.getElementById('sidebar');
                const content = document.getElementById('content');
                
                if (sidebar.style.width === '80px' || sidebar.style.width === '80px') {
                    sidebar.style.width = '250px';
                    content.style.marginLeft = '250px';
                    content.style.width = 'calc(100% - 250px)';
                    
                    // Show text in sidebar
                    const spanElements = sidebar.querySelectorAll('span');
                    spanElements.forEach(span => {
                        span.style.display = 'inline';
                    });
                    
                    const headerText = sidebar.querySelector('.sidebar-header h3');
                    if (headerText) headerText.style.display = 'block';
                } else {
                    sidebar.style.width = '80px';
                    content.style.marginLeft = '80px';
                    content.style.width = 'calc(100% - 80px)';
                    
                    // Hide text in sidebar
                    const spanElements = sidebar.querySelectorAll('span');
                    spanElements.forEach(span => {
                        span.style.display = 'none';
                    });
                    
                    const headerText = sidebar.querySelector('.sidebar-header h3');
                    if (headerText) headerText.style.display = 'none';
                }
            });

            // User Signups Chart
            const userSignupsCtx = document.getElementById('userSignupsChart').getContext('2d');
            const userSignupsChart = new Chart(userSignupsCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'User Signups',
                        data: [12, 19, 15, 17, 22, 25, 30, 28, 32, 40, 35, 38],
                        borderColor: '#4361ee',
                        backgroundColor: 'rgba(67, 97, 238, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });

            // Subscription Plans Chart
            const subscriptionPlansCtx = document.getElementById('subscriptionPlansChart').getContext('2d');
            const subscriptionPlansChart = new Chart(subscriptionPlansCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Free', 'Basic', 'Premium'],
                    datasets: [{
                        data: [45, 30, 25],
                        backgroundColor: [
                            '#ffc107',
                            '#0dcaf0',
                            '#198754'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>