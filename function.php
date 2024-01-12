<?php
    require_once 'vendor/autoload.php';
    require 'config.php';

    use PhpOffice\PhpSpreadsheet\Worksheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    session_start();
    if (isset($_SESSION['user'])) {
    $username = $_SESSION['user'];
    }

    if (isset($_SESSION['tahunAjar'])) {
    $tahunAjar = $_SESSION['tahunAjar'];
    }

    if (isset($_SESSION['kelas'])) {
    $kelas = $_SESSION['kelas'];
    }

    // 1. Ganti Password User
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

    // 4. Tambah Nilai Mapel Harian
    if(isset($_POST['tambahNilaiMapel'])){
        $semester = $_POST['semester'];
        $idMapel = $_POST['mapel'];
        $lingkupMateri = $_POST['lingkupMateri'];
        $tujuanPembelajaran = $_POST['tujuanPembelajaran'];
        $namaUser = $_POST['namaUser'];

        $queryTahunAjar = mysqli_query($conn, "SELECT id_tahun_ajar FROM tahun_ajar WHERE tahun_ajar = '$tahunAjar'");
        $rowTahunAjar = mysqli_fetch_array($queryTahunAjar);
        $idTahunAjar = $rowTahunAjar['id_tahun_ajar'];       

        // Initialize variables outside the loop
        $lastIdSiswa = null;
        $lastValue = null;

        try {
            $dataNilai = [];

            foreach ($_POST as $key => $value) {
                if (strpos($key, 'nilai_') !== false) {
                    $idSiswa = substr($key, 6);
                    $lastIdSiswa = $idSiswa; // Store the last id_siswa
                    $dataNilai[$idSiswa] = $value;
                    $lastValue = $value; // Store the last value
                    $queryInsertNilaiMapel = "INSERT INTO `nilai_mapel`
                        (`tanggal`, `id_tahun_Ajar`, `semester`, `id_siswa`, `kelas`, `id_mapel`, `lingkup_materi`, `tujuan_pembelajaran`, `nilai`, `guru_penilai`) 
                        VALUES ('$tanggal','$idTahunAjar','$semester','$idSiswa', '$kelas', '$idMapel','$lingkupMateri','$tujuanPembelajaran','$value','$namaUser')";
                        
                    $insertNilaiMapel = mysqli_query($conn, $queryInsertNilaiMapel);
                }
            }            

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
                id_siswa='$lastIdSiswa' AND
                kelas='$kelas' AND
                id_mapel='$idMapel' AND
                lingkup_materi='$lingkupMateri' AND
                tujuan_pembelajaran='$tujuanPembelajaran' AND
                nilai='$lastValue'            
            ");

            if ($result && mysqli_num_rows($result) === 1) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Tambah nilai harian berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_nilai_mapel.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak ditemukan setelah ditambahkan");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:input_nilai_mapel.php');
            exit;
        }
    }

    // 5. Edit Nilai Mapel Harian
    if(isset($_POST['ubahNilaiMapel'])){
        $idNilaiMapel = $_POST['idNilaiMapel'];
        $semester = $_POST['semester'];
        $idSiswa = $_POST['siswa'];
        $idMapel = $_POST['mapel'];
        $lingkupMateri = $_POST['lingkupMateri'];
        $tujuanPembelajaran = $_POST['tujuanPembelajaran'];
        $nilai = $_POST['nilai'];
        $namaUser = $_POST['namaUser'];

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
                $_SESSION['flash_message'] = 'Ubah nilai harian berhasil';
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
            header('location:input_nilai_mapel.php');
            exit;
        }
    }

    // 6. Hapus Nilai Mapel Harian
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
                $_SESSION['flash_message'] = 'Hapus nilai harian berhasil';
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
        $kategoriPraktek = $_POST['kategoriPraktek'];
        $namaUser = $_POST['namaUser'];

        $queryTahunAjar = mysqli_query($conn, "SELECT id_tahun_ajar FROM tahun_ajar WHERE tahun_ajar = '$tahunAjar'");
        $rowTahunAjar = mysqli_fetch_array($queryTahunAjar);
        $idTahunAjar = $rowTahunAjar['id_tahun_ajar'];

        $lastIdSiswa = null;
        $lastValue = null;

        try {

            $dataNilai = [];

            foreach ($_POST as $key => $value) {
                if (strpos($key, 'nilai_') !== false) {
                    $idSiswa = substr($key, 6);
                    $lastIdSiswa = $idSiswa; // Store the last id_siswa
                    $dataNilai[$idSiswa] = $value;
                    $lastValue = $value; // Store the last value
                    $queryInsertNilaiPraktek = "INSERT INTO `nilai_praktek`
                    (`tanggal`, `id_tahun_Ajar`, `semester`, `kelas`, `id_siswa`, `kategori_praktek`, `nilai`, `guru_penilai`) 
                    VALUES ('$tanggal','$idTahunAjar','$semester','$kelas','$idSiswa','$kategoriPraktek','$value','$namaUser')";
                        
                    $insertNilaiPraktek = mysqli_query($conn, $queryInsertNilaiPraktek);
                }
            } 

            if (!$insertNilaiPraktek) {
                throw new Exception("Query insert gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $queryCek = "SELECT * 
            FROM nilai_praktek 
            WHERE 
            tanggal='$tanggal' AND
            id_tahun_Ajar='$idTahunAjar' AND
            semester='$semester' AND
            kelas='$kelas' AND
            id_siswa='$lastIdSiswa' AND
            kategori_praktek='$kategoriPraktek' AND
            nilai='$lastValue'";
            
            $result = mysqli_query($conn, $queryCek);

            if ($result && mysqli_num_rows($result) === 1) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Tambah nilai praktek berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_nilai_praktek.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak ditemukan setelah ditambahkan");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
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
        $namaUser = $_POST['namaUser'];

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

    // 9. Hapus Nilai Praktek
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
                $_SESSION['flash_message'] = 'Hapus nilai praktek berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_nilai_praktek.php');
                exit;
            } else {
                // Data masih ada dalam database, itu berarti gagal
                throw new Exception("Data masih ada setelah dihapus");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $queryHapusNilaiMapel . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:input_nilai_praktek.php');
            exit;
        }
    } 

    // 10. Tambah Nilai Kepribadian
    if(isset($_POST['tambahNilaiKepribadian'])){
        $semester = $_POST['semester'];
        $kategoriKepribadian = $_POST['kategoriKepribadian'];
        $namaUser = $_POST['namaUser'];

        $queryTahunAjar = mysqli_query($conn, "SELECT id_tahun_ajar FROM tahun_ajar WHERE tahun_ajar = '$tahunAjar'");
        $rowTahunAjar = mysqli_fetch_array($queryTahunAjar);
        $idTahunAjar = $rowTahunAjar['id_tahun_ajar'];

        $lastIdSiswa = null;
        $lastValue = null;

        try {

            $dataNilai = [];

            foreach ($_POST as $key => $value) {
                if (strpos($key, 'nilai_') !== false) {
                    $idSiswa = substr($key, 6);
                    $lastIdSiswa = $idSiswa; // Store the last id_siswa
                    $dataNilai[$idSiswa] = $value;
                    $lastValue = $value; // Store the last value
                    $queryInsertNilaiKepribadian = "INSERT INTO `nilai_kepribadian`
                    (`tanggal`, `id_tahun_Ajar`, `semester`, `kelas`, `id_siswa`, `kategori_kepribadian`, `nilai`, `guru_penilai`) 
                    VALUES ('$tanggal','$idTahunAjar','$semester','$kelas','$idSiswa','$kategoriKepribadian','$value','$namaUser')";
                        
                    $insertNilaiKepribadian = mysqli_query($conn, $queryInsertNilaiKepribadian);
                }
            }

            if (!$insertNilaiKepribadian) {
                throw new Exception("Query insert gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $queryCek = "SELECT * 
            FROM nilai_kepribadian 
            WHERE 
            tanggal='$tanggal' AND
            id_tahun_Ajar='$idTahunAjar' AND
            semester='$semester' AND
            id_siswa='$lastIdSiswa' AND
            kategori_kepribadian='$kategoriKepribadian' AND
            nilai='$lastValue'            
            ";
            $result = mysqli_query($conn, $queryCek);

            if ($result && mysqli_num_rows($result) === 1) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Tambah nilai kepribadian berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_nilai_kepribadian.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak ditemukan setelah ditambahkan");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $queryCek . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:input_nilai_kepribadian.php');
            exit;
        }
    }

    // 11. Ubah Nilai Kepribadian
    if(isset($_POST['ubahNilaiKepribadian'])){
        $idNilaiKepribadian = $_POST['idNilaiKepribadian'];
        $semester = $_POST['semester'];
        $idSiswa = $_POST['siswa'];
        $kategoriKepribadian = $_POST['kategoriKepribadian'];
        $nilai = $_POST['nilai'];
        $namaUser = $_POST['namaUser'];

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

    // 12. Hapus Nilai Kepribadian
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
                $_SESSION['flash_message'] = 'Hapus nilai kepribadian berhasil';
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
    
    // 13. Tambah Nilai Catatan
    if(isset($_POST['tambahNilaiCatatan'])){
        $semester = $_POST['semester'];
        $namaUser = $_POST['namaUser'];

        $queryTahunAjar = mysqli_query($conn, "SELECT id_tahun_ajar FROM tahun_ajar WHERE tahun_ajar = '$tahunAjar'");
        $rowTahunAjar = mysqli_fetch_array($queryTahunAjar);
        $idTahunAjar = $rowTahunAjar['id_tahun_ajar'];

        $lastIdSiswa = null;
        $lastValue = null;

        try {
            
            $dataNilai = [];

            foreach ($_POST as $key => $value) {
                if (strpos($key, 'nilai_') !== false) {
                    $idSiswa = substr($key, 6);
                    $lastIdSiswa = $idSiswa; // Store the last id_siswa
                    $dataNilai[$idSiswa] = $value;
                    $lastValue = $value; // Store the last value
                    $queryInsertNilaiCatatan = "INSERT INTO `nilai_catatan`
                    (`tanggal`, `id_tahun_Ajar`, `semester`, `kelas`, `id_siswa`, `catatan`, `guru_penilai`) 
                    VALUES ('$tanggal','$idTahunAjar','$semester','$kelas','$idSiswa','$value','$namaUser')";
                        
                    $insertNilaiCatatan = mysqli_query($conn, $queryInsertNilaiCatatan);
                }
            }            
            
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
            kelas='$kelas' AND
            id_siswa='$lastIdSiswa' AND
            catatan='$lastValue'            
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
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:input_nilai_catatan.php');
            exit;
        }
    }

    // 14. Ubah Nilai Catatan
    if(isset($_POST['ubahNilaiCatatan'])){
        $idNilaiCatatan = $_POST['idNilaiCatatan'];
        $semester = $_POST['semester'];
        $idSiswa = $_POST['siswa'];
        $catatan = $_POST['catatan'];
        $namaUser = $_POST['namaUser'];

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
            $queryCek = "SELECT * 
            FROM nilai_catatan
            WHERE 
            id_tahun_Ajar='$idTahunAjar' AND
            semester='$semester' AND
            id_siswa='$idSiswa' AND
            catatan='$catatan'; ";
            $result = mysqli_query($conn, $queryCek);

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
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:input_nilai_catatan.php');
            exit;
        }
    }

    // 15. Hapus Nilai Catatan
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
                $_SESSION['flash_message'] = 'Hapus catatan berhasil';
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

    // 16. Tambah Absen
    if(isset($_POST['tambahAbsen'])){
        $tanggalAbsen = $_POST['tanggalAbsen'];
        $tanggalInput = date("Y-m-d", strtotime($tanggalAbsen));
        $semester = $_POST['semester'];
        $idSiswa = $_POST['siswa'];
        $absen = $_POST['absen'];
        $namaUser = $_POST['namaUser'];

        $queryTahunAjar = mysqli_query($conn, "SELECT id_tahun_ajar FROM tahun_ajar WHERE tahun_ajar = '$tahunAjar'");
        $rowTahunAjar = mysqli_fetch_array($queryTahunAjar);
        $idTahunAjar = $rowTahunAjar['id_tahun_ajar'];

        try {
            $queryInsertAbsen = "INSERT INTO `absensi`
            (`id_tahun_ajar`, `semester`, `kelas`, `id_siswa`, `tanggal`, `absen`, `guru_pencatat`) 
            VALUES 
            ('$idTahunAjar','$semester','$kelas','$idSiswa','$tanggalInput','$absen','$namaUser')";
                
            $insertAbsen = mysqli_query($conn, $queryInsertAbsen);

            if (!$insertAbsen) {
                throw new Exception("Query insert gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $query = "SELECT * 
            FROM absensi
            WHERE 
            `id_tahun_ajar`='$idTahunAjar' AND 
            `semester`='$semester' AND 
            `kelas`='$kelas' AND 
            `id_siswa`='$idSiswa' AND 
            `tanggal`='$tanggalInput' AND 
            `absen`='$absen' AND 
            `guru_pencatat`='$namaUser'";

            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Tambah absen berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_absensi.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak ditemukan setelah ditambahkan");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:input_absensi.php');
            exit;
        }
    }    

    // 17. Ubah Absen
    if(isset($_POST['ubahAbsen'])){
        $idAbsen = $_POST['idAbsen'];
        $tanggalAbsen = $_POST['tanggalAbsen'];
        $tanggalInput = date("Y-m-d", strtotime($tanggalAbsen));
        $semester = $_POST['semester'];
        $idSiswa = $_POST['siswa'];
        $absen = $_POST['absen'];
        $namaUser = $_POST['namaUser'];

        $queryTahunAjar = mysqli_query($conn, "SELECT id_tahun_ajar FROM tahun_ajar WHERE tahun_ajar = '$tahunAjar'");
        $rowTahunAjar = mysqli_fetch_array($queryTahunAjar);
        $idTahunAjar = $rowTahunAjar['id_tahun_ajar'];

        try {
            $queryUpdateAbsen = "UPDATE `absensi` 
            SET 
            `id_tahun_ajar`='$idTahunAjar',
            `semester`='$semester',
            `kelas`='$kelas',
            `id_siswa`='$idSiswa',
            `tanggal`='$tanggalInput',
            `absen`='$absen',
            `guru_pencatat`='$namaUser' 
            WHERE
            `id_absen`='$idAbsen'
            ";
                
            $updateAbsen = mysqli_query($conn, $queryUpdateAbsen);

            if (!$updateAbsen) {
                throw new Exception("Query update gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $query = "SELECT * 
            FROM absensi
            WHERE 
            `id_tahun_ajar`='$idTahunAjar' AND 
            `semester`='$semester' AND 
            `kelas`='$kelas' AND 
            `id_siswa`='$idSiswa' AND 
            `tanggal`='$tanggalInput' AND 
            `absen`='$absen' AND 
            `guru_pencatat`='$namaUser'";

            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Update absen berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_absensi.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak ditemukan setelah diubah");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $query . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:input_absensi.php');
            exit;
        }
    }
    
    // 18. Hapus Absen
    if(isset($_POST['hapusAbsen'])){
        $idAbsen = $_POST['idAbsen'];

        try {
            $queryHapusAbsen = "DELETE FROM `absensi` WHERE `id_absen`='$idAbsen'";          
            $hapusAbsen = mysqli_query($conn, $queryHapusAbsen);

            if (!$hapusAbsen) {
                throw new Exception("Query hapus gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * FROM `absensi` WHERE `id_absen`='$idAbsen'");

            if ($result && mysqli_num_rows($result) === 0) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Hapus absen berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_absensi.php');
                exit;
            } else {
                // Data masih ada dalam database, itu berarti gagal
                throw new Exception("Data masih ada setelah dihapus");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:input_absensi.php');
            exit;
        }
    }

    // 19. Tambah Status Naik/Tinggal Kelas
    if(isset($_POST['tambahKenaikkanKelas'])){
        $tanggalKenaikan = $_POST['tanggalKenaikan'];
        $tanggalInput = date("Y-m-d", strtotime($tanggalKenaikan));
        $semester = "Genap";
        $idSiswa = $_POST['siswa'];
        $kenaikan = $_POST['kenaikan']; 
        $namaUser = $_POST['namaUser'];       

        $queryTahunAjar = mysqli_query($conn, "SELECT id_tahun_ajar FROM tahun_ajar WHERE tahun_ajar = '$tahunAjar'");
        $rowTahunAjar = mysqli_fetch_array($queryTahunAjar);
        $idTahunAjar = $rowTahunAjar['id_tahun_ajar'];

        try {
            $queryInsertKenaikkanKelas = "INSERT INTO `kenaikan_kelas`
            (`tanggal`, `id_tahun_ajar`, `semester`, `kelas`, `id_siswa`, `status`, `guru_pencatat`) 
            VALUES 
            ('$tanggalInput ','$idTahunAjar','$semester','$kelas','$idSiswa','$kenaikan','$namaUser')";
                
            $insertKenaikkanKelas = mysqli_query($conn, $queryInsertKenaikkanKelas);

            if (!$insertKenaikkanKelas) {
                throw new Exception("Query insert gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * 
            FROM kenaikan_kelas
            WHERE 
            tanggal='$tanggalInput' AND
            id_tahun_Ajar='$idTahunAjar' AND
            semester='$semester' AND
            kelas='$kelas' AND
            id_siswa='$idSiswa' AND
            `status`='$kenaikan'            
            ");

            if ($result && mysqli_num_rows($result) === 1) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Tambah status berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_naik_kelas.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak ditemukan atau duplikat");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $queryInsertKenaikkanKelas . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:input_naik_kelas.php');
            exit;
        }
    }

    // 20. Ubah Status Naik/Tinggal Kelas
    if(isset($_POST['ubahKenaikkanKelas'])){
        $idKenaikkanKelas = $_POST['idKenaikkanKelas'];
        $tanggalKenaikan = $_POST['tanggalKenaikan'];
        $tanggalInput = date("Y-m-d", strtotime($tanggalKenaikan));
        $idSiswa = $_POST['siswa'];
        $kenaikan = $_POST['kenaikan']; 
        $namaUser = $_POST['namaUser'];       

        $queryTahunAjar = mysqli_query($conn, "SELECT id_tahun_ajar FROM tahun_ajar WHERE tahun_ajar = '$tahunAjar'");
        $rowTahunAjar = mysqli_fetch_array($queryTahunAjar);
        $idTahunAjar = $rowTahunAjar['id_tahun_ajar'];

        try {
            $queryUpdateKenaikkanKelas = "UPDATE `kenaikan_kelas` 
            SET             
            `tanggal`='$tanggalInput',
            `id_tahun_ajar`='$idTahunAjar',
            `kelas`='$kelas',
            `id_siswa`='$idSiswa',
            `status`='$kenaikan',
            `guru_pencatat`='$namaUser' 
            WHERE 
            `id_kn`='$idKenaikkanKelas'
            ";
                
            $updateKenaikkanKelas = mysqli_query($conn, $queryUpdateKenaikkanKelas);

            if (!$updateKenaikkanKelas) {
                throw new Exception("Query update gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * 
            FROM kenaikan_kelas
            WHERE 
            tanggal='$tanggalInput' AND
            id_tahun_Ajar='$idTahunAjar' AND
            kelas='$kelas' AND
            id_siswa='$idSiswa' AND
            `status`='$kenaikan'            
            ");

            if ($result && mysqli_num_rows($result) === 1) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Ubah status berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_naik_kelas.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak ditemukan atau duplikat");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $queryUpdateKenaikkanKelas . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:input_naik_kelas.php');
            exit;
        }
    }

    // 21. Hapus Status Naik/Tinggal Kelas
    if(isset($_POST['hapusKenaikkanKelas'])){
        $idKenaikanKelas = $_POST['idKenaikanKelas'];

        try {
            $queryHapusKenaikan = "DELETE FROM `kenaikan_kelas` WHERE `id_kn`='$idKenaikanKelas'";          
            $hapusKenaikan = mysqli_query($conn, $queryHapusKenaikan);

            if (!$hapusKenaikan) {
                throw new Exception("Query hapus gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * FROM `kenaikan_kelas` WHERE `id_kn`='$idKenaikanKelas'");

            if ($result && mysqli_num_rows($result) === 0) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Hapus status berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_naik_kelas.php');
                exit;
            } else {
                // Data masih ada dalam database, itu berarti gagal
                throw new Exception("Data masih ada setelah dihapus");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:input_naik_kelas.php');
            exit;
        }
    }

    // 22. Tambah Nilai Ujian
    if(isset($_POST['tambahNilaiUjian'])){
        $semester = $_POST['semester'];
        $idMapel = $_POST['mapel'];
        $ujian = $_POST['ujian'];
        $namaUser = $_POST['namaUser'];

        $queryTahunAjar = mysqli_query($conn, "SELECT id_tahun_ajar FROM tahun_ajar WHERE tahun_ajar = '$tahunAjar'");
        $rowTahunAjar = mysqli_fetch_array($queryTahunAjar);
        $idTahunAjar = $rowTahunAjar['id_tahun_ajar'];

        $lastIdSiswa = null;
        $lastValue = null;

        try {

            $dataNilai = [];

            foreach ($_POST as $key => $value) {
                if (strpos($key, 'nilai_') !== false) {
                    $idSiswa = substr($key, 6);
                    $lastIdSiswa = $idSiswa; // Store the last id_siswa
                    $dataNilai[$idSiswa] = $value;
                    $lastValue = $value; // Store the last value
                    $queryInsertNilaiUjian = "INSERT INTO `nilai_ujian`
                    (`tanggal`, `id_tahun_ajar`, `semester`, `id_siswa`, `kelas`, `id_mapel`, `ujian`, `nilai`, `guru_penilai`) 
                    VALUES ('$tanggal','$idTahunAjar','$semester','$idSiswa', '$kelas', '$idMapel','$ujian','$value','$namaUser')";
                        
                    $insertNilaiUjian = mysqli_query($conn, $queryInsertNilaiUjian);
                }
            }

            if (!$insertNilaiUjian) {
                throw new Exception("Query insert gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * 
            FROM nilai_ujian
            WHERE 
            tanggal='$tanggal' AND
            id_tahun_Ajar='$idTahunAjar' AND
            semester='$semester' AND
            id_siswa='$lastIdSiswa' AND
            kelas='$kelas' AND
            id_mapel='$idMapel' AND
            ujian='$ujian' AND
            nilai='$lastValue'            
            ");

            if ($result && mysqli_num_rows($result) <> 0) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Tambah nilai ujian berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_nilai_ujian.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak ditemukan setelah ditambahkan");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:input_nilai_ujian.php');
            exit;
        }
    }

    // 23. Edit Nilai Ujian
    if(isset($_POST['ubahNilaiUjian'])){
        $idNilaiUjian = $_POST['idNilaiUjian'];
        $semester = $_POST['semester'];
        $idSiswa = $_POST['siswa'];
        $idMapel = $_POST['mapel'];
        $ujian = $_POST['ujian'];
        $nilai = $_POST['nilai'];
        $namaUser = $_POST['namaUser'];

        try {
            $queryUpdateNilaiUjian = "UPDATE `nilai_ujian` 
            SET 
            `semester`='$semester',
            `id_siswa`='$idSiswa',
            `id_mapel`='$idMapel',
            `ujian`='$ujian',
            `nilai`='$nilai'
            WHERE
            `id_nu`=$idNilaiUjian
            ";
            
            $updateNilaiUjian = mysqli_query($conn, $queryUpdateNilaiUjian);

            if (!$updateNilaiUjian) {
                throw new Exception("Query update gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $queryResult = "SELECT * FROM `nilai_ujian` 
            WHERE 
            `semester`='$semester' AND 
            `id_siswa`='$idSiswa' AND 
            `id_mapel`='$idMapel' AND 
            `ujian`='$ujian' AND 
            `nilai`='$nilai'
            ";
            $result = mysqli_query($conn, $queryResult);

            if ($result && mysqli_num_rows($result) === 1) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Ubah nilai ujian berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_nilai_ujian.php');
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
            header('location:input_nilai_ujian.php');
            exit;
        }
    }

    // 24. Hapus Nilai Ujian
    if(isset($_POST['hapusNilaiUjian'])){
        $idNilaiUjian = $_POST['idNilaiUjian'];

        try {
            $queryHapusNilaiUjian = "DELETE FROM `nilai_ujian` WHERE `id_nu`='$idNilaiUjian'";          
            $hapusNilaiUjian = mysqli_query($conn, $queryHapusNilaiUjian);

            if (!$hapusNilaiUjian) {
                throw new Exception("Query hapus gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * FROM `nilai_ujian` WHERE `id_nu`='$idNilaiUjian'");

            if ($result && mysqli_num_rows($result) === 0) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Hapus nilai ujian berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_nilai_ujian.php');
                exit;
            } else {
                // Data masih ada dalam database, itu berarti gagal
                throw new Exception("Data masih ada setelah dihapus");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $queryHapusNilaiUjian . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:input_nilai_ujian.php');
            exit;
        }
    }

    // 25. Tambah Nilai Ulangan
    if(isset($_POST['tambahNilaiUlangan'])){
        $semester = $_POST['semester'];
        $idMapel = $_POST['mapel'];
        $lingkupMateri = $_POST['lingkupMateri'];
        $namaUser = $_POST['namaUser'];

        $queryTahunAjar = mysqli_query($conn, "SELECT id_tahun_ajar FROM tahun_ajar WHERE tahun_ajar = '$tahunAjar'");
        $rowTahunAjar = mysqli_fetch_array($queryTahunAjar);
        $idTahunAjar = $rowTahunAjar['id_tahun_ajar'];

        $lastIdSiswa = null;
        $lastValue = null;

        try {

            $dataNilai = [];

            foreach ($_POST as $key => $value) {
                if (strpos($key, 'nilai_') !== false) {
                    $idSiswa = substr($key, 6);
                    $lastIdSiswa = $idSiswa; // Store the last id_siswa
                    $dataNilai[$idSiswa] = $value;
                    $lastValue = $value; // Store the last value
                    $queryInsertNilaiUlangan = "INSERT INTO `nilai_ulangan`
                    (`tanggal`, `id_tahun_Ajar`, `semester`, `id_siswa`, `kelas`, `id_mapel`, `lingkup_materi`, `nilai`, `guru_penilai`) 
                    VALUES ('$tanggal','$idTahunAjar','$semester','$idSiswa', '$kelas', '$idMapel','$lingkupMateri','$value','$namaUser')";
                        
                    $insertNilaiUlangan = mysqli_query($conn, $queryInsertNilaiUlangan);
                }
            }    

            if (!$insertNilaiUlangan) {
                throw new Exception("Query insert gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * 
            FROM nilai_ulangan 
            WHERE 
            tanggal='$tanggal' AND
            id_tahun_Ajar='$idTahunAjar' AND
            semester='$semester' AND
            id_siswa='$lastIdSiswa' AND
            kelas='$kelas' AND
            id_mapel='$idMapel' AND
            lingkup_materi='$lingkupMateri' AND
            nilai='$lastValue'            
            ");

            if ($result && mysqli_num_rows($result) <> 0) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Tambah nilai ulangan berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_nilai_ulangan.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak ditemukan setelah ditambahkan");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:input_nilai_ulangan.php');
            exit;
        }
    }

    // 26. Edit Nilai Ulangan
    if(isset($_POST['ubahNilaiUlangan'])){
        $idNilaiUlangan = $_POST['idNilaiUlangan'];
        $semester = $_POST['semester'];
        $idSiswa = $_POST['siswa'];
        $idMapel = $_POST['mapel'];
        $lingkupMateri = $_POST['lingkupMateri'];
        $nilai = $_POST['nilai'];
        $namaUser = $_POST['namaUser'];

        try {
            $queryUpdateNilaiUlangan = "UPDATE `nilai_ulangan` 
            SET 
            `semester`='$semester',
            `id_siswa`='$idSiswa',
            `id_mapel`='$idMapel',
            `lingkup_materi`='$lingkupMateri',
            `nilai`='$nilai'
            WHERE
            `id_ul`=$idNilaiUlangan
            ";
            
            $updateNilaiUlangan = mysqli_query($conn, $queryUpdateNilaiUlangan);

            if (!$updateNilaiUlangan) {
                throw new Exception("Query update gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $queryResult = "SELECT * FROM `nilai_ulangan` 
            WHERE 
            `semester`='$semester' AND 
            `id_siswa`='$idSiswa' AND 
            `id_mapel`='$idMapel' AND 
            `lingkup_materi`='$lingkupMateri' AND 
            `nilai`='$nilai'
            ";
            $result = mysqli_query($conn, $queryResult);

            if ($result && mysqli_num_rows($result) === 1) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Ubah nilai ulangan berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_nilai_ulangan.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak ditemukan setelah diubah");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $queryUpdateNilaiUlangan . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:input_nilai_ulangan.php');
            exit;
        }
    }
    
    // 27. Hapus Nilai Ulangan
    if(isset($_POST['hapusNilaiUlangan'])){
        $idNilaiUlangan = $_POST['idNilaiUlangan'];

        try {
            $queryHapusNilaiUlangan = "DELETE FROM `nilai_ulangan` WHERE `id_ul`='$idNilaiUlangan'";          
            $hapusNilaiUlangan = mysqli_query($conn, $queryHapusNilaiUlangan);

            if (!$hapusNilaiUlangan) {
                throw new Exception("Query hapus gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * FROM `nilai_ulangan` WHERE `id_ul`='$idNilaiUlangan'");

            if ($result && mysqli_num_rows($result) === 0) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Hapus nilai ulangan berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_nilai_ulangan.php');
                exit;
            } else {
                // Data masih ada dalam database, itu berarti gagal
                throw new Exception("Data masih ada setelah dihapus");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:input_nilai_ulangan.php');
            exit;
        }
    }

    // 28. Tambah Deskripsi
    if(isset($_POST['tambahDeskripsi'])){
        $semester = $_POST['semester'];
        $mapel = $_POST['mapel'];
        $kelas = $_POST['kelas'];
        $deskripsi = $_POST['deskripsi'];
        $deskripsi = lcfirst($deskripsi);
        $deskripsi = rtrim($deskripsi, '.');

        try {
            
            $queryInsertDeskripsi = "INSERT INTO `asesmen`
            (`semester`, `id_mapel`, `id_kelas`, `deskripsi`) 
            VALUES ('$semester','$mapel','$kelas','$deskripsi');";
                
            $insertDeskripsi = mysqli_query($conn, $queryInsertDeskripsi);
          
            
            if (!$insertDeskripsi) {
                throw new Exception("Query insert gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * 
            FROM asesmen
            WHERE 
            `semester` = '$semester' AND
            `id_mapel` = '$mapel' AND
            `id_kelas` = '$kelas' AND
            `deskripsi` ='$deskripsi'            
            ");

            if ($result && mysqli_num_rows($result) === 1) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Tambah deskripsi berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                if ($semester == 'Ganjil') {
                    header('location:input_nilai_kualitatif_ganjil.php');
                } elseif ($semester == 'Genap') {
                    header('location:input_nilai_kualitatif_genap.php'); 
                }
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak ditemukan setelah ditambahkan");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            if ($semester == 'Ganjil') {
                header('location:input_nilai_kualitatif_ganjil.php');
            } elseif ($semester == 'Genap') {
                header('location:input_nilai_kualitatif_genap.php'); 
            }
            exit;
        }
    }

    // 29. Ubah Deskripsi
    if(isset($_POST['ubahDeskripsi'])){
        $semester = $_POST['semester'];
        $kelas = $_POST['kelas'];
        $idMapel = $_POST['idMapel'];

        try {
            
            foreach ($_POST as $key => $value) {
                // Check if the form field is related to deskripsi
                if (strpos($key, 'deskripsi_') !== false) {
                    // Extract id_asesmen from the form field name
                    $idAsesmen = substr($key, strlen('deskripsi_'));
                    $lastId = $idAsesmen;
                    // Sanitize and update the database
                    $newDeskripsi = mysqli_real_escape_string($conn, $value);
                    $newDeskripsi = rtrim($newDeskripsi, '.');
                    $lastValue = $newDeskripsi;
                    $updateQuery = "UPDATE asesmen SET deskripsi = '$newDeskripsi' WHERE id_asesmen = $idAsesmen";
                    $updateDeskripsi = mysqli_query($conn, $updateQuery);
                }
            }       
            
            if (!$updateDeskripsi) {
                throw new Exception("Query update gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * 
            FROM asesmen
            WHERE
            `id_asesmen` = '$lastId' AND
            `semester` = '$semester' AND
            `id_mapel` = '$idMapel' AND
            `id_kelas` = '$kelas' AND
            `deskripsi` ='$lastValue'            
            ");

            if ($result && mysqli_num_rows($result) === 1) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Ubah deskripsi berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                if ($semester == 'Ganjil') {
                    header('location:input_nilai_kualitatif_ganjil.php');
                } elseif ($semester == 'Genap') {
                    header('location:input_nilai_kualitatif_genap.php'); 
                }
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak berubah");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            if ($semester == 'Ganjil') {
                header('location:input_nilai_kualitatif_ganjil.php');
            } elseif ($semester == 'Genap') {
                header('location:input_nilai_kualitatif_genap.php'); 
            }
            exit;
        }
    }

    // 30. Hapus Deskripsi
    if(isset($_POST['hapusAsesmen'])){
        $semester = $_POST['semester'];
        $kelas = $_POST['kelas'];
        $idMapel = $_POST['idMapel'];
        $idAsesmen = $_POST['deskripsi'];      

        try {           

            $deleteQuery = "DELETE FROM asesmen WHERE id_asesmen = $idAsesmen";
            $deleteDeskripsi = mysqli_query($conn, $deleteQuery);

    
            
            if (!$deleteDeskripsi) {
                throw new Exception("Query hapus gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * 
            FROM asesmen
            WHERE
            `id_asesmen` = '$idAsesmen' AND
            `semester` = '$semester' AND
            `id_mapel` = '$idMapel' AND
            `id_kelas` = '$kelas'           
            ");

            if ($result && mysqli_num_rows($result) === 0) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Hapus deskripsi berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                if ($semester == 'Ganjil') {
                    header('location:input_nilai_kualitatif_ganjil.php');
                } elseif ($semester == 'Genap') {
                    header('location:input_nilai_kualitatif_genap.php'); 
                }
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak terhapus");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $deleteQuery . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            if ($semester == 'Ganjil') {
                header('location:input_nilai_kualitatif_ganjil.php');
            } elseif ($semester == 'Genap') {
                header('location:input_nilai_kualitatif_genap.php'); 
            }
            exit;
        }
    }

    

?>