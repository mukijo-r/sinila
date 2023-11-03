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

    // Edit Kategori Kas
    if(isset($_POST['ubahKategoriKas'])){
        $idKas = $_POST['idk'];
        $jenisKas = $_POST['jenisKas'];
        $kelompok = $_POST['kelompok'];
        $idGuru = $_POST['guru'];
        $kode = $_POST['kode'];
        $keterangan = $_POST['keterangan'];

        try {
            $queryUpdateKategori = "UPDATE `kategori` SET `nama_kategori`='$jenisKas', `kelompok`='$kelompok', `id_guru`='$idGuru', `kode`='$kode', `keterangan`='$keterangan' WHERE `id_kategori`='$idKas'";
            
            $kategori = mysqli_query($conn, $queryUpdateKategori);

            if (!$kategori) {
                throw new Exception("Query update gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database

            $queryResult = "SELECT * FROM kategori WHERE nama_kategori='$jenisKas' AND `kelompok`='$kelompok' AND `id_guru`='$idGuru' AND `kode`='$kode' AND `keterangan`='$keterangan'";
            $result = mysqli_query($conn, $queryResult);

            if ($result && mysqli_num_rows($result) === 1) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Ubah kategori berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:kategori_kas.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data tidak ditemukan setelah diubah");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $queryResult . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            echo $queryInsertTabung;
            header('location:kategori_kas.php');
            exit;
        }
    }

    // Hapus Kategori Kas
    if(isset($_POST['hapusKategoriKas'])){
        $idKas = $_POST['idk'];

        try {
            $queryHapusKategori = "DELETE from `kategori` WHERE `id_kategori`='$idKas'";          
            $kategori = mysqli_query($conn, $queryHapusKategori);

            if (!$kategori) {
                throw new Exception("Query hapus gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * FROM kategori WHERE `id_kategori`='$idKas'");

            if ($result && mysqli_num_rows($result) === 0) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Hapus kategori berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:kategori_kas.php');
                exit;
            } else {
                // Data masih ada dalam database, itu berarti gagal
                throw new Exception("Data masih ada setelah dihapus");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            echo $queryInsertTabung;
            header('location:kategori_kas.php');
            exit;
        }
    }

    // Tambah Transaksi Masuk Siswa
    if(isset($_POST['tambahTransMasukSiswa'])){
        $tanggal = $_POST['tanggal'];
        $tanggalBayar = date("Y-m-d H:i:s", strtotime($tanggal));

        // Menggunakan query untuk mendapatkan id_tahun_ajar berdasarkan tahun_ajar yang dipilih
        $queryTahunAjar = mysqli_query($conn, "SELECT id_tahun_ajar FROM tahun_ajar WHERE tahun_ajar = '$tahun_ajar'");

        if ($queryTahunAjar && mysqli_num_rows($queryTahunAjar) > 0) {
            $dataTahunAjar = mysqli_fetch_assoc($queryTahunAjar);
            $idTahunAjar = $dataTahunAjar['id_tahun_ajar'];
        } else {
            // Kelas tidak ditemukan, tangani kesalahan di sini
            $_SESSION['flash_message'] = 'Tahun ajar tidak ditemukan.';
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location: transaksi_masuk_siswa.php');
            exit;
        }
        
        $kelas = $_POST['kelas'];
        // Menggunakan query untuk mendapatkan id_kelas berdasarkan nama_kelas yang dipilih
        $queryGetKelas = mysqli_query($conn, "SELECT id_kelas FROM kelas WHERE nama_kelas = '$kelas'");

        if ($queryGetKelas && mysqli_num_rows($queryGetKelas) > 0) {
            $kelasData = mysqli_fetch_assoc($queryGetKelas);
            $id_kelas = $kelasData['id_kelas'];
        } else {
            // Kelas tidak ditemukan, tangani kesalahan di sini
            $_SESSION['flash_message'] = 'Kelas tidak ditemukan.';
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location: transaksi_masuk_siswa.php');
            exit;
        }

        $idSiswa = $_POST['siswa'];
        $idSubKategori = $_POST['subKategori']; 
        // Menggunakan query untuk mendapatkan id_kategori berdasarkan id_subkategori yang dipilih
        $queryGetKategori = mysqli_query($conn, "SELECT id_kategori FROM sub_kategori_siswa WHERE id_sub_kategori = '$idSubKategori'");
        $rowKategori = mysqli_fetch_assoc($queryGetKategori);

        $id_kategori = $rowKategori['id_kategori'];
        $bulan = $_POST['bulan'];
        $penetapan = $_POST['nominal'];
        $bulanIni = $_POST['bulanIni'];
        $tunggakan = $_POST['tunggakan'];
        if ($tunggakan == ''){
            $tunggakan = 0;
        }
        $jumlah = $bulanIni + $tunggakan;
        $idGuru = $_POST['guru'];
        $keterangan = $_POST['keterangan'];    

        try {
            $queryInsertTransSiswa = "INSERT INTO `transaksi_masuk_siswa`(`tanggal`, `id_tahun_ajar`, `id_siswa`, `id_kategori`, `id_sub_kategori`, `bulan`, `penetapan`, `bulan_ini`, `tunggakan`,`jumlah`, `id_guru`, `keterangan`) 
            VALUES 
            ('$tanggalBayar','$idTahunAjar','$idSiswa','$id_kategori','$idSubKategori','$bulan', '$penetapan', '$bulanIni', '$tunggakan', '$jumlah','$idGuru','$keterangan')";
                
            $insertTransSiswa = mysqli_query($conn, $queryInsertTransSiswa);

            if (!$insertTransSiswa) {
                throw new Exception("Query insert gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $result = mysqli_query($conn, "SELECT * FROM `transaksi_masuk_siswa` WHERE bulan = '$bulan' and id_siswa = $idSiswa and jumlah=$jumlah");

            if ($result && mysqli_num_rows($result) > 0) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Tambah transaksi siswa berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:transaksi_masuk_siswa.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data transaksi tidak ditemukan setelah ditambahkan");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $queryInsertTransSiswa . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal        
            header('location:transaksi_masuk_siswa.php');
            exit;
        }
    }

    // Edit Transaksi Masuk Siswa
    if(isset($_POST['editTransSiswa'])){
        $idTransaksiMasukSiswa = $_POST['id_tms_masuk'];
        $tanggal = $_POST['tanggal'];
        $tanggalBayar = date("Y-m-d H:i:s", strtotime($tanggal));
        

        // Menggunakan query untuk mendapatkan id_tahun_ajar berdasarkan tahun_ajar yang dipilih
        $queryTahunAjar = mysqli_query($conn, "SELECT id_tahun_ajar FROM tahun_ajar WHERE tahun_ajar = '$tahun_ajar'");

        if ($queryTahunAjar && mysqli_num_rows($queryTahunAjar) > 0) {
            $dataTahunAjar = mysqli_fetch_assoc($queryTahunAjar);
            $idTahunAjar = $dataTahunAjar['id_tahun_ajar'];
        } else {
            // Kelas tidak ditemukan, tangani kesalahan di sini
            $_SESSION['flash_message'] = 'Tahun ajar tidak ditemukan.';
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location: transaksi_masuk_siswa.php');
            exit;
        }
        
        $kelas = $_POST['kelasEdit'];
        // Menggunakan query untuk mendapatkan id_kelas berdasarkan nama_kelas yang dipilih
        $queryGetKelas = mysqli_query($conn, "SELECT id_kelas FROM kelas WHERE nama_kelas = '$kelas'");

        if ($queryGetKelas && mysqli_num_rows($queryGetKelas) > 0) {
            $kelasData = mysqli_fetch_assoc($queryGetKelas);
            $id_kelas = $kelasData['id_kelas'];
        } else {
            // Kelas tidak ditemukan, tangani kesalahan di sini
            $_SESSION['flash_message'] = 'Kelas tidak ditemukan.';
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location: tabung.php');
            exit;
        }

        $siswa = $_POST['siswaEdit'];
        // Menggunakan query untuk mendapatkan id_siswa berdasarkan nama_siswa yang dipilih
        $queryGetSiswa = mysqli_query($conn, "SELECT id_siswa FROM siswa WHERE nama = '$siswa'");

        if ($queryGetSiswa) {
            $siswaData = mysqli_fetch_assoc($queryGetSiswa);
            $idSiswa = $siswaData['id_siswa'];
        } else {
            // siswa tidak ditemukan, tangani kesalahan di sini
            $_SESSION['flash_message'] = 'Siswa tidak ditemukan.';
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location: transaksi_masuk_siswa.php');
            exit;
        }

        $idSubKategori = $_POST['subKategoriEdit'];
        $getIdKategori = mysqli_query($conn, "SELECT id_kategori FROM sub_kategori_siswa WHERE id_sub_kategori = '$idSubKategori'");
        $rowKategori = mysqli_fetch_assoc($getIdKategori);    
        $idKategori = $rowKategori['id_kategori'];

        $bulan = $_POST['bulanEdit'];
        $penetapan = $_POST['nominalEdit'];
        $bulanIni = $_POST['bulanIniEdit'];
        $tunggakan = $_POST['tunggakanEdit'];
        $jumlah = $bulanIni + $tunggakan;
        $guru = $_POST['guruEdit'];
        // Menggunakan query untuk mendapatkan id_guru berdasarkan nama_guru yang dipilih
        $queryGetGuru = mysqli_query($conn, "SELECT id_guru FROM guru WHERE nama_lengkap = '$guru'");

        if ($queryGetGuru && mysqli_num_rows($queryGetGuru) > 0) {
            $guruData = mysqli_fetch_assoc($queryGetGuru);
            $idGuru = $guruData['id_guru'];
        } else {
            // siswa tidak ditemukan, tangani kesalahan di sini
            $_SESSION['flash_message'] = 'Guru tidak ditemukan.';
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal
            header('location:transaksi_masuk_siswa.php');
            exit;
        }

        $keterangan = $_POST['keteranganEdit'];    

        try {
            $queryEditTransSiswa = "UPDATE `transaksi_masuk_siswa` 
            SET 
            `tanggal`='$tanggalBayar', 
            `id_tahun_ajar`='$idTahunAjar', 
            `id_siswa`='$idSiswa',        
            `id_sub_kategori`='$idSubKategori',
            `id_kategori`='$idKategori',  
            `bulan`='$bulan',
            `bulan_ini`='$bulanIni',
            `tunggakan`='$tunggakan', 
            `jumlah`='$jumlah', 
            `id_guru`='$idGuru', 
            `keterangan`='$keterangan'
            WHERE 
            `id_tms`= '$idTransaksiMasukSiswa'";      
                        
            $editTransSiswa = mysqli_query($conn, $queryEditTransSiswa);

            if (!$editTransSiswa) {
                throw new Exception("Query edit gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $queryCek = "SELECT * 
            FROM `transaksi_masuk_siswa` 
            WHERE
            `tanggal`='$tanggalBayar' AND 
            `id_tahun_ajar`='$idTahunAjar' AND 
            `id_siswa`='$idSiswa' AND         
            `id_sub_kategori`='$idSubKategori' AND
            `id_kategori`='$idKategori' AND 
            `bulan`='$bulan' AND
            `bulan_ini`='$bulanIni' AND 
            `tunggakan`='$tunggakan' AND  
            `jumlah`='$jumlah' AND 
            `id_guru`='$idGuru' AND 
            `keterangan`='$keterangan'";

            $result = mysqli_query($conn, $queryCek);
            

            if ($result && mysqli_num_rows($result) === 1) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Edit transaksi siswa berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:transaksi_masuk_siswa.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data transaksi tidak berubah setelah diedit");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $queryCek . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal        
            header('location:transaksi_masuk_siswa.php');
            exit;
        }
    }

    // Hapus Transaksi Masuk Siswa
    if(isset($_POST['hapusTransaksiSiswa'])){
        $idTransaksiMasukSiswa = $_POST['idTms'];   

        try {
            $queryHapusTransSiswa = "DELETE FROM `transaksi_masuk_siswa` WHERE `id_tms`= '$idTransaksiMasukSiswa'";                     
                        
            $hapusTransSiswa = mysqli_query($conn, $queryHapusTransSiswa);

            if (!$hapusTransSiswa) {
                throw new Exception("Query hapus gagal"); // Lempar exception jika query gagal
            }

            // Query SELECT untuk memeriksa apakah data sudah masuk ke database
            $queryCek = "SELECT * FROM `transaksi_masuk_siswa` 
            WHERE `id_tms`= '$idTransaksiMasukSiswa'";

            $result = mysqli_query($conn, $queryCek);
            

            if ($result && mysqli_num_rows($result) === 0) {
                // Data sudah masuk ke database, Anda dapat mengatur pesan flash message berhasil
                $_SESSION['flash_message'] = 'Hapus transaksi siswa berhasil';
                $_SESSION['flash_message_class'] = 'alert-success'; // Berhasil
                header('location:transaksi_masuk_siswa.php');
                exit;
            } else {
                // Data tidak ada dalam database, itu berarti gagal
                throw new Exception("Data transaksi belum terhapus");
            }
        } catch (Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $queryCek . $e->getMessage();
            $_SESSION['flash_message_class'] = 'alert-danger'; // Gagal        
            header('location:transaksi_masuk_siswa.php');
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