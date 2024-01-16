<?php
include_once("../../helpers/connection.php");
include_once("../../helpers/function.php");
$login = cekSession();
if ($login == 0) {
    toastr_set("mixin-error", "Forbiden access!!");
    redirect("../../login.php");
}

if ($_SESSION['level'] !== 'admin') {
    toastr_set("error", "Forbiden Access!!");
    redirect("../dashboard.php");
}

$month = date('m');
$year_now = date('Y');
for ($month = 1; $month <= 12; $month++) {
    $query = mysqli_query($conn, "SELECT COUNT(log) AS total_messages FROM logs WHERE MONTH(created_at)='$month' AND YEAR(created_at)='$year_now' AND log='send-whatsapp' AND log_status='200'");
    $row = mysqli_fetch_assoc($query);
    $amount = $row['total_messages'];
    $total_success .= "'$amount'" . ", ";
}

for ($month = 1; $month <= 12; $month++) {
    $query = mysqli_query($conn, "SELECT COUNT(log) AS total_messages FROM logs WHERE MONTH(created_at)='$month' AND YEAR(created_at)='$year_now' AND log='send-whatsapp' AND (log_status='400' OR log_status='401' OR log_status='415' OR log_status='500')");
    $row = mysqli_fetch_assoc($query);
    $amount = $row['total_messages'];
    $total_failed .= "'$amount'" . ", ";
}

$query = mysqli_query($conn, "SELECT log_status, COUNT(log) AS count_status FROM logs WHERE log='send-whatsapp' GROUP BY log_status");
$status_code = array(); // Initialize an array to store log_status values
while ($row = mysqli_fetch_assoc($query)) {
    $status_code[] = $row['log_status'];
    $count_status[] = $row['count_status'];
}

// var_dump($status_code, false);
// die;

require_once('../templates/header.php');
?>
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-mail-bulk text-success"></i><b> Chart Logs</b></h1>
    </div>
    <div class="row">
        <div class="col-lg-6 col-md-12 col-sm-12">
            <!-- Content Row -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status Massages</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 100%;">
                        <canvas id="statusMessages"></canvas>
                    </div>
                    <hr>
                    Stastistic All Messages.
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-12 col-sm-12">
            <!-- Content Row -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bar Status Code Messages</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 100%;">
                        <canvas id="codeStatusMessages"></canvas>
                    </div>
                    <hr>
                    Stastistic Status Code Messages.
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <!-- Content Row -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status Massages</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Status Code</th>
                                    <th>Log Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Status Code</th>
                                    <th>Log Description</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                <?php
                                $no = 1;
                                $email = $_SESSION['email'];
                                if ($_SESSION['level'] === "admin") {
                                    $query = mysqli_query($conn, "SELECT * FROM logs");
                                } elseif ($_SESSION['level'] === "user") {
                                    $query = mysqli_query($conn, "SELECT * FROM logs WHERE log='send-whatsapp'");
                                }
                                while ($row = mysqli_fetch_assoc($query)) {
                                    $id = encryptor('encrypt', $row['id']);
                                    echo '<tr>';
                                    echo '<td class="text-center">' . $no++ . '</td>';
                                    echo '<td>' . $row['log_status'] . '</td>';
                                    echo '<td>' . $row['log_description'] . '</td>';
                                    echo '<td class="text-center">
                                        <a id="details" class="btn-circle btn-info btn-sm" data-toggle="modal" data-target="#detailModal" title="Info"
                                            data-log_description="'. $row['log_description'] .'"
                                            data-log_status="' . $row['log_status'] . '" 
                                            data-users_id="' . $row['user_id'] . '">
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
        <div class="col-lg-6 col-md-12 col-sm-12">
            <!-- Content Row -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bar Status Code Messages</h6>
                </div>
                <div class="card-body">
                </div>
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
                <h5 style="text-align: center; "><b>Status Code :&nbsp;</b></h5>
                <p id="log_status" class="card-text text-center"></p>
                <hr>
                <h5 style="text-align: center; "><b>Log Description :&nbsp;</b></h5>
                <p id="log_description" class="card-text">.</p>
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
</body>
<script>
    var delayed;
    var ctx = document.getElementById("statusMessages");
    var myChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: [
                "Jan",
                "Feb",
                "Mar",
                "Apr",
                "Mei",
                "Jun",
                "Jul",
                "Agu",
                "Sep",
                "Oct",
                "Nov",
                "Dec",
            ],
            datasets: [{
                    label: "Messages Success",
                    data: [<?= $total_success ?>],
                    borderColor: "rgb(22, 160, 133)",
                    borderWidth: 2,
                    borderRadius: 4,
                    pointBorderWidth: 4,
                    lineTension: 0.2,
                },
                {
                    label: "Messages Failed",
                    data: [<?= $total_failed ?>],
                    borderColor: "rgb(231, 76, 60)",
                    borderWidth: 2,
                    borderRadius: 4,
                    pointBorderWidth: 4,
                    lineTension: 0.2,
                },
            ],
        },
        options: {
            animation: {
                onComplete: () => {
                    delayed = true;
                },
                delay: (context) => {
                    let delay = 0;
                    if (context.type === "data" && context.mode === "default" && !delayed) {
                        delay = context.dataIndex * 300 + context.datasetIndex * 100;
                    }
                    return delay;
                },
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function(value) {
                            if (value % 1 === 0) {
                                return value;
                            }
                        },
                    },
                }, ],
            },
        },
    });

    var delayed;
    var ctx = document.getElementById("codeStatusMessages");
    var myChart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: <?php echo json_encode($status_code, true); ?>,
            datasets: [{
                label: <?php echo json_encode($status_code, true); ?>,
                data: <?php echo json_encode($count_status, true); ?>,
                borderColor: "rgb(0,0,255)",
                borderWidth: 2,
                borderRadius: 4,
                pointBorderWidth: 4,
                lineTension: 0.2,
            }, ],
        },
        options: {
            animation: {
                onComplete: () => {
                    delayed = true;
                },
                delay: (context) => {
                    let delay = 0;
                    if (context.type === "data" && context.mode === "default" && !delayed) {
                        delay = context.dataIndex * 300 + context.datasetIndex * 100;
                    }
                    return delay;
                },
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function(value) {
                            if (value % 1 === 0) {
                                return value;
                            }
                        },
                    },
                }, ],
            },
        },
    });
</script>
<script>
    $(document).ready(function() {
        $(document).on('click', '#details', function() {
            var log_status = $(this).data('log_status');
            var log_description = $(this).data('log_description');
            var user_id = $(this).data('users_id');
            $('#log_status').text(log_status);
            $('#log_description').text(log_description);
            $('#user_id').text(user_id);
            $("#detailModal").modal('show');
        });
    });
</script>

</html>