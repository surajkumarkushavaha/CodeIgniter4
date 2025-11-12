<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Ecommerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Ecommerce Admin</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="<?= base_url('admin/logout') ?>">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-3">
                <div class="list-group">
                    <a href="<?= base_url('admin/dashboard') ?>" class="list-group-item list-group-item-action active">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                    <a href="<?= base_url('admin/products') ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-box me-2"></i>Products
                    </a>
                    <a href="<?= base_url('admin/orders') ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-cart me-2"></i>Orders
                    </a>
                </div>
            </div>
            
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card text-white bg-primary mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?= $total_products ?></h4>
                                        <p>Total Products</p>
                                    </div>
                                    <i class="bi bi-box fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card text-white bg-success mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?= $total_orders ?></h4>
                                        <p>Total Orders</p>
                                    </div>
                                    <i class="bi bi-cart fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Quick Actions</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <a href="<?= base_url('admin/products') ?>" class="btn btn-outline-primary w-100 mb-2">
                                    <i class="bi bi-plus-circle me-2"></i>Manage Products
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="<?= base_url('admin/orders') ?>" class="btn btn-outline-success w-100 mb-2">
                                    <i class="bi bi-cart-check me-2"></i>View Orders
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>