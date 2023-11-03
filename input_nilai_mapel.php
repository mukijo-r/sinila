<?php
require 'function.php';
require 'cek.php';
require 'config.php';
date_default_timezone_set('Asia/Jakarta');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Halaman Input Nilai</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">
        <?php include 'navbar.php'; ?>
        <div id="layoutSidenav">
            <?php include 'sidebar.php'; ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h2 class="mt-4">Input Data</h2>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">PROSES / Input Data / Nilai Mapel</li>                            
                        </ol>                        
                        <br>
                        <div class="container-fluid px-4">
                            <div class="row">
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambahNilaiMapel">
                                        Input Nilai
                                    </button>
                                </div>
                                <div class="col-md-8">
                                    <?php
                                    if (isset($_SESSION['flash_message'])) {
                                        $message_class = isset($_SESSION['flash_message_class']) ? $_SESSION['flash_message_class'] : 'alert-success';
                                        echo '<div class="alert ' . $message_class . ' text-center">' . $_SESSION['flash_message'] . '</div>';
                                        unset($_SESSION['flash_message']); // Hapus pesan flash setelah ditampilkan
                                    }
                                    
                                    ?>
                                </div>
                            </div>
                        </div>                    
                        <br>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                Daftar Transaksi Masuk
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Tanggal</th>    
                                            <th>Tahun Ajar</th>
                                            <th>Semester</th>
                                            <th>Kelas</th>
                                            <th>Nama Siswa</th>                                            
                                            <th>Mapel</th>
                                            <th>LM</th>
                                            <th>TP</th>
                                            <th>Nilai</th>
                                            <th>Penilai</th>
                                            <th colspan='2'>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                    $dataNilaiMapel = mysqli_query($conn, "SELECT
                                    nm.*,
                                    ta.tahun_ajar AS tahun_ajar,
                                    s.nama AS nama_siswa,
                                    s.id_kelas AS id_kelas,
                                    m.mapel as mapel
                                    FROM nilai_mapel nm
                                    LEFT JOIN tahun_ajar ta ON nm.id_tahun_ajar = ta.id_tahun_ajar
                                    LEFT JOIN siswa s ON nm.id_siswa = s.id_siswa
                                    LEFT JOIN mapel m ON nm.id_mapel = m.id_mapel
                                    ORDER BY nm.tanggal DESC;");

                                    $totalEntries = mysqli_num_rows($dataNilaiMapel);
                                    $i = $totalEntries;
                                    
                                    while($data=mysqli_fetch_array($dataNilaiMapel)){
                                        $idNilaiMapel = $data['id_nm'];                                       
                                        $tanggal = $data['tanggal'];
                                        $tanggalTampil = date("d-m-Y", strtotime($tanggal));
                                        $tahunAjar = $data['tahun_ajar'];
                                        $semester = $data['semester'];                                      
                                        $kelas = $data['id_kelas'];
                                        $idSiswa = $data['id_siswa'];
                                        $namaSiswa = $data['nama_siswa'];                                        
                                        $mapel = $data['mapel'];
                                        $lingkupMateri = $data['lingkup_materi'];
                                        $tujuanPembelajaran = $data['tujuan_pembelajaran'];
                                        $nilai = $data['nilai'];
                                        $guruPenilai = $data['guru_penilai'];  
                                        ?>
                                        <tr>
                                            <td><?=$i--;?></td>
                                            <td><?=$tanggalTampil;?></td>
                                            <td><?=$tahunAjar;?></td>
                                            <td><?=$semester;?></td>
                                            <td><?=$kelas;?></td>
                                            <td><?=$namaSiswa;?></td>                                            
                                            <td><?=$mapel;?></td>
                                            <td><?=$lingkupMateri;?></td>
                                            <td><?=$tujuanPembelajaran;?></td>
                                            <td><?=$nilai;?></td>
                                            <td><?=$guruPenilai;?></td>
                                            <td>
                                                <button type="button" class="btn btn-warning" name="tblEdit" data-bs-toggle="modal" data-bs-target="#modalEditTransSiswa<?=$idNilaiMapel;?>">Edit</button>
                                                <button type="button" class="btn btn-danger" name="tblHapus" data-bs-toggle="modal" data-bs-target="#modalHapusTransSiswa<?=$idNilaiMapel;?>">Hapus</button> 
                                            </td>
                                        </tr>

                                        <!-- Modal edit Transaksi Masuk Siswa -->
                                        <div class="modal fade" id="modalEditTransSiswa<?=$idNilaiMapel;?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <!-- Modal Header -->
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Edit Transaksi Masuk Siswa</h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <!-- Modal Body -->
                                                    <form method="post">
                                                        <div class="modal-body">
                                                            <div>
                                                                <label for="tanggal">Tanggal Bayar :</label>       
                                                                <input type="datetime-local" name="tanggal" value="<?=$tanggalBayar; ?>" class="form-control">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="kelasEdit">Kelas :</label>
                                                                <select class="form-select" name="kelasEdit" id="kelasEdit" aria-label="Kelas">
                                                                    <option selected><?=$kelas;?></option>
                                                                    <?php
                                                                    // Ambil data kelas dari tabel kelas
                                                                    $queryKelas = mysqli_query($conn, "SELECT id_kelas, nama_kelas FROM kelas");
                                                                    while ($kelas = mysqli_fetch_assoc($queryKelas)) {
                                                                        echo '<option value="' . $kelas['id_kelas'] . '">' . $kelas['nama_kelas'] . '</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="siswaEdit">Siswa :</label>
                                                                <select name="siswaEdit" class="form-select" id="siswaEdit" aria-label="Siswa">
                                                                    <option selected><?=$namaSiswa;?></option>
                                                                    <!-- Opsi siswa akan diisi secara dinamis menggunakan JavaScript -->
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="subKategoriEdit">Sub Kategori : </label>
                                                                <select class="form-select" name="subKategoriEdit" id="subKategoriEdit" aria-label="subKategori">
                                                                    <option value=<?=$idSubKategori;?>><?=$subKategori;?></option>
                                                                    <?php
                                                                    // Ambil data kelas dari tabel kelas
                                                                    $querySubKategori = mysqli_query($conn, "SELECT id_sub_kategori, nama_sub_kategori FROM sub_kategori_siswa");
                                                                    while ($subKategori = mysqli_fetch_assoc($querySubKategori)) {
                                                                        echo '<option value="' . $subKategori['id_sub_kategori'] . '">' . $subKategori['nama_sub_kategori'] . '</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>                                                           
                                                            <div class="mb-3">
                                                                <label for="bulanEdit">Bulan :</label>
                                                                <select class="form-select" name="bulanEdit" id="bulanEdit" aria-label="Edit Bulan">
                                                                    <option selected><?=$bulan;?></option>
                                                                    <option value="Januari">Januari</option>
                                                                    <option value="Februari">Februari</option>
                                                                    <option value="Maret">Maret</option>
                                                                    <option value="April">April</option>
                                                                    <option value="Mei">Mei</option>
                                                                    <option value="Juni">Juni</option>
                                                                    <option value="Juli">Juli</option>
                                                                    <option value="Agustus">Agustus</option>
                                                                    <option value="September">September</option>
                                                                    <option value="Oktober">Oktober</option>
                                                                    <option value="November">November</option>
                                                                    <option value="Desember">Desember</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="nominalEdit">Penetapan :</label>                     
                                                                <input type="text" name="nominalEdit" id="nominalEdit" value="<?=$penetapan;?>" class="form-control" readonly>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="bulanIniEdit">Bulan ini :</label>                     
                                                                <input type="text" name="bulanIniEdit" id="bulanIniEdit" value="<?=$bulanIni;?>" class="form-control">
                                                            </div><div class="mb-3">
                                                                <label for="tunggakanEdit">Tunggakan :</label>                     
                                                                <input type="text" name="tunggakanEdit" id="tunggakanEdit" value="<?=$tunggakan;?>" class="form-control" readonly>
                                                            </div>
                                                            <!-- <div class="mb-3">
                                                                <label for="jumlahEdit">jumlah Pembayaran :</label>                        
                                                                <input type="number" name="jumlahEdit" id="jumlahEdit" value="<?=$nominal;?>" class="form-control">
                                                            </div> -->
                                                            <div class="mb-3">   
                                                                <label for="guruEdit">Penerima :</label>                     
                                                                <select name="guruEdit" class="form-select" id="guruEdit" value="<?=$namaGuru;?>" aria-label="Guru">>
                                                                <option selected><?=$namaGuru;?></option>
                                                                    <?php
                                                                    // Ambil data guru dari tabel guru
                                                                    $queryGuru = mysqli_query($conn, "SELECT id_guru, nama_lengkap FROM guru");
                                                                    while ($guru = mysqli_fetch_assoc($queryGuru)) {
                                                                        echo '<option value="' . $guru['id_guru'] . '">' . $guru['nama_lengkap'] . '</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                            <label for="keteranganEdit">Keterangan :</label>   
                                                                <textarea name="keteranganEdit" class="form-control" id="keteranganEdit" value="<?=$keterangan;?>" rows="2"><?=$keterangan;?></textarea>
                                                            </div>
                                                        </div>
                                                        <!-- Modal Footer -->
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                            <input type="hidden" name="id_tms_masuk" value="<?=$idTransaksiMasukSiswa;?>">
                                                            <button type="submit" class="btn btn-primary" name="editTransSiswa">Simpan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    <!-- Modal Hapus Transaksi Masuk Siswa-->
                                    <div class="modal fade" id="modalHapusTransSiswa<?=$idTransaksiMasukSiswa;?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">

                                            <!-- Modal Header -->
                                            <div class="modal-header">
                                                <h4 class="modal-title">Hapus Transaksi Siswa ini?</h4>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <!-- Modal body -->
                                            
                                            <form method="post">
                                            <div class="modal-body">
                                                <h5>Anda yakin ingin menghapus data pembayaran <u> <?=$namaSiswa;?> </u> dengan nominal Rp. <b><?=$nominal;?>?</h5>
                                                
                                            </div>
                                            <div class="text-center">
                                                <input type="hidden" name="idTms" value="<?=$idTransaksiMasukSiswa;?>">
                                                <button type="submit" class="btn btn-danger" name="hapusTransaksiSiswa">Hapus</button> 
                                            </div>
                                            <br> 
                                            </form>       
                                            </div>
                                        </div>
                                    <?php
                                    };

                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>
        <script>
        </script>
    </body>   

    <!-- Modal Tambah Nilai Siswa -->
<div class="modal fade" id="modalTambahNilaiMapel">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Tambah Nilai Mapel Siswa kelas <?=$kelas;?></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- Modal Body -->
            <form method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="semester">Semester :</label><br>
                        <select class="form-select" name="semester" id="semester" aria-label="Semester" required>>
                            <option selected>Pilih semester</option>                            
                            <option value="Ganjil">Ganjil</option>
                            <option value="Genap">Genap</option>
                        </select>
                    </div>      
                    <div class="mb-3">
                        <label for="siswa">Siswa :</label>
                        <select name="siswa" class="form-select" id="siswa" aria-label="Siswa" required>>
                            <option selected disabled>Pilih Siswa</option>
                            <?php
                            // Ambil data siswa dari tabel siswa
                            $querySiswa = mysqli_query($conn, "SELECT id_siswa, nama FROM siswa WHERE id_kelas = $kelas");
                            while ($rowSiswa = mysqli_fetch_assoc($querySiswa)) {
                                echo '<option value="' . $rowSiswa['id_siswa'] . '">' . $rowSiswa['nama'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="mapel">Mata Pelajaran :</label>
                        <select class="form-select" name="mapel" id="mapel" aria-label="mapel" required>
                            <option selected disabled>Pilih Mapel</option>
                            <?php
                            // Ambil data kelas dari tabel kelas
                            $queryMapel = mysqli_query($conn, "SELECT id_mapel, mapel FROM mapel");
                            while ($rowMapel = mysqli_fetch_assoc($queryMapel)) {
                                echo '<option value="' . $rowMapel['id_mapel'] . '">' . $rowMapel['mapel'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="lingkupMateri">Lingkup Materi :</label>
                        <select class="form-select" name="lingkupMateri" id="lingkupMateri" aria-label="lingkupMateri" required>
                            <option selected disabled>Pilih LM</option>                         
                            <option value="LM 1">LM 1</option>
                            <option value="LM 2">LM 2</option>
                            <option value="LM 3">LM 3</option>
                            <option value="LM 4">LM 4</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tujuanPembelajaran">Tujuan Pembelajaran :</label>
                        <select class="form-select" name="tujuanPembelajaran" id="tujuanPembelajaran" aria-label="tujuanPembelajaran" required>
                            <option selected disabled>Pilih TP</option>                         
                            <option value="TP1">TP 1</option>
                            <option value="TP2">TP 2</option>
                            <option value="TP3">TP 3</option>
                            <option value="TP4">TP 4</option>
                            <option value="TP5">TP 5</option>
                            <option value="TP6">TP 6</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="nilai">Nilai :</label>                        
                        <input type="number" name="nilai" id="nilai" class="form-control" required>                    
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary" name="tambahNilaiMapel">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

</html>
