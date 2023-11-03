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
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambahTransSiswa">
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
                                            <th>Mata Pelajaran</th>
                                            <th>Lingkup Materi</th>
                                            <th>Tujuan Pendidikan</th>
                                            <th>Nilai</th>
                                            <th colspan='2'>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                    $dataTransaksiSiswa = mysqli_query($conn, "SELECT
                                    tms.*,
                                    ta.tahun_ajar AS tahun_ajar,
                                    s.nama AS nama_siswa, 
                                    g.nama_lengkap AS nama_guru, 
                                    kat.nama_kategori AS kategori,
                                    s.id_kelas AS id_kelas,
                                    subkat.nama_sub_kategori AS sub_kategori_siswa
                                    FROM transaksi_masuk_siswa tms
                                    LEFT JOIN tahun_ajar ta ON tms.id_tahun_ajar = ta.id_tahun_ajar
                                    LEFT JOIN siswa s ON tms.id_siswa = s.id_siswa
                                    LEFT JOIN guru g ON tms.id_guru = g.id_guru
                                    LEFT JOIN kategori kat ON tms.id_kategori = kat.id_kategori
                                    LEFT JOIN sub_kategori_siswa subkat ON tms.id_sub_kategori = subkat.id_sub_kategori
                                    ORDER BY tms.tanggal DESC;");

                                    $totalEntries = mysqli_num_rows($dataTransaksiSiswa);
                                    $i = $totalEntries;
                                    
                                    while($data=mysqli_fetch_array($dataTransaksiSiswa)){
                                        $idTransaksiMasukSiswa = $data['id_tms'];                                         
                                        $tanggal = $data['tanggal'];
                                        $tanggalTampil = date("d-m-Y", strtotime($tanggal));
                                        $tanggalBayar = date("Y-m-d H:i:s", strtotime($tanggal));
                                        $tahunAjar = $data['tahun_ajar'];                                        
                                        $kelas = $data['id_kelas'];
                                        $idSiswa = $data['id_siswa'];
                                        $namaSiswa = $data['nama_siswa'];                                        
                                        $kategori = $data['kategori'];
                                        $idSubKategori = $data['id_sub_kategori'];
                                        
                                        // Menggunakan query untuk mendapatkan subkategori berdasarkan id_subkategori yang dipilih
                                        $queryGetSubKategori = mysqli_query($conn, "SELECT nama_sub_kategori FROM sub_kategori_siswa WHERE id_sub_kategori = '$idSubKategori'");
                                        $rowSubKategori = mysqli_fetch_assoc($queryGetSubKategori);
                                        $subKategori = $rowSubKategori['nama_sub_kategori'];  

                                        $namaGuru = $data['nama_guru'];
                                        $bulan = $data['bulan'];
                                        $penetapan = $data['penetapan'];
                                        $bulanIni = $data['bulan_ini'];
                                        $tunggakan = $data['tunggakan'];
                                        $nominal = $data['bulan_ini'];
                                        $jumlah = $data['jumlah'];
                                        $keterangan = $data['keterangan'];
                                        $idSiswa = $data['id_siswa'];


                                        // Hitung tunggakan
                                        

                                        // Menghitung saldo
                                        $queryMasuk = mysqli_query($conn, "SELECT SUM(jumlah) AS total_masuk FROM transaksi_masuk_siswa WHERE id_sub_kategori = '$idSubKategori' AND tanggal <= '$tanggalBayar'");
                                        $queryKeluar = mysqli_query($conn, "SELECT SUM(jumlah) AS total_keluar FROM transaksi_keluar_siswa WHERE id_sub_kategori = '$idSubKategori' AND tanggal <= '$tanggalBayar'");

                                        $totalMasuk = 0;
                                        $totalKeluar = 0;

                                        if ($rowMasuk = mysqli_fetch_assoc($queryMasuk)) {
                                            $totalMasuk = $rowMasuk['total_masuk'];
                                        }

                                        if ($rowKeluar = mysqli_fetch_assoc($queryKeluar)) {
                                            $totalKeluar = $rowKeluar['total_keluar'];
                                        }

                                        $saldo = $totalMasuk - $totalKeluar;

                                        ?>
                                        <tr>
                                            <td><?=$i--;?></td>
                                            <td><?=$tanggalTampil;?></td>
                                            <td><?=$tahunAjar;?></td>
                                            <td><?=$kelas;?></td>
                                            <td><?=$namaSiswa;?></td>                                            
                                            <td><?=$kategori;?></td>
                                            <td><?=$subKategori;?></td>
                                            <td><?=$bulan;?></td>
                                            <td><?="Rp " . number_format($penetapan, 0, ',', '.');?></td>
                                            <td><?="Rp " . number_format($nominal, 0, ',', '.');?></td>
                                            <td><?="Rp " . number_format($tunggakan, 0, ',', '.');?></td>
                                            <td><?="Rp " . number_format($jumlah, 0, ',', '.');?></td>
                                            <td><?="Rp " . number_format($saldo, 0, ',', '.');?></td>
                                            <td><?=$namaGuru;?></td>
                                            <td><?=$keterangan;?></td>
                                            <td>
                                                <button type="button" class="btn btn-warning" name="tblEdit" data-bs-toggle="modal" data-bs-target="#modalEditTransSiswa<?=$idTransaksiMasukSiswa;?>">Edit</button>
                                                <input type="hidden" name="idTms" value="<?=$idTransaksiMasukSiswa;?>">
                                                <button type="button" class="btn btn-danger" name="tblHapus" data-bs-toggle="modal" data-bs-target="#modalHapusTransSiswa<?=$idTransaksiMasukSiswa;?>">Hapus</button> 
                                            </td>
                                        </tr>

                                        <!-- Modal edit Transaksi Masuk Siswa -->
                                        <div class="modal fade" id="modalEditTransSiswa<?=$idTransaksiMasukSiswa;?>">
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

    <!-- Modal Tambah Transaksi Siswa -->
<div class="modal fade" id="modalTambahTransSiswa">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Tambah Transaksi Siswa</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- Modal Body -->
            <form method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tanggal">Tanggal Bayar :</label>
                        <?php $tanggalSaatIni = date('Y-m-d\TH:i:s', time());?>
                        <input type="datetime-local" name="tanggal" value="<?=$tanggalSaatIni;?>" class="form-control">
                    </div>
                    <div class="mb-3">
                            <label for="kelas">Kelas :</label>
                            <select class="form-select" name="kelas" id="kelas" aria-label="Kelas">
                                <option selected disabled>Pilih Kelas</option>
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
                            <label for="siswa">Siswa :</label>
                            <select name="siswa" class="form-select" id="siswa" aria-label="Siswa">
                                <option selected disabled>Pilih Kelas Terlebih Dahulu</option>
                                <!-- Opsi siswa akan diisi secara dinamis menggunakan JavaScript -->
                            </select>
                    </div>
                    <div class="mb-3">
                            <label for="subKategori">Sub Kategori :</label>
                            <select class="form-select" name="subKategori" id="subKategori" aria-label="subKategori" required>
                                <option selected disabled>Pilih Kategori</option>
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
                        <label for="bulan">Bulan :</label><br>
                        <select class="form-select" name="bulan" id="bulan" aria-label="Bulan">
                            <option selected>Pilih bulan</option>                            
                            <option value="Juli">Juli</option>
                            <option value="Agustus">Agustus</option>
                            <option value="September">September</option>
                            <option value="Oktober">Oktober</option>
                            <option value="November">November</option>
                            <option value="Desember">Desember</option>
                            <option value="Januari">Januari</option>
                            <option value="Februari">Februari</option>
                            <option value="Maret">Maret</option>
                            <option value="April">April</option>
                            <option value="Mei">Mei</option>
                            <option value="Juni">Juni</option>
                            </select>
                    </div>              
                    <div class="mb-3">
                        <label for="nominal">Penetapan :</label>                        
                        <input type="text" name="nominal" id="nominal" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="bulanIni">Bulan ini :</label>                        
                        <input type="number" name="bulanIni" id="bulanIni" class="form-control">
                    </div><div class="mb-3">
                        <label for="tunggakan">Tunggakan :</label>                        
                        <input type="number" name="tunggakan" id="tunggakan" class="form-control">
                    </div>
                    <div class="mb-3">   
                        <label for="guru">Penerima :</label>                     
                        <select name="guru" class="form-select" id="guru" aria-label="Guru">>
                        <option selected disabled>Guru Penerima</option>
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
                    <label for="keterangan">Keterangan :</label>   
                        <textarea name="keterangan" class="form-control" id="keterangan" rows="2"></textarea>
                    </div>
                </div>
                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" name="tambahTransMasukSiswa">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Modal Tambah Transaksi Siswa Kolektif-->
    <div class="modal fade" id="modalTambahTransSiswaKolektif">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Tambah Transaksi Siswa Kolektif</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- Modal Body -->
            <form method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tanggal">Tanggal Bayar :</label>
                        <?php $tanggalSaatIni = date('Y-m-d\TH:i:s', time());?>
                        <input type="datetime-local" name="tanggalKolektif" value="<?=$tanggalSaatIni;?>" class="form-control">
                    </div>
                    <div class="mb-3">
                            <label for="kelas">Kelas :</label>
                            <select class="form-select" name="kelasKolektif" id="kelasKolektif" aria-label="Kelas">
                                <option selected disabled>Pilih Kelas</option>
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
                            <label for="subKategori">Sub Kategori :</label>
                            <select class="form-select" name="subKategoriKolektif" id="subKategoriKolektif" aria-label="subKategori" required>
                                <option selected disabled>Pilih Kategori</option>
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
                        <label for="bulanIni">Jumlah total :</label>                        
                        <input type="number" name="bulanIniKolektif" id="bulanIniKolektif" class="form-control">
                    </div>                    
                    <div class="mb-3">   
                        <label for="guru">Penerima :</label>                     
                        <select name="guru" class="form-select" id="guru" aria-label="Guru">>
                        <option selected disabled>Guru Penerima</option>
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
                    <label for="keterangan">Keterangan :</label>   
                        <textarea name="keterangan" class="form-control" id="keterangan" rows="2"></textarea>
                    </div>
                </div>
                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" name="tambahTransSiswaKolektif">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Temukan elemen "kelas" dan "siswa" pada Tambah Transaksi Tabungan
        var kelasDropdown = document.getElementById('kelas');
        var siswaDropdown = document.getElementById('siswa');

        var kelasDropdownEdit = document.getElementById('kelasEdit');
        var siswaDropdownEdit = document.getElementById('siswaEdit');

        // Temukan elemen "subkategori" dan "nominal"
        var kategoriDropdown = document.getElementById('subKategori');
        var nominalInput = document.getElementById('nominal');

        var kategoriDropdownKolektif = document.getElementById('subKategoriKolektif');
        //var nominalInputKolektif = document.getElementById('nominalKolektif');
        var kelasDropdownKolektif = document.getElementById('kelasKolektif');
        var jumlahInputKolektif = document.getElementById('bulanIniKolektif');

        var kategoriDropdownEdit = document.getElementById('subKategoriEdit');
        var nominalInputEdit = document.getElementById('nominalEdit');

        // Temukan elemen bulan dan tunggakan
        var bulanDropdown = document.getElementById('bulan');
        var tunggakanInput = document.getElementById('tunggakan');

        // Tambahkan event listener ketika nilai "kelas" berubah pada Tambah Transaksi siswa
        kelasDropdown.addEventListener('change', function() {
            var selectedKelas = kelasDropdown.value;            

            // Gunakan AJAX untuk mengambil data siswa berdasarkan kelas
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_siswa_by_kelas.php?kelas=' + selectedKelas, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Parse data JSON yang diterima
                    var dataSiswa = JSON.parse(xhr.responseText);                    

                    // Bersihkan dropdown "siswa" dan tambahkan opsi-opsi baru
                    siswaDropdown.innerHTML = '<option selected disabled>Pilih Siswa</option>';
                    dataSiswa.forEach(function(siswa) {
                        siswaDropdown.innerHTML += '<option value="' + siswa.id_siswa + '">' + siswa.nama + '</option>';                        
                    });                               
                }
            };
            xhr.send();
        });

        // Tambahkan event listener ketika nilai "kelas" berubah pada Edit Transaksi siswa
        kelasDropdownEdit.addEventListener('change', function() {
            var selectedKelasEdit = kelasDropdownEdit.value;            

            // Gunakan AJAX untuk mengambil data siswa berdasarkan kelas
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_siswa_by_kelas.php?kelas=' + selectedKelasEdit, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Parse data JSON yang diterima
                    var dataSiswaEdit = JSON.parse(xhr.responseText);                    

                    // Bersihkan dropdown "siswa" dan tambahkan opsi-opsi baru
                    siswaDropdownEdit.innerHTML = '<option selected disabled>Pilih Siswa</option>';
                    dataSiswaEdit.forEach(function(siswa) {
                        siswaDropdownEdit.innerHTML += '<option value="' + siswa.id_siswa + '">' + siswa.nama + '</option>';
                        
                    });                
                                      
                }
            };
            xhr.send();
        });

        // Tambahkan event listener ketika nilai "siswa" berubah
        siswaDropdown.addEventListener('change', function() {
            updateNominalValue();
        });

        // Tambahkan event listener ketika nilai "kategori" berubah
        kategoriDropdown.addEventListener('change', function() {
            updateNominalValue();
        });

        // Fungsi untuk mengambil nilai nominal yang sesuai
        function updateNominalValue() {
            // Dapatkan nilai terpilih dari dropdown
            var selectedSiswa = siswaDropdown.value;
            var selectedKategori = kategoriDropdown.value;

            // Lakukan AJAX request untuk mengambil nilai nominal
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_nominal.php?siswa=' + selectedSiswa + '&subKategori=' + selectedKategori, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var responseText = xhr.responseText.trim(); // Hapus spasi di awal dan akhir
                    if (/^\d+$/.test(responseText)) { // Periksa apakah respons hanya mengandung angka
                        var nominalValue = parseInt(responseText.replace(/"/g, '')); // Hapus tanda kutip ganda
                        nominalInput.value = nominalValue;
                    } else {
                        console.error('Nilai nominal tidak valid: ' + responseText);
                    }
                }
            };
            xhr.send();
        }

        // Tambahkan event listener ketika nilai "siswa" berubah
        siswaDropdownEdit.addEventListener('change', function() {
            updateNominalValueEdit();
        });

        // Tambahkan event listener ketika nilai "kategori" berubah
        kategoriDropdownEdit.addEventListener('change', function() {
            updateNominalValueEdit();
        });

        // Fungsi untuk mengambil nilai nominal yang sesuai
        function updateNominalValueEdit() {
            // Dapatkan nilai terpilih dari dropdown
            var selectedSiswaEdit = siswaDropdownEdit.value;
            var selectedKategoriEdit = kategoriDropdownEdit.value;

            // Lakukan AJAX request untuk mengambil nilai nominal
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_nominal.php?siswa=' + selectedSiswaEdit + '&subKategori=' + selectedKategoriEdit, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var responseText = xhr.responseText.trim(); // Hapus spasi di awal dan akhir
                    if (/^\d+$/.test(responseText)) { // Periksa apakah respons hanya mengandung angka
                        var nominalValueEdit = parseInt(responseText.replace(/"/g, '')); // Hapus tanda kutip ganda
                        nominalInputEdit.value = nominalValueEdit;
                    } else {
                        console.error('Nilai nominal tidak valid: ' + responseText);
                    }
                }
            };
            xhr.send();
        }

        // Tambahkan event listener ketika nilai "siswa", "kategori" dan "bulan" berubah
        bulanDropdown.addEventListener('change', function() {
            updateTunggakanValue();
        });

        // Fungsi untuk mendapatkan nilai tunggakan yang sesuai
        function updateTunggakanValue() {
            //dapatkan nilai terpilih dari dropdown
            var selectedSiswa = siswaDropdown.value;
            var selectedKategori = kategoriDropdown.value;
            var selectedBulan = bulanDropdown.value;

            //lakukan AJAX request untuk mengambil nilai tunggakan
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_tunggakan.php?siswa=' + selectedSiswa + '&subKategori=' + selectedKategori + '&bulan=' + selectedBulan, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var responseText = xhr.responseText.trim(); // Hapus spasi di awal dan akhir
                    if (/^\d+$/.test(responseText)) { // Periksa apakah respons hanya mengandung angka
                        var tunggakanValue = parseInt(responseText.replace(/"/g, '')); // Hapus tanda kutip ganda
                        tunggakanInput.value = tunggakanValue;
                    } else {
                        console.error('Nilai nominal tidak valid: ' + responseText);
                    }
                }
            };
            xhr.send();
        }

        // Tambahkan event listener ketika nilai "kategori" berubah
        kategoriDropdownKolektif.addEventListener('change', function() {
            //updateNominalKolektifValue();
            updateTotalKolektifValue();
        });

        // // Fungsi untuk mengambil nilai nominal yang sesuai
        // function updateNominalKolektifValue() {
        //     // Dapatkan nilai terpilih dari dropdown
        //     var selectedKategoriKolektif = kategoriDropdownKolektif.value;

        //     // Lakukan AJAX request untuk mengambil nilai nominal
        //     var xhr = new XMLHttpRequest();
        //     xhr.open('GET', 'get_nominal_kolektif.php?subKategori=' + selectedKategoriKolektif, true);
        //     xhr.onreadystatechange = function() {
        //         if (xhr.readyState === 4 && xhr.status === 200) {
        //             var responseText = xhr.responseText.trim(); // Hapus spasi di awal dan akhir
        //             if (/^\d+$/.test(responseText)) { // Periksa apakah respons hanya mengandung angka
        //                 var nominalValueKolektif = parseInt(responseText.replace(/"/g, '')); // Hapus tanda kutip ganda
        //                 nominalInputKolektif.value = nominalValueKolektif;
        //             } else {
        //                 console.error('Nilai nominal tidak valid: ' + responseText);
        //             }
        //         }
        //     };
        //     xhr.send();
        // }

        // Fungsi untuk mengambil nilai nominal yang sesuai
        function updateTotalKolektifValue() {
            // Dapatkan nilai terpilih dari dropdown
            var selectedKelasKolektif = kelasDropdownKolektif.value;
            var selectedKategoriKolektif = kategoriDropdownKolektif.value;

            // Lakukan AJAX request untuk mengambil nilai nominal
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_total_kolektif.php?kelas=' + selectedKelasKolektif + '&subKategori=' + selectedKategoriKolektif, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var responseText = xhr.responseText.trim(); // Hapus spasi di awal dan akhir
                    if (/^\d+$/.test(responseText)) { // Periksa apakah respons hanya mengandung angka
                        var jumlahValueKolektif = parseInt(responseText.replace(/"/g, '')); // Hapus tanda kutip ganda
                        jumlahInputKolektif.value = jumlahValueKolektif;
                    } else {
                        console.error('Nilai nominal tidak valid: ' + responseText);
                    }
                }
            };
            xhr.send();
        }


    });
</script>


    
</html>
