<?php
include_once("../../helpers/connection.php");
include_once("../../helpers/function.php");
$login = cekSession();
if ($login == 0) {
    toastr_set("mixin-error", "Forbiden access!!");
    redirect("../../login.php");
}

$email = $_SESSION['email'];

if (post("_token")) {
    csrf_validate();
    $category = post("category");
    $sort_category = post("sort_category");
    $count = countDB("categories", "category", $category);

    if ($category) {
        $query = mysqli_query($conn, "INSERT INTO categories(`category`, `sort_category`, `user_id`, `created_at`) VALUES ('$category', '$sort_category', '$email', '$timeZone')");
        toastr_set("success", "Success: Create data!");
    } else {
        toastr_set("error", "Aborted: Failed create data!");
    }
}

if (get("act") == "delete") {
    $id = encryptor('decrypt', get("id"));
    $query = mysqli_query($conn, "DELETE FROM categories WHERE id='$id' AND user_id='$email'");
    toastr_set("mixin-success", "Success: Delete data!");
    redirect("category.php");
}

require_once('../templates/header.php');
?>
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-sitemap"></i> <b>Master Category</b></h1>
    </div>

    <!-- Content Row -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <a class="btn btn-primary" data-toggle="modal" data-target="#addModal" title="Add Data"><b><i class="fas fa-plus"></i> Create</b></a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Category</th>
                            <th>Sort Category</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No</th>
                            <th>Category</th>
                            <th>Sort Category</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        $no = 1;
                        $email = $_SESSION['email'];
                        if ($_SESSION['level'] === "admin") {
                            $query = mysqli_query($conn, "SELECT * FROM categories");
                        } elseif ($_SESSION['level'] === "user") {
                            $query = mysqli_query($conn, "SELECT * FROM categories WHERE user_id='$email'");
                        }
                        while ($row = mysqli_fetch_assoc($query)) {
                            $id = encryptor('encrypt', $row['id']);
                            echo '<tr>';
                            echo '<td class="text-center">' . $no++ . '</td>';
                            echo '<td>' . $row['category'] . '</td>';
                            echo '<td>' . $row['sort_category'] . '</td>';
                            echo '<td class="text-center">
                                <a onclick="confirmation(event)" class="btn-circle btn-danger btn-sm delete_confirm" href="category.php?act=delete&id=' . $id . '" title="Delete"><i class="fas fa-trash"></i></a>
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
                    <div class="form-group">
                        <label for="category">Category <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="category" name="category" placeholder="Category.." required />
                    </div>
                    <div class="form-group">
                        <label for="sort_category">Choose Category</label>
                        <select class="form-control" name="sort_category" id="sort_category" required>
                            <option value="PERSONAL">Personal</option>
                            <option value="GROUP">Group</option>
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

</div>
<!-- End of Main Content -->
<?php require_once('../templates/footer.php'); ?>
</body>

</html>