<?php
include_once("../../helpers/connection.php");
include_once("../../helpers/function.php");
$login = cekSession();
if ($login == 0) {
    toastr_set("mixin-error", "Forbiden access!!");
    redirect("../../login.php");
}
$sender = $_SESSION['email'];
$schedule = date("Y-m-d H:i:s");

if (post("send_message")) {
    csrf_validate();
    $number = post("number");
    $message = utf8_encode($_POST['message']);

    if ($_FILES["media"]) {
        $categories = "send-message";
        $target_directory = "../../public/uploads/";
        $target_send_media = "public/uploads/" . basename($_FILES["media"]["name"]);
        $target_file = $target_directory . basename($_FILES["media"]["name"]);
        $uploadOK = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // check if image file is a actual image or fake image
        $check = getimagesize($_FILES["media"]["tmp_name"]);
        if ($check !== false) {
            $uploadOK = 1;
        } else {
            $msg = "File is not an image";
            $uploadOK = 0;
        }

        // allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $msg = "Aborted: Sorry, only extension JPG, JPEG, PNG, PDF and GIF files allowed";
            $uploadOK = 0;
        }

        if ($uploadOK == 0) {
            toastr_set("error", $msg);
        } else {
            if (move_uploaded_file($_FILES['media']['tmp_name'], $target_file)) {
                $media = $base_url . $target_send_media;
                $res = sendMSG($number, $message, $media);
                if ($res['status'] == 200) {
                    $status = "success";
                    $query = mysqli_query($conn, "INSERT INTO messages(`message`, `message_categories`, `message_media`, `message_status`, `send_to`, `message_schedule`, `user_id`, `created_at`) 
                    VALUES ('$message', '$categories', '$media', '$status', '$number', '$schedule', '$sender', '$timeZone')");
                    $insertMedia = mysqli_query($conn, "INSERT INTO media(`media`, `user_id`, `created_at`) VALUES('$media', '$sender', '$timeZone')");
                    toastr_set("success", "Success: Message sent successfully");
                    redirect("send-message.php");
                } else {
                    $status = "failed";
                    $query = mysqli_query($conn, "INSERT INTO messages(`message`, `message_categories`, `message_media`, `message_status`, `send_to`, `message_schedule`, `user_id`, `created_at`) 
                    VALUES ('$message', '$categories', '$media', '$status', '$number', '$schedule', '$sender', '$timeZone')");
                    $insertMedia = mysqli_query($conn, "INSERT INTO media(`media`, `user_id`, `created_at`) VALUES('$media', '$sender', '$timeZone')");
                    toastr_set("error", "Aborted: Sending message error1!");
                    redirect("send-message.php");
                }
            } else {
                toastr_set("error", $msg);
                redirect("send-message.php");
            }
        }
    } elseif (post('media')) {
        $categories = "send-message";
        $media = post('media');
        $res = sendMSG($number, $message, $media);
        if ($res['status'] == 200) {
            $status = "success";
            $query = mysqli_query($conn, "INSERT INTO messages(`message`, `message_categories`, `message_media`, `message_status`, `send_to`, `message_schedule`, `user_id`, `created_at`) 
            VALUES ('$message', '$categories', '$media', '$status', '$number', '$schedule', '$sender', '$timeZone')");
            $insertMedia = mysqli_query($conn, "INSERT INTO media(`media`, `user_id`, `created_at`) VALUES('$media', '$sender', '$timeZone')");
            toastr_set("success", "Success: Message sent successfully");
            redirect("send-message.php");
        } else {
            $status = "failed";
            $query = mysqli_query($conn, "INSERT INTO messages(`message`, `message_categories`, `message_media`, `message_status`, `send_to`, `message_schedule`, `user_id`, `created_at`) 
            VALUES ('$message', '$categories', '$media', '$status', '$number', '$schedule', '$sender', '$timeZone')");
            $insertMedia = mysqli_query($conn, "INSERT INTO media(`media`, `user_id`, `created_at`) VALUES('$media', '$sender', '$timeZone')");
            toastr_set("error", "Aborted: Sending message error2!");
            redirect("send-message.php");
        }
    } else {
        $categories = "send-message";
        $res = sendMSG($number, $message, $media);
        if ($res['status'] == 200) {
            $status = "success";
            $query = mysqli_query($conn, "INSERT INTO messages(`message`, `message_categories`, `message_media`, `message_status`, `send_to`, `message_schedule`, `user_id`, `created_at`) 
            VALUES ('$message', '$categories', NULL, '$status', '$number', '$schedule', '$sender', '$timeZone')");
            toastr_set("success", "Success: Message sent successfully");
            redirect("send-message.php");
        } else {
            $status = "failed";
            $query = mysqli_query($conn, "INSERT INTO messages(`message`, `message_categories`, `message_media`, `message_status`, `send_to`, `message_schedule`, `user_id`, `created_at`) 
            VALUES ('$message', '$categories', '$media', '$status', '$number', '$schedule', '$sender', '$timeZone')");
            toastr_set("error", "Aborted: Sending message error3!");
            redirect("send-message.php");
        }
    }
}

if (post("send_group")) {
    csrf_validate();
    $id_group = post("id_group");
    $message = utf8_encode($_POST['message']);

    if ($_FILES["media"]) {
        $categories = "send-group";
        $target_directory = "../../public/uploads/";
        $target_send_media = "public/uploads/" . basename($_FILES["media"]["name"]);
        $target_file = $target_directory . basename($_FILES["media"]["name"]);
        $uploadOK = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // check if image file is a actual image or fake image
        $check = getimagesize($_FILES["media"]["tmp_name"]);
        if ($check !== false) {
            $uploadOK = 1;
        } else {
            $msg = "File is not an image";
            $uploadOK = 0;
        }

        // allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $msg = "Aborted: Sorry, only extension JPG, JPEG, PNG, PDF and GIF files allowed";
            $uploadOK = 0;
        }

        if ($uploadOK == 0) {
            toastr_set("error", $msg);
        } else {
            if (move_uploaded_file($_FILES['media']['tmp_name'], $target_file)) {
                $media = $base_url . $target_send_media;
                $res = sendGroup($id_group, $message, $media);
                if ($res['status'] == 200) {
                    $status = "success";
                    $query = mysqli_query($conn, "INSERT INTO messages(`message`, `message_categories`, `message_media`, `message_status`, `send_to`, `message_schedule`, `user_id`, `created_at`) 
                    VALUES ('$message', '$categories', '$media', '$status', '$number', '$schedule', '$sender', '$timeZone')");
                    $insertMedia = mysqli_query($conn, "INSERT INTO media(`media`, `user_id`, `created_at`) VALUES('$media', '$sender', '$timeZone')");
                    toastr_set("success", "Success: Message sent successfully");
                    redirect("send-message.php");
                } else {
                    $status = "failed";
                    $query = mysqli_query($conn, "INSERT INTO messages(`message`, `message_categories`, `message_media`, `message_status`, `send_to`, `message_schedule`, `user_id`, `created_at`) 
                    VALUES ('$message', '$categories', '$media', '$status', '$number', '$schedule', '$sender', '$timeZone')");
                    $insertMedia = mysqli_query($conn, "INSERT INTO media(`media`, `user_id`, `created_at`) VALUES('$media', '$sender', '$timeZone')");
                    toastr_set("error", "Aborted: Sending message error!");
                    redirect("send-message.php");
                }
            } else {
                toastr_set("error", $msg);
                redirect("send-message.php");
            }
        }
    } elseif (post('media')) {
        $categories = "send-group";
        $media = post('media');
        $res = sendGroup($id_group, $message, $media);
        if ($res['status'] == 200) {
            $status = "success";
            $query = mysqli_query($conn, "INSERT INTO messages(`message`, `message_categories`, `message_media`, `message_status`, `send_to`, `message_schedule`, `user_id`, `created_at`) 
            VALUES ('$message', '$categories', '$media', '$status', '$number', '$schedule', '$sender', '$timeZone')");
            $insertMedia = mysqli_query($conn, "INSERT INTO media(`media`, `user_id`, `created_at`) VALUES('$media', '$sender', '$timeZone')");
            toastr_set("success", "Success: Message sent successfully");
            redirect("send-message.php");
        } else {
            $status = "failed";
            $query = mysqli_query($conn, "INSERT INTO messages(`message`, `message_categories`, `message_media`, `message_status`, `send_to`, `message_schedule`, `user_id`, `created_at`) 
            VALUES ('$message', '$categories', '$media', '$status', '$number', '$schedule', '$sender', '$timeZone')");
            $insertMedia = mysqli_query($conn, "INSERT INTO media(`media`, `user_id`, `created_at`) VALUES('$media', '$sender', '$timeZone')");
            toastr_set("error", "Aborted: Sending message error!");
            redirect("send-message.php");
        }
    } else {
        $categories = "send-group";
        $res = sendGroup($id_group, $message, $media);
        if ($res['status'] == 200) {
            $status = "success";
            $query = mysqli_query($conn, "INSERT INTO messages(`message`, `message_categories`, `message_media`, `message_status`, `send_to`, `message_schedule`, `user_id`, `created_at`) 
            VALUES ('$message', '$categories', '$media', '$status', '$number', '$schedule', '$sender', '$timeZone')");
            toastr_set("success", "Success: Message sent successfully");
            redirect("send-message.php");
        } else {
            $status = "failed";
            $query = mysqli_query($conn, "INSERT INTO messages(`message`, `message_categories`, `message_media`, `message_status`, `send_to`, `message_schedule`, `user_id`, `created_at`) 
            VALUES ('$message', '$categories', '$media', '$status', '$number', '$schedule', '$sender', '$timeZone')");
            toastr_set("error", "Aborted: Sending message error!");
            redirect("send-message.php");
        }
    }
}

require_once('../templates/header.php');
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-paper-plane"></i> <b>Send Message</b></h1>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-lg-6 col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h5 class="modal-title"><i class="fas fa-paper-plane"></i> <b>Send Message</b></h5>
                </div>
                <form class="form" action="" method="post" enctype="multipart/form-data">
                    <div class="card-body">
                        <input type="hidden" class="form-control" id="sendMessage" name="send_message" value="send_message" />
                        <?= csrf_input(); ?>
                        <div class="form-group">
                            <label for="number">Send to number <span style="color: red;">*</span></label>
                            <input type="number" class="form-control" id="number" name="number" placeholder="628***** / 08*****." required />
                        </div>
                        <div class="form-group">
                            <label for="message">Messages <span style="color: red;">*</span></label>
                            <textarea class="form-control" id="message" name="message" placeholder="Your message.." required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="message-media">Media <span class="text-primary"> <small>Ext: jpg, jpeg, png, gif <a data-toggle="modal" data-target="#info-modal" title="send broadcast message"><i class="fas fa-info-circle"></i></a></small></span></label>
                            <input type="url" class="form-control" id="message-media" name="media" placeholder="Example: https://source.unsplash.com/360x200?games" />
                            <div class="form-check">
                                <input type="checkbox" name="check-message-media" class="form-check-input" id="check-message-media" checked>
                                <label class="form-check-label" for="check-message-media" id="label-message-media">Type url image</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" id="submit" onclick="loadingButton()" type="submit">
                            <i class="fas fa-spinner fa-spin" style="display:none;"></i>
                            <span class="btn-text">Submit</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-6 col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h5 class="modal-title"><i class="fas fa-paper-plane"></i> <b>Send To Group</b></h5>
                </div>
                <form class="form" action="" method="post" enctype="multipart/form-data">
                    <div class="card-body">
                        <input type="hidden" class="form-control" id="sendGroup" name="send_group" value="send_group" />
                        <?= csrf_input(); ?>
                        <div class="form-group">
                            <label for="id_group">ID Group <span style="color: red;">*</span></label>
                            <select id="id_group" class="custom-select select2" name="id_group" style="width: 100%" required>
                                <option value="">-- Choose Group ID --</option>
                                <?php
                                $email = $_SESSION['email'];
                                if ($_SESSION['level'] == 'admin') {
                                    $query = mysqli_query($conn, "SELECT * FROM contacts WHERE contact_type='GROUP'");
                                } else {
                                    $query = mysqli_query($conn, "SELECT * FROM contacts WHERE contact_type='GROUP' AND user_id='$email'");
                                }
                                while ($row = mysqli_fetch_assoc($query)) {
                                    echo '<option value="' . $row['contact_number'] . '">' . $row['contact_name'] . ' - ' . $row['contact_number'] . ' </option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="message">Messages <span style="color: red;">*</span></label>
                            <textarea class="form-control" id="message" name="message" placeholder="Your message.." required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="group-media">Media <span class="text-primary"> <small>Ext: jpg, jpeg, png, gif <a data-toggle="modal" data-target="#info-modal" title="send broadcast message"><i class="fas fa-info-circle"></i></a></small></span></label>
                            <input type="url" class="form-control" id="group-media" name="media" placeholder="Example: https://source.unsplash.com/360x200?games" />
                            <div class="form-check">
                                <input type="checkbox" name="check-group-media" class="form-check-input" id="check-group-media" checked>
                                <label class="form-check-label" for="check-group-media" id="label-group-media">Type url image</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
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
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->
<?php require_once('../templates/footer.php'); ?>
</body>

<script>
    $(document).ready(function() {
        $('#check-message-media').click(function() {
            if ($(this).is(':checked')) {
                $('#message-media').attr('type', 'url');
                $('#label-message-media').text("Type url image");
            } else {
                $('#message-media').attr('type', 'file');
                $('#label-message-media').text("Browse file max: 2MB.");
            }
        })
    })
    $(document).ready(function() {
        $('#check-group-media').click(function() {
            if ($(this).is(':checked')) {
                $('#group-media').attr('type', 'url');
                $('#label-group-media').text("Type url image");
            } else {
                $('#group-media').attr('type', 'file');
                $('#label-group-media').text("Browse file max: 2MB.");
            }
        })
    })
</script>

</html>