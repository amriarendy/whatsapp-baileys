<?php
include_once("../../helpers/connection.php");
include_once("../../helpers/function.php");
$login = cekSession();
if ($login == 0) {
    toastr_set("mixin-error", "Forbiden access!!");
    redirect("../../login.php");
}

if (post('number')) {
    csrf_validate();
    $number = post('number');
    $api_key = api_key();
    $res = check_numbers($api_key, $number);
    $status = $res['status'];
    $message = $res['message'];
    if ($res) {
        if ($status === 200) {
            $json = json_encode($res, JSON_PRETTY_PRINT);
            toastr_set("success", $message);
        } else {
            toastr_set("error", $message);
        }
    } else {
        toastr_set("error", "You are not login whatsapp session");
    }
}

require_once('../templates/header.php');
?>
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fab fa-whatsapp text-success"></i><b> Check Whatsapp Numbers</b></h1>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <!-- Topbar Search -->
                <form class="form" action="" method="POST">
                    <div class="input-group mb-3">
                        <?= csrf_input() ?>
                        <input type="number" class="form-control" placeholder="number format: 62****** or 08******" name="number" aria-label="Recipient's whatsapp number" aria-describedby="basic-addon2">
                        <button class="btn btn-primary" id="submit" onclick="loadingButton()" type="submit">
                            <i class="fas fa-spinner fa-spin" style="display:none;"></i>
                            <span class="btn-text">Submit</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12">
                <div class="card">
                    <div class="card body">
                        <div class="col-12">
                            <?php
                            if ($status === 200) {
                                echo '<p style="text-align:center;">
                                    <span style="color:#558b2f;font-size:20px;"><strong>Whatsapp Number Exists&nbsp;<i class="fas fa-check-circle"></i></strong></span>
                                </p>';
                            } else if ($status === 400 || $status === 401 || $status === 500) {
                                echo '
                                <p style="text-align:center;">
                                    <span style="color:#d32f2f;font-size:20px;"><strong>Whatsapp Number Not Exists&nbsp; <i class="fas fa-times-circle"></i></strong></span>
                                </p>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php if ($json) {
                    echo '<div class="card">';
                    echo '<div class="card-body">';
                    echo '<div class="col-12">';
                    echo '<pre><code class="language-json">' . htmlspecialchars($json) . '</code></pre>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->
<?php require_once('../templates/footer.php'); ?>
</body>

</html>