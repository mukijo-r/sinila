    <?php
    require_once 'vendor/autoload.php';
    require 'config.php';

    use PhpOffice\PhpSpreadsheet\Worksheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    session_start();
    //Koneksi ke database
    if (isset($_SESSION['user'])) {
        $username = $_SESSION['user'];
    } else {
        // Pengguna tidak masuk. Lakukan sesuatu, seperti mengarahkan mereka kembali ke halaman login.
        header('location: login.php');
    }
    
    if (isset($_SESSION['tahunAjar'])) {
        $tahunAjar = $_SESSION['tahunAjar'];
    }
    
    if (isset($_SESSION['kelas'])) {
        $kelas = $_SESSION['kelas'];
    }
    
    $queryUser = mysqli_query($conn, "SELECT nama_lengkap FROM users WHERE username = '$username'");
    $rowUser = mysqli_fetch_array($queryUser);
    $namaUser = $rowUser['nama_lengkap'];

    $conn = mysqli_connect("localhost:3306","root","","sdk");

    //1. login
    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $tahunAjar = $_POST['tahunAjar'];
        $kelas = $_POST['kelas'];
        // Dapatkan kata sandi terenkripsi dari database
        $result = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
        if ($row = mysqli_fetch_assoc($result)) {
            $hashedPassword = $row['password'];

            // Periksa apakah kata sandi yang dimasukkan sesuai dengan yang terenkripsi
            if (password_verify($password, $hashedPassword)) {
                // Kata sandi cocok, beri izin login
                $_SESSION['user'] = $username; // Simpan nama user dalam sesi
                $_SESSION['log'] = 'True';
                $_SESSION['previous_user'] = $username;
                $_SESSION['tahunAjar'] = $tahunAjar;
                $_SESSION['kelas'] = $kelas;
                header('location:index.php');
            } else {
                // Kata sandi tidak cocok, arahkan kembali ke halaman login
                header('location:login.php');
            }
        } else {
            // Tidak ada akun dengan username tersebut
            header('location:login.php');
        }
    }
  
    // 2. Tambah Mapel
    if(isset($_POST['tambahMapel'])){
        $mapel = $_POST['mapel'];

        try {
            $queryMapel = "INSERT INTO `mapel`(`mapel`) VALUES ('$mapel')";
                
            $insertMapel = mysqli_query($conn, $queryMapel);

            if (!$insertMapel) {
                throw new Exception("Query insert gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * FROM mapel WHERE mapel='$mapel'");

            if ($result && mysqli_num_rows($result) === 1) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Tambah mata pelajaran berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:mapel.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak ditemukan setelah ditambahkan");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:mapel.php');
            exit;
        }
    }

    // 3. Edit Mapel
    if(isset($_POST['UbahMapel'])){
        $idMapel = $_POST['idMapel'];
        $newMapel = $_POST['mapelEdit'];

        try {
            $queryMapel = "UPDATE `mapel` SET `mapel`='$newMapel' WHERE `id_mapel`='$idMapel'";
                
            $updateMapel = mysqli_query($conn, $queryMapel);

            if (!$updateMapel) {
                throw new Exception("Query update gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * FROM mapel WHERE mapel='$newMapel'");

            if ($result && mysqli_num_rows($result) === 1) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Ubah mata pelajaran berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:mapel.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak ditemukan");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:mapel.php');
            exit;
        }
    }

    // 4. Tambah Nilai Mapel
    if(isset($_POST['tambahNilaiMapel'])){
        $semester = $_POST['semester'];
        $idSiswa = $_POST['siswa'];
        $idMapel = $_POST['mapel'];
        $lingkupMateri = $_POST['lingkupMateri'];
        $tujuanPembelajaran = $_POST['tujuanPembelajaran'];
        $nilai = $_POST['nilai'];

        $queryTahunAjar = mysqli_query($conn, "SELECT id_tahun_ajar FROM tahun_ajar WHERE tahun_ajar = '$tahunAjar'");
        $rowTahunAjar = mysqli_fetch_array($queryTahunAjar);
        $idTahunAjar = $rowTahunAjar['id_tahun_ajar'];

        try {
            $queryInsertNilaiMapel = "INSERT INTO `nilai_mapel`
            (`tanggal`, `id_tahun_Ajar`, `semester`, `id_siswa`, `id_mapel`, `lingkup_materi`, `tujuan_pembelajaran`, `nilai`, `guru_penilai`) 
            VALUES ('$tanggal','$idTahunAjar','$semester','$idSiswa','$idMapel','$lingkupMateri','$tujuanPembelajaran','$nilai','$namaUser')";
                
            $insertNilaiMapel = mysqli_query($conn, $queryInsertNilaiMapel);

            if (!$insertNilaiMapel) {
                throw new Exception("Query insert gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * 
            FROM nilai_mapel 
            WHERE 
            tanggal='$tanggal' AND
            id_tahun_Ajar='$idTahunAjar' AND
            semester='$semester' AND
            id_siswa='$idSiswa' AND
            id_mapel='$idMapel' AND
            lingkup_materi='$lingkupMateri' AND
            tujuan_pembelajaran='$tujuanPembelajaran' AND
            nilai='$nilai'            
            ");

            if ($result && mysqli_num_rows($result) === 1) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Tambah nilai berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_nilai_mapel.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak ditemukan setelah ditambahkan");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $queryInsertNilaiMapel . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:input_nilai_mapel.php');
            exit;
        }
    }

    // 5. Edit Nilai Mapel
    if(isset($_POST['ubahNilaiMapel'])){
        $idNilaiMapel = $_POST['idNilaiMapel'];
        $semester = $_POST['semester'];
        $idSiswa = $_POST['siswa'];
        $idMapel = $_POST['mapel'];
        $lingkupMateri = $_POST['lingkupMateri'];
        $tujuanPembelajaran = $_POST['tujuanPembelajaran'];
        $nilai = $_POST['nilai'];

        try {
            $queryUpdateNilaiMapel = "UPDATE `nilai_mapel` 
            SET 
            `semester`='$semester',
            `id_siswa`='$idSiswa',
            `id_mapel`='$idMapel',
            `lingkup_materi`='$lingkupMateri',
            `tujuan_pembelajaran`='$tujuanPembelajaran',
            `nilai`='$nilai'
            WHERE
            `id_nm`=$idNilaiMapel
            ";
            
            $updateNilaiMapel = mysqli_query($conn, $queryUpdateNilaiMapel);

            if (!$updateNilaiMapel) {
                throw new Exception("Query update gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $queryResult = "SELECT * FROM `nilai_mapel` 
            WHERE 
            `semester`='$semester' AND 
            `id_siswa`='$idSiswa' AND 
            `id_mapel`='$idMapel' AND 
            `lingkup_materi`='$lingkupMateri' AND 
            `tujuan_pembelajaran`='$tujuanPembelajaran' AND  
            `nilai`='$nilai'
            ";
            $result = mysqli_query($conn, $queryResult);

            if ($result && mysqli_num_rows($result) === 1) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Ubah kategori berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_nilai_mapel.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak ditemukan setelah diubah");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $queryUpdateNilaiMapel . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            echo $queryInsertTabung;
            header('location:input_nilai_mapel.php');
            exit;
        }
    }

    // 6. Hapus Nilai Mapel
    if(isset($_POST['hapusNilaiMapel'])){
        $idNilaiMapel = $_POST['idNilaiMapel'];

        try {
            $queryHapusNilaiMapel = "DELETE FROM `nilai_mapel` WHERE `id_nm`='$idNilaiMapel'";          
            $hapusNilaiMapel = mysqli_query($conn, $queryHapusNilaiMapel);

            if (!$hapusNilaiMapel) {
                throw new Exception("Query hapus gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * FROM `nilai_mapel` WHERE `id_nm`='$idNilaiMapel'");

            if ($result && mysqli_num_rows($result) === 0) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Hapus kategori berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_nilai_mapel.php');
                exit;
            } else {
                // Data masih ada dalam database, itu berarti gagal
                throw new Exception("Data masih ada setelah dihapus");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $queryHapusNilaiMapel . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:input_nilai_mapel.php');
            exit;
        }
    }

    // 7. Tambah Nilai Praktek
    if(isset($_POST['tambahNilaiPraktek'])){
        $semester = $_POST['semester'];
        $idSiswa = $_POST['siswa'];
        $kategoriPraktek = $_POST['kategoriPraktek'];
        $nilai = $_POST['nilai'];

        $queryTahunAjar = mysqli_query($conn, "SELECT id_tahun_ajar FROM tahun_ajar WHERE tahun_ajar = '$tahunAjar'");
        $rowTahunAjar = mysqli_fetch_array($queryTahunAjar);
        $idTahunAjar = $rowTahunAjar['id_tahun_ajar'];

        try {
            $queryInsertNilaiPraktek = "INSERT INTO `nilai_praktek`
            (`tanggal`, `id_tahun_Ajar`, `semester`, `id_siswa`, `kategori_praktek`, `nilai`, `guru_penilai`) 
            VALUES ('$tanggal','$idTahunAjar','$semester','$idSiswa','$kategoriPraktek','$nilai','$namaUser')";
                
            $insertNilaiPraktek = mysqli_query($conn, $queryInsertNilaiPraktek);

            if (!$insertNilaiPraktek) {
                throw new Exception("Query insert gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * 
            FROM nilai_praktek 
            WHERE 
            tanggal='$tanggal' AND
            id_tahun_Ajar='$idTahunAjar' AND
            semester='$semester' AND
            id_siswa='$idSiswa' AND
            kategori_praktek='$kategoriPraktek' AND
            nilai='$nilai'            
            ");

            if ($result && mysqli_num_rows($result) === 1) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Tambah nilai berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_nilai_praktek.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak ditemukan setelah ditambahkan");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $queryInsertNilaiPraktek . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:input_nilai_praktek.php');
            exit;
        }
    }

    // 8. Ubah Nilai Praktek
    if(isset($_POST['ubahNilaiPraktek'])){
        $idNilaiPraktek = $_POST['idNilaiPraktek'];
        $semester = $_POST['semester'];
        $idSiswa = $_POST['siswa'];
        $kategoriPraktek = $_POST['kategoriPraktek'];
        $nilai = $_POST['nilai'];

        try {
            $queryUpdateNilaiPraktek = "UPDATE `nilai_praktek` 
            SET 
            `semester`='$semester',
            `id_siswa`='$idSiswa',
            `kategori_praktek`='$kategoriPraktek',
            `nilai`='$nilai'
            WHERE
            `id_np`=$idNilaiPraktek
            ";
            
            $updateNilaiPraktek = mysqli_query($conn, $queryUpdateNilaiPraktek);

            if (!$updateNilaiPraktek) {
                throw new Exception("Query update gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $queryResult = "SELECT * FROM `nilai_praktek` 
            WHERE 
            `semester`='$semester' AND 
            `id_siswa`='$idSiswa' AND 
            `kategori_praktek`='$kategoriPraktek' AND  
            `nilai`='$nilai'
            ";
            $result = mysqli_query($conn, $queryResult);

            if ($result && mysqli_num_rows($result) === 1) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Ubah nilai praktek berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_nilai_praktek.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak ditemukan setelah diubah");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            echo $queryInsertTabung;
            header('location:input_nilai_praktek.php');
            exit;
        }
    }

    // 6. Hapus Nilai Praktek
    if(isset($_POST['hapusNilaiPraktek'])){
        $idNilaiPraktek = $_POST['idNilaiPraktek'];

        try {
            $queryHapusNilaiPraktek = "DELETE FROM `nilai_praktek` WHERE `id_np`='$idNilaiPraktek'";          
            $hapusNilaiPraktek = mysqli_query($conn, $queryHapusNilaiPraktek);

            if (!$hapusNilaiPraktek) {
                throw new Exception("Query hapus gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * FROM `nilai_praktek` WHERE `id_np`='$idNilaiPraktek'");

            if ($result && mysqli_num_rows($result) === 0) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Hapus kategori berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_nilai_mapel.php');
                exit;
            } else {
                // Data masih ada dalam database, itu berarti gagal
                throw new Exception("Data masih ada setelah dihapus");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $queryHapusNilaiMapel . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:input_nilai_mapel.php');
            exit;
        }
    } 

    // 7. Tambah Nilai Kepribadian
    if(isset($_POST['tambahNilaiKepribadian'])){
        $semester = $_POST['semester'];
        $idSiswa = $_POST['siswa'];
        $kategoriKepribadian = $_POST['kategoriKepribadian'];
        $nilai = $_POST['nilai'];

        $queryTahunAjar = mysqli_query($conn, "SELECT id_tahun_ajar FROM tahun_ajar WHERE tahun_ajar = '$tahunAjar'");
        $rowTahunAjar = mysqli_fetch_array($queryTahunAjar);
        $idTahunAjar = $rowTahunAjar['id_tahun_ajar'];

        try {
            $queryInsertNilaiKepribadian = "INSERT INTO `nilai_kepribadian`
            (`tanggal`, `id_tahun_Ajar`, `semester`, `id_siswa`, `kategori_kepribadian`, `nilai`, `guru_penilai`) 
            VALUES ('$tanggal','$idTahunAjar','$semester','$idSiswa','$kategoriKepribadian','$nilai','$namaUser')";
                
            $insertNilaiKepribadian = mysqli_query($conn, $queryInsertNilaiKepribadian);

            if (!$insertNilaiKepribadian) {
                throw new Exception("Query insert gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * 
            FROM nilai_kepribadian 
            WHERE 
            tanggal='$tanggal' AND
            id_tahun_Ajar='$idTahunAjar' AND
            semester='$semester' AND
            id_siswa='$idSiswa' AND
            kategori_kepribadian='$kategoriKepribadian' AND
            nilai='$nilai'            
            ");

            if ($result && mysqli_num_rows($result) === 1) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Tambah nilai berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_nilai_kepribadian.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak ditemukan setelah ditambahkan");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $queryInsertNilaiKepribadian . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:input_nilai_kepribadian.php');
            exit;
        }
    }

    // 8. Ubah Nilai Kepribadian
    if(isset($_POST['ubahNilaiKepribadian'])){
        $idNilaiKepribadian = $_POST['idNilaiKepribadian'];
        $semester = $_POST['semester'];
        $idSiswa = $_POST['siswa'];
        $kategoriKepribadian = $_POST['kategoriKepribadian'];
        $nilai = $_POST['nilai'];

        try {
            $queryUpdateNilaiKepribadian = "UPDATE `nilai_kepribadian` 
            SET 
            `semester`='$semester',
            `id_siswa`='$idSiswa',
            `kategori_kepribadian`='$kategoriKepribadian',
            `nilai`='$nilai'
            WHERE
            `id_nk`=$idNilaiKepribadian
            ";
            
            $updateNilaiKepribadian = mysqli_query($conn, $queryUpdateNilaiKepribadian);

            if (!$updateNilaiKepribadian) {
                throw new Exception("Query update gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $queryResult = "SELECT * FROM `nilai_kepribadian` 
            WHERE 
            `semester`='$semester' AND 
            `id_siswa`='$idSiswa' AND 
            `kategori_kepribadian`='$kategoriKepribadian' AND  
            `nilai`='$nilai'
            ";
            $result = mysqli_query($conn, $queryResult);

            if ($result && mysqli_num_rows($result) === 1) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Ubah nilai kepribadian berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_nilai_kepribadian.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak ditemukan setelah diubah");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            echo $queryInsertTabung;
            header('location:input_nilai_kepribadian.php');
            exit;
        }
    }

    // 6. Hapus Nilai Kepribadian
    if(isset($_POST['hapusNilaiKepribadian'])){
        $idNilaiKepribadian = $_POST['idNilaiKepribadian'];

        try {
            $queryHapusNilaiKepribadian = "DELETE FROM `nilai_kepribadian` WHERE `id_nk`='$idNilaiKepribadian'";          
            $hapusNilaiKepribadian = mysqli_query($conn, $queryHapusNilaiKepribadian);

            if (!$hapusNilaiKepribadian) {
                throw new Exception("Query hapus gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * FROM `nilai_kepribadian` WHERE `id_nk`='$idNilaiKepribadian'");

            if ($result && mysqli_num_rows($result) === 0) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Hapus nilai berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_nilai_kepribadian.php');
                exit;
            } else {
                // Data masih ada dalam database, itu berarti gagal
                throw new Exception("Data masih ada setelah dihapus");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $queryHapusNilaiKepribadian . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:input_nilai_kepribadian.php');
            exit;
        }
    }
    
    // 7. Tambah Nilai Catatan
    if(isset($_POST['tambahNilaiCatatan'])){
        $semester = $_POST['semester'];
        $idSiswa = $_POST['siswa'];
        $catatan = $_POST['catatan'];

        $queryTahunAjar = mysqli_query($conn, "SELECT id_tahun_ajar FROM tahun_ajar WHERE tahun_ajar = '$tahunAjar'");
        $rowTahunAjar = mysqli_fetch_array($queryTahunAjar);
        $idTahunAjar = $rowTahunAjar['id_tahun_ajar'];

        try {
            $queryInsertNilaiCatatan = "INSERT INTO `nilai_catatan`
            (`tanggal`, `id_tahun_Ajar`, `semester`, `id_siswa`, `catatan`, `guru_penilai`) 
            VALUES ('$tanggal','$idTahunAjar','$semester','$idSiswa','$catatan','$namaUser')";
                
            $insertNilaiCatatan = mysqli_query($conn, $queryInsertNilaiCatatan);

            if (!$insertNilaiCatatan) {
                throw new Exception("Query insert gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * 
            FROM nilai_catatan
            WHERE 
            tanggal='$tanggal' AND
            id_tahun_Ajar='$idTahunAjar' AND
            semester='$semester' AND
            id_siswa='$idSiswa' AND
            catatan='$catatan'            
            ");

            if ($result && mysqli_num_rows($result) === 1) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Tambah catatan berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_nilai_catatan.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak ditemukan setelah ditambahkan");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $queryInsertNilaiCatatan . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:input_nilai_catatan.php');
            exit;
        }
    }

    // 8. Ubah Nilai Catatan
    if(isset($_POST['ubahNilaiCatatan'])){
        $idNilaiCatatan = $_POST['idNilaiCatatan'];
        $semester = $_POST['semester'];
        $idSiswa = $_POST['siswa'];
        $catatan = $_POST['catatan'];

        $queryTahunAjar = mysqli_query($conn, "SELECT id_tahun_ajar FROM tahun_ajar WHERE tahun_ajar = '$tahunAjar'");
        $rowTahunAjar = mysqli_fetch_array($queryTahunAjar);
        $idTahunAjar = $rowTahunAjar['id_tahun_ajar'];

        try {
            $queryUpdateNilaiCatatan = "UPDATE `nilai_catatan` 
            SET
            `id_tahun_ajar`='$idTahunAjar',
            `semester`='$semester',
            `id_siswa`='$idSiswa',
            `catatan`='$catatan'
            WHERE 
            `id_nc`='$idNilaiCatatan'
            ";
                
            $updateNilaiCatatan = mysqli_query($conn, $queryUpdateNilaiCatatan);

            if (!$updateNilaiCatatan) {
                throw new Exception("Query update gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * 
            FROM nilai_catatan
            WHERE 
            tanggal='$tanggal' AND
            id_tahun_Ajar='$idTahunAjar' AND
            semester='$semester' AND
            id_siswa='$idSiswa' AND
            catatan='$catatan'            
            ");

            if ($result && mysqli_num_rows($result) === 1) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Ubah catatan berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_nilai_catatan.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak ditemukan setelah diubah");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $queryUpdateNilaiCatatan . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:input_nilai_catatan.php');
            exit;
        }
    }

    // 9. Hapus Nilai Catatan
    if(isset($_POST['hapusNilaiCatatan'])){
        $idNilaiCatatan = $_POST['idNilaiCatatan'];

        try {
            $queryHapusNilaiCatatan = "DELETE FROM `nilai_catatan` WHERE `id_nc`='$idNilaiCatatan'";          
            $hapusNilaiCatatan = mysqli_query($conn, $queryHapusNilaiCatatan);

            if (!$hapusNilaiCatatan) {
                throw new Exception("Query hapus gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * FROM `nilai_catatan` WHERE `id_nc`='$idNilaiCatatan'");

            if ($result && mysqli_num_rows($result) === 0) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Hapus nilai berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_nilai_catatan.php');
                exit;
            } else {
                // Data masih ada dalam database, itu berarti gagal
                throw new Exception("Data masih ada setelah dihapus");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:input_nilai_catatan.php');
            exit;
        }
    }

    // Ganti Password User
    if (isset($_POST['gantiPassword'])) {
        $username = $_POST['username'];
        $passwordLama = $_POST['passwordLama'];
        $password1 = $_POST['passwordBaru'];
        $password2 = $_POST['confirmPasswordBaru'];

        //Cek user lama 
        $checkPasswordQuery = "SELECT `password` FROM `users` WHERE `username` = '$username'";
        $checkPasswordResult = mysqli_query($conn, $checkPasswordQuery);
        $dataPassword = mysqli_fetch_assoc($checkPasswordResult);
        $password = $dataPassword['password'];

        if (password_verify($passwordLama, $password)) {
            //Cek password input sama
            if ($password1 == $password2) {
                    $password = password_hash($password1, PASSWORD_BCRYPT);
                
                // Coba jalankan query update
                $queryUpdateUser = "UPDATE `users` 
                SET `password`='$password'
                WHERE
                `username`='$username'
                ";
                $UpdateUser = mysqli_query($conn, $queryUpdateUser);

                $checkUserQuery = "SELECT * FROM `users` WHERE `username` = '$username' AND `password`='$password'";
                $checkUserResult = mysqli_query($conn, $checkUserQuery);

                // Setelah berhasil mengubah password
                if (mysqli_num_rows($checkUserResult) == 1) {

                    $sweetAlert = "Swal.fire({
                        title: 'Sukses!',
                        text: 'Password berhasil diubah.',
                        icon: 'success',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });";

                } else {
                    // Gagal menambahkan akun
                    $sweetAlert = "Swal.fire({
                        title: 'Gagal!',
                        text: 'Ubah password gagal.',
                        icon: 'error',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });";
                }
            } else {
                $sweetAlert = "Swal.fire({
                    title: 'Gagal!',
                    text: 'Password tidak sama. Ubah password gagal.',
                    icon: 'error',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });";
            }
        } else {
            $sweetAlert = "Swal.fire({
                title: 'Gagal!',
                text: 'Password lama salah. $passwordLamaEncrypted',
                icon: 'error',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false
            });";
        }
    }



    

?>