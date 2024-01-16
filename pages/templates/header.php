<?php
$query = mysqli_query($conn, "SELECT * FROM seo WHERE id='1'");
$row = mysqli_fetch_assoc($query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= getSingleValDB("seo", "id", "1", "title") ?></title>
    <meta name="author" content="<?= getSingleValDB("seo", "id", "1", "author") ?>" />
    <meta name="copyright" content="<?= getSingleValDB("seo", "id", "1", "copyright") ?>" />
    <meta name="image" content="<?= getSingleValDB("seo", "id", "1", "image") ?>">
    <meta name="robots" content="noindex,nofollow" />
    <meta name="googlebot" content="noindex,nofollow" />
    <link rel="icon" type="image/x-icon" href="<?= getSingleValDB("seo", "id", "1", "favicon") ?>">
    <link rel="canonical" content href="<?= getSingleValDB("seo", "id", "1", "url") ?>" />
    <!-- Custom fonts for this template-->
    <link href="<?= $base_url ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;400;600;700;800;900&display=swap" rel="stylesheet" />
    <!-- Custom styles for this template-->
    <link href="<?= $base_url ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
    <!-- Datatables Vendor CSS -->
    <link href="<?= $base_url ?>/assets/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <!-- Select2 Vendor CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Sweetalert 2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.min.css" integrity="sha256-VJuwjrIWHWsPSEvQV4DiPfnZi7axOaiWwKfXaJnR5tA=" crossorigin="anonymous">
    <style>
        #pageloader {
            background: rgba(255, 255, 255, 0.8);
            display: none;
            height: 100%;
            position: fixed;
            width: 100%;
            z-index: 9999;
        }

        #pageloader img {
            left: 50%;
            margin-left: -32px;
            margin-top: -32px;
            position: absolute;
            top: 50%;
        }

        .button {
            position: relative;
            padding: 8px 16px;
            background: #009579;
            border: none;
            outline: none;
            border-radius: 2px;
            cursor: pointer;
        }

        .button:active {
            background: #007a63;
        }

        .button__text {
            font: bold 20px "Quicksand", san-serif;
            color: #ffffff;
            transition: all 0.2s;
        }

        .button--loading .button__text {
            visibility: hidden;
            opacity: 0;
        }

        .button--loading::after {
            content: "";
            position: absolute;
            width: 16px;
            height: 16px;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            margin: auto;
            border: 4px solid transparent;
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: button-loading-spinner 1s ease infinite;
        }

        @keyframes button-loading-spinner {
            from {
                transform: rotate(0turn);
            }

            to {
                transform: rotate(1turn);
            }
        }
    </style>
</head>

<body id="page-top">
    <div id="pageloader">
        <img src="http://cdnjs.cloudflare.com/ajax/libs/semantic-ui/0.16.1/images/loader-large.gif" alt="on loading..." />
    </div>
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-black sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="https://goican.com/">
                <div class="sidebar-brand-icon">
                    <img class="img-fluid px-1 px-sm-1 mt-1 mb-1" src="<?= $base_url ?>public/files/wbg-logo.png" alt="logo">
                </div>
            </a>
            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link font-weight-bold" href="<?= $base_url ?>pages/dashboard.php">
                    <i class="fas fa-fw fa-bars"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Whatsapp Action
            </div>

            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseWhatsapp" aria-expanded="true" aria-controls="collapseWhatsapp">
                    <i class="fas fa-fw fa-paper-plane"></i>
                    <span>Whatsapp Tools</span>
                </a>
                <div id="collapseWhatsapp" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                    <div class="bg-black py-0 collapse-inner rounded">
                        <h6 class="collapse-header">BOT:</h6>
                        <a class="collapse-item" href="<?= $base_url ?>pages/chatbot/check-numbers.php">Check numbers</a>
                        <a class="collapse-item" href="<?= $base_url ?>pages/chatbot/send-message.php">Send message</a>
                        <a class="collapse-item" href="<?= $base_url ?>pages/chatbot/send-broadcast.php">Send broadcast</a>
                        <a class="collapse-item" href="<?= $base_url ?>pages/chatbot/send-group.php">Send to group</a>
                        <a class="collapse-item" href="<?= $base_url ?>pages/chatbot/autoreply.php">Auto reply</a>
                        <a class="collapse-item" href="<?= $base_url ?>pages/chatbot/log-sender.php">Log sender</a>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Public Interaction
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMaster" aria-expanded="true" aria-controls="collapseMaster">
                    <i class="fas fa-fw fa-database"></i>
                    <span>Master Data</span>
                </a>
                <div id="collapseMaster" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-black py-0 collapse-inner rounded">
                        <h6 class="collapse-header">Master:</h6>
                        <?php if ($_SESSION['level'] == 'admin') { ?>
                            <a class="collapse-item" href="<?= $base_url ?>pages/master/users.php">Master Users</a>
                        <?php } ?>
                        <a class="collapse-item" href="<?= $base_url ?>pages/master/category.php">Master Category</a>
                        <a class="collapse-item" href="<?= $base_url ?>pages/master/contact.php">Master Contact</a>
                        <a class="collapse-item" href="<?= $base_url ?>pages/master/template.php">Master Template</a>
                        <a class="collapse-item" href="<?= $base_url ?>pages/master/media.php">Master Media</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSetting" aria-expanded="true" aria-controls="collapseSetting">
                    <i class="fas fa-fw fa-cogs"></i>
                    <span>Settings</span>
                </a>
                <div id="collapseSetting" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-black py-0 collapse-inner rounded">
                        <h6 class="collapse-header">Setting:</h6>
                        <a class="collapse-item" href="<?= $base_url ?>pages/docs.php">Docs and API</a>
                        <?php if ($_SESSION['level'] === 'admin') { ?>
                            <a class="collapse-item" href="<?= $base_url ?>pages/settings/app-info.php">App & Setting</a>
                        <?php } ?>
                    </div>
                </div>
            </li>

            <?php if ($_SESSION['level'] === 'admin') { ?>
                <!-- Nav Item - Pages Collapse Menu -->
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseManagementsCharts" aria-expanded="true" aria-controls="collapseManagementsCharts">
                        <i class="fas fa-fw fa-chart-pie"></i>
                        <span>Chart & Managements</span>
                    </a>
                    <div id="collapseManagementsCharts" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                        <div class="bg-black py-0 collapse-inner rounded">
                            <h6 class="collapse-header">Log Histories:</h6>
                            <a class="collapse-item" href="<?= $base_url ?>pages/manage/logs.php">Chart</a>
                        </div>
                    </div>
                </li>
            <?php } ?>

            <!-- Nav Item - Tables -->
            <li class="nav-item">
                <a class="nav-link" href="<?= $base_url ?>pages/information.php">
                    <i class="fas fa-fw fa-info-circle"></i>
                    <span>Information</span></a>
            </li>

            <!-- Divider -->

            <hr class="sidebar-divider d-none d-md-block">

            <li class="nav-item">
                <a class="nav-link text-danger action_confirm" href="<?= $base_url ?>logout.php">
                    <i class="fas fa-sign-out-alt text-danger"></i>
                    <span>Logout</span></a>
            </li>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-black topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <!-- Topbar Search -->
                    <span class="font-weight-bold m-1 p-1">
                        <h5>[ IP ADDRESS <?= $_SERVER['REMOTE_ADDR'] ?> ]</h5>
                    </span>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $_SESSION['email']; ?></span>
                                <img class="img-profile rounded-circle" src="<?= $base_url ?>public/files/profile.png">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="<?= $base_url ?>pages/profile.php">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" data-toggle="modal" data-target="#info-modal">
                                    <i class="fas fa-question fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Quick Info
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" data-toggle="modal" data-target="#aboutModal">
                                    <i class="fas fa-info fa-sm fa-fw mr-2 text-gray-400"></i>
                                    About
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger action_confirm" href="<?= $base_url ?>logout.php" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-danger"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->