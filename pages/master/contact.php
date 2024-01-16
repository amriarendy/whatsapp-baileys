<?php
include_once("../../helpers/connection.php");
include_once("../../helpers/function.php");
$login = cekSession();
if ($login == 0) {
    toastr_set("mixin-error", "Forbiden access!!");
    redirect("../../login.php");
}

$email = $_SESSION['email'];
$level = $_SESSION['level'];

if (post("contact")) {
    csrf_validate();
    $contact_name = post("contact_name");
    $contact_number = post("contact_number");
    $contact_category = post("contact_category");
    $contact_type = 'PERSONAL';
    $count = countDB("contacts", "contact_name", $contact_name);
    if ($count == 0) {
        $query = mysqli_query($conn, "INSERT INTO contacts(`contact_name`, `contact_number`, `contact_type`, `contact_category`, `user_id`, `created_at`) 
        VALUES ('$contact_name', '$contact_number', '$contact_type', '$contact_category', '$email', '$timeZone')");
        toastr_set("success", "Success: Create data!");
        redirect('contact.php');
        exit;
    } else {
        toastr_set("error", "Aborted: Failed create data (name duplicated)!");
        redirect('contact.php');
        exit;
    }
}

if (post('get_contacts') === "get_contacts") {
    $contact_category = post('contact_category');
    $api_key = api_key();
    $res = getContacts($api_key);
    $status = $res['status'];
    $message = $res['message'];
    $data = $res['data'];
    $count = 0;
    if ($res['status'] === 200) {
        foreach ($data as $item) {
            $id = $item['id_group'];
            $subject = $item['name'];
            $duplicate = countContactDB("contacts", "contact_number", $id);
            if ($duplicate == 0) {
                $query = mysqli_query($conn, "INSERT INTO contacts(`contact_name`, `contact_number`, `contact_type`, `contact_category`, `user_id`, `created_at`) 
                VALUES ('$subject', '$id', 'GROUP', '$contact_category', '$email', '$timeZone')");
                toastr_set("success", "Success: Create " . $count++ . " Data");
            } else {
                toastr_set("error", $count++ . " Data Duplicated");
            }
        }
    } else {
        toastr_set("error", "You are not login whatsapp session");
    }
    redirect('contact.php');
    exit;
}

if (post('get_groups') === "get_groups") {
    $contact_category = post('contact_category');
    $api_key = api_key();
    $res = getGroups($api_key);
    $status = $res['status'];
    $message = $res['message'];
    $data = $res['data'];
    $count = 0;
    if ($res['status'] === 200) {
        foreach ($data as $item) {
            $id = $item['id_group'];
            $subject = $item['name'];
            $duplicate = countContactDB("contacts", "contact_number", $id);
            if ($duplicate == 0) {
                $query = mysqli_query($conn, "INSERT INTO contacts(`contact_name`, `contact_number`, `contact_type`, `contact_category`, `user_id`, `created_at`) 
                VALUES ('$subject', '$id', 'GROUP', '$contact_category', '$email', '$timeZone')");
                toastr_set("success", "Success: Create " . $count++ . " Data");
            } else {
                toastr_set("error", $count++ . " Data Duplicated");
            }
        }
    } else {
        toastr_set("error", "You are not login whatsapp session");
    }
    redirect('contact.php');
    exit;
}

if (get("act") == "delete") {
    $id = encryptor('decrypt', get("id"));

    $query = mysqli_query($conn, "DELETE FROM contacts WHERE id='$id' AND user_id='$email'");
    toastr_set("mixin-success", "Success: Delete data!");
    redirect("contact.php");
}

if (get("act") == "delete_all") {
    if ($_SESSION['level'] === "admin") {
        $query = mysqli_query($conn, "DELETE FROM contacts");
        toastr_set("mixin-success", "Success: Delete all data!");
    } else {
        $query = mysqli_query($conn, "DELETE FROM contacts WHERE user_id='$email'");
        toastr_set("mixin-success", "Success: Delete all data!");
    }
    redirect("contact.php");
}

require_once('../templates/header.php');
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-phone"></i><b> Master Contact</b></h1>
    </div>

    <!-- Content Row -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <a class="btn btn-primary" data-toggle="modal" data-target="#addModal" title="Create Data Contacts"><b><i class="fas fa-plus"></i> Create</b></a>
            <button class="btn btn-secondary dropdown-toggle" type="button" id="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <b><i class="fas fa-tty"></i> Pull Contacts</b>
            </button>
            <div class="dropdown-menu animated--fade-in" aria-labelledby="#dropDownPullContacts">
                <!-- <a class="dropdown-item" data-toggle="modal" data-target="#getContactsModal"><b>Pull Contacts</b></a> -->
                <a class="dropdown-item" data-toggle="modal" data-target="#getGroupsModal"><b>Pull Groups</b></a>
            </div>
            <a class="btn btn-success" data-toggle="modal" data-target="#importContact" title="Import Contacts"><b><i class="fas fa-file-excel"></i> Import Excel</b></a>
            <a class="btn btn-danger delete_confirm" href="contact.php?act=delete_all" title="Delete All"><b><i class="fas fa-dumpster fa-sm text-white-50"></i> Delete All</b></a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Full Name</th>
                            <th>Number</th>
                            <th>Category</th>
                            <th>Type Contact</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No</th>
                            <th>Full Name</th>
                            <th>Number</th>
                            <th>Category</th>
                            <th>Type Contact</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        $no = 1;
                        $email = $_SESSION['email'];
                        if ($_SESSION['level'] === "admin") {
                            $query = mysqli_query($conn, "SELECT * FROM contacts");
                        } elseif ($_SESSION['level'] === "user") {
                            $query = mysqli_query($conn, "SELECT * FROM contacts WHERE user_id='$email'");
                        }
                        while ($row = mysqli_fetch_assoc($query)) {
                            $id = encryptor('encrypt', $row['id']);
                            echo '<tr>';
                            echo '<td class="text-center">' . $no++ . '</td>';
                            echo '<td>' . $row['contact_name'] . '</td>';
                            echo '<td class="text-center">' . $row['contact_number'] . '</td>';
                            echo '<td class="text-center">' . $row['contact_category'] . '</td>';
                            echo '<td class="text-center">' . $row['contact_type'] . '</td>';
                            echo '<td class="text-center">
                                <a class="btn-circle btn-danger btn-sm delete_confirm" href="contact.php?act=delete&id=' . $id . '" title="Delete"><i class="fas fa-trash"></i></a>
                            </td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

<!-- modal start -->
<!-- Add Modal-->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><b>Create Data</b></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form class="form" action="" method="post">
                <div class="modal-body">
                    <?= csrf_input(); ?>
                    <input type="hidden" name="contact" id="contact" value="contact">
                    <div class="form-group">
                        <label for="contact_name">Name <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="contact_name" name="contact_name" placeholder="Full name.." required />
                    </div>
                    <div class="form-group">
                        <label for="contact_number" id="label_contact">Number <span style="color: red;">*</span></label>
                        <input type="number" class="form-control" id="contact_number" name="contact_number" placeholder="628**** OR 08*****" required />
                    </div>
                    <div class="form-group">
                        <label for="contact_category">Category <span style="color: red;">*</span></label>
                        <select class="form-control" name="contact_category" id="contact_category" required>
                            <option value="uncategorized">Uncategorized</option>
                            <?php
                            $email = $_SESSION['email'];
                            $level = $_SESSION['level'];
                            if ($level === 'admin') {
                                $query = mysqli_query($conn, "SELECT * FROM categories WHERE sort_category='PERSONAL'");
                            } else {
                                $query = mysqli_query($conn, "SELECT * FROM categories WHERE user_id='$email' AND sort_category='PERSONAL'");
                            }
                            while ($row = mysqli_fetch_assoc($query)) {
                                echo '<option value="' . $row['category'] . '">' . $row['category'] . ' </option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" id="submit" onclick="loadingButton()" type="submit">
                        <i class="fas fa-spinner fa-spin" style="display:none;"></i>
                        <span class="btn-text">Submit</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- end add modal -->

<!-- End Get Contacts-->
<!-- <div class="modal fade" id="getContactsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><b>Get My Contacts</b></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form class="form" action="" method="post">
                <div class="modal-body">
                    <?= csrf_input(); ?>
                    <div class="form-group">
                        <input type="hidden" class="form-control" id="get_contacts" name="get_contacts" value="get_contacts" required />
                        <label for="category">Category <span style="color: red;">*</span></label>
                        <select class="form-control" name="contact_category" id="contact_category" required>
                            <option value="uncategorized" selected>Uncategorized</option>
                            <?php
                            $email = $_SESSION['email'];
                            $level = $_SESSION['level'];
                            if ($level === 'admin') {
                                $query = mysqli_query($conn, "SELECT * FROM categories WHERE sort_category='PERSONAL'");
                            } else {
                                $query = mysqli_query($conn, "SELECT * FROM categories WHERE user_id='$email' AND sort_category='PERSONAL'");
                            }
                            while ($row = mysqli_fetch_assoc($query)) {
                                echo '<option value="' . $row['category'] . '">' . $row['category'] . ' </option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" id="submit" onclick="loadingButton()" type="submit">
                        <i class="fas fa-spinner fa-spin" style="display:none;"></i>
                        <span class="btn-text">Submit</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> -->
<!-- End Get Contacts -->

<!-- End Get Groups-->
<div class="modal fade" id="getGroupsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><b>Get My Groups</b></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form class="form" action="" method="post">
                <div class="modal-body">
                    <?= csrf_input(); ?>
                    <div class="form-group">
                        <input type="hidden" class="form-control" id="get_groups" name="get_groups" value="get_groups" required />
                        <label for="category">Category <span style="color: red;">*</span></label>
                        <select class="form-control" name="contact_category" id="contact_category" required>
                            <option value="uncategorized" selected>Uncategorized</option>
                            <?php
                            $email = $_SESSION['email'];
                            $level = $_SESSION['level'];
                            if ($level === 'admin') {
                                $query = mysqli_query($conn, "SELECT * FROM categories WHERE sort_category='GROUP'");
                            } else {
                                $query = mysqli_query($conn, "SELECT * FROM categories WHERE user_id='$email' AND sort_category='GROUP'");
                            }
                            while ($row = mysqli_fetch_assoc($query)) {
                                echo '<option value="' . $row['category'] . '">' . $row['category'] . ' </option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" id="submit" onclick="loadingButton()" type="submit">
                        <i class="fas fa-spinner fa-spin" style="display:none;"></i>
                        <span class="btn-text">Submit</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End Get Groups -->

<!-- Import Personal-->
<div class="modal fade" id="importContact" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><b>Import Personal</b></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form class="form" action="../../lib/import-excel.php" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <?= csrf_input(); ?>
                    <input type="hidden" name="personal" id="personal" value="personal">
                    <div class="form-group">
                        <label for="id_data">Import <span style="color: red;">*</span></label>
                        <input type="file" class="form-control" id="file" name="file" required />
                    </div>
                    <div class="form-group">
                        <label for="contact_category">Category <span style="color: red;">*</span></label>
                        <select class="form-control" name="contact_category" id="contact_category" required>
                            <option value="uncategorized" selected>Uncategorized</option>
                            <?php
                            $email = $_SESSION['email'];
                            $level = $_SESSION['level'];
                            if ($level === 'admin') {
                                $query = mysqli_query($conn, "SELECT * FROM categories WHERE sort_category='PERSONAL'");
                            } else {
                                $query = mysqli_query($conn, "SELECT * FROM categories WHERE user_id='$email' AND sort_category='PERSONAL'");
                            }
                            while ($row = mysqli_fetch_assoc($query)) {
                                echo '<option value="' . $row['category'] . '">' . $row['category'] . ' </option>';
                            }
                            ?>
                        </select>
                    </div>
                    <p>Download Template <a href="<?php echo $base_url . 'public/files/Template Import Personal.xlsx' ?>" target="_blank">link</a></p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" id="submit" onclick="loadingButton()" type="submit">
                        <i class="fas fa-spinner fa-spin" style="display:none;"></i>
                        <span class="btn-text">Submit</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- modal end -->

</div>
<!-- End of Main Content -->
<?php require_once('../templates/footer.php'); ?>
</body>

</html>