<?php
require 'function.php';
require 'cek.php';

$tgl_mulai = isset($_GET['tgl_mulai']) ? $_GET['tgl_mulai'] : '';
$tgl_selesai = isset($_GET['tgl_selesai']) ? $_GET['tgl_selesai'] : '';
?>
<html>

<head>
    <title>Barang Masuk</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
</head>

<body>
    <div class="container">
        <h2>Barang Masuk</h2>
        <h4>(Inventory)</h4>
        <div class="data-tables datatable-dark">

            <table class="table table-bordered" id="mauexport" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Barang</th>
                        <th>Penerima</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    if ($tgl_mulai && $tgl_selesai) {
                        // Query dengan date range menggunakan prepared statements
                        $stmt = $conn->prepare("SELECT * FROM masuk m, stock s WHERE s.idbarang = m.idbarang AND tanggal BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)");
                        $stmt->bind_param("ss", $tgl_mulai, $tgl_selesai);
                        $stmt->execute();
                        $ambilsemuadatastock = $stmt->get_result();
                    } else {
                        $ambilsemuadatastock = mysqli_query($conn, "SELECT * FROM masuk m, stock s WHERE s.idbarang = m.idbarang");
                    }

                    while ($data = mysqli_fetch_array($ambilsemuadatastock)) {
                        $tanggal = $data['tanggal'];
                        $namabarang = $data['Namabarang'];
                        $keterangan = $data['keterangan'];
                        $qty = $data['qty'];
                    ?>

                        <tr>
                            <td><?php echo $tanggal; ?></td>
                            <td><?php echo $namabarang; ?></td>
                            <td><?php echo $keterangan; ?></td>
                            <td><?php echo $qty; ?></td>
                        </tr>
                    <?php
                    };
                    ?>

                </tbody>
            </table>
        </div>

        <script>
            $(document).ready(function() {
                $('#mauexport').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        'excel', 'pdf', 'print'
                    ]
                });
            });
        </script>

        <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.flash.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js"></script>

</body>

</html>