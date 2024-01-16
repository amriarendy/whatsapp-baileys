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
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-info-circle"></i><b>Information</b></h1>
    </div>
    <div class="row">
        <div class="col-lg-6 col-md-12 col-sm-12">
            <!-- Content Row -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">App Info</h6>
                </div>
                <div class="card-body">
                    <!-- Write Starter Content Here -->
                    <p>
                        <strong>Whatsapp Bot Gateway</strong>
                    </p>
                    <p>
                        Whatsapp Bot Gateway adalah sebuah program komputer atau sistem berbasis perangkat lunak yang dapat berinteraksi dengan pengguna Whatsapp, Whatsapp Bot Gateway dirancang untuk mengirim pesan dan merespon pesan dari lawan obrolan anda. Implementasi Aplikasi Whatsapp Bot ini berbagai tujuan, seperti layanan pengiriman pesan secara masal atau buk ke kontak anda dan group anda, merespons pesan dari lawan obrolan anda, memasarkan produk atau layanan anda ke calon konsumen anda, memberikan pesan secara broadcsat ke seluruh anggota organisasi anda dan mengintegrasikan Aplikasi yang sedang berjalan saat ini dengan memanfaatkan REST API dari kami, anda bisa melakukan pengiriman Kode OTP, Forgot Password, Account Verify dan Link Undangan untuk usaha anda.
                    </p>
                    <p>
                        &nbsp;
                    </p>
                    <p>
                        <strong>Kegunaan dan Manfaat</strong>
                    </p>
                    <ul>
                        <li>
                            Broadcast: Pengiriman pesan secara massal sangat mempermudah anda dalam menjangkau konsumen dan anggota tim anda dalam pemberitahuan informasi.
                        </li>
                        <li>
                            Notify: Pemberian pesan mengingat dan notifikasi tagihan, pembayaran, dll.
                        </li>
                        <li>
                            Autoreply: Membalas pesan dari lawan obrolan anda, Whatsapp anda akan mengirimkan pesan secara otomatis ketika lawan obrolan anda memberikan keyword yang sama seperti anda buat dan tersimpan dalam database. Anda bisa menambahkan media berupa gambar, foto, dokumen dan audio pada fitur ini.
                        </li>
                        <li>
                            Account Verify: Sebagai parameter verifikasi untuk daftar akun pada aplikasi anda.
                        </li>
                        <li>
                            Alternatif Code Verify: Pengiriman kode verifikasi contohnya: OTP, dan Reset Password.
                        </li>
                        <li>
                            Invitation: Bisa melalukan pengiriman undangan online secara broadcast.
                        </li>
                    </ul>
                    <p>
                        &nbsp;
                    </p>
                    <p>
                        <strong>Fitur Aplikasi</strong>
                    </p>
                    <ul>
                        <li>
                            Send Message: Fitur ini untuk mengirimkan pesan ke target whatsapp anda.
                        </li>
                        <li>
                            Send Schedule: Pengiriman pesan terjadwal ke nomor whatsapp.
                        </li>
                        <li>
                            Send Group: Pengiriman pesan ke group.
                        </li>
                        <li>
                            Send Group Schedule: Pengiriman pesan terjadwal ke group.
                        </li>
                        <li>
                            Autoreply: Pengiriman pesan secara otomatis, keyword dari lawan obrolan akan diresponse otomatis oleh bot whatsapp.
                        </li>
                        <li>
                            Send Message Template: Pembuatan template pesan, format pesan akan disimpan bisa digunakan kembali.
                        </li>
                        <li>
                            Master Contact: Import kontak PERSONAL dan GROUP menggunakan excel.
                        </li>
                        <li>
                            REST API: Integrasi REST API sebagai Third-Party Aplikasi anda.
                        </li>
                    </ul>
                    <!-- Write End Content Here -->
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-12 col-sm-12">
            <!-- Content Row -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Guide</h6>
                </div>
                <div class="card-body">
                    <!-- Write Starter Content Here -->
                    <div class="text-center">
                        <img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 40rem;" src="<?= $base_url ?>/public/files/linked-device.gif" alt="...">
                    </div>
                    <p>
                        <strong>Cara Authentifikasi Whatsapp</strong>
                    </p>
                    <ul>
                        <li>
                            Pada halaman dasbhoard di authentifiaction akan muncul QR Code.&nbsp;
                        </li>
                        <li>
                            Silahkan tunggu QR Code. Ketika ada pesan “QR Code received, scan please!” silahkan acan QR Code segera. (jangan reload halaman browser anda)
                        </li>
                        <li>
                            Buka aplikasi Whatsapp mobile (Android/IOS) anda.
                        </li>
                        <li>
                            Silahkan klik &nbsp; <i class="fas fa-ellipsis-v"></i> (titik 3), pilih Linked Device kemudian acan QR Code.
                        </li>
                        <li>
                            Mohon tunggu sampai proses authentifikasi berhasil.
                        </li>
                    </ul>
                    <p>
                        &nbsp;
                    </p>
                    <p>
                        <strong>Cara Logout Session Whatsapp</strong>
                    </p>
                    <ul>
                        <li>
                            Pada halaman dashboard silahkan untuk klik tombol logout session <button class="text-center btn btn-sm btn-danger"><b><i class="fas fa-sign-out-alt"></i> Logout Session</b></button>, untuk mengakhiri session pada Whatsapp Bot anda.
                        </li>
                        <li>
                            Atau anda bisa juga mengakhiri session anda melalui Whatsapp mobil anda dengan klik pada menu &nbsp; <i class="fas fa-ellipsis-v"></i> (titik 3), kemudian pilih Linked Device dan pilih keluar. Session anda akan mengakhiri session dari Aplikasi Whatsapp Bot ini.
                        </li>
                    </ul>
                    <p>
                        &nbsp;
                    </p>
                    <p>
                        <strong>Informasi Penting Lainnya</strong>
                    </p>
                    <ul>
                        <li>
                            Aplikasi Whatsapp Bot Gateway akan mengirimkan 50 pesan per satu menit (1 menit) semua pesan secara berurutan.
                        </li>
                        <li>
                            Sebelum mengirimkan pesan secara broadcast/schedule, silahkan untuk input kategori terlebih dahulu, kemudian input kontak yang ingin anda kirimkan secara massal.
                        </li>
                        <li>
                            Nomor kontak bisa diinput menggunakan fitur Import Excel. Pembuatan kontak silahkan untuk menambahkan format untuk nomor: “628******@g.us atau <a href="mailto:08******@g.us”">08******@g.us”</a> untuk group id gunakan format: “12345*****@g.us”. (Disclaimer: bisa juga menggunakan akhiran @s.whatsapp.net)
                        </li>
                        <li>
                            File Support Extention: jpg, jpeg, png, gif, pdf, dan mp3. File size max. 2MB
                        </li>
                        <li>
                            Pengiriman media bisa menggunakan 2 cara yaitu: mengambil filet melalui browser pada perangkat anda dan mengambil filet URL di internet.
                        </li>
                        <li>
                            Untuk penggunaan REST API bisa anda baca pada halaman <a href="<?= $base_url ?>pages/docs.php">docs;</a>.
                        </li>
                    </ul>
                    <!-- Write End Content Here -->
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