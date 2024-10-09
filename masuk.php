<?php
require 'function.php';
require 'cek.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Barang Masuk</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="index.php">Biro Kepesantrenan</a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <a class="nav-link" href="index.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Persediaan Barang
                        </a>
                        <a class="nav-link" href="masuk.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-box"></i></div>
                            Barang Masuk
                        </a>
                        <a class="nav-link" href="keluar.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-dolly"></i></div>
                            Barang Keluar
                        </a>
                        <a class="nav-link" href="logout.php">
                            keluar
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Proyek oleh:</div>
                    M. Babun Waseptian
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Barang Masuk</h1>
                    <div class="card mb-4">
                        <div class="card-header">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal">
                                Tambah Barang
                            </button>
                            <a href="exportmasuk.php?tgl_mulai=<?= isset($_POST['tgl_mulai']) ? $_POST['tgl_mulai'] : ''; ?>&tgl_selesai=<?= isset($_POST['tgl_selesai']) ? $_POST['tgl_selesai'] : ''; ?>" class="btn btn-info">Export Data</a>
                            <br>
                            <div class="mt-3">
                                <form method="POST" class="row g-3">
                                    <div class="col-auto">
                                        <input type="date" name="tgl_mulai" class="form-control" value="<?= isset($_POST['tgl_mulai']) ? $_POST['tgl_mulai'] : ''; ?>">
                                    </div>
                                    <div class="col-auto">
                                        <input type="date" name="tgl_selesai" class="form-control" value="<?= isset($_POST['tgl_selesai']) ? $_POST['tgl_selesai'] : ''; ?>">
                                    </div>
                                    <div class="col-auto">
                                        <button type="submit" name="filter_tgl" class="btn btn-info">Filter</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Nama Barang</th>
                                        <th>Gambar</th>
                                        <th>Penerima</th>
                                        <th>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (isset($_POST['filter_tgl'])) {
                                        $mulai = $_POST['tgl_mulai'];
                                        $selesai = $_POST['tgl_selesai'];

                                        if (!empty($mulai) && !empty($selesai)) {
                                            // Query dengan date range menggunakan prepared statements
                                            $stmt = $conn->prepare("SELECT * FROM masuk m, stock s WHERE s.idbarang = m.idbarang AND tanggal BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)");
                                            $stmt->bind_param("ss", $mulai, $selesai);
                                            $stmt->execute();
                                            $ambilsemuadatastock = $stmt->get_result();
                                        } else {
                                            $ambilsemuadatastock = mysqli_query($conn, "SELECT * FROM masuk m, stock s WHERE s.idbarang = m.idbarang");
                                        }
                                    } else {
                                        $ambilsemuadatastock = mysqli_query($conn, "SELECT * FROM masuk m, stock s WHERE s.idbarang = m.idbarang");
                                    }

                                    while ($data = mysqli_fetch_array($ambilsemuadatastock)) {
                                        $tanggal = $data['tanggal'];
                                        $namabarang = $data['Namabarang'];
                                        $keterangan = $data['keterangan'];
                                        $qty = $data['qty'];
                                        $gambar = $data['Gambar']; //cek data gambar
                                        if ($gambar == null) {
                                            $img = 'Tidak ada foto';
                                        } else {
                                            $img = '<img src="image/' . $gambar . '" width="100" height="100" alt="Gambar Barang">';
                                        }
                                    ?>
                                        <tr>
                                            <td><?= $tanggal; ?></td>
                                            <td><?= $namabarang; ?></td>
                                            <td><?= $img; ?></td>
                                            <td><?= $keterangan; ?></td>
                                            <td><?= $qty; ?></td>
                                        </tr>
                                    <?php
                                    };
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Hak Cipta &copy; TU BIKTREN</div>
                        <div>
                            <a href="#">Kebijakan Privasi</a>
                            &middot;
                            <a href="#">Syarat & Ketentuan</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
    <script src="//cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
</body>

<!-- Modal Tambah Barang -->
<div class="modal fade" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Header Modal -->
            <div class="modal-header">
                <h4 class="modal-title">Tambah Barang Masuk</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- Body Modal -->
            <form action="" method="post">
                <div class="modal-body">
                    <select name="barangnya" class="form-select" required>
                        <?php
                        $ambilsemuadatanya = mysqli_query($conn, "SELECT * FROM stock");
                        while ($fetcharray = mysqli_fetch_array($ambilsemuadatanya)) {
                            $namabarangnya = $fetcharray['Namabarang'];
                            $idbarangnya = $fetcharray['idbarang'];
                        ?>
                            <option value="<?= $idbarangnya; ?>"><?= $namabarangnya; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <br>
                    <input type="number" name="qty" class="form-control" placeholder="Jumlah Barang Masuk" required>
                    <br>
                    <input type="text" name="penerima" class="form-control" placeholder="Penerima" required>
                    <br>
                    <button type="submit" class="btn btn-primary" name="barangmasuk">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

</html>