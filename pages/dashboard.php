<?php
include_once("../helpers/connection.php");
include_once("../helpers/function.php");
$login = cekSession();
if ($login == 0) {
    toastr_set("mixin-error", "Forbiden access!!");
    redirect("../login.php");
}
$level = $_SESSION['level'];
$email = $_SESSION['email'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
$user = mysqli_fetch_assoc($query);
if ($level == 'admin') {
    $query = mysqli_query($conn, "SELECT COUNT(message) AS message FROM messages");
    $total_messages = mysqli_fetch_assoc($query, true);
} else {
    $query = mysqli_query($conn, "SELECT COUNT(message) AS message FROM messages WHERE user_id='$email'");
}

if (post("logout_session")) {
    csrf_validate();
    $res = logout_session();
    if ($res['status'] == 200) {
        toastr_set("success", "Success: Logout Whatsapp Session");
    } else {
        toastr_set("error", "Abort: Something error!");
    }
    redirect("dashboard.php");
}

// $total_success = "";
// $total_failed = "";
$month = date('m');
for ($month = 1; $month <= 12; $month++) {
    $year_now = date('Y');
    if ($level == "admin") {
        $query = mysqli_query($conn, "SELECT COUNT(message) AS total_messages FROM messages WHERE MONTH(created_at)='$month' AND YEAR(created_at)='$year_now' AND message_status='success'");
    } else {
        $query = mysqli_query($conn, "SELECT COUNT(message) AS total_messages FROM messages WHERE MONTH(created_at)='$month' AND YEAR(created_at)='$year_now' AND message_status='success' AND user_id='$email'");
    }
    $row = mysqli_fetch_assoc($query);
    $amount = $row['total_messages'];
    $total_success .= "'$amount'" . ", ";
}

for ($month = 1; $month <= 12; $month++) {
    $year_now = date('Y');
    if ($level == "admin") {
        $query = mysqli_query($conn, "SELECT COUNT(message) AS total_messages FROM messages WHERE MONTH(created_at)='$month' AND YEAR(created_at)='$year_now' AND message_status='waiting'");
    } else {
        $query = mysqli_query($conn, "SELECT COUNT(message) AS total_messages FROM messages WHERE MONTH(created_at)='$month' AND YEAR(created_at)='$year_now' AND message_status='waiting' AND user_id='$email'");
    }
    $row = mysqli_fetch_assoc($query);
    $amount = $row['total_messages'];
    $total_waiting .= "'$amount'" . ", ";
}

for ($month = 1; $month <= 12; $month++) {
    $year_now = date('Y');
    if ($level == "admin") {
        $query = mysqli_query($conn, "SELECT COUNT(message) AS total_messages FROM messages WHERE MONTH(created_at)='$month' AND YEAR(created_at)='$year_now' AND message_status='failed'");
    } else {
        $query = mysqli_query($conn, "SELECT COUNT(message) AS total_messages FROM messages WHERE MONTH(created_at)='$month' AND YEAR(created_at)='$year_now' AND message_status='failed' AND user_id='$email'");
    }
    $row = mysqli_fetch_assoc($query);
    $amount = $row['total_messages'];
    $total_failed .= "'$amount'" . ", ";
}

require_once('./templates/header.php');
?>
<style>
    body {
        background: rgb(200, 220, 224);
    }

    ul.logs {
        max-height: 150px;
        padding: 15px 15px 15px 30px;
        margin-top: 5px;
        border-radius: 4px;
        overflow-y: auto;
        background: #efefef;
        color: #666;
        font-size: 14px;
    }

    ul.logs li:first-child {
        color: green;
    }

    .form {
        font-family: Helvetica, sans-serif;
        max-width: 400px;
        margin: 5px auto;
        text-align: center;
        padding: 16px;
        background: #ffffff;
    }

    .form h1 {
        background: #03773f;
        padding: 20px 0;
        font-weight: 300;
        text-align: center;
        color: #fff;
        margin: -16px -16px 16px -16px;
        font-size: 25px;
    }

    .form input[type="text"],
    .form input[type="url"] {
        box-sizing: border-box;
        width: 100%;
        background: #fff;
        margin-bottom: 4%;
        border: 1px solid #ccc;
        padding: 4%;
        font-size: 17px;
        color: rgb(9, 61, 125);
    }

    .form input[type="text"]:focus,
    .form input[type="url"]:focus {
        box-shadow: 0 0 5px #5868bf;
        padding: 4%;
        border: 1px solid #5868bf;
    }

    .form button {
        box-sizing: border-box;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        width: 180px;
        margin: 0 auto;
        padding: 3%;
        background: #0853b6;
        border: none;
        border-radius: 3px;
        font-size: 17px;
        border-top-style: none;
        border-right-style: none;
        border-left-style: none;
        color: #fff;
        cursor: pointer;
    }

    .form button:hover {
        background: rgba(88, 104, 191, 0.5);
    }

    .qrcode {
        padding: 16px;
        margin-bottom: 30px;
    }

    .qrcode img {
        margin: 0 auto;
        box-shadow: 0 0 10px rgba(67, 67, 68, 0.25);
        padding: 4px;
    }
</style>
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><b> Dashboard</b></h1>
        <?php if ($_SESSION['level'] == 'admin') { ?>
            <a href="./../helpers/cronjob.php" id="linkButton" onclick="return confirm('Push cronjob now?')" class="d-none d-sm-inline-block btn btn-sm btn-danger shadow-sm action_confirm" title="Push Cronjob"><b><i class="fas fa-clock"></i> Push Cronjob</b></a>
        <?php } ?>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark mb-1">
                                <span style="font-size: 1rem;"><b>Total Message</b></span>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php if ($_SESSION['level'] == "admin") {
                                    echo countDB("messages");
                                } else {
                                    echo countDB('messages', 'user_id', $_SESSION['email']);
                                } ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fab fa-whatsapp fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark mb-1">
                                <span style="font-size: 1rem;"><b>All Contact</b></span>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php if ($_SESSION['level'] == "admin") {
                                    echo countDB('contacts');
                                } else {
                                    echo countDB('contacts', 'user_id', $_SESSION['email']);
                                } ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-address-book fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark mb-1">
                                <span style="font-size: 1rem;"><b>Contact Personal</b></span>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php if ($_SESSION['level'] == "admin") {
                                    echo countDB('contacts', 'contact_type', 'PERSONAL');
                                } else {
                                    echo countAndDB('contacts', 'user_id', $_SESSION['email'], 'contact_type', 'PERSONAL');
                                } ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-person fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Requests Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark mb-1">
                                <span style="font-size: 1rem;"><b>Contact Group</b></span>
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php if ($_SESSION['level'] == "admin") {
                                    echo countDB('contacts', 'contact_type', 'GROUP');
                                } else {
                                    echo countAndDB('contacts', 'user_id', $_SESSION['email'], 'contact_type', 'GROUP');
                                } ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-people-group fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-12">
            <!-- Area Chart -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Message Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 100%;">
                        <canvas id="myAreaChart"></canvas>
                    </div>
                    <hr>
                    Stastistic your send messages.
                    <code>send to personal and group</code>.
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-12">
            <!-- Area Chart -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Authentification <a class="text-danger" data-toggle="modal" data-target="#info-modal" title="guide info"><i class="fas fa-info-circle"></i></a></small></span></label></h6>
                </div>
                <div class="card-body">
                    <div class="form">
                        <div id="qrcode-container">
                            <img src="<?= $base_url ?>/public/files/loading.gif" alt="QR Code" id="qrcode" style="width: 250px;">
                            <div id="error">
                                <p class="text-danger font-weight-bold">500 Internal Server Error<br><small>"Error Connecting Server"</small></p>
                            </div>
                        </div>
                    </div>
                    <div id="logOut" class="py-1 text-center"></div>
                    <div class="py-1 text-center">
                        <ul id="logs" class="logs"></ul>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->
<?php require_once('./templates/footer.php'); ?>
<!-- Socket.io -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.5.4/socket.io.js" integrity="sha512-YeeA/Qxn5hYdkukScTCNNOhTrv1C2RubAGButJ1rmgQwZf/HdRaCGl+JAVkqsqaNRaYNHdheiuKKuPf9mDcqKg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    $(document).ready(function() {
        const qrcode = document.getElementById("qrcode");
        const socket = io('<?= getSingleValDB("setting", "id", "1", "url_gateway") ?>', {
            transports: ['websocket',
                'polling',
                'flashsocket'
            ]
        });
        socket.on("qr", src => {
            qrcode.setAttribute("src", src);
            qrcode.setAttribute("alt", "qrcode");
            $('#logOut').hide();
        });
        socket.on("qrstatus", src => {
            qrcode.setAttribute("src", src);
            qrcode.setAttribute("alt", "loading");
        });
        socket.on("logout", logout => {
            $('#logOut').html(`<form action="" method="POST">
                            <?= csrf_input() ?>
                            <input type="hidden" name="logout_session" value="logout_session" />
                            <button id="logout-session" class="text-center btn btn-sm btn-danger" title="Logout Bot Session"><b><i class="fas fa-sign-out-alt"></i> Logout Session</b></button>
                        </form>`);
        })
        socket.on("log", log => {
            $('#logs').prepend($('<li>').text(log));
            $('#error').hide();
            console.log(log);
        })
    });
</script>
<script>
    var delayed;
    var ctx = document.getElementById("myAreaChart");
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
                    data: [<?php echo $total_success; ?>],
                    borderColor: "rgb(22, 160, 133)",
                    borderWidth: 2,
                    borderRadius: 4,
                    pointBorderWidth: 4,
                    lineTension: 0.2,
                },
                {
                    label: "Messages Waiting",
                    data: [<?php echo $total_waiting; ?>],
                    borderColor: "rgb(241, 196, 15)",
                    borderWidth: 2,
                    borderRadius: 4,
                    pointBorderWidth: 4,
                    lineTension: 0.2,
                },
                {
                    label: "Messages Failed",
                    data: [<?php echo $total_failed; ?>],
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
</script>
</body>

</html>