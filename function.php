<?php
session_start();

// Membuat koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "stokbarang");

// Periksa koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Menambah barang baru
if (isset($_POST['addnewbarang'])) {
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $stok = 0; // Set stok ke 0

    // Gambar
    $allowed_extension = array('png', 'jpg');
    $nama = $_FILES['file']['name']; // Mengambil nama file gambar
    $dot = explode('.', $nama);
    $ekstensi = strtolower(end($dot)); // Mengambil ekstensi file
    $ukuran = $_FILES['file']['size']; // Mengambil ukuran file
    $file_tmp = $_FILES['file']['tmp_name']; // Lokasi sementara file

    // Penamaan file -> enkripsi
    $image = md5(uniqid($nama, true)) . time() . '.' . $ekstensi; // Nama file dienkripsi dengan ekstensi

    // Proses upload gambar
    if (in_array($ekstensi, $allowed_extension) === true) {
        if ($ukuran < 15000000) { // Ukuran maksimal 15MB
            move_uploaded_file($file_tmp, 'image/' . $image); // Pindahkan file ke direktori 'image'

            // Masukkan data ke database
            $addtotable = mysqli_query($conn, "INSERT INTO stock (namabarang, deskripsi, stock, Gambar) VALUES ('$namabarang', '$deskripsi', '$stok', '$image')");
            if ($addtotable) {
                header('Location:index.php');
                exit();
            } else {
                echo 'Gagal: ' . mysqli_error($conn);
                header('Location:index.php');
                exit();
            }
        } else {
            echo '<script>alert("Ukuran terlalu besar"); window.location.href="index.php";</script>';
        }
    } else {
        echo '<script>alert("File harus png/jpg"); window.location.href="index.php";</script>';
    }
}

// Menambah barang masuk
if (isset($_POST['barangmasuk'])) {
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    // Ambil data stok sekarang
    $cekstoksekarang = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang='$barangnya'");
    $ambildatanya = mysqli_fetch_array($cekstoksekarang);
    $stoksekarang = $ambildatanya['stock'];
    $tambahkanstoksekarangdenganquantity = $stoksekarang + $qty;

    // Masukkan data barang masuk
    $addtomasuk = mysqli_query($conn, "INSERT INTO masuk (idbarang, keterangan, qty) VALUES ('$barangnya', '$penerima', '$qty')");
    // Update stok barang
    $updatestokmasuk = mysqli_query($conn, "UPDATE stock SET stock='$tambahkanstoksekarangdenganquantity' WHERE idbarang='$barangnya'");
    if ($addtomasuk && $updatestokmasuk) {
        header('Location:masuk.php');
        exit();
    } else {
        echo 'Gagal: ' . mysqli_error($conn);
        header('Location:masuk.php');
        exit();
    }
}

// Menambah barang keluar
if (isset($_POST['addbarangkeluar'])) {
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    // Ambil data stok sekarang
    $cekstoksekarang = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang='$barangnya'");
    $ambildatanya = mysqli_fetch_array($cekstoksekarang);
    $stoksekarang = $ambildatanya['stock'];
    $tambahkanstoksekarangdenganquantity = $stoksekarang - $qty;

    // Masukkan data barang keluar
    $addtokeluar = mysqli_query($conn, "INSERT INTO keluar (idbarang, penerima, qty) VALUES ('$barangnya', '$penerima', '$qty')");
    // Update stok barang
    $updatestokkeluar = mysqli_query($conn, "UPDATE stock SET stock='$tambahkanstoksekarangdenganquantity' WHERE idbarang='$barangnya'");
    if ($addtokeluar && $updatestokkeluar) {
        header('Location:keluar.php');
        exit();
    } else {
        echo 'Gagal: ' . mysqli_error($conn);
        header('Location:keluar.php');
        exit();
    }
}

// Update info barang
if (isset($_POST['updatebarang'])) {
    $idb = $_POST['idb'];
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];

    // Gambar
    $allowed_extension = array('png', 'jpg');
    $nama = $_FILES['file']['name']; // Mengambil nama file gambar
    $dot = explode('.', $nama);
    $ekstensi = strtolower(end($dot)); // Mengambil ekstensi file
    $ukuran = $_FILES['file']['size']; // Mengambil ukuran file
    $file_tmp = $_FILES['file']['tmp_name']; // Lokasi sementara file

    // Penamaan file -> enkripsi
    $image = md5(uniqid($nama, true)) . time() . '.' . $ekstensi; // Nama file dienkripsi dengan ekstensi

    // Jika pengguna ingin mengubah gambar
    if ($ukuran > 0) {
        // Proses upload gambar baru
        if (in_array($ekstensi, $allowed_extension) === true) {
            if ($ukuran < 15000000) { // Ukuran maksimal 15MB
                move_uploaded_file($file_tmp, 'image/' . $image); // Pindahkan file ke direktori 'image'

                // Update data barang dengan gambar baru
                $update = mysqli_query($conn, "UPDATE stock SET namabarang='$namabarang', deskripsi='$deskripsi', Gambar='$image' WHERE idbarang='$idb'");
                if ($update) {
                    header('Location:index.php');
                    exit();
                } else {
                    echo 'Gagal: ' . mysqli_error($conn);
                }
            } else {
                echo '<script>alert("Ukuran terlalu besar"); window.location.href="index.php";</script>';
            }
        } else {
            echo '<script>alert("File harus png/jpg"); window.location.href="index.php";</script>';
        }
    } else {
        // Jika tidak ingin mengubah gambar, update tanpa mengubah gambar
        $update = mysqli_query($conn, "UPDATE stock SET namabarang='$namabarang', deskripsi='$deskripsi' WHERE idbarang='$idb'");
        if ($update) {
            header('Location:index.php');
            exit();
        } else {
            echo 'Gagal: ' . mysqli_error($conn);
        }
    }
}

// Hapus barang
if (isset($_POST['hapusbarang'])) {
    $idb = $_POST['idb'];

    // Ambil nama file gambar dari database
    $gambar = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang='$idb'");
    $get = mysqli_fetch_array($gambar);
    $image = $get['Gambar']; // Pastikan ini sesuai dengan nama kolom pada tabel database

    // Hapus gambar dari direktori
    $img_path = 'image/' . $image; // Path lengkap ke gambar
    if (file_exists($img_path)) {
        unlink($img_path); // Hapus gambar dari server
    } else {
        echo 'Gagal menghapus gambar: File tidak ditemukan.';
    }

    // Hapus data dari database
    if ($conn) {
        $delete = mysqli_query($conn, "DELETE FROM stock WHERE idbarang='$idb'");
        if ($delete) {
            header('Location:index.php');
            exit();
        } else {
            echo 'Gagal menghapus data: ' . mysqli_error($conn);
        }
    } else {
        echo 'Gagal: Koneksi database tidak ada.';
    }
}
