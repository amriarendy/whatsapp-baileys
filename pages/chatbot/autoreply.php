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
if (post("_token")) {
    csrf_validate();
    $keyword = post("keyword");
    $response = post("response");
    $type = post("type");
    if (post("media")) {
        $media = post("media");
    } else if (!empty($_FILES['media']) && $_FILES['media']['error'] == UPLOAD_ERR_OK) {
        // Be sure we're dealing with an upload
        if (is_uploaded_file($_FILES['media']['tmp_name']) === false) {
            throw new \Exception('Error on upload: Invalid file definition');
        }

        // Rename the uploaded file
        $uploadName = $_FILES['media']['name'];
        $ext = strtolower(substr($uploadName, strripos($uploadName, '.') + 1));
        $size = $_FILES['media']['size'];
        if ($size > 3000000) {
            toastr_set("error", "Max. 1 mb");
            redirect("autoreply.php");
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
            toastr_set("error", "Format: png, jpg, pdf only");
            redirect("autoreply.php");
            exit;
        }

        move_uploaded_file($_FILES['media']['tmp_name'], '../../public/uploads/' . $filename);
        print_r(error_get_last());
        // Insert it into our tracking along with the original name
        $media = $base_url . "public/uploads/" . $filename;
    } else {
        $media = null;
    }
    if (post("keyword")) {
        if ($media == null) {
            $query = mysqli_query($conn, "INSERT INTO autoreplies(`keyword`, `response`, `user_id`, `created_at`)
            VALUES('$keyword', '$response', '$email', '$timeZone')");
            toastr_set("success", "Success: Create dataa");
            redirect("autoreply.php");
            exit;
        } else {
            $query = mysqli_query($conn, "INSERT INTO autoreplies(`keyword`, `response`, `media`, `user_id`, `created_at`)
            VALUES('$keyword', '$response', '$media', '$email', '$timeZone')");
            $insertMedia = mysqli_query($conn, "INSERT INTO media(`media`, `user_id`, `created_at`) VALUES('$media', '$email', '$timeZone')");
            toastr_set("success", "Success: Create data");
            redirect("autoreply.php");
            exit;
        }
    } else {
        toastr_set("error", "Aborted: Failed create data");
        redirect("autoreply.php");
        exit;
    }
}

if (get("act") == "delete") {
    $id = encryptor('decrypt', get("id"));
    if ($level === 'admin') {
        $where = mysqli_query($conn, "SELECT * FROM autoreplies WHERE id='$id'");
    } else {
        $where = mysqli_query($conn, "SELECT * FROM autoreplies WHERE id='$id' AND user_id='$email'");
    }
    $row = mysqli_fetch_assoc($where);
    $media = $row['media'];
    $url = parse_url($row['media']);
    $fileOnly = str_replace("/whatsapp-baileys/public/uploads/", '', $url['path']);
    $dir = "../../public/uploads/" . $fileOnly;
    unlink($dir);
    if ($level === 'admin') {
        mysqli_query($conn, "DELETE FROM media WHERE media='$media'");
        mysqli_query($conn, "DELETE FROM autoreplies WHERE id='$id'");
    } else {
        mysqli_query($conn, "DELETE FROM media WHERE media='$media' AND user_id='$email'");
        mysqli_query($conn, "DELETE FROM autoreplies WHERE id='$id' AND user_id='$email'");
    }
    toastr_set("mixin-success", "Success: Delete data!");
    redirect("autoreply.php");
}

require_once('../templates/header.php');
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><b><i class="fas fa-robot text-primary"></i> Auto Reply</b></h1>
    </div>

    <!-- Content Row -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <a class="btn btn-primary" data-toggle="modal" data-target="#addModal" title="Add"><b><i class="fas fa-plus"></i> Create</b></a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Keyword</th>
                            <th width="1px">Reply</th>
                            <th>Media</th>
                            <th>Owner</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No</th>
                            <th>Keyword</th>
                            <th>Reply</th>
                            <th>Media</th>
                            <th>Owner</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        $no = 1;
                        $email = $_SESSION['email'];
                        if ($_SESSION['level'] === "admin") {
                            $query = mysqli_query($conn, "SELECT * FROM autoreplies");
                        } elseif ($_SESSION['level'] === "user") {
                            $query = mysqli_query($conn, "SELECT * FROM autoreplies WHERE user_id='$email'");
                        }
                        while ($row = mysqli_fetch_assoc($query)) {
                            $id = encryptor('encrypt', $row['id']);
                            echo '<tr>';
                            echo '<td>' . $no++ . '</td>';
                            echo '<td>' . $row['keyword'] . '</td>';
                            echo '<td><h5><span class="badge badge-secondary">' . substr($row['response'], 0, 64) . '</span></h5></td>';
                            if (getExtention($row['media']) === 'jpg' || getExtention($row['media']) === 'jpeg' || getExtention($row['media']) === 'png') {
                                echo '<td class="text-center"> <img class="img-fluid px-1 px-sm-1 mt-1 mb-1" style="width: 5rem;" src="' . $row['media'] . '" alt="image" title="images"/></td>';
                            } else if (getExtention($row['media']) === 'gif') {
                                echo '<td class="text-center"> <img class="img-fluid px-1 px-sm-1 mt-1 mb-1" style="width: 5rem;" src="' . $row['media'] . '" alt="pdf" title="images"/></td>';
                            } else if (getExtention($row['media']) === 'pdf') {
                                echo '<td class="text-center"> <img class="img-fluid px-1 px-sm-1 mt-1 mb-1" style="width: 5rem;" src="' . $base_url . '/public/files/mp3.svg' . '" alt="mp3" title="images"/></td>';
                            } else if (getExtention($row['media']) === 'mp3') {
                                echo '<td class="text-center"> <img class="img-fluid px-1 px-sm-1 mt-1 mb-1" style="width: 5rem;" src="' . $base_url . '/public/files/mp3.svg' . '" alt="mp3" title="images"/></td>';
                            } else if (getExtention($row['media']) === 'xlsx' || getExtention($row['media']) === 'xls') {
                                echo '<td class="text-center"> <img class="img-fluid px-1 px-sm-1 mt-1 mb-1" style="width: 5rem;" src="' . $base_url . '/public/files/excel.svg' . '" alt="excel" title="images"/></td>';
                            } else if (getExtention($row['media']) === null) {
                                echo '<td class="text-center"> <img class="img-fluid px-1 px-sm-1 mt-1 mb-1" style="width: 5rem;" src="' . $base_url . '/public/files/nyan-cat.gif' . '" alt="etc" title="images"/></td>';
                            } else {
                                echo '<td class="text-center"> <img class="img-fluid px-1 px-sm-1 mt-1 mb-1" style="width: 5rem;" src="' . $base_url . '/public/files/nyan-cat.gif' . '" alt="etc" title="images"/></td>';
                            }
                            echo '<td>' . $row['user_id'] . '</td>';
                            echo '<td class="text-center">
                                    <a id="detail" class="btn-circle btn-info btn-sm" data-toggle="modal" data-target="#detailModal" title="Info"
                                        data-keyword="' . $row['keyword'] . '" 
                                        data-response="' . $row['response'] . '" 
                                        data-media="' . $row['media'] . '" 
                                        data-user_id="' . $row['user_id'] . '">
                                        <i class="fas fa-info"></i>
                                    </a>
                                    <a class="btn-circle btn-danger btn-sm delete_confirm" href="autoreply.php?act=delete&id=' . $id . '" title="Delete"><i class="fas fa-trash"></i></a>
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
                    <div class="form-group" id="keyword" style="display: block;">
                        <label for="keyword">Keyword</label>
                        <input type="text" class="form-control" name="keyword" placeholder="Request..">
                    </div>
                    <div class="form-group" id="response" style="display: block;">
                        <label for="response">Response</label>
                        <textarea class="form-control" id="response" name="response" placeholder="Response from keyword.."></textarea>
                    </div>
                    <div class="form-group" style="display: block;"></div>
                    <label for="media">Media <span style="color: red;">*</span></label>
                    <input type="url" class="form-control" id="media" name="media" placeholder="Example: https://source.unsplash.com/360x200?games" />
                    <div class="form-check">
                        <input type="checkbox" name="checkMedia" class="form-check-input" id="checkMedia" checked>
                        <label class="form-check-label" for="checkMedia" id="labelMedia">Type url image</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>

                    <button class="btn btn-primary" id="submit" onclick="loadingButton()" type="submit">
                        <i class="fas fa-spinner fa-spin" style="display:none;"></i>
                        <span class="btn-text">Submit</span>
                    </button>
                </div>
        </div>
        </form>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><b>Detail Autoreply</b></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 500px; overflow-y: auto;">
                <h5 style="text-align: center; "><b>Keyword :&nbsp;</b></h5>
                <p id="keyword_detail" class="card-text text-center"></p>
                <hr>
                <h5 style="text-align: center; "><b>Response :&nbsp;</b></h5>
                <p id="response_detail" class="card-text"></p>
                <hr>
                <h5 style="text-align: center; "><b>Media :&nbsp;</b></h5>
                <div class="text-center">
                    <img id="media_view" class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 50%;" src="" alt="images" title="images"><br>
                    <div class="input-group">
                        <input id="url_media" type="text" class="form-control bg-light border-0 small" aria-label="Search" aria-describedby="basic-addon2" />
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button" onclick="copyMediaURL()">
                                <i class="fas fa-copy fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
                </p>
                <hr>
                <h5 style="text-align: center; "><b>Make By :&nbsp;</b></h5>
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
        $('#checkMedia').click(function() {
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
        $(document).on('click', '#detail', function() {
            var keyword_detail = $(this).data('keyword');
            var response_detail = $(this).data('response');
            var media_view = $(this).data('media');
            var user_id = $(this).data('user_id');
            $('#keyword_detail').text(keyword_detail);
            $('#response_detail').text(response_detail);
            $('#media_view').attr('src', media_view);
            $('#url_media').val(media_view);
            $('#user_id').text(user_id);
            $("#detailModal").modal('show');
        });
    });

    function copyMediaURL() {
        var copyURL = document.getElementById("url_media");
        copyURL.select();
        copyURL.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyURL.value);
        alert("Success Copy URL: " + copyURL.value);
    }
</script>
</body>

</html>