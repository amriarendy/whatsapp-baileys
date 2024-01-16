<?php
include_once("../helpers/connection.php");
include_once("../helpers/function.php");
$login = cekSession();
if ($login == 0) {
    toastr_set("mixin-error", "Forbiden access!!");
    redirect("../login.php");
}

require_once('./templates/header.php');
?>
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-fire text-danger"></i><b>Docs and API</b></h1>
    </div>
    <div class="row">
        <div class="col-lg-6 col-md-12 col-sm-12">
            <!-- Content Row -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Documentation</h6>
                </div>
                <div class="card-body">
                    <h4 style="text-align:center;">
                        <strong>DOCUMENTATION REST API WHATSAPP GATEWAY</strong>
                    </h4>
                    <div class="text-center">
                        <img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 40rem;" src="<?= $base_url ?>/public/files/dashboard.png" alt="...">
                    </div>
                    <!-- Starter Content -->
                    <p style="text-align:justify;">
                        <strong>Send Message</strong>
                    </p>
                    <p style="text-align:justify;">
                        Send Message Rest Api anda bisa mengirimkan pesan ke whatsapp target dengan waktu terkini. untuk send message anda bisa mengirimkan pesan media, berikut spesifikasi parameternya;
                    </p>
                    <figure class="table">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>
                                        <strong>Endpoint:</strong>
                                    </td>
                                    <td>
                                        : <?= $base_url ?>api/send-message.php
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Method</strong>
                                    </td>
                                    <td>
                                        : POST
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </figure>
                    <p>
                        Method yang digunakan adalah Methode POST dengan Endpoint <a target="_blank" rel="noopener noreferrer" href="<?= $base_url ?>api/send-message.php."><?= $base_url ?>api/send-message.php.</a> Berikut parameter boda dan validasinya.
                    </p>
                    <figure class="table">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>
                                        <strong>api_key</strong>
                                    </td>
                                    <td>
                                        “YOUR_APIKEY”
                                    </td>
                                    <td>
                                        “string”, “required *”
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>number</strong>
                                    </td>
                                    <td>
                                        “628**** or 08****”
                                    </td>
                                    <td>
                                        “string”, “required *”
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>message</strong>
                                    </td>
                                    <td>
                                        “YOUR MESSAGE CONTENT”
                                    </td>
                                    <td>
                                        “string”, “required *”
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>url_media</strong>
                                    </td>
                                    <td>
                                        “YOUR MEDIA URL”
                                    </td>
                                    <td>
                                        “string”, “optional”
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </figure>

                    <p>
                        &nbsp;
                    </p>
                    <p>
                        <strong>Send Schedule</strong>
                    </p>
                    <p>
                        Send Message Schedule pengiriman pesan akan dijadwalkan oleh client, dan dikirim ke target sesuai dengan penanggalan client. Berikut spesifikasi parameternya;
                    </p>
                    <figure class="table">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>
                                        Endpoint
                                    </td>
                                    <td>
                                        : <?= $base_url ?>api/send-schedule.php
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Method
                                    </td>
                                    <td>
                                        : POST
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </figure>
                    <p>
                        Method yang digunakan adalah Methode POST dengan Endpoint <a target="_blank" rel="noopener noreferrer" href="<?= $base_url ?>api/send-message.php."><?= $base_url ?>api/send-message.php.</a> Berikut parameter boda dan validasinya.
                    </p>
                    <figure class="table">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>
                                        api_key
                                    </td>
                                    <td>
                                        “YOUR_APIKEY”
                                    </td>
                                    <td>
                                        “string”, “required *”
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        number
                                    </td>
                                    <td>
                                        “628**** or 08****”
                                    </td>
                                    <td>
                                        “string”, “required *”
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        message
                                    </td>
                                    <td>
                                        “YOUR MESSAGE CONTENT”
                                    </td>
                                    <td>
                                        “string”, “required *”
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        url_media
                                    </td>
                                    <td>
                                        “YOUR MEDIA URL”
                                    </td>
                                    <td>
                                        “string”, “optional”
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        date
                                    </td>
                                    <td>
                                        “2023-09-05”
                                    </td>
                                    <td>
                                        “date("Y-m-d")”, “required *”
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        time
                                    </td>
                                    <td>
                                        "10:09:54"
                                    </td>
                                    <td>
                                        “date("H:i:s")”, “required *”
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </figure>
                    <p>
                        &nbsp;
                    </p>
                    <p style="text-align:justify;">
                        <strong>Send Group</strong>
                    </p>
                    <p style="text-align:justify;">
                        Send Group Rest Api anda bisa mengirimkan pesan ke whatsapp group target dengan waktu terkini. untuk send message anda bisa mengirimkan pesan media untuk pengambilan id_group bisa dilihat pada halaman Master Contact dengan kategori GROUP, berikut spesifikasi parameternya;
                    </p>
                    <figure class="table">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>
                                        <strong>Endpoint:</strong>
                                    </td>
                                    <td>
                                        : <?= $base_url ?>api/send-group.php
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Method</strong>
                                    </td>
                                    <td>
                                        : POST
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </figure>
                    <p>
                        Method yang digunakan adalah Methode POST dengan Endpoint <a target="_blank" rel="noopener noreferrer" href="<?= $base_url ?>api/send-message.php."><?= $base_url ?>api/send-message.php.</a> Berikut parameter boda dan validasinya.
                    </p>
                    <figure class="table">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>
                                        <strong>api_key</strong>
                                    </td>
                                    <td>
                                        “YOUR_APIKEY”
                                    </td>
                                    <td>
                                        “string”, “required *”
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>id_group</strong>
                                    </td>
                                    <td>
                                        “123456****@g.us”
                                    </td>
                                    <td>
                                        “string”, “required *”
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>message</strong>
                                    </td>
                                    <td>
                                        “YOUR MESSAGE CONTENT”
                                    </td>
                                    <td>
                                        “string”, “required *”
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>url_media</strong>
                                    </td>
                                    <td>
                                        “YOUR MEDIA URL”
                                    </td>
                                    <td>
                                        “string”, “optional”
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </figure>

                    <p>
                        &nbsp;
                    </p>
                    <p>
                        <strong>Send Group Schedule</strong>
                    </p>
                    <p>
                        Send Message Schedule pengiriman pesan akan dijadwalkan oleh client, dan dikirim ke target sesuai dengan penanggalan client. untuk pengambilan id_group bisa dilihat pada halaman Master Contact dengan kategori GROUP Berikut spesifikasi parameternya;
                    </p>
                    <figure class="table">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>
                                        Endpoint
                                    </td>
                                    <td>
                                        : <?= $base_url ?>api/send-group-schedule.php
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Method
                                    </td>
                                    <td>
                                        : POST
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </figure>
                    <p>
                        Method yang digunakan adalah Methode POST dengan Endpoint <a target="_blank" rel="noopener noreferrer" href="<?= $base_url ?>api/send-message.php."><?= $base_url ?>api/send-group-message.php.</a> Berikut parameter boda dan validasinya.
                    </p>
                    <figure class="table">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>
                                        api_key
                                    </td>
                                    <td>
                                        “YOUR_APIKEY”
                                    </td>
                                    <td>
                                        “string”, “required *”
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        number
                                    </td>
                                    <td>
                                        “123456****@g.us”
                                    </td>
                                    <td>
                                        “string”, “required *”
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        message
                                    </td>
                                    <td>
                                        “YOUR MESSAGE CONTENT”
                                    </td>
                                    <td>
                                        “string”, “required *”
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        url_media
                                    </td>
                                    <td>
                                        “YOUR MEDIA URL”
                                    </td>
                                    <td>
                                        “string”, “optional”
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        date
                                    </td>
                                    <td>
                                        “2023-09-05”
                                    </td>
                                    <td>
                                        “date("Y-m-d")”, “required *”
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        time
                                    </td>
                                    <td>
                                        "10:09:54"
                                    </td>
                                    <td>
                                        “date("H:i:s")”, “required *”
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </figure>

                    <p>
                        &nbsp;
                    </p>
                    <p>
                        <strong>Response</strong>
                    </p>
                    <p>
                        Berikut adalah response json dari request Rest API;
                    </p>
                    <div style="background: #ffffff; overflow:auto;width:auto;border:solid gray;border-width:.1em .1em .1em .8em;padding:.2em .6em;">
                        <pre style="margin: 0; line-height: 125%"><span style="color: #507090">
<pre><code class="language-plaintext">{"status":200,"message":"Success: Sent anda create message"}
{"status":401,"message":"Unathorized: Invalid API KEY"}
{"status":415,"message":"Unsupported Media Type: Ext support: jpg, jpeg, png anda gif"}
{"status":422,"message":"Unprocessable Entity: Wrong parameters!"}
{"status":500,"message":"Internal Server Error: Something went wrong!"}</code></pre>
                    </div>
                    <p>
                        dan berikut adalah daftar response dengan kode status dan keterangannya
                    </p>
                    <figure class="table">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>
                                        <p style="text-align:center;">
                                            <strong>KODE STATUS</strong>
                                        </p>
                                    </td>
                                    <td>
                                        <p style="text-align:center;">
                                            <strong>MESSAGE</strong>
                                        </p>
                                    </td>
                                    <td>
                                        <p style="text-align:center;">
                                            <strong>KETERANGAN</strong>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="text-align:center;">
                                            200
                                        </p>
                                    </td>
                                    <td>
                                        “Success: Sent anda create message”
                                    </td>
                                    <td>
                                        <p style="text-align:center;">
                                            <span style="color:#558b2f;"><strong>sukses</strong></span>
                                        </p>
                                        <p style="text-align:center;">
                                            <span style="font-size:12px;">“request berhasil diterima server”</span>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="text-align:center;">
                                            401
                                        </p>
                                    </td>
                                    <td>
                                        “Unathorized: Invalid API KEY”
                                    </td>
                                    <td>
                                        <p style="text-align:center;">
                                            <span style="color:#e53935;"><strong>gagal</strong></span>
                                        </p>
                                        <p style="text-align:center;">
                                            <span style="font-size:12px;">“input api key anda tidak terdaftar atau salah”</span>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="text-align:center;">
                                            415
                                        </p>
                                    </td>
                                    <td>
                                        “Unsupported Media Type: Ext support: jpg, jpeg, png anda gif”
                                    </td>
                                    <td>
                                        <p style="text-align:center;">
                                            <span style="color:#e53935;"><strong>gagal</strong></span>
                                        </p>
                                        <p style="text-align:center;">
                                            <span style="font-size:12px;">“hanya support dengan extention file tertentu”</span>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="text-align:center;">
                                            422
                                        </p>
                                    </td>
                                    <td>
                                        “Unprocessable Entity: Wrong parameters!”
                                    </td>
                                    <td>
                                        <p style="text-align:center;">
                                            <span style="color:#e53935;"><strong>gagal</strong></span>
                                        </p>
                                        <p style="text-align:center;">
                                            <span style="font-size:12px;">“parameter ada salah atau ada yang kosong”</span>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="text-align:center;">
                                            500
                                        </p>
                                    </td>
                                    <td>
                                        “Internal Server Error: Something went wrong!”
                                    </td>
                                    <td>
                                        <p style="text-align:center;">
                                            <span style="color:#e53935;"><strong>gagal</strong></span>
                                        </p>
                                        <p style="text-align:center;">
                                            <span style="font-size:12px;">“terjadi kegagalan pada server”</span>
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </figure>

                    <p>
                        &nbsp;
                    </p>
                    <p>
                        <strong>Catatan</strong>
                    </p>
                    <p>
                    <ul>
                        <li>Sebelum mengirimkan pesan silakan untuk melakukan authentifikasi whatsapp dengan Scan QR Code di halaman dashboard. pesan akan dikirim menggunakan akun whatsapp anda.&nbsp;</li>
                        <li>Masukan nomor whatsapp dengan benar menggunakan format “628**** atau 08****” jangan gunakan karakter simbol seperti “+62****”.</li>
                        <li>Masukan id_group dengan benar dengan format 1234*****@g.us</li>
                        <li>API KEY bisa didapatkan pada halaman profile</li>
                        <li>Pengiriman dengan send-schedule (penjadwalan) akan dikirimkan sesuai jadwal yang dibuat oleh client, Dan pengiriman send-message (kirim pesan) akan dikirimkan pada waktu yang sekarang.</li>
                        <li>Pengiriman pesan akan diantrikan untuk 1-5 menit per 100 pesan.</li>
                    </ul>
                    </p>
                    <p>
                        &nbsp;
                    </p>
                    <!-- End Content -->
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-12 col-sm-12">
            <!-- Content Row -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Rest Api</h6>
                </div>
                <div class="card-body">
                    <h4 style="text-align:center;">
                        <strong>EXAMPLE REQUEST</strong>
                    </h4>
                    <div class="text-center">
                        <img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 40rem;" src="<?= $base_url ?>/public/files/dashboard.png" alt="...">
                    </div>
                    <!-- Starter Content -->
                    <p>
                        <strong>PHP LANGUAGE</strong>
                    </p>
                    <p>
                        <strong>Send Message</strong>
                    </p>
                    <div style="background: #ffffff; overflow:auto;width:auto;border:solid gray;border-width:.1em .1em .1em .8em;padding:.2em .6em;">
                        <pre style="margin: 0; line-height: 125%"><span style="color: #507090">
<pre><code class="language-php">$url = '<?= $base_url ?>api/send-message.php';
	$data = [
		"api_key" =&gt; "YOUR_API_KEY", // get your api key
		"number" =&gt; "628**** or 08****", // not use +628****
		"message" =&gt; "YOUR MESSAGE CONTENT HERE!", 
		"url_media" =&gt; "https://dataload.goican.id/assets/resource/promosi_goican.jpg" // optional, Ext: jpg, jpeg, png, gif
	];

$ch = curl_init();
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$result = curl_exec($ch);
curl_close($ch);
return json_decode($result, true);</code></pre>
                    </div>
                    <p>
                        &nbsp;
                    </p>
                    <p>
                        <strong>Send Schedule</strong>
                    </p>
                    <div style="background: #ffffff; overflow:auto;width:auto;border:solid gray;border-width:.1em .1em .1em .8em;padding:.2em .6em;">
                        <pre style="margin: 0; line-height: 125%"><span style="color: #507090">
<pre><code class="language-php">$url = '<?= $base_url ?>api/send-message.php';
	$data = [
		"api_key" =&gt; "YOUR_API_KEY", // get your api key
		"number" =&gt; "628**** or 08****", // not use +628****
		"message" =&gt; "YOUR MESSAGE CONTENT HERE!", 
		"url_media" =&gt; "https://dataload.goican.id/assets/resource/promosi_goican.jpg" // optional, Ext: jpg, jpeg, png, gif
		"date" =&gt; "2023-09-05" // date("Y-m-d")
		"time" =&gt; "10:52:23" // date("H:i:s")
	];

$ch = curl_init();
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$result = curl_exec($ch);
curl_close($ch);
return json_decode($result, true);</code></pre>
                    </div>
                    <p>
                        &nbsp;
                    </p>
                    <p>
                        <strong>Send Group</strong>
                    </p>
                    <div style="background: #ffffff; overflow:auto;width:auto;border:solid gray;border-width:.1em .1em .1em .8em;padding:.2em .6em;">
                        <pre style="margin: 0; line-height: 125%"><span style="color: #507090">
<pre><code class="language-php">$url = '<?= $base_url ?>api/send-message.php';
	$data = [
		"api_key" =&gt; "YOUR_API_KEY", // get your api key
		"id_group" =&gt; "12345****@g.us", // use @g.us
		"message" =&gt; "YOUR MESSAGE CONTENT HERE!", 
		"url_media" =&gt; "https://dataload.goican.id/assets/resource/promosi_goican.jpg" // optional, Ext: jpg, jpeg, png, gif
	];

$ch = curl_init();
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$result = curl_exec($ch);
curl_close($ch);
return json_decode($result, true);</code></pre>
                    </div>
                    <p>
                        &nbsp;
                    </p>
                    <p>
                        <strong>Send Group Schedule</strong>
                    </p>
                    <div style="background: #ffffff; overflow:auto;width:auto;border:solid gray;border-width:.1em .1em .1em .8em;padding:.2em .6em;">
                        <pre style="margin: 0; line-height: 125%"><span style="color: #507090">
<pre><code class="language-plaintext">$url = '<?= $base_url ?>api/send-message.php';
	$data = [
		"api_key" =&gt; "YOUR_API_KEY", // get your api key
		"id_group" =&gt; "12345****@g.us", // use @g.us
		"message" =&gt; "YOUR MESSAGE CONTENT HERE!", 
		"url_media" =&gt; "https://dataload.goican.id/assets/resource/promosi_goican.jpg" // optional, Ext: jpg, jpeg, png, gif
		"date" =&gt; "2023-09-05" // date("Y-m-d")
		"time" =&gt; "10:52:23" // date("H:i:s")
	];
$ch = curl_init();
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$result = curl_exec($ch);
curl_close($ch);
return json_decode($result, true);</code></pre>
                    </div>
                    <!-- End Content -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->
<?php require_once('./templates/footer.php'); ?>
</body>

</html>