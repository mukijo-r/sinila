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

    if (isset($_SESSION['previous_user'])) {
        $previousUsername = $_SESSION['previous_user'];
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

        try {
            $queryUpdateNilaiCatatan = "UPDATE `nilai_catatan` 
            SET
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
            semester='$semester' AND
            id_siswa='$idSiswa' AND
            catatan='$catatan'; ";
            $result = mysqli_query($conn, $queryCek);

            if ($result && mysqli_num_rows($result) > 0) {
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
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $queryCek . $e->getMessage();
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

        try {
            $queryUpdateAbsen = "UPDATE `absensi` 
            SET 
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
                    $queryInsertKenaikkanKelas = "INSERT INTO `kenaikan_kelas`
                    (`tanggal`, `id_tahun_ajar`, `semester`, `kelas`, `id_siswa`, `status`, `guru_pencatat`) 
                    VALUES ('$tanggalInput','$idTahunAjar','Genap','$kelas','$idSiswa','$value','$namaUser')";
                        
                    $insertKenaikkanKelas = mysqli_query($conn, $queryInsertKenaikkanKelas);
                }
            };

            if (!$insertKenaikkanKelas) {
                throw new Exception("Query insert gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $queryCek = "SELECT * 
            FROM kenaikan_kelas
            WHERE 
            tanggal='$tanggalInput' AND
            id_tahun_Ajar='$idTahunAjar' AND
            semester='Genap' AND
            kelas='$kelas' AND
            id_siswa='$lastIdSiswa' AND
            `status`='$lastValue'            
            ";

            $result = mysqli_query($conn, $queryCek);

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
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $queryCek . $e->getMessage();
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

        try {
            $queryUpdateKenaikkanKelas = "UPDATE `kenaikan_kelas` 
            SET             
            `tanggal`='$tanggalInput',
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
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $queryInsertNilaiUlangan . $e->getMessage();
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
        $mapel = $_POST['mapel'];
        $fase = $_POST['fase'];
        $deskripsi = $_POST['deskripsi'];
        $deskripsi = lcfirst($deskripsi);
        $deskripsi = rtrim($deskripsi, '.');

        try {
            
            $queryInsertDeskripsi = "INSERT INTO `capaian_kompetensi`
            (`id_mapel`,`fase`,`deskripsi`) 
            VALUES ('$mapel','$fase','$deskripsi');";           
                
            $insertDeskripsi = mysqli_query($conn, $queryInsertDeskripsi);

            // $queryIdAsesmen = "SELECT `id_asesmen` FROM `capaian_kompetensi` WHERE `deskripsi` = '$deskripsi'";
            // $dataIdAsesmen = mysqli_query($conn, $queryIdAsesmen);
            // while($data=mysqli_fetch_array($dataIdAsesmen)){
            //     $idAsesmen = $data['id_asesmen'];
            // }
            
            // $querySiswa = "SELECT id_siswa FROM siswa";
            // $siswa = mysqli_query($conn, $querySiswa);

            // $siswaArray = []; // Buat array untuk menyimpan data siswa
                        
            // while ($row = mysqli_fetch_assoc($siswa)) {
            //     $siswaArray[] = $row['id_siswa']; // Tambahkan id_siswa ke dalam array
            // }

            // for ($i = 0; $i < count($siswaArray); $i++) {
            //     $idSiswa = $siswaArray[$i];
            //     $queryInsertCapaianKompetensi = "INSERT INTO `capaian_kompetensi`
            //     (`id_siswa`, `id_asesmen`, `capaian`) 
            //     VALUES 
            //     ('$idSiswa','$idAsesmen','0')";

            //     $setCapaianKompetensi = mysqli_query($conn, $queryInsertCapaianKompetensi);
            // }           
            
            if (!$insertDeskripsi) {
                throw new Exception("Query insert gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * 
            FROM capaian_kompetensi
            WHERE 
            `fase` = '$fase' AND
            `id_mapel` = '$mapel' AND
            `deskripsi` ='$deskripsi'            
            ");

            if ($result && mysqli_num_rows($result) > 0) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Tambah deskripsi berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_capaian_kompetensi.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak ditemukan setelah ditambahkan");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $queryInsertDeskripsi . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:input_capaian_kompetensi.php');
                exit;
        }
    }

    // 29. Ubah Deskripsi
    if(isset($_POST['ubahDeskripsi'])){
        $fase = $_POST['kelas'];
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
                    $updateQuery = "UPDATE capaian_kompetensi SET deskripsi = '$newDeskripsi' WHERE id_ck = $idAsesmen";
                    $updateDeskripsi = mysqli_query($conn, $updateQuery);
                }
            }       
            
            if (!$updateDeskripsi) {
                throw new Exception("Query update gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $queryResult = "SELECT * 
            FROM capaian_kompetensi
            WHERE
            `id_ck` = '$lastId' AND
            `id_mapel` = '$idMapel' AND
            `deskripsi` ='$lastValue'            
            ";
            $result = mysqli_query($conn, $queryResult);

            if ($result && mysqli_num_rows($result) === 1) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Ubah deskripsi berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_capaian_kompetensi.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak berubah");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $queryResult . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:input_capaian_kompetensi.php');
            exit;
        }
    }

    // 30. Hapus Deskripsi
    if(isset($_POST['hapusDeskripsi'])){
        $fase = $_POST['fase'];
        $idMapel = $_POST['idMapel'];
        $idCapkom = $_POST['deskripsi'];      

        try {           

            $queryDeleteCapaianKompetensi = "DELETE FROM `capaian_kompetensi` WHERE  `id_ck` = $idCapkom";
            $deleteCapaianKompetensi = mysqli_query($conn, $queryDeleteCapaianKompetensi);          
            
            if (!$deleteCapaianKompetensi) {
                throw new Exception("Query hapus gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah dihapus dari database
            $result = mysqli_query($conn, "SELECT * 
            FROM capaian_kompetensi
            WHERE
            `id_ck` = '$idCapkom' AND
            `id_mapel` = '$idMapel' AND
            `fase` = '$fase'           
            ");

            if ($result && mysqli_num_rows($result) === 0) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Hapus deskripsi berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_capaian_kompetensi.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak terhapus");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $queryDeleteCapaianKompetensi . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:input_capaian_kompetensi.php');
            exit;
        }
    }

    // 31. Update Capaian Kompetensi
    if (isset($_POST['btnSimpanAsesmen'])) {
        $semester = $_POST['semester'];

        $queryTahunAjar = mysqli_query($conn, "SELECT id_tahun_ajar FROM tahun_ajar WHERE tahun_ajar = '$tahunAjar'");
        $rowTahunAjar = mysqli_fetch_array($queryTahunAjar);
        $idTahunAjar = $rowTahunAjar['id_tahun_ajar'];
        try {
            $capaianArray = $_POST['capaian'];
    
            foreach ($capaianArray as $idSiswa => $asesmenArray) {
                foreach ($asesmenArray as $idCk => $value) {
                    $capaianValue = ($value == 1) ? 1 : 0;
    
                    $sqlUpdate = "UPDATE asesmen_capkom 
                    SET 
                    capaian = '$capaianValue' 
                    WHERE 
                    id_tahun_ajar =  '$idTahunAjar' AND
                    semester = '$semester' AND
                    kelas = '$kelas' AND
                    id_siswa = '$idSiswa' AND 
                    id_ck = '$idCk'";
                    if (!mysqli_query($conn, $sqlUpdate)) {
                        throw new Exception(mysqli_error($conn));
                    }
                }
            }
    
            $_SESSION['flash_message'] = "Capaian berhasil diperbarui.";
        } catch (Exception $e) {
            $_SESSION['flash_message'] = "Error: " . $e->getMessage();
        }

        if ($semester == 'Ganjil') {
            header('location:capkom_siswa_ganjil.php');
        } elseif ($semester == 'Genap') {
            header('location:capkom_siswa_genap.php');
        } 

        exit();
    }    

    // 32. Tambah User
    if (isset($_POST['tambahUser'])) {
        $nama = $_POST['nama'];
        $username = $_POST['username'];
        $password1 = $_POST['password'];
        $password2 = $_POST['confirmPassword'];
        $role = $_POST['role'];
        if ($password1 == $password2) {
                $password = password_hash($password1, PASSWORD_BCRYPT);
            
            // Coba jalankan query insert
            $addUser = mysqli_query($conn, "INSERT INTO `users`(`username`, `password`, `nama_lengkap`, `role`) 
            VALUES ('$username', '$password', '$nama', '$role')");

            $checkUserQuery = "SELECT * FROM `users` WHERE `username` = '$username'";
            $checkUserResult = mysqli_query($conn, $checkUserQuery);

            // Setelah berhasil menambahkan akun baru
            if (mysqli_num_rows($checkUserResult) == 1) {

                if (isset($_SESSION['previous_user'])) {
                    $_SESSION['user'] = $_SESSION['previous_user'];
                }

                $sweetAlert = "Swal.fire({
                    title: 'Sukses!',
                    text: 'Akun berhasil ditambahkan.',
                    icon: 'success',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });";

            } else {
                // Gagal menambahkan akun
                $sweetAlert = "Swal.fire({
                    title: 'Gagal!',
                    text: 'Tambah akun gagal.',
                    icon: 'error',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });";
            }
        } else {
            $sweetAlert = "Swal.fire({
                title: 'Gagal!',
                text: 'Password tidak sama. Tambah akun gagal.',
                icon: 'error',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false
            });";
        }
    }

    // 33. Tambah Nilai Catatan Ekstrakurikuler
    if(isset($_POST['tambahCatatanEkstra'])){
        $semester = $_POST['semester'];
        $namaUser = $_POST['namaUser'];
        $idEkstra = $_POST['ekstra'];

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
                    $queryInsertCatatanEkstra = "INSERT INTO `nilai_catatan_ekstrakurikuler`
                    (`tanggal`, `id_tahun_ajar`, `semester`, `kelas`, `id_ek`, `id_siswa`, `catatan`, `guru_penilai`)
                    VALUES ('$tanggal','$idTahunAjar','$semester','$kelas','$idEkstra','$idSiswa','$value','$namaUser')";
                        
                    $insertCatatanEkstra = mysqli_query($conn, $queryInsertCatatanEkstra);
                }
            }            
            
            if (!$insertCatatanEkstra) {
                throw new Exception("Query insert gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * 
            FROM nilai_catatan_ekstrakurikuler
            WHERE 
            tanggal='$tanggal' AND
            id_tahun_Ajar='$idTahunAjar' AND
            semester='$semester' AND
            kelas='$kelas' AND
            id_ek='$idEkstra' AND
            id_siswa='$lastIdSiswa' AND
            catatan='$lastValue'            
            ");

            if ($result && mysqli_num_rows($result) === 1) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Tambah catatan berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_nilai_catatan_ekstrakurikuler.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak ditemukan atau duplikat");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:input_nilai_catatan_ekstrakurikuler.php');
            exit;
        }
    }

    // 34. Ubah Nilai Catatan
    if(isset($_POST['ubahNilaiCatatanEkstra'])){
        $idNilaiCatatan = $_POST['idNilaiCatatan'];
        $semester = $_POST['semester'];
        $idSiswa = $_POST['siswa'];
        $idEkstra = $_POST['ekstra'];
        $catatan = $_POST['catatan'];
        $namaUser = $_POST['namaUser'];

        try {
            $queryUpdateCatatanEkstra = "UPDATE `nilai_catatan_ekstrakurikuler` 
            SET
            `semester`='$semester',
            `id_siswa`='$idSiswa',
            `id_ek`='$idEkstra',
            `catatan`='$catatan'
            WHERE 
            `id_nce`='$idNilaiCatatan'
            ";
                
            $updateCatatanEkstra = mysqli_query($conn, $queryUpdateCatatanEkstra);

            if (!$updateCatatanEkstra) {
                throw new Exception("Query update gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $queryCek = "SELECT * 
            FROM nilai_catatan_ekstrakurikuler
            WHERE 
            semester='$semester' AND
            id_ek='$idEkstra' AND
            id_siswa='$idSiswa' AND
            catatan='$catatan'; ";
            $result = mysqli_query($conn, $queryCek);

            if ($result && mysqli_num_rows($result) === 1) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Ubah catatan berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_nilai_catatan_ekstrakurikuler.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak ditemukan setelah diubah");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $queryCek . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:input_nilai_catatan_ekstrakurikuler.php');
            exit;
        }
    }

    // 35. Hapus Nilai Catatan
    if(isset($_POST['hapusCatatanEkstra'])){
        $idNilaiCatatan = $_POST['idCatatanEkstra'];

        try {
            $queryHapusCatatanEkstra = "DELETE FROM `nilai_catatan_ekstrakurikuler` WHERE `id_nce`='$idNilaiCatatan'";          
            $hapusCatatanEkstra = mysqli_query($conn, $queryHapusCatatanEkstra);

            if (!$hapusCatatanEkstra) {
                throw new Exception("Query hapus gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * FROM `nilai_catatan_ekstrakurikuler` WHERE `id_nce`='$idNilaiCatatan'");

            if ($result && mysqli_num_rows($result) === 0) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Hapus catatan berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_nilai_catatan_ekstrakurikuler.php');
                exit;
            } else {
                // Data masih ada dalam database, itu berarti gagal
                throw new Exception("Data masih ada setelah dihapus");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:input_nilai_catatan_ekstrakurikuler.php');
            exit;
        }
    }

    //36. Tambah Kategori Ekstrakurikuler
    if(isset($_POST['tambahEkstrakurikuler'])){
        $namaEkstra = $_POST['ekstra'];

        try {

            $insertEkstra = mysqli_query($conn, "INSERT INTO `ekstrakurikuler`(`nama_ek`) VALUES ('$namaEkstra')");

            $queryCheckEkstra = "SELECT * FROM `ekstrakurikuler` WHERE `nama_ek` = '$namaEkstra'";
            $checkEkstra = mysqli_query($conn, $queryCheckEkstra);

            if ($checkEkstra && mysqli_num_rows($checkEkstra) === 1) {
                $_SESSION['flash_message'] = 'Tambah kategori ekstrakurikuler berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:input_nilai_catatan_ekstrakurikuler.php');
                exit;
            } else {
                throw new Exception("Data tidak ditemukan atau duplikat");
            }

        } catch (Exception $e) {
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:input_nilai_catatan_ekstrakurikuler.php');
            exit;
        }
    }

    //37. Tambah Asesmen Capaian Kompetensi
    if (isset($_POST['btnCapkomLanjut'])) {
        $idMapel = $_POST['idMapel'];
        $kelas = $_POST['kelas'];
        $semester = $_POST['semester'];

        if ($kelas == 1 || $kelas == 2) {
            $fase = 'A';
        } elseif ($kelas == 3 || $kelas == 4) {
            $fase = 'B';
        } elseif ($kelas == 5 || $kelas == 6) {
            $fase = 'C';
        }
        
        $queryTahunAjar = mysqli_query($conn, "SELECT id_tahun_ajar FROM tahun_ajar WHERE tahun_ajar = '$tahunAjar'");
        $rowTahunAjar = mysqli_fetch_array($queryTahunAjar);
        $idTahunAjar = $rowTahunAjar['id_tahun_ajar'];

        try {
            $querySiswa = "SELECT id_siswa FROM siswa WHERE id_kelas = '$kelas'";
            $siswaResult = mysqli_query($conn, $querySiswa);

            $siswaArray = [];
            while ($row = mysqli_fetch_assoc($siswaResult)) {
                $siswaArray[] = $row['id_siswa'];
            }
    
            $queryCk = "SELECT id_ck, deskripsi FROM `capaian_kompetensi` WHERE id_mapel = '$idMapel' AND fase = '$fase' ORDER BY id_ck ASC;";           
            $capaianKompetensi = mysqli_query($conn, $queryCk);

            foreach ($_POST['checkbox'] as $idCapkom => $checkboxValues) {
                foreach ($siswaArray as $idSiswa) {
                    foreach ($checkboxValues as $checkboxValue) {
                        $queryCheckExistence = "SELECT * FROM `asesmen_capkom`
                            WHERE 
                            `id_tahun_ajar` = '$idTahunAjar' AND
                            `semester` = '$semester' AND
                            `id_siswa` = '$idSiswa' AND
                            `id_ck` = '$idCapkom' AND
                            `kelas` = '$kelas';";
                
                        $resultExistence = mysqli_query($conn, $queryCheckExistence);
                    
                        if (!$resultExistence) {
                            throw new Exception("Query select gagal: " . mysqli_error($conn));
                        }
                    
                        if (mysqli_num_rows($resultExistence) > 0) {
                            // If the combination already exists, perform an update
                            $queryUpdateDeskripsi = "UPDATE `asesmen_capkom`
                                SET
                                `tampil` = '$checkboxValue'
                                WHERE 
                                `id_tahun_ajar` = '$idTahunAjar' AND
                                `semester` = '$semester' AND
                                `id_siswa` = '$idSiswa' AND
                                `id_ck` = '$idCapkom' AND
                                `kelas` = '$kelas';";
                    
                            $updateDeskripsi = mysqli_query($conn, $queryUpdateDeskripsi);
                    
                            if (!$updateDeskripsi) {
                                throw new Exception("Query update gagal: " . mysqli_error($conn));
                            }
                        } else {
                            // If the combination doesn't exist, perform an insert
                            $queryInsertDeskripsi = "INSERT INTO `asesmen_capkom`
                                (`id_tahun_ajar`, `semester`, `id_siswa`, `id_ck`, `kelas`, `tampil`, `capaian`)
                                VALUES 
                                ('$idTahunAjar', '$semester', '$idSiswa', '$idCapkom', '$kelas', '$checkboxValue', '0');";
                    
                            $insertDeskripsi = mysqli_query($conn, $queryInsertDeskripsi);
                    
                            if (!$insertDeskripsi) {
                                throw new Exception("Query insert gagal: " . mysqli_error($conn));
                            }
                        }
                    }
                }                
            }
    
            // Check if data exists in the database
            $queryCek = "SELECT * FROM asesmen_capkom WHERE `id_ck` = '$idCapkom'";
            $result = mysqli_query($conn, $queryCek);
    
            if (!$result) {
                throw new Exception("Query SELECT gagal: " . mysqli_error($conn));
            }
    
            if (mysqli_num_rows($result) > 0) {
                $_SESSION['flash_message'] = 'Tambah deskripsi berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; 
                header('location: asesmen_cp.php?idMapel=' . $idMapel . '&semester=' . $semester);
                exit;
            } else {
                throw new Exception("Data tidak ditemukan setelah ditambahkan");
            }
        } catch (Exception $e) {
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger';
            if ($semester == 'Ganjil') {
                header('location:capkom_siswa_ganjil.php');
            } elseif ($semester == 'Genap') {
                header('location:capkom_siswa_genap.php');
            }            
            exit;
        }
    }

    // 38. Tambah Dimensi
    if(isset($_POST['tambahDimensi'])){
        $dimensi = $_POST['dimensi'];

        try {
            $queryDimensi = "INSERT INTO `p5_dimensi` (`dimensi`) VALUES ('$dimensi')";
                
            $insertDimensi = mysqli_query($conn, $queryDimensi);

            if (!$insertDimensi) {
                throw new Exception("Query insert gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * FROM p5_dimensi WHERE dimensi='$dimensi'");

            if ($result && mysqli_num_rows($result) === 1) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Tambah dimensi berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:p5_dimensi.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak ditemukan setelah ditambahkan");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:p5_dimensi.php');
            exit;
        }
    }

    // 39. Ubah Dimensi
    if(isset($_POST['ubahDimensi'])){
        $dimensi = $_POST['dimensi'];
        $idDimensi = $_POST['idDimensi'];

        try {
            $queryDimensi = "UPDATE`p5_dimensi` SET dimensi='$dimensi' WHERE id_dimensi='$idDimensi';";
                
            $updateDimensi = mysqli_query($conn, $queryDimensi);

            if (!$updateDimensi) {
                throw new Exception("Query update gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * FROM p5_dimensi WHERE dimensi='$dimensi'");

            if ($result && mysqli_num_rows($result) === 1) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Ubah dimensi berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:p5_dimensi.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak ditemukan");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $queryDimensi . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:p5_dimensi.php');
            exit;
        }
    }

    // 40. Tambah Elemen
    if(isset($_POST['tambahElemen'])){
        $idDimensi = $_POST['dimensi1'];
        $elemen = $_POST['elemen'];

        try {
            $queryElemen = "INSERT INTO `p5_elemen`
            (`id_dimensi`, `elemen`)
             VALUES 
             ('$idDimensi','$elemen')";
                
            $insertElemen = mysqli_query($conn, $queryElemen);

            if (!$insertElemen) {
                throw new Exception("Query insert gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * FROM p5_elemen WHERE id_dimensi='$idDimensi' AND elemen='$elemen'");

            if ($result && mysqli_num_rows($result) === 1) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Tambah elemen berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:p5_elemen.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak ditemukan setelah ditambahkan");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $queryElemen . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:p5_elemen.php');
            exit;
        }
    }

    // 41. Tambah Project
    if(isset($_POST['btnSimpanProject'])){
        $jumlahDimensi = $_POST['jumlahDimensi'];
        $namaProject = $_POST['namaProject'];
        $deskripsiProject = $_POST['deskripsiProject'];
        $namaUser = $_POST['namaUser'];
        $tanggal = date('Y-m-d'); 

        if ($kelas == 1 | $kelas == 2) { 
            $fase = 'A';
        } elseif ($kelas == 3 | $kelas == 4) {
            $fase = 'B';
        } elseif ($kelas == 5 | $kelas == 6) {
            $fase = 'C';
        }

        try {
            $queryInsertProject = "INSERT INTO `p5_project`
            (`tanggal`, `pembuat`, `nama_project`, `fase`, `deskripsi_project`) 
            VALUES 
            ('$tanggal', '$namaUser', '$namaProject','$fase','$deskripsiProject');";
                
            $insertProject1 = mysqli_query($conn, $queryInsertProject);

            if (!$insertProject1) {
                throw new Exception("Query insert gagal"); 
            }

            $selectIdProject = "SELECT id_project FROM p5_project WHERE 
            `tanggal` = '$tanggal' AND
            `pembuat` = '$namaUser' AND
            `nama_project` = '$namaProject' AND
            `fase` = '$fase' AND
            `deskripsi_project` = '$deskripsiProject';";

            $queryIdProject = mysqli_query($conn, $selectIdProject);

            if ($rowProject = mysqli_fetch_assoc($queryIdProject)) {
                $idProject = $rowProject['id_project'];
            }

            for ($d = 1; $d <= $jumlahDimensi; $d++) {
                $capaian = $_POST["capaian$d"];
                $idCapaian = $_POST["id_capaian$d"];

                $queryProject2 = "INSERT INTO `p5_project_capaian`
                (`id_project`, `pc`, `id_capaian`) 
                VALUES 
                ('$idProject','capaian$d','$idCapaian');";
        
                $queryInsertProject2 = mysqli_query($conn, $queryProject2);
            } 

            if ($queryInsertProject2) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Tambah Project baru berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:p5_daftar_project.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak ditemukan atau duplikat");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $queryProject2 . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:p5_daftar_project.php');
            exit;
        }
    }

    // 42. Tambah Penilaian Project
    if(isset($_POST['btnSubmitNilaiProject'])){
        $idProject = $_POST['idProject'];
        $penilaianData = $_POST['penilaian'];

        $queryTahunAjar = mysqli_query($conn, "SELECT id_tahun_ajar FROM tahun_ajar WHERE tahun_ajar = '$tahunAjar'");
        $rowTahunAjar = mysqli_fetch_array($queryTahunAjar);
        $idTahunAjar = $rowTahunAjar['id_tahun_ajar'];

        try {
            foreach ($penilaianData as $idSiswa => $penilaianCapaian) {
                foreach ($penilaianCapaian as $idCapaian => $nilai) {
                    // Lakukan query SELECT untuk memeriksa apakah kombinasi data sudah ada
                    $querySelect = "
                        SELECT * FROM p5_penilaian
                        WHERE
                            `id_tahun_ajar` = '$idTahunAjar' AND
                            `kelas` = '$kelas' AND
                            `id_project` = '$idProject' AND
                            `id_siswa` = '$idSiswa' AND
                            `id_capaian` = '$idCapaian'";
                    
                    $result = mysqli_query($conn, $querySelect);            

                    // Jika data sudah ada, lakukan UPDATE; jika tidak, lakukan INSERT
                    if (mysqli_num_rows($result) > 0) {
                        $queryUpdate = "
                            UPDATE p5_penilaian
                            SET `nilai` = '$nilai'
                            WHERE
                                `id_tahun_ajar` = '$idTahunAjar' AND
                                `kelas` = '$kelas' AND
                                `id_project` = '$idProject' AND
                                `id_siswa` = '$idSiswa' AND
                                `id_capaian` = '$idCapaian'";
                        
                        $updateNilai = mysqli_query($conn, $queryUpdate);
        
                    } else {
                        $queryInsert = "
                            INSERT INTO p5_penilaian (`id_tahun_ajar`, `kelas`, `id_project`, `id_siswa`, `id_capaian`, `nilai`)
                            VALUES ('$idTahunAjar', '$kelas', '$idProject', '$idSiswa', '$idCapaian', '$nilai')";
        
                        $insertNilai = mysqli_query($conn, $queryInsert);
        
                    }
                }
            }
            
            if ($insertNilai | $updateNilai) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Penilaian berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:p5_penilaian.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak ditemukan setelah ditambahkan");
            }

            
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $querySelect . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:p5_penilaian.php');
            exit;
        }
    }

    // 43. Tambah Nilai Catatan
    if(isset($_POST['tambahCatatanProject'])){
        $idProject = $_POST['idProject'];

        $queryTahunAjar = mysqli_query($conn, "SELECT id_tahun_ajar FROM tahun_ajar WHERE tahun_ajar = '$tahunAjar'");
        $rowTahunAjar = mysqli_fetch_array($queryTahunAjar);
        $idTahunAjar = $rowTahunAjar['id_tahun_ajar'];

        try {
            
            $dataNilai = [];
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'nilai_') === 0) {
                    $idSiswa = substr($key, 6);
                    $dataNilai[$idSiswa] = $value;
                    $queryInsertNilaiCatatanProject = "INSERT INTO `p5_penilaian_catatan`
                    (`id_tahun_ajar`, `kelas`, `id_siswa`, `id_project`, `catatan`) 
                    VALUES 
                    ('$idTahunAjar','$kelas','$idSiswa ','$idProject','$value')";
                        
                    $insertNilaiCatatanProject = mysqli_query($conn, $queryInsertNilaiCatatanProject);
                }
            }            
            
            if ($insertNilaiCatatanProject) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Tambah catatan berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:p5_catatan.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak ditemukan setelah ditambahkan");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $queryInsertNilaiCatatanProject . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:p5_catatan.php');
            exit;
        }
    }

    // 44. Remedial Nilai Ulangan Remedial
    if(isset($_POST['tambahNilaiUlanganRemed'])){
        $semester = $_POST['semester'];
        $idMapel = $_POST['mapel'];
        $lingkupMateri = $_POST['lingkupMateri'];
        $namaUser = $_POST['namaUser'];

        $queryTahunAjar = mysqli_query($conn, "SELECT id_tahun_ajar FROM tahun_ajar WHERE tahun_ajar = '$tahunAjar'");
        $rowTahunAjar = mysqli_fetch_array($queryTahunAjar);
        $idTahunAjar = $rowTahunAjar['id_tahun_ajar'];
        $idSiswa = $_POST['siswa'];
        $nilai = $_POST['nilai'];


        try {

            $queryInsertNilaiUlangan = "INSERT INTO `nilai_ulangan`
            (`tanggal`, `id_tahun_Ajar`, `semester`, `id_siswa`, `kelas`, `id_mapel`, `lingkup_materi`, `nilai`, `guru_penilai`) 
            VALUES ('$tanggal','$idTahunAjar','$semester','$idSiswa', '$kelas', '$idMapel','$lingkupMateri','$nilai','$namaUser')";
                
            $insertNilaiUlangan = mysqli_query($conn, $queryInsertNilaiUlangan);
   

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
            id_siswa='$idSiswa' AND
            kelas='$kelas' AND
            id_mapel='$idMapel' AND
            lingkup_materi='$lingkupMateri' AND
            nilai='$nilai'            
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

    // 45. Tambah Nilai Ujian Remed
    if(isset($_POST['tambahNilaiUjianRemed'])){
        $semester = $_POST['semester'];
        $idMapel = $_POST['mapel'];
        $ujian = $_POST['ujian'];
        $namaUser = $_POST['namaUser'];

        $queryTahunAjar = mysqli_query($conn, "SELECT id_tahun_ajar FROM tahun_ajar WHERE tahun_ajar = '$tahunAjar'");
        $rowTahunAjar = mysqli_fetch_array($queryTahunAjar);
        $idTahunAjar = $rowTahunAjar['id_tahun_ajar'];

        $idSiswa = $_POST['siswa'];
        $nilai = $_POST['nilai'];

        try {


            $queryInsertNilaiUjian = "INSERT INTO `nilai_ujian`
            (`tanggal`, `id_tahun_ajar`, `semester`, `id_siswa`, `kelas`, `id_mapel`, `ujian`, `nilai`, `guru_penilai`) 
            VALUES ('$tanggal','$idTahunAjar','$semester','$idSiswa', '$kelas', '$idMapel','$ujian','$nilai','$namaUser')";
                
            $insertNilaiUjian = mysqli_query($conn, $queryInsertNilaiUjian);


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
            id_siswa='$idSiswa' AND
            kelas='$kelas' AND
            id_mapel='$idMapel' AND
            ujian='$ujian' AND
            nilai='$nilai'            
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

    // 46. Ubah Kelas
    if(isset($_POST['editKelas'])){
        $idKelas = $_POST['idk'];
        $waliKelas = $_POST['waliKelas'];

        try {
            $queryUpdateKelas = "UPDATE `kelas` SET `id_guru`='$waliKelas' WHERE `id_kelas`='$idKelas'";
                
            $updateKelas = mysqli_query($conn, $queryUpdateKelas);

            if (!$updateKelas) {
                throw new Exception("Query update gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * FROM `kelas` WHERE `id_kelas`='$idKelas' AND `id_guru`='$waliKelas'");

            if ($result && mysqli_num_rows($result) === 1) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Ubah Wali Kelas berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:kelas.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak ditemukan");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:kelas.php');
            exit;
        }
    }

    // 47. Tambah Guru
    if(isset($_POST['tambahGuru'])){
        $nip = $_POST['nip'];
        $namaGuru = $_POST['namaGuru'];
        $jk = $_POST['jk'];
        $jabatan = $_POST['jabatan'];

        try {
            // Coba jalankan query insert
            $addGuru = mysqli_query($conn, "INSERT INTO guru (nip, nama_lengkap, jk, jabatan) VALUES ('$nip', '$namaGuru', '$jk', '$jabatan')");

            if (!$addGuru) {
                throw new Exception("Query insert gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * FROM guru WHERE nip = '$nip'");

            if ($result && mysqli_num_rows($result) > 0) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Tambah data guru berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:guru.php');
                exit;
            } else {
                // Data tidak ada dalam database setelah insert, itu berarti gagal
                throw new Exception("Data guru tidak ditemukan setelah ditambahkan");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:guru.php');
            exit;
        }
    }

    // 48. Edit Guru
    if(isset($_POST['editGuru'])){
        $nip = $_POST['nip'];
        $namaGuru = $_POST['namaGuru'];
        $jk = $_POST['jk'];
        $jabatan= $_POST['jabatan'];
        $idg = $_POST['idg'];

        try {
            // Coba jalankan query update
            $editGuru = mysqli_query($conn, "UPDATE guru SET nip='$nip', nama_lengkap='$namaGuru', jk='$jk', jabatan='$jabatan' WHERE id_guru=$idg");

            if (!$editGuru) {
                throw new Exception("Query update gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk mengambil data yang baru saja diperbarui
            $result = mysqli_query($conn, "SELECT * FROM guru WHERE id_guru = $idg");

            if ($result && mysqli_num_rows($result) > 0) {
                // Data yang baru saja diperbarui ada dalam database, itu berarti edit berhasil
                $_SESSION['flash_message'] = 'Edit data guru berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:guru.php');
                exit;
            } else {
                // Data yang baru saja diperbarui tidak ada dalam database, itu berarti edit gagal
                $_SESSION['flash_message'] = 'Edit data guru gagal';
                $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
                header('location:guru.php');
                exit;
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:guru.php');
            exit;
        }
    }  
    
    // 49. Tambah Siswa
    if(isset($_POST['tambahSiswa'])){
        $nipd = $_POST['nipd'];
        $nisn = $_POST['nisn'];
        $namaSiswa = $_POST['namaSiswa'];
        $kelas = $_POST['kelas'];
        $jk = $_POST['jk'];
        $kotaLahir = $_POST['tempatLahir'];
        $tglLahir = $_POST['tanggalLahir'];
        $agama = $_POST['agama'];
        $alamat = $_POST['alamat'];


        $tanggalLahir = date("Y-m-d", strtotime($tglLahir));

        try {
            // Coba jalankan query insert
            $addSiswa = mysqli_query($conn, "INSERT INTO siswa 
            (nis, nisn, nama, id_kelas, jk, tempat_lahir, tanggal_lahir, agama, alamat, `status`) 
            VALUES 
            ('$nipd','$nisn', '$namaSiswa', $kelas, '$jk', '$kotaLahir', '$tanggalLahir', '$agama', '$alamat', 'aktif')");

            if (!$addSiswa) {
                throw new Exception("Query insert gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * FROM siswa WHERE nisn = '$nisn'");

            if ($result && mysqli_num_rows($result) > 0) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Tambah data siswa berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:siswa.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data siswa tidak ditemukan setelah ditambahkan");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:siswa.php');
            exit;
        }
    }

    // 50. Edit Siswa
    if(isset($_POST['editSiswa'])){
        $nipd = $_POST['nipd'];
        $nisn = $_POST['nisn'];
        $namaSiswa = $_POST['namaSiswa'];
        $kelas = $_POST['kelas'];
        $jk = $_POST['jk'];
        $kotaLahir = $_POST['tempatLahir'];
        $tglLahir = $_POST['tanggalLahir'];
        $agama = $_POST['agama'];
        $alamat = $_POST['alamat'];
        $ids = $_POST['ids'];
        $status = $_POST['status'];
        if ($status == 'drop out') {
            $kelas = 404;
        }

        $tanggalLahir = date("Y-m-d", strtotime($tglLahir));

        try {
            // Coba jalankan query update
            $editSiswa = mysqli_query($conn, "UPDATE siswa 
            SET 
            nis='$nipd',
            nisn='$nisn', 
            nama='$namaSiswa', 
            id_kelas='$kelas', 
            jk='$jk', 
            tempat_lahir='$kotaLahir', 
            tanggal_lahir='$tanggalLahir', 
            agama='$agama', 
            alamat='$alamat', 
            `status`='$status' 
            WHERE 
            id_siswa='$ids'");

            if (!$editSiswa) {
                throw new Exception("Query update gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah benar-benar diperbarui dalam database
            $queryUpdate = "SELECT * FROM siswa 
            WHERE 
            id_siswa = '$ids' AND
            nis='$nipd' AND
            nisn='$nisn' AND
            nama='$namaSiswa' AND 
            id_kelas='$kelas' AND
            jk='$jk' AND 
            tempat_lahir='$kotaLahir' AND 
            tanggal_lahir='$tanggalLahir' AND
            agama='$agama' AND 
            alamat='$alamat' AND
            `status`='$status'
            ";
            $result = mysqli_query($conn, $queryUpdate);

            if ($result && mysqli_num_rows($result) > 0) {
                // Data sudah benar-benar diperbarui dalam database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Edit data siswa berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:siswa.php');
                exit;
            } else {
                // Data tidak ada dalam database setelah edit, itu berarti gagal
                throw new Exception("Data siswa tidak ditemukan setelah diedit");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $queryUpdate . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:siswa.php');
            exit;
        }
    }

    // 51. Hapus Siswa
    if(isset($_POST['hapusSiswa'])){
        $ids = $_POST['ids'];

        try {
            //Hapus capaian kompetensi siswa bersangkutan

            $hapusCK = mysqli_query($conn, "DELETE FROM capaian_kompetensi WHERE id_siswa='$ids'");

            // Coba jalankan query hapus
            $hapusSiswa = mysqli_query($conn, "DELETE FROM siswa WHERE id_siswa='$ids'");

            if (!$hapusSiswa) {
                throw new Exception("Query hapus gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah tidak ada dalam database setelah dihapus
            $result = mysqli_query($conn, "SELECT * FROM siswa WHERE id_siswa = '$ids'");

            if ($result && mysqli_num_rows($result) === 0) {
                // Data sudah tidak ada dalam database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Hapus data siswa berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:siswa.php');
                exit;
            } else {
                // Data masih ada dalam database setelah dihapus, itu berarti gagal
                throw new Exception("Data siswa masih ada dalam database setelah dihapus");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:siswa.php');
            exit;
        }
    }

    // 52. Menaikkan siswa
    if (isset($_POST['naikkanSiswa'])) {
        try {
            // Jalankan query update
            $querySiswa = mysqli_query($conn, "SELECT id_siswa, nama, id_kelas FROM siswa WHERE id_kelas IN (1, 2, 3, 4, 5)");
    
            while ($row = mysqli_fetch_assoc($querySiswa)) {
                $idSiswa = $row['id_siswa'];
                $nama = $row['nama'];
                $idKelas = $row['id_kelas'];
                $idKelasBaru = $idKelas + 1;
                
                // Eksekusi query update di sini
                $naikkanSiswa = mysqli_query($conn, "UPDATE siswa SET id_kelas='$idKelasBaru' WHERE id_siswa='$idSiswa'");
                
                if (!$naikkanSiswa) {
                    throw new Exception("Naikkan siswa gagal");
                }
            }
    
            // Tambahkan kode Anda untuk memeriksa apakah data sudah diperbarui dengan benar
    
            $_SESSION['flash_message'] = 'Naikkan siswa berhasil';
            $_SESSION['flash_message_class'] = 'alert-success';
            header('location:siswa.php');
            exit;
        } catch (Exception $e) {
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger';
            header('location:siswa.php');
            exit;
        }
    }

    //53. Meluluskan siswa
    if (isset($_POST['luluskanSiswa'])) {
        try {
            // Jalankan query select
            $querySiswa = mysqli_query($conn, "SELECT id_siswa, nama, id_kelas FROM siswa WHERE id_kelas = 6");
    
            while ($row = mysqli_fetch_assoc($querySiswa)) {
                $idSiswa = $row['id_siswa'];
                $nama = $row['nama'];
                $idKelas = $row['id_kelas'];
                $idKelasBaru = 404;
                
                // Eksekusi query update di sini
                $luluskanSiswa = mysqli_query($conn, "UPDATE siswa SET `id_kelas`= $idKelasBaru, `status` = 'lulus' WHERE id_siswa='$idSiswa'");
                
                if (!$luluskanSiswa) {
                    throw new Exception("Luluskan siswa gagal");
                }
            }
    
            // Tambahkan kode Anda untuk memeriksa apakah data sudah diperbarui dengan benar
    
            $_SESSION['flash_message'] = 'Luluskan siswa berhasil, siswa masuk ke data alumni';
            $_SESSION['flash_message_class'] = 'alert-success';
            header('location:siswa.php');
            exit;
        } catch (Exception $e) {
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger';
            header('location:siswa.php');
            exit;
        }
    }
    
    // 54. Tarik Siswa Lama
    if(isset($_POST['tarikSiswa'])){
        $ids = $_POST['idSiswa'];
        $idKelas = $_POST['idKelas'];
        $status = "aktif";       

        try {
            // Coba jalankan query update
            $quryEditSiswa = "UPDATE siswa 
            SET 
            id_kelas='$idKelas', 
            `status`='$status' 
            WHERE 
            id_siswa='$ids'";

            $editSiswa = mysqli_query($conn, $quryEditSiswa);

            if (!$editSiswa) {
                throw new Exception("Query update gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah benar-benar diperbarui dalam database
            $cekQuery = "SELECT * FROM siswa 
            WHERE 
            id_siswa = '$ids' AND
            id_kelas='$idKelas' AND
            `status`='$status'
            ";
            $result = mysqli_query($conn, $cekQuery);

            if ($result && mysqli_num_rows($result) == 1) {
                // Data sudah benar-benar diperbarui dalam database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Tarik siswa lama berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:siswa.php');
                exit;
            } else {
                // Data tidak ada dalam database setelah edit, itu berarti gagal
                throw new Exception("Data siswa tidak ditemukan setelah ditarik");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $cekQuery . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:siswa.php');
            exit;
        }
    }

    // 55. Import Excel
    if (isset($_POST['importExcel'])) {
        // Membaca file Excel yang diunggah
        $inputFileName = $_FILES['formFile']['tmp_name'];
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);

        try {
        // Loop melalui baris-baris data Excel (mulai dari baris kedua karena baris pertama biasanya adalah header)
        foreach ($spreadsheet->getActiveSheet()->getRowIterator(1) as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $data = [];
            foreach ($cellIterator as $cell) {
                $data[] = $cell->getValue();
            }

            // Ambil data dari kolom-kolom Excel
            $namaSiswa = $data[0];
            $idKelas = $data[1];
            $jk = $data[2];
            $nis = $data[3];
            $nisn = strval($data[4]);
            $tempatLahir = $data[5];
            $tanggalLahir = $data[6];
            $agama = $data[7];
            $alamat = $data[8];
            $status = $data[9];

            // Lakukan operasi INSERT ke tabel "siswa" dalam database
            $sql = "INSERT INTO siswa (nama, id_kelas, jk, nis, nisn, tempat_lahir, tanggal_lahir, agama, alamat, `status`) VALUES ('$namaSiswa', '$idKelas', '$jk', '$nis', '$nisn', '$tempatLahir', '$tanggalLahir', '$agama', '$alamat', '$status')";
            
            // // Eksekusi query INSERT
            if (!mysqli_query($conn, $sql)) {
                throw new Exception(mysqli_error($conn));
            }

            //mendapatkan id siswa
            $querysiswa = "SELECT id_siswa FROM siswa WHERE nisn = '$nisn'";
            $selectSiswa = mysqli_query($conn, $querysiswa);
            while ($dataSiswa = mysqli_fetch_assoc($selectSiswa)) {
                $idSiswa = $dataSiswa['id_siswa'];
            };

            //Tambahkan capaian kompetensi dengan nilai 0
            $querySelectAsesmen = "SELECT id_asesmen FROM asesmen";
            $selectAsesmen = mysqli_query($conn, $querySelectAsesmen);

            while ($dataAsesmen = mysqli_fetch_assoc($selectAsesmen)) {
                $idAsesmen = $dataAsesmen['id_asesmen'];

                // Insert untuk setiap id_asesmen
                $queryInsertCK = "INSERT INTO capaian_kompetensi (id_siswa, id_asesmen, capaian) 
                                VALUES ($idSiswa, $idAsesmen, 0);";
                $insertCK = mysqli_query($conn, $queryInsertCK);
            }

        }

        // Tutup koneksi database
        mysqli_close($conn);

        // Set pesan flash
        $_SESSION['flash_message'] = 'Import Data Siswa Berhasil';
        $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
        header('location:siswa.php');
        exit;
    }
        catch (Exception $e) {
        // Tangani exception di sini
        $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
        $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
        header('location:siswa.php');
        exit;
        }
    }
    
    
    

?>