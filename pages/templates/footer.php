        <!-- Footer -->
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>Create by: <a href="https://goican.id/">goican.id</a> Development - <b class="text-primary">Whatsapp Bot Single Device <i class="fab fa-whatsapp"></i></b></span>
                </div>
            </div>
        </footer>
        <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

        </div>
        <!-- End of Page Wrapper -->
        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>

        <!-- About Modal-->
        <div class="modal fade" id="aboutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold" id="exampleModalLabel">About Application</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <p>
                            <span class="font-weight-bold">Whatsapp Bot Api.</span><br>
                            Application Version: 1.0.0 (Beta).<br>
                            Published: <a href="https://goican.id/">www.goican.id - www.goican.com</a><br>
                            Created by: <a href="https://linkedin.com/amriarendy">amriarendy</a><br>
                            <a href="https://instagram.com/amriarendy90" title="instagram"><i class="fab fa-instagram"></i></a>
                            <a href="https://facebook.com/amriarendy.desbintra" title="facebook"><i class="fab fa-facebook"></i></a>
                            <a href="https://api.whatsapp.com/send?phone=6282246686900" title="whatsapp"><i class="fab fa-whatsapp"></i></a>
                            <a href="https://linkedin.com/in/amriarendy" title="linkedin"><i class="fab fa-linkedin"></i></a>
                            <a href="https://github.com/amriarendy" title="github"><i class="fab fa-github"></i></a>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <a href="https://api.whatsapp.com/send?phone=6282246686900&text=Hallo%20admin%20mau%20beli.." class="btn btn-success btn-icon-split font-weight-bold" title="Beli Aplikasi">
                            <span class="icon text-white-50">
                                <i class="fa fa-dollar"></i>
                            </span>
                            <span class="text">Purchase</span>
                        </a>
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- About Modal-->

        <!-- quick info -->
        <div class="modal fade" id="info-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold" id="exampleModalLabel"><i class="fas fa-info-circle text-info"></i> Quick Info</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body text-center" style="max-height: 500px; overflow-y: auto;">
                        <p>
                        <h5 style="text-align: center; "><b>Cara Create Sessoin Whataspp</b></h5>
                        <ul>
                            <li style="text-align: justify;">Silahkan tunggu QR Qode muncul dengan pesan: "QR Code received, scan please!".</li>
                            <li style="text-align: justify;">Buka aplikasi whatsapp Android / IOS anda.</li>
                            <li style="text-align: justify;">Silahkan klik&nbsp; <i class="fas fa-ellipsis-v"></i> (titik 3) dan pilih <b>Linked Device</b> kemudian Scan QR Code.</li>
                            <li style="text-align: justify;">Tunggu sampai proses authentifikasi selesai.</li>
                        </ul>
                        <p style="text-align: center;"><img src="<?= $base_url ?>/public/files/linked-device.gif" style="width: 100%;"><br></p>
                        <h5 style="text-align: center;"><span style="font-weight: bolder;">Cara Logout Sessoin Whataspp</span></h5>
                        <ul>
                            <li style="text-align: justify;">Pada halaman Dashboard silahkan untuk klik tombol ini <button class="text-center btn btn-sm btn-danger"><b><i class="fas fa-sign-out-alt"></i> Logout Session</b></button>, untuk mengakhiri session whatsapp anda.</li>
                            <li style="text-align: justify;">Atau anda bisa membuka menu di aplikasi whatsapp Android / IOS anda dengan klik &nbsp; <i class="fas fa-ellipsis-v"></i> (titik 3) , kemudian pilih Linked Device dan pilih keluar. Session whatsapp anda akan keluar dari Whatsapp Bot Api ini.</li>
                        </ul>
                        <hr>

                        <h5 style="text-align: center; "><b>Informasi Penting Lainya</b></h5>
                        <ul>
                            <li style="text-align: justify;">Whatsapp Bot Gateway akan mengirimkan pesan anda dalam per 300 pesan setiap 1 - 5 menit sekali.</li>
                            <li style="text-align: justify;">Untuk Rest API bisa baca disini <a href="<?= $base_url ?>pages/docs.php">docs;</a></li>
                            <li style="text-align: justify;">ID Group bisa ambil melalui menu Master Contact.</li>
                            <li style="text-align: justify;">Nomor ID Group gunakan @g.us atau @s.whatsapp.net</li>
                            <li style="text-align: justify;">Extentsion support: jpg, jpeg, png, pdf, dan mp3 dengan max 2mb.</li>
                            <li style="text-align: justify;">URL Media bisa diambil melalui google atau website lainya, pastikan url belakang menggunakan extension yang disupport aplikasi.</li>
                        </ul>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- quick info -->

        <!-- Logout Modal-->
        <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        <a class="btn btn-primary" href="<?= $base_url ?>logout.php">Logout</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Bootstrap core JavaScript-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.3.0/socket.io.js" crossorigin="anonymous"></script>
        <script src="<?= $base_url ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <!-- Core plugin JavaScript-->
        <script src="<?= $base_url ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
        <!-- Custom scripts for all pages-->
        <script src="<?= $base_url ?>/assets/js/sb-admin-2.min.js"></script>
        <script src="<?= $base_url ?>/assets/vendor/datatables/jquery.dataTables.min.js"></script>
        <script src="<?= $base_url ?>/assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>
        <script src="<?= $base_url ?>/assets/vendor/toastr/toastr.min.js"></script>
        <!-- Sweetalert 2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.all.min.js" integrity="sha256-S/HO+Ru8zrLDmcjzwxjl18BQYDCvFDD7mPrwJclX6U8=" crossorigin="anonymous"></script>
        <!-- Select2 Vendor  -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <!-- Page level plugins -->
        <script src="<?= $base_url ?>/assets/vendor/chart.js/Chart.min.js"></script>
        <script>
            $(document).ready(function() {
                $('.select2').select2({
                    dropdownAutoWidth: true
                });
            });
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
            $(document).ready(function() {
                $('.delete_confirm').on('click', function() {
                    var getLink = $(this).attr('href');
                    Swal.fire({
                        title: "Are you sure to delete action?",
                        icon: "warning",
                        showCancelButton: true,
                        showCancelButton: true,
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then(result => {
                        if (result.isConfirmed) {
                            window.location.href = getLink
                        }
                    })
                    return false;
                });
            });
            $(document).ready(function() {
                $('.action_confirm').on('click', function() {
                    var getLink = $(this).attr('href');
                    Swal.fire({
                        title: "Are you sure to confirm action?",
                        icon: "warning",
                        showCancelButton: true,
                        showCancelButton: true,
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Go it!'
                    }).then(result => {
                        if (result.isConfirmed) {
                            window.location.href = getLink
                        }
                    })
                    return false;
                });
            });
            <?php
            toastr_show();
            ?>
            $(document).ready(function() {
                $('#dataTable').DataTable();
            });
            $(document).ready(function() {
                $(".form").on("submit", function() {
                    $("#pageloader").fadeIn();
                    $(".btn .fa-spinner").show();
                    $(".btn .btn-text").html("Loading");
                });
            });
        </script>