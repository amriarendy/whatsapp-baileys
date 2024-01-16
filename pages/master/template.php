<?php
include_once("../../helpers/connection.php");
include_once("../../helpers/function.php");
$login = cekSession();

if ($login == 0) {
    toastr_set("error", "Forbiden: sorry you are not login!");
    redirect("../../login.php");
}

$email = $_SESSION['email'];
if (post("_token")) {
    csrf_validate();
    $email = $_SESSION['email'];
    $template_title = post("template_title");
    $template_content = post("template_content");
    $postMedia = post("media");

    if ($postMedia) {
        $media = $postMedia;
    } else if (!empty($_FILES['media']) && $_FILES['media']['error'] == UPLOAD_ERR_OK) {
        // Be sure we're dealing with an upload
        if (is_uploaded_file($_FILES['media']['tmp_name']) === false) {
            throw new \Exception('Error on upload: Invalid file definition');
        }

        // Rename the uploaded file
        $uploadName = $_FILES['media']['name'];
        $ext = strtolower(substr($uploadName, strripos($uploadName, '.') + 1));
        $size = $_FILES['media']['size'];
        if ($size > 1000000) {
            toastr_set("error", "Maximal 1 mb");
            redirect("template.php");
            exit;
        }

        $allow = ['png', 'jpeg', 'gif', 'jpg', 'pdf', 'mp3'];
        if (in_array($ext, $allow)) {
            if ($ext == "png") {
                $filename = round(microtime(true)) . mt_rand() . '.png';
            }

            if ($ext == "gif") {
                $filename = round(microtime(true)) . mt_rand() . '.gif';
            }

            if ($ext == "jpg") {
                $filename = round(microtime(true)) . mt_rand() . '.jpg';
            }

            if ($ext == "jpeg") {
                $filename = round(microtime(true)) . mt_rand() . '.jpeg';
            }

            if ($ext == "pdf") {
                $filename = round(microtime(true)) . mt_rand() . '.pdf';
            }

            if ($ext == "mp3") {
                $filename = round(microtime(true)) . mt_rand() . '.mp3';
            }
        } else {
            toastr_set("error", "Format png, jpg, pdf only");
            redirect("send-broadcast.php");
            exit;
        }

        move_uploaded_file($_FILES['media']['tmp_name'], '../../public/uploads/' . $filename);
        print_r(error_get_last());
        // Insert it into our tracking along with the original name
        $media = $base_url . "public/uploads/" . $filename;
    } else {
        $media = null;
    }

    if ($media == null) {
        echo "if";
        $query = mysqli_query($conn, "INSERT INTO templates(`template_title`, `template_content`, `user_id`, `created_at`)
        VALUES('$template_title', '$template_content', '$email', '$timeZone')");
    } else {
        echo "else";
        $query = mysqli_query($conn, "INSERT INTO templates(`template_title`, `template_content`, `template_media`, `user_id`, `created_at`)
        VALUES('$template_title', '$template_content', '$media', '$email', '$timeZone')");
        $insertMedia = mysqli_query($conn, "INSERT INTO media(`media`, `user_id`, `created_at`) VALUES('$media', '$email', '$timeZone')");
    }

    toastr_set("success", "Success: Create data!");
    redirect("template.php");
    exit;
}

if (get("act") == "delete") {
    $id = encryptor('decrypt', get("id"));
    if ($level === 'admin') {
        $where = mysqli_query($conn, "SELECT * FROM templates WHERE id='$id'");
    } else {
        $where = mysqli_query($conn, "SELECT * FROM templates WHERE id='$id' AND user_id='$email'");
    }
    $row = mysqli_fetch_assoc($where);
    $media = $row['message_media'];
    $url = parse_url($row['message_media']);
    $fileOnly = str_replace("/whatsapp-baileys/public/uploads/", '', $url['path']);
    $dir = "../../public/uploads/" . $fileOnly;
    unlink($dir);
    if ($level === 'admin') {
        mysqli_query($conn, "DELETE FROM media WHERE media='$media'");
        mysqli_query($conn, "DELETE FROM templates WHERE id='$id'");
    } else {
        mysqli_query($conn, "DELETE FROM media WHERE media='$media' AND user_id='$email'");
        mysqli_query($conn, "DELETE FROM templates WHERE id='$id' AND user_id='$email'");
    }
    toastr_set("mixin-success", "Success: Delete data!");
    redirect("template.php");
}

require_once('../templates/header.php');
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-pencil-alt"></i> <b>Master Template</b></h1>
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
                            <th>Title</th>
                            <th>Content</th>
                            <th>Images</th>
                            <th>Owner</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No</th>
                            <th>Title</th>
                            <th>Content</th>
                            <th>Images</th>
                            <th>Owner</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        $no = 1;
                        $email = $_SESSION['email'];
                        if ($_SESSION['level'] === "admin") {
                            $query = mysqli_query($conn, "SELECT * FROM templates");
                        } elseif ($_SESSION['level'] === "user") {
                            $query = mysqli_query($conn, "SELECT * FROM templates WHERE user_id='$email'");
                        }
                        while ($row = mysqli_fetch_assoc($query)) {
                            $id = encryptor('encrypt', $row['id']);
                            echo '<tr>';
                            echo '<td class="text-center">' . $no++ . '</td>';
                            echo '<td>' . $row['template_title'] . '</td>';
                            echo '<td>' . $row['template_content'] . '</td>';
                            if (getExtention($row['template_media']) === 'jpg' || getExtention($row['template_media']) === 'jpeg' || getExtention($row['template_media']) === 'png') {
                                echo '<td class="text-center"> <img class="img-fluid px-1 px-sm-1 mt-1 mb-1" style="width: 5rem;" src="' . $row['template_media'] . '" alt="image" title="images"/></td>';
                            } else if (getExtention($row['template_media']) === 'gif') {
                                echo '<td class="text-center"> <img class="img-fluid px-1 px-sm-1 mt-1 mb-1" style="width: 5rem;" src="' . $row['template_media'] . '" alt="pdf" title="images"/></td>';
                            } else if (getExtention($row['template_media']) === 'pdf') {
                                echo '<td class="text-center"> <img class="img-fluid px-1 px-sm-1 mt-1 mb-1" style="width: 5rem;" src="' . $base_url . '/public/files/mp3.svg' . '" alt="mp3" title="images"/></td>';
                            } else if (getExtention($row['template_media']) === 'mp3') {
                                echo '<td class="text-center"> <img class="img-fluid px-1 px-sm-1 mt-1 mb-1" style="width: 5rem;" src="' . $base_url . '/public/files/mp3.svg' . '" alt="mp3" title="images"/></td>';
                            } else if (getExtention($row['template_media']) === 'xlsx' || getExtention($row['template_media']) === 'xls') {
                                echo '<td class="text-center"> <img class="img-fluid px-1 px-sm-1 mt-1 mb-1" style="width: 5rem;" src="' . $base_url . '/public/files/excel.svg' . '" alt="excel" title="images"/></td>';
                            } else if (getExtention($row['template_media']) === null) {
                                echo '<td class="text-center"> <img class="img-fluid px-1 px-sm-1 mt-1 mb-1" style="width: 5rem;" src="' . $base_url . '/public/files/nyan-cat.gif' . '" alt="etc" title="images"/></td>';
                            } else {
                                echo '<td class="text-center"> <img class="img-fluid px-1 px-sm-1 mt-1 mb-1" style="width: 5rem;" src="' . $base_url . '/public/files/nyan-cat.gif' . '" alt="etc" title="images"/></td>';
                            }
                            echo '<td>' . $row['user_id'] . '</td>';
                            echo '<td class="text-center">
                                <a id="details" class="btn-circle btn-info btn-sm" data-toggle="modal" data-target="#detailModal" title="Info"
                                    data-template_title="' . $row['template_title'] . '" 
                                    data-template_content="' . $row['template_content'] . '" 
                                    data-template_media="' . $row['template_media'] . '" 
                                    data-user_id="' . $row['user_id'] . '" 
                                    data-created_at="' . $row['created_at'] . '">
                                    <i class="fas fa-info"></i>
                                </a>
                                <a class="btn-circle btn-danger btn-sm delete_confirm" href="template.php?act=delete&id=' . $id . '" title="Delete"><i class="fas fa-trash"></i></a>
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
            <form class="form" action="" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <?= csrf_input(); ?>
                    <div class="form-group">
                        <label for="template_title">Title <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="template_title" name="template_title" placeholder="Title Template.." required />
                    </div>
                    <div class="form-group">
                        <label for="template_content">Message <span style="color: red;">*</span></label>
                        <textarea class="form-control" name="template_content" id="template_content" cols="30" rows="10" placeholder="text your message" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="media">Media <span style="color: red;">*</span></label>
                        <input type="url" class="form-control" id="media" name="media" placeholder="Example: https://source.unsplash.com/360x200?games" />
                        <div class="form-check">
                            <input type="checkbox" name="mediacheck" class="form-check-input" id="mediacheck" checked>
                            <label class="form-check-label" for="mediacheck" id="labelMedia">Type url image</label>
                        </div>
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
                <h5 class="modal-title" id="exampleModalLabel"><b>Detail Template</b></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 500px; overflow-y: auto;">
                <h5 style="text-align: center; "><b>Title :&nbsp;</b></h5>
                <p id="templateTitle" class="card-text text-center">.</p>
                <hr>
                <h5 style="text-align: center; "><b>Content :&nbsp;</b></h5>
                <p id="templateContent" class="card-text">.</p>
                <hr>
                <h5 style="text-align: center; "><b>Media :&nbsp;</b></h5>
                <p class="card-text text-center">
                    <img id="templateMedia" class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 50%;" src="" alt="images" title="images" /><br>
                </p>
                <hr>
                <h5 style="text-align: center; "><b>Owner :&nbsp;</b></h5>
                <p id="user_id" class="card-text text-center"></p>
                <hr>
                <h5 style="text-align: center; "><b>Timestamp :&nbsp;</b></h5>
                <p id="createdAt" class="card-text text-center"></p>

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
        $('#mediacheck').click(function() {
            if ($(this).is(':checked')) {
                $('#media').attr('type', 'url');
                $('#labelMedia').text("Type url image");
            } else {
                $('#media').attr('type', 'file');
                $('#labelMedia').text("Browse file. ext: jpg, jpeg, png & pdf. max: 1MB.");
            }
        })
    })

    $(document).ready(function() {
        $(document).on('click', '#details', function() {
            var templateTitle = $(this).data('template_title');
            var templateContent = $(this).data('template_content');
            var templateMedia = $(this).data('template_media');
            var user_id = $(this).data('user_id');
            var createdAt = $(this).data('created_at');
            $('#templateTitle').text(templateTitle);
            $('#templateContent').text(templateContent);
            $('#templateMedia').attr('src', templateMedia);
            $('#user_id').text(user_id);
            $('#createdAt').text(createdAt);
        });
    });
</script>
</body>

</html>