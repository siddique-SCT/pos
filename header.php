<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>POS</title>
        <link href="asset/css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" type="text/css" href="asset/vendor/datatables/dataTables.bootstrap5.min.css"/>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <style>
    /* Sidebar Navigation Links */
    .nav-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 15px;
        border-radius: 5px;
        background-color: maroon; 
        color: orange;
        font-weight: bold;
        text-decoration: none;
        margin-bottom: 8px;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    /* Text Section (Link Name & Shortcut) */
    .nav-text {
        display: flex;
        flex-direction: column;
        color: white;
    }

    /* Shortcut Key Style */
    .shortcut {
        font-size: 0.75rem;
        color: white; 
    }

    /* Icon Style */
    .nav-link-icon {
        width: 36px;
        height: 36px;
        background-color: #FFD580; 
        color: maroon;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 1.2rem;
        transition: transform 0.2s ease;
    }

    /* Hover Effects */
    .nav-link:hover {
        background-color: orange !important;
        color: white !important;
    }
    
    .nav-link:hover .nav-link-icon {
        transform: scale(1.1); /* Slight zoom effect on hover */
    }
</style>

    </head>
    <body>
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <a class="navbar-brand ps-3" href="index.html">POS</a>
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="user_profile.php">Profile</a></li>
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">    
                            <a class="nav-link" href="dashboard.php" accesskey="d">
    <div class="nav-text">
        Dashboard
        <span class="shortcut">[Alt + D]</span>
    </div>
    <div class="nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
</a>

<a class="nav-link" href="category.php" accesskey="c">
    <div class="nav-text">
        Category
        <span class="shortcut">[Alt + C]</span>
    </div>
    <div class="nav-link-icon"><i class="far fa-building"></i></div>
</a>

<a class="nav-link" href="user.php" accesskey="u">
    <div class="nav-text">
        User
        <span class="shortcut">[Alt + U]</span>
    </div>
    <div class="nav-link-icon"><i class="fas fa-user-md"></i></div>
</a>

<a class="nav-link" href="product.php" accesskey="p">
    <div class="nav-text">
        Product
        <span class="shortcut">[Alt + P]</span>
    </div>
    <div class="nav-link-icon"><i class="fas fa-box"></i></div>
</a>

<a class="nav-link" href="add_order.php" accesskey="o">
    <div class="nav-text">
        Create Order
        <span class="shortcut">[Alt + O]</span>
    </div>
    <div class="nav-link-icon"><i class="fas fa-plus-circle"></i></div>
</a>

<a class="nav-link" href="order.php" accesskey="h">
    <div class="nav-text">
        Order History
        <span class="shortcut">[Alt + H]</span>
    </div>
    <div class="nav-link-icon"><i class="fas fa-history"></i></div>
</a>

<a class="nav-link" href="change_password.php" accesskey="s">
    <div class="nav-text">
        Change Password
        <span class="shortcut">[Alt + S]</span>
    </div>
    <div class="nav-link-icon"><i class="fas fa-key"></i></div>
</a>

<a class="nav-link" href="logout.php">
    <div class="nav-text">
        Logout
    </div>
    <div class="nav-link-icon"><i class="fas fa-sign-out-alt"></i></div>
</a>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        Admin
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4 mb-4">