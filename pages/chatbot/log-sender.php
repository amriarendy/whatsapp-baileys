<?php
include_once("../../helpers/connection.php");
include_once("../../helpers/function.php");
$login = cekSession();
if ($login == 0) {
    toastr_set("mixin-error", "Forbiden access!!");
    redirect("../../login.php");
}
$level = $_SESSION['level'];
$email = $_SESSION['email'];
if (get("act") == "delete") {
    $id = encryptor('decrypt', get("id"));
    if ($level === 'admin') {
        $where = mysqli_query($conn, "SELECT * FROM messages WHERE id='$id'");
    } else {
        $where = mysqli_query($conn, "SELECT * FROM messages WHERE id='$id' AND user_id='$email'");
    }
    $row = mysqli_fetch_assoc($where);
    $media = $row['message_media'];
    $url = parse_url($row['message_media']);
    $fileOnly = str_replace("/whatsapp-baileys/public/uploads/", '', $url['path']);
    $dir = "../../public/uploads/" . $fileOnly;
    unlink($dir);
    if ($level === 'admin') {
        mysqli_query($conn, "DELETE FROM media WHERE media='$media'");
        mysqli_query($conn, "DELETE FROM messages WHERE id='$id'");
    } else {
        mysqli_query($conn, "DELETE FROM media WHERE media='$media' AND user_id='$email'");
        mysqli_query($conn, "DELETE FROM messages WHERE id='$id' AND user_id='$email'");
    }
    toastr_set("mixin-success", "Success: Delete data!");
    redirect("log-sender.php");
}

if (get("act") == "delete_all") {
    if ($_SESSION['level'] === 'admin') {
        $result = mysqli_query($conn, "SELECT * FROM messages");
        toastr_set("mixin-success", "Success: Delete all data!");
    } else {
        $result = mysqli_query($conn, "SELECT * FROM messages WHERE user_id='$email'");
        toastr_set("mixin-success", "Success: Delete data!");
    }

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Extract the file path from the database record
            $media = $row['message_media'];
            if ($row['message_media'] !== null || !empty($row['message_media'])) {
                // Check if the file exists before attempting to delete it
                $url = parse_url($row['message_media']);
                $fileOnly = str_replace("/whatsapp-baileys/public/uploads/", '', $url['path']);
                $dir = "../../public/uploads/" . $fileOnly;
                // Check if the file exists before attempting to delete it
                if (file_exists($dir)) {
                    // Attempt to delete the file
                    if (unlink($dir)) {
                        // File was deleted successfully

                        // Delete the corresponding record from the database
                        if ($_SESSION['level'] === 'admin') {
                            mysqli_query($conn, "DELETE FROM media WHERE media='$media'");
                            mysqli_query($conn, "DELETE FROM messages");
                            toastr_set("mixin-success", "Success: Delete all file and data!");
                        } else {
                            mysqli_query($conn, "DELETE FROM media WHERE media='$media' AND user_id='$email'");
                            mysqli_query($conn, "DELETE FROM messages WHERE user_id='$email'");
                            toastr_set("mixin-success", "Success: Delete all file and data!");
                        }
                    } else {
                        // Unable to delete the file
                        toastr_set("mixin-success", "Failed to delete file: $dir\n");
                    }
                } else {
                    if ($_SESSION['level'] === 'admin') {
                        mysqli_query($conn, "DELETE FROM media WHERE media='$media'");
                        mysqli_query($conn, "DELETE FROM messages");
                        toastr_set("mixin-success", "Success: Delete all data!");
                    } else {
                        mysqli_query($conn, "DELETE FROM media WHERE media='$media' AND user_id='$email'");
                        mysqli_query($conn, "DELETE FROM messages WHERE user_id='$email'");
                        toastr_set("mixin-success", "Success: Delete all data!");
                    }
                }
            } else {
                if ($_SESSION['level'] === 'admin') {
                    mysqli_query($conn, "DELETE FROM media WHERE media='$media'");
                    mysqli_query($conn, "DELETE FROM messages");
                    toastr_set("mixin-success", "Success: Delete all data!");
                } else {
                    mysqli_query($conn, "DELETE FROM media WHERE media='$media' AND user_id='$email'");
                    mysqli_query($conn, "DELETE FROM messages WHERE user_id='$email'");
                    toastr_set("mixin-success", "Success: Delete all data!");
                }
            }
        }

        // Optionally, you can add a success message or perform other actions here
    } else {
        // Query to retrieve records failed
        toastr_set("error", "Failed to retrieve records from the database.\n");
    }
}
require_once('../templates/header.php');
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-history text-secondary"></i> <b>Log Sender</b></h1>
        <?php if ($_SESSION['level'] == 'admin') { ?>
            <a href="../../helpers/cronjob.php" id="linkButton" class="d-none d-sm-inline-block btn btn-sm btn-danger shadow-sm" title="Push Cronjob"><b><i class="fas fa-clock"></i> Push Cronjob</b></a>
        <?php } ?>
    </div>

    <!-- Content Row -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <?php if ($_SESSION['level'] == 'admin') { ?>
                <a class="btn btn-danger delete_confirm" href="log-sender.php?act=delete_all" onclick="return confirm('Are you sure to delete all data?')" title="Delete All"><b><i class="fas fa-dumpster fa-sm text-white-50"></i> Delete All</b></a>
            <?php } ?>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Log</th>
                            <th>Send to</th>
                            <th>Sender</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No</th>
                            <th>Log</th>
                            <th>Send to</th>
                            <th>Sender</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        $no = 1;
                        $email = $_SESSION['email'];
                        if ($_SESSION['level'] === "admin") {
                            $query = mysqli_query($conn, "SELECT * FROM messages");
                        } elseif ($_SESSION['level'] === "user") {
                            $query = mysqli_query($conn, "SELECT * FROM messages WHERE user_id='$email'");
                        }
                        while ($row = mysqli_fetch_assoc($query)) {
                            $id = encryptor('encrypt', $row['id']);
                            echo '<tr>';
                            echo '<td class="text-center">' . $no++ . '</td>';
                            echo '<td>' . $row['message_categories'] . '</td>';
                            echo '<td class="text-center">' . $row['send_to'] . '</td>';
                            echo '<td class="text-center">' . $row['user_id'] . '</td>';
                            if ($row['message_status'] == "failed") {
                                echo '<td class="text-center"><span class="btn btn-danger">Failed</span></td>';
                            } else if ($row['message_status'] == "waiting") {
                                echo '<td class="text-center"><span class="btn btn-warning">Waiting</span></td>';
                            } else {
                                echo '<td class="text-center"><span class="btn btn-success">Success</span></td>';
                            }
                            echo '<td class="text-center">
                                <a id="details" class="btn-circle btn-info btn-sm" data-toggle="modal" data-target="#detailModal" title="Info"
                                    data-send_to="' . $row['send_to'] . '" 
                                    data-message="' . $row['message'] . '" 
                                    data-message_categories="' . $row['message_categories'] . '" 
                                    data-message_media="' . $row['message_media'] . '" 
                                    data-message_status="' . $row['message_status'] . '" 
                                    data-message_schedule="' . $row['message_schedule'] . '" 
                                    data-users_id="' . $row['user_id'] . '">
                                    <i class="fas fa-info"></i>
                                </a>
                                <a class="btn-circle btn-danger btn-sm delete_confirm" href="log-sender.php?act=delete&id=' . $id . '" title="Delete"><i class="fas fa-trash"></i></a>
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

<!-- Detail Modal-->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><b>Detail Message</b></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 500px; overflow-y: auto;">
                <h5 style="text-align: center; "><b>Send To :&nbsp;</b></h5>
                <p id="sendTo" class="card-text text-center">.</p>
                <hr>
                <h5 style="text-align: center; "><b>Message :&nbsp;</b></h5>
                <p id="messages" class="card-text">.</p>
                <hr>
                <h5 style="text-align: center; "><b>Date and Time :&nbsp;</b></h5>
                <p id="messageSchedule" class="card-text text-center"></p>
                <hr>
                <h5 style="text-align: center; "><b>Status :&nbsp;</b></h5>
                <p id="messageStatus" class="card-text text-center"></p>
                <hr>
                <h5 style="text-align: center; "><b>Category :&nbsp;</b></h5>
                <p id="messageCategories" class="card-text text-center">.</p>
                <hr>
                <h5 style="text-align: center; "><b>Media :&nbsp;</b></h5>
                <div class="text-center">
                    <img id="messageMediaAttr" class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 50%;" src="" alt="images" title="images"><br>
                </div>
                </p>
                <hr>
                <h5 style="text-align: center; "><b>Owner :&nbsp;</b></h5>
                <p id="user_id" class="card-text text-center"></p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<!-- End of Main Content -->

</div>
<!-- End of Main Content -->
<?php require_once('../templates/footer.php'); ?>
<script>
    $(document).ready(function() {
        $(document).on('click', '#details', function() {
            var sendTo = $(this).data('send_to');
            var messages = $(this).data('message');
            var messageCategories = $(this).data('message_categories');
            var messageMedia = $(this).data('message_media');
            var messageStatus = $(this).data('message_status');
            var messageSchedule = $(this).data('message_schedule');
            var user_id = $(this).data('users_id');
            $('#sendTo').text(sendTo);
            $('#messages').text(messages);
            $('#messageCategories').text(messageCategories);
            $('#messageMedia').text(messageMedia);
            $('#messageMediaAttr').attr('src', messageMedia);
            $('#messageStatus').text(messageStatus);
            $('#messageSchedule').text(messageSchedule);
            $('#user_id').text(user_id);
            $("#detailModal").modal('show');
        });
    });
</script>
</body>

</html>