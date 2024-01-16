<?php
include_once("../../helpers/connection.php");
include_once("../../helpers/function.php");
$login = cekSession();
if ($login == 0) {
    toastr_set("mixin-error", "Forbiden access!!");
    redirect("../../login.php");
}

if (post("send_group")) {
    csrf_validate();
    $email = $_SESSION['email'];
    $message = post("message");
    $messageCategories = "send-group-schedule";
    $messageStatus = "waiting";
    $post_media = post("media");

    $schedule = date("Y-m-d H:i:s", strtotime(post("date") . " " . post("time")));
    if ($post_media) {
        $media = $post_media;
    } else if (!empty($_FILES['media']) && $_FILES['media']['error'] == UPLOAD_ERR_OK) {
        // Be sure we're dealing with an upload
        if (is_uploaded_file($_FILES['media']['tmp_name']) === false) {
            throw new \Exception('Error on upload: Invalid file definition');
        }

        // Rename the uploaded file
        $uploadName = $_FILES['media']['name'];
        $ext = strtolower(substr($uploadName, strripos($uploadName, '.') + 1));
        $size = $_FILES['media']['size'];
        if ($size > 4000000) {
            toastr_set("error", "Maximal 1 mb");
            redirect("send-group.php");
            exit;
        }

        $allow = ['png', 'jpeg', 'pdf', 'jpg', 'pdf', 'mp3'];
        if (in_array($ext, $allow)) {
            if ($ext == "png") {
                $filename = round(microtime(true)) . mt_rand() . '.png';
            }

            if ($ext == "pdf") {
                $filename = round(microtime(true)) . mt_rand() . '.pdf';
            }

            if ($ext == "jpg") {
                $filename = round(microtime(true)) . mt_rand() . '.jpg';
            }

            if ($ext == "pdf") {
                $filename = round(microtime(true)) . mt_rand() . '.pdf';
            }

            if ($ext == "mp3") {
                $filename = round(microtime(true)) . mt_rand() . '.mp3';
            }
        } else {
            toastr_set("error", "Format: png, jpg, mp3 only");
            redirect("send-group.php");
            exit;
        }

        move_uploaded_file($_FILES['media']['tmp_name'], '../../public/uploads/' . $filename);
        print_r(error_get_last());
        // Insert it into our tracking along with the original name
        $media = $base_url . "public/uploads/" . $filename;
    } else {
        $media = null;
    }

    if ($_POST['type_target'] == "send_to_group") {
        if (isset($_POST['target'])) {
            foreach ($_POST['target'] as $data) {
                $n = $data;
                $checkNumbers = mysqli_query($conn, "SELECT * FROM contacts WHERE contact_number = '$n'");
                $data2 = $checkNumbers->fetch_assoc();
                $messages = strtr($message, array(
                    '{contact_name}' => $data2['contact_name'],
                ));
                $messages2 = utf8_encode($messages);
                if ($media == null) {
                    $query = mysqli_query($conn, "INSERT INTO messages(`message`, `send_to`, `message_categories`, `message_schedule`, `message_status`, `user_id`, `created_at`) 
                    VALUES('$messages2', '$n', '$messageCategories', '$schedule', '$messageStatus', '$email', '$timeZone')");
                } else {
                    $query = mysqli_query($conn, "INSERT INTO messages(`message`, `send_to`, `message_categories`, `message_media`, `message_schedule`, `message_status`, `user_id`, `created_at`) 
                    VALUES('$messages2', '$n', '$messageCategories', '$media', '$schedule', '$messageStatus', '$email', '$timeZone')");
                    $insert_media = mysqli_query($conn, "INSERT INTO media(`media`, `user_id`, `created_at`) VALUES('$media', '$email', '$timeZone')");
                }
            }
        } else {
            toastr_set("error", "Abort: Contact empty!");
            redirect("send-group.php");
            exit;
        }
    } else if ($_POST['type_target'] == "group_category") {
        if (empty($_POST['target_category'])) {
            toastr_set("error", "Abort: Contact category empty!");
            redirect("send-group.php");
            exit;
        } else {
            $categories = $_POST['target_category'];
            $n = getCategoryGroupID($categories);
            foreach ($n as $nn) {
                if ($media == null) {
                    $query = mysqli_query($conn, "INSERT INTO messages(`message`, `send_to`, `message_categories`, `message_schedule`, `message_status`, `user_id`, `created_at`) 
                VALUES('$message', '$nn', '$messageCategories', '$schedule', '$messageStatus', '$email', '$timeZone')");
                } else {
                    $query = mysqli_query($conn, "INSERT INTO messages(`message`, `send_to`, `message_categories`, `message_media`, `message_schedule`, `message_status`, `user_id`, `created_at`) 
                    VALUES('$message', '$nn', '$messageCategories', '$media', '$schedule', '$messageStatus', '$email', '$timeZone')");
                    $insert_media = mysqli_query($conn, "INSERT INTO media(`media`, `user_id`, `created_at`) VALUES('$media', '$email', '$timeZone')");
                }
            }
        }
    } else if ($_POST['type_target'] == "send_all") {
        $email = $_SESSION['email'];
        $checkNumbers = mysqli_query($conn, "SELECT * FROM contacts WHERE contact_type='GROUP' AND user_id='$email'");
        while ($data = $checkNumbers->fetch_assoc()) {
            $messages = strtr($message, array(
                '{contact_name}' => $data['contact_name'],
            ));
            $messages2 = utf8_encode($messages);
            $n = $data['contact_number'];
            if ($media == null) {
                $query = mysqli_query($conn, "INSERT INTO messages(`message`, `send_to`, `message_categories`, `message_schedule`, `message_status`, `user_id`, `created_at`) 
                    VALUES('$messages2', '$n', '$messageCategories', '$schedule', '$messageStatus', '$email', '$timeZone')");
            } else {
                $query = mysqli_query($conn, "INSERT INTO messages(`message`, `send_to`, `message_categories`, `message_media`, `message_schedule`, `message_status`, `user_id`, `created_at`) 
                    VALUES('$messages2', '$n', '$messageCategories', '$media', '$schedule', '$messageStatus', '$email', '$timeZone')");
                $insert_media = mysqli_query($conn, "INSERT INTO media(`media`, `user_id`, `created_at`) VALUES('$media', '$email', '$timeZone')");
            }
        }
    }

    // if (!$result) {
    //     die('Query Error : ' . mysqli_errno($conn) .
    //         ' - ' . mysqli_error($conn));
    // }
    toastr_set("success", "Success: Create schedule message");
    redirect("send-group.php");
    exit;
}

if (post("send_group_template")) {
    csrf_validate();
    $email = $_SESSION['email'];
    $message = post("template");
    $messageCategories = "send-group-template";
    $messageStatus = "waiting";

    if ($message) {
        $where = mysqli_query($conn, "SELECT * FROM templates WHERE id='$message'");
        $row = mysqli_fetch_array($where);
        $content = $row['template_content'];
        $media = $row['template_media'];
    }

    $schedule = date("Y-m-d H:i:s", strtotime(post("date") . " " . post("time")));
    if ($_POST['type_target'] == "send_template_by_group") {
        if (isset($_POST['target'])) {
            foreach ($_POST['target'] as $data) {
                $n = $data;
                $checkNumbers = mysqli_query($conn, "SELECT * FROM contacts WHERE contact_number = '$n'");
                $data2 = $checkNumbers->fetch_assoc();
                $messages = strtr($content, array(
                    '{contact_name}' => $data2['contact_name'],
                ));
                $messages2 = utf8_encode($messages);
                if ($media == null) {
                    $query = mysqli_query($conn, "INSERT INTO messages(`message`, `send_to`, `message_categories`, `message_schedule`, `message_status`, `user_id`, `created_at`) 
                    VALUES('$messages2', '$n', '$messageCategories', '$schedule', '$messageStatus', '$email', '$timeZone')");
                } else {
                    $query = mysqli_query($conn, "INSERT INTO messages(`message`, `send_to`, `message_categories`, `message_media`, `message_schedule`, `message_status`, `user_id`, `created_at`) 
                    VALUES('$messages2', '$n', '$messageCategories', '$media', '$schedule', '$messageStatus', '$email', '$timeZone')");
                }
            }
        } else {
            toastr_set("error", "Abort: Contact is empty!");
            redirect("send-group.php");
            exit;
        }
    } else if ($_POST['type_target'] == "send_template_group_by_category") {
        if (empty($_POST['target_category'])) {
            toastr_set("error", "Abort: Contact category is empty!");
            redirect("send-group.php");
            exit;
        } else {
            $categories = $_POST['target_category'];
            $n = getCategoryGroupID($categories);
            $messages = strtr($content, array(
                '{contact_name}' => $data2['contact_name'],
            ));
            $messages2 = utf8_encode($messages);
            foreach ($n as $nn) {
                if ($media == null) {
                    $query = mysqli_query($conn, "INSERT INTO messages(`message`, `send_to`, `message_categories`, `message_schedule`, `message_status`, `user_id`, `created_at`) 
                VALUES('$messages2', '$nn', '$messageCategories', '$schedule', '$messageStatus', '$email', '$timeZone')");
                } else {
                    $query = mysqli_query($conn, "INSERT INTO messages(`message`, `send_to`, `message_categories`, `message_media`, `message_schedule`, `message_status`, `user_id`, `created_at`) 
                    VALUES('$messages2', '$nn', '$messageCategories', '$media', '$schedule', '$messageStatus', '$email', '$timeZone')");
                }
            }
        }
    } else if ($_POST['type_target'] == "send_template_all") {
        $email = $_SESSION['email'];
        $checkNumbers = mysqli_query($conn, "SELECT * FROM contacts WHERE contact_type='GROUP' AND user_id='$email'");
        while ($data = $checkNumbers->fetch_assoc()) {
            $messages = strtr($content, array(
                '{contact_name}' => $data['contact_name'],
            ));
            $messages2 = utf8_encode($messages);
            $n = $data['contact_number'];
            if ($media == null) {
                $query = mysqli_query($conn, "INSERT INTO messages(`message`, `send_to`, `message_categories`, `message_schedule`, `message_status`, `user_id`, `created_at`) 
                    VALUES('$messages2', '$n', '$messageCategories', '$schedule', '$messageStatus', '$email', '$timeZone')");
            } else {
                $query = mysqli_query($conn, "INSERT INTO messages(`message`, `send_to`, `message_categories`, `message_media`, `message_schedule`, `message_status`, `user_id`, `created_at`) 
                    VALUES('$messages2', '$n', '$messageCategories', '$media', '$schedule', '$messageStatus', '$email', '$timeZone')");
            }
        }
    }

    // if (!$result) {
    //     die('Query Error : ' . mysqli_errno($conn) .
    //         ' - ' . mysqli_error($conn));
    // }
    toastr_set("success", "Success: create schedule message");
    redirect("send-group.php");
    exit;
}

require_once('../templates/header.php');
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-bullhorn text-danger"></i> <b>Send Group Broadcast</b></h1>
        <?php if ($_SESSION['level'] == 'admin') { ?>
            <a href="../../helpers/cronjob.php" id="linkButton" class="d-none d-sm-inline-block btn btn-sm btn-danger shadow-sm" title="Push Cronjob"><b><i class="fas fa-clock"></i> Push Cronjob</b></a>
        <?php } ?>
    </div>

    <!-- Content Row -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <a class="btn btn-primary" data-toggle="modal" data-target="#group_broadcast_modal" title="send broadcast message"><b><i class="fas fa-plus"></i> Create Group Broadcast</b></a>
            <a class="btn btn-primary" data-toggle="modal" data-target="#group_template_modal" title="send broadcast template"><b><i class="fas fa-plus"></i> Create Group Broadcast Template</b></a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Send to</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Schedule</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No</th>
                            <th>Send to</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Schedule</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        $no = 1;
                        $email = $_SESSION['email'];
                        if ($_SESSION['level'] === "admin") {
                            $query = mysqli_query($conn, "SELECT * FROM `messages` WHERE `message_categories` IN ('send-group', 'send-group-schedule', 'api-send-group', 'api-send-group-schedule', 'send-group-template')");
                        } elseif ($_SESSION['level'] === "user") {
                            $query = mysqli_query($conn, "SELECT * FROM `messages` WHERE `user_id`='$email' AND `message_categories` IN ('send-group', 'send-group-schedule', 'api-send-group', 'api-send-group-schedule', 'send-group-template')");
                        }
                        while ($row = mysqli_fetch_assoc($query)) {
                            echo '<tr>';
                            echo '<td class="text-center">' . $no++ . '</td>';
                            echo '<td class="text-center">' . $row['send_to'] . '</td>';
                            echo '<td class="text-center">' . substr($row['message'], 0, 60) . '...</td>';
                            if ($row['message_status'] == "success") {
                                echo '<td class="text-center"><span class="btn btn-success">Success</span></td>';
                            } else if ($row['message_status'] == "waiting") {
                                echo '<td class="text-center"><span class="btn btn-warning">Waiting</span></td>';
                            } else {
                                echo '<td class="text-center"><span class="btn btn-danger">Failed</span></td>';
                            }
                            echo '<td class="text-center">' . $row['message_schedule'] . '</td>';
                            echo '<td class="text-center">
                                <a id="details" class="btn-circle btn-info btn-sm" data-toggle="modal" data-target="#detailModal" title="Info"
                                    data-send_to="' . $row['send_to'] . '" 
                                    data-message="' . $row['message'] . '" 
                                    data-message_categories="' . $row['message_categories'] . '" 
                                    data-message_media="' . $row['message_media'] . '" 
                                    data-message_status="' . $row['message_status'] . '" 
                                    data-message_schedule="' . $row['message_schedule'] . '" 
                                    data-user_id="' . $row['user_id'] . '">
                                    <i class="fas fa-info"></i>
                                </a>
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
<div class="modal fade" id="group_broadcast_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><b>Send Group Broadcast</b></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form class="form" action="" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <?= csrf_input(); ?>
                    <input type="hidden" class="form-control" id="send_group" name="send_group" value="send_group" />
                    <div class="form-group">
                        <label for="message">Messages <span style="color: red;">*</span></label>
                        <textarea class="form-control" id="message" name="message" placeholder="type your holly text.." required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="group-media">Media <span class="text-primary"> <small>Ext: jpg, jpeg, png, gif mp3 <a data-toggle="modal" data-target="#info-modal" title="send broadcast message"><i class="fas fa-info-circle"></i></a></small></span></label>
                        <input type="url" class="form-control" id="media" name="media" placeholder="Example: https://source.unsplash.com/360x200?games" />
                        <div class="form-check">
                            <input type="checkbox" name="mediacheck" class="form-check-input" id="mediacheck" checked>
                            <label class="form-check-label" for="mediacheck" id="labelMedia">Type url image</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="date">Date <span style="color: red;">*</span></label>
                        <input type="date" class="form-control" id="date" name="date" required />
                    </div>
                    <div class="form-group">
                        <label for="time">Time <span style="color: red;">*</span></label>
                        <input type="time" class="form-control" id="time" name="time" required />
                    </div>
                    <div class="form-group">
                        <label for="type_target">Send By Contact Categories <span style="color: red;">*</span></label>
                        <select class="form-control js-states" id="type_target" name="type_target" onchange="sendSelectedBroadcast(this);" required>
                            <option value="" selected>Selected Target</option>
                            <option value="send_to_group">One Group</option>
                            <option value="group_category">Category Group</option>
                            <option value="send_all">Send All Group</option>
                        </select>
                    </div>
                    <div id="send_by_group" class="form-group" style="display: none;">
                        <label for="target">Send To <span style="color: red;">*</span></label>
                        <select id="target" class="form-control js-example-basic-multiple" name="target[]" multiple="multiple" style="width: 100%">
                            <?php
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
                    <div id="send_group_by_category" class="form-group" style="display: none;">
                        <label for="target_category">Send To Categories <span style="color: red;">*</span></label>
                        <select id="target_category" class="form-control" name="target_category">
                            <option value="" selected>Select Target</option>
                            <?php
                            $email = $_SESSION['email'];

                            if ($_SESSION['level'] == 'admin') {
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
<!-- Template -->
<div class="modal fade" id="group_template_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><b>Send Broadcast Template</b></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form class="form" action="" method="POST">
                <div class="modal-body">
                    <?= csrf_input(); ?>
                    <input type="hidden" class="form-control" id="send_group_template" name="send_group_template" value="send_group_template" />
                    <div class="form-group">
                        <label for="template">Message Template <span style="color: red;">*</span></label>
                        <select id="template" class="form-control" name="template" required>
                            <option value="" selected> Choose Your Templates </option>
                            <?php
                            if ($_SESSION['level'] == 'admin') {
                                $query = mysqli_query($conn, "SELECT * FROM templates");
                            } else {
                                $query = mysqli_query($conn, "SELECT * FROM templates WHERE user_id='$email'");
                            }
                            while ($row = mysqli_fetch_assoc($query)) {
                                echo '<option value="' . $row['id'] . '">' . $row['template_title'] . ' </option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="date">Date <span style="color: red;">*</span></label>
                        <input type="date" class="form-control" id="date" name="date" required />
                    </div>
                    <div class="form-group">
                        <label for="time">Time <span style="color: red;">*</span></label>
                        <input type="time" class="form-control" id="time" name="time" required />
                    </div>
                    <div class="form-group">
                        <label for="type_target">Send By Contact Categories <span style="color: red;">*</span></label>
                        <select class="form-control js-states" id="template_type_target" name="type_target" onchange="sendSelectedTemplates(this);" required>
                            <option value="" selected>Selected Target</option>
                            <option value="send_template_by_group">One Group</option>
                            <option value="send_template_group_by_category">Category Group</option>
                            <option value="send_template_all">Send All Group</option>
                        </select>
                    </div>
                    <div id="send_template_by_group" class="form-group" style="display: none;">
                        <label for="target_template">Send To <span style="color: red;">*</span></label>
                        <select id="target_template" class="form-control js-example-basic-multiple-template" name="target[]" multiple="multiple" style="width: 100%">
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
                    <div id="send_template_group_by_category" class="form-group" style="display: none;">
                        <label for="t">Send To Categories <span style="color: red;">*</span></label>
                        <select id="target_category_template" class="form-control" name="target_category">
                            <option value="" selected>Select Target</option>
                            <?php
                            $email = $_SESSION['email'];

                            if ($_SESSION['level'] == 'admin') {
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
                <h5 style="text-align: center; "><b>Make By :&nbsp;</b></h5>
                <p id="usersID" class="card-text text-center"></p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

</div>
<!-- End of Main Content -->
<?php require_once('../templates/footer.php'); ?>
<script>
    $(document).ready(function() {
        $('.js-example-basic-multiple').select2({
            dropdownAutoWidth: true
        });
    });
    $(document).ready(function() {
        $('.js-example-basic-multiple-template').select2({
            dropdownAutoWidth: true
        });
    });

    /* for broadcast messages */
    function sendSelectedBroadcast(that) {
        if (that.value == "send_to_group") {
            document.getElementById("send_by_group").style.display = "block";
        } else {
            document.getElementById("send_by_group").style.display = "none";
        }

        if (that.value == "group_category") {
            document.getElementById("send_group_by_category").style.display = "block";
        } else {
            document.getElementById("send_group_by_category").style.display = "none";
        }
    }

    /* for template messages */
    function sendSelectedTemplates(that) {
        if (that.value == "send_template_by_group") {
            document.getElementById("send_template_by_group").style.display = "block";
        } else {
            document.getElementById("send_template_by_group").style.display = "none";
        }

        if (that.value == "send_template_group_by_category") {
            document.getElementById("send_template_group_by_category").style.display = "block";
        } else {
            document.getElementById("send_template_group_by_category").style.display = "none";
        }
    }

    $(document).ready(function() {
        $('#mediacheck').click(function() {
            if ($(this).is(':checked')) {
                $('#media').attr('type', 'url');
                $('#labelMedia').text("Type url image");
            } else {
                $('#media').attr('type', 'file');
                $('#labelMedia').text("Browse file max: 2MB.");
            }
        });
    });

    $(document).ready(function() {
        $(document).on('click', '#details', function() {
            var sendTo = $(this).data('send_to');
            var messages = $(this).data('message');
            var messageCategories = $(this).data('message_categories');
            var messageMedia = $(this).data('message_media');
            var messageStatus = $(this).data('message_status');
            var messageSchedule = $(this).data('message_schedule');
            var usersID = $(this).data('user_id');
            $('#sendTo').text(sendTo);
            $('#messages').text(messages);
            $('#messageCategories').text(messageCategories);
            $('#messageMedia').text(messageMedia);
            $('#messageMediaAttr').attr('src', messageMedia);
            $('#messageStatus').text(messageStatus);
            $('#messageSchedule').text(messageSchedule);
            $('#usersID').text(usersID);
            $("#detailModal").modal('show');
        });
    });
</script>
</body>

</html>