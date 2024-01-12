<?php
// Include the main TCPDF library (search for installation path).
require_once('TCPDF-main/tcpdf.php');
$conn = mysqli_connect("localhost:3306","root","","sdk");

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {
    public function Header() {
        // Tambahkan baris baru
        $this->SetCellPaddings(0, 0.2);
        $this->Ln(50); // Pindahkan ke posisi tertentu sebelum menambahkan gambar latar belakang
    
        // Gambar latar belakang (di tengah halaman)
        $backgroundImage = K_PATH_IMAGES.'bg_rapot2.jpg';
        $this->Image($backgroundImage, 0, 68, 150, 180, '', '', '', true, 300, 'C', false, false, false);
    
        // Logo
        $image_file = K_PATH_IMAGES.'logo.jpg';
        $this->Image($image_file, 30, 8, 20, '', 'JPG', '', 'T', true, 50, '', false, false, 0, false, false, false);
    
        // Geser ke posisi baru untuk tulisan
        $this->SetXY(10, 12);
    
        // Tambahkan baris baru
        $this->SetCellPaddings(0, 0);
    
        $this->SetFont('helvetica', '', 20);
        $this->Cell(0, 0, 'SD KATOLIK BHAKTI', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln();  
        $this->SetFont('helvetica', '', 11);
        $this->Cell(0, 6, 'Jl. Ki. Hajar Dewantoro Tlp. (0333) 631698', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln(); 
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 6, 'ROGOJAMPI - BANYUWANGI 68462', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->SetLineWidth(0); // Atur ketebalan garis
        $this->Line(10, 32, 200, 32); // Koordinat untuk garis mendatar
        $this->Line(10, 31, 200, 31);
        $this->Ln();
    }    
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Mukijo');
$pdf->SetTitle('Rapot Sisipan');
$pdf->SetSubject('TCPDF');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetFont('times', '', 14);
// set margins
$pdf->SetMargins(13, 30, 0);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// add a page
$pdf->AddPage();

$idTahunAjar = $_POST['idTahunAjar'];
$semester = $_POST['semester'];


if ($semester == 'Ganjil') {
    $semCap = 'GANJIL';
} elseif ($semester == 'Genap') {
    $semCap = 'GENAP';
}

$kelas = $_POST['kelas'];

if ($kelas == 1){
    $stringKelas = 'I (Satu)';
} elseif ($kelas == 2) {
    $stringKelas = 'II (Dua)';
} elseif ($kelas == 3) {
    $stringKelas = 'III (Tiga)';
} elseif ($kelas == 4) {
    $stringKelas = 'IV (Empat)';
} elseif ($kelas == 5) {
    $stringKelas = 'V (Lima)';
} elseif ($kelas == 6) {
    $stringKelas = 'VI (Enam)';
} else {
    $stringKelas = '';
}

$idSiswa = $_POST['idSiswa'];
$nomorUrut = $_POST['nomorUrut'];

$querySiswa = mysqli_query($conn, "SELECT nama, nis, nisn FROM siswa WHERE id_siswa='$idSiswa'");
$rowSiswa = mysqli_fetch_assoc($querySiswa); 
$namaSiswa = $rowSiswa['nama'];
$nis = $rowSiswa['nis'];
$nisn = $rowSiswa['nisn'];

$tplm1 = $_POST['tplm1'];
$tplm2 = $_POST['tplm2'];

$queryTahunAjar = mysqli_query($conn, "SELECT tahun_ajar FROM tahun_ajar WHERE id_tahun_ajar = '$idTahunAjar'");
$rowTahunAjar = mysqli_fetch_array($queryTahunAjar);
$tahunAjar = $rowTahunAjar['tahun_ajar'];

$queryKepsek = mysqli_query($conn, "SELECT nip, nama_lengkap FROM guru WHERE jabatan = 'Kepala Sekolah'");
$rowKepsek= mysqli_fetch_array($queryKepsek);
$nipKepsek = $rowKepsek['nip'];
$kepsek = $rowKepsek['nama_lengkap'];

$queryGuru = mysqli_query($conn, "SELECT kelas.*, guru.nip, guru.nama_lengkap
FROM kelas
JOIN guru ON kelas.id_guru = guru.id_guru
WHERE id_kelas = $kelas
;");

$rowGuru= mysqli_fetch_array($queryGuru);
$nipWaliKelas = $rowGuru['nip'];
$waliKelas = $rowGuru['nama_lengkap'];

$pdf->SetFont('helvetica', '', 10);
$txt = <<<EOD

LAPORAN PENILAIAN HASIL BELAJAR
TENGAH SEMESTER  $semCap
TAHUN PELAJARAN $tahunAjar


EOD;
$pdf->SetFont('times', '', 12);
$pdf->SetMargins(15, 15, 0);
$pdf->SetCellMargins(0, 0, 0, 0);

$html = '<div><table style="font-family: helvetica; font-size: 12px;"><tr><td style="width: 60%">

    <table>
        <tr>
            <th style="font-weight: bold; width: 35%">Nama</th>
            <th style="width: 6%">:</th>
            <th style="width: 45%">' . $namaSiswa . '</th>       
        </tr>
        <tr>
            <th style="font-weight: bold; width: 35%">No.Induk/NISN</th>
            <th style="width: 6%">:</th>
            <th style="width: 45%">' . $nis. '/' . $nisn. '</th>       
        </tr>
    </table></td><td>

    <table>
        <tr>
            <th style="font-weight: bold; width: 30%">Kelas</th>
            <th style="width: 6%">:</th>
            <th style="width: 45%">' . $stringKelas . '</th>       
        </tr>
        <tr>
            <th style="font-weight: bold; width: 30%">No.Absen</th>
            <th style="width: 6%">:</th>
            <th style="width: 45%">' . $nomorUrut . '</th>       
        </tr>
    </table>

</td></tr></table></div>';

$queryRapotSisipan = "SELECT m.id_mapel, m.mapel,
                    MAX(CASE WHEN nm.lingkup_materi = 'LM 1' AND nm.tujuan_pembelajaran = 'TP1' THEN nm.nilai ELSE 0 END) AS LM1_TP1,
                    MAX(CASE WHEN nm.lingkup_materi = 'LM 1' AND nm.tujuan_pembelajaran = 'TP2' THEN nm.nilai ELSE 0 END) AS LM1_TP2,
                    MAX(CASE WHEN nm.lingkup_materi = 'LM 1' AND nm.tujuan_pembelajaran = 'TP3' THEN nm.nilai ELSE 0 END) AS LM1_TP3,
                    MAX(CASE WHEN nm.lingkup_materi = 'LM 1' AND nm.tujuan_pembelajaran = 'TP4' THEN nm.nilai ELSE 0 END) AS LM1_TP4,
                    MAX(CASE WHEN nm.lingkup_materi = 'LM 2' AND nm.tujuan_pembelajaran = 'TP1' THEN nm.nilai ELSE 0 END) AS LM2_TP1,
                    MAX(CASE WHEN nm.lingkup_materi = 'LM 2' AND nm.tujuan_pembelajaran = 'TP2' THEN nm.nilai ELSE 0 END) AS LM2_TP2,
                    MAX(CASE WHEN nm.lingkup_materi = 'LM 2' AND nm.tujuan_pembelajaran = 'TP3' THEN nm.nilai ELSE 0 END) AS LM2_TP3,
                    MAX(CASE WHEN nm.lingkup_materi = 'LM 2' AND nm.tujuan_pembelajaran = 'TP4' THEN nm.nilai ELSE 0 END) AS LM2_TP4,
                    MAX(CASE WHEN nm.lingkup_materi = 'LM 3' AND nm.tujuan_pembelajaran = 'TP1' THEN nm.nilai ELSE 0 END) AS LM3_TP1,
                    MAX(CASE WHEN nm.lingkup_materi = 'LM 3' AND nm.tujuan_pembelajaran = 'TP2' THEN nm.nilai ELSE 0 END) AS LM3_TP2,
                    MAX(CASE WHEN nm.lingkup_materi = 'LM 3' AND nm.tujuan_pembelajaran = 'TP3' THEN nm.nilai ELSE 0 END) AS LM3_TP3,
                    MAX(CASE WHEN nm.lingkup_materi = 'LM 3' AND nm.tujuan_pembelajaran = 'TP4' THEN nm.nilai ELSE 0 END) AS LM3_TP4,
                    MAX(CASE WHEN nm.lingkup_materi = 'LM 4' AND nm.tujuan_pembelajaran = 'TP1' THEN nm.nilai ELSE 0 END) AS LM4_TP1,
                    MAX(CASE WHEN nm.lingkup_materi = 'LM 4' AND nm.tujuan_pembelajaran = 'TP2' THEN nm.nilai ELSE 0 END) AS LM4_TP2,
                    MAX(CASE WHEN nm.lingkup_materi = 'LM 4' AND nm.tujuan_pembelajaran = 'TP3' THEN nm.nilai ELSE 0 END) AS LM4_TP3,
                    MAX(CASE WHEN nm.lingkup_materi = 'LM 4' AND nm.tujuan_pembelajaran = 'TP4' THEN nm.nilai ELSE 0 END) AS LM4_TP4,
                    MAX(CASE WHEN nul.lingkup_materi = 'LM 1' THEN nul.nilai ELSE 0 END) AS LM1,
                    MAX(CASE WHEN nul.lingkup_materi = 'LM 2' THEN nul.nilai ELSE 0 END) AS LM2,
                    MAX(CASE WHEN nul.lingkup_materi = 'LM 3' THEN nul.nilai ELSE 0 END) AS LM3,
                    MAX(CASE WHEN nul.lingkup_materi = 'LM 4' THEN nul.nilai ELSE 0 END) AS LM4,                        
                    MAX(CASE WHEN nuj.ujian = 'STS' THEN nuj.nilai ELSE 0 END) AS STS,
                    MAX(CASE WHEN nuj.ujian = 'SAS' THEN nuj.nilai ELSE 0 END) AS SAS
                    FROM mapel m
                    LEFT JOIN nilai_mapel nm ON m.id_mapel = nm.id_mapel
                    LEFT JOIN nilai_ulangan nul ON m.id_mapel = nul.id_mapel
                    LEFT JOIN nilai_ujian nuj ON m.id_mapel = nuj.id_mapel
                    WHERE nm.kelas = '$kelas' AND
                        nm.semester = '$semester' AND
                        nm.id_siswa = '$idSiswa' AND
                        nm.id_tahun_ajar = '$idTahunAjar' AND
                        nul.kelas = '$kelas' AND
                        nul.semester = '$semester' AND
                        nul.id_siswa = '$idSiswa' AND
                        nul.id_tahun_ajar = '$idTahunAjar' AND
                        nuj.kelas = '$kelas' AND
                        nuj.semester = '$semester' AND
                        nuj.id_siswa = '$idSiswa' AND
                        nuj.id_tahun_ajar = '$idTahunAjar'
                    GROUP BY  m.id_mapel, m.mapel;
                    ";

                    $query = mysqli_query($conn, $queryRapotSisipan);

                    $html  .= '<table border="0.75" style="width: 100%; text-align: center; font-family: helvetica; font-size: 12px; ">';
                    $html  .= '<tr>';
                    $html  .= '<th rowspan="3" style="width: 5%; height: 30px; vertical-align: middle; font-weight: bold;"><br><br>No.</th>';
                    $html  .= '<th rowspan="3" style="width: 24%; height: 30px; text-align: left; font-weight: bold;"><br><br>Mata Pelajaran</th>';
                    $html  .= '<th colspan="8" style="line-height: 1.5; text-align: center; font-weight: bold;  width: 40%">FORMATIF</th>';
                    $html  .= '<th rowspan="2" colspan="2" style="line-height: 2; text-align: center;font-weight: bold; width: 14%">NSLM</th>';
                    $html  .= '<th rowspan="2" style="line-height: 2; font-weight: bold; width: 8%">NS</th>';
                    $html  .= '</tr>';
                    $html  .= '<tr>';
                    $html  .= '<th colspan="2" style="font-weight: bold;width: 10%">LM 1</th>';
                    $html  .= '<th colspan="2" style="font-weight: bold;width: 10%">LM 2</th>';
                    $html  .= '<th colspan="2" style="font-weight: bold;width: 10%">LM 3</th>';
                    $html  .= '<th colspan="2" style="font-weight: bold;width: 10%">LM 4</th>';
                    $html  .= '</tr>';
                    $html  .= '<tr>';
                    $html  .= '<th style="font-weight: bold; width: 5%">TP1</th>';
                    $html  .= '<th style="font-weight: bold; width: 5%">TP2</th>';                   
                    $html  .= '<th style="font-weight: bold; width: 5%">TP1</th>';
                    $html  .= '<th style="font-weight: bold; width: 5%">TP2</th>';
                    $html  .= '<th style="font-weight: bold; width: 5%">TP1</th>';
                    $html  .= '<th style="font-weight: bold; width: 5%">TP2</th>';
                    $html  .= '<th style="font-weight: bold; width: 5%">TP1</th>';
                    $html  .= '<th style="font-weight: bold; width: 5%">TP2</th>';
                    $html  .= '<th style="font-weight: bold; width: 7%">LM1</th>';
                    $html  .= '<th style="font-weight: bold; width: 7%">LM2</th>';
                    $html  .= '<th style="font-weight: bold; width: 8%">STS</th>';
                    $html  .= '</tr>';
                    
                    $i = 1;
                
                    while ($row = mysqli_fetch_array($query)) {
                        $idMapel = $row['id_mapel'];
                        $mapel = $row['mapel'];
                        $LM1_TP1 = $row['LM1_TP1'];
                        $LM1_TP2 = $row['LM1_TP2'];
                        $LM1_TP3 = $row['LM1_TP3'];
                        $LM1_TP4 = $row['LM1_TP4'];
                        $LM2_TP1 = $row['LM2_TP1'];
                        $LM2_TP2 = $row['LM2_TP2'];
                        $LM2_TP3 = $row['LM2_TP3'];
                        $LM2_TP4 = $row['LM2_TP4'];
                        $LM3_TP1 = $row['LM3_TP1'];
                        $LM3_TP2 = $row['LM3_TP2'];
                        $LM3_TP3 = $row['LM3_TP3'];
                        $LM3_TP4 = $row['LM3_TP4'];
                        $LM4_TP1 = $row['LM4_TP1'];
                        $LM4_TP2 = $row['LM4_TP2'];
                        $LM4_TP3 = $row['LM4_TP3'];
                        $LM4_TP4 = $row['LM4_TP4'];
                        $LM1 = $row['LM1'];
                        $LM2 = $row['LM2'];
                        $LM3 = $row['LM3'];
                        $LM4 = $row['LM4'];
                        $rerataUlangan = ($LM1 + $LM2 + $LM3 + $LM4)/4;
                        $STS = $row['STS'];
                        $SAS = $row['SAS'];
                        $nilaiRapot = ((2 * $rerataUlangan) + $STS + (2 * $SAS)) / 5;

                        if ($tplm1 == 3) {
                            $lm1tp1 = ($LM1_TP1 + $LM1_TP2)/2;
                            $lm1tp2 = ($LM1_TP2 + $LM1_TP3)/2;                            
                        } elseif ($tplm1 == 4) {
                            $lm1tp1 = ($LM1_TP1 + $LM1_TP2)/2;
                            $lm1tp2 = ($LM1_TP3 + $LM1_TP4)/2;
                        } else {
                            $lm1tp1 = $LM1_TP1;
                            $lm1tp2 = $LM1_TP2;
                        }

                        if ($tplm2 == 3) {
                            $lm2tp1 = ($LM2_TP1 + $LM2_TP2)/2;
                            $lm2tp2 = ($LM2_TP2 + $LM2_TP3)/2;
                        } elseif ($tplm2 == 4) {
                            $lm2tp1 = ($LM2_TP1 + $LM2_TP2)/2;
                            $lm2tp2 = ($LM2_TP3 + $LM2_TP4)/2;
                        } else {
                            $lm2tp1 = $LM2_TP1;
                            $lm2tp2 = $LM2_TP2;
                        }

                        $html  .= '<tr>';
                        $html  .= '<td style="width: 5%">' . $i++ . '</td>';
                        $html  .= '<td style="text-align: left; width: 24%">' . $mapel . '</td>';
                        $html  .= '<td style="width: 5%">' . $lm1tp1 . '</td>';
                        $html  .= '<td style="width: 5%">' . $lm1tp2 . '</td>';
                        $html  .= '<td style="width: 5%">' . $lm2tp1 . '</td>';
                        $html  .= '<td style="width: 5%">' . $lm2tp2 . '</td>';
                        $html  .= '<td style="width: 5%"></td>';
                        $html  .= '<td style="width: 5%"></td>';
                        $html  .= '<td style="width: 5%"></td>';
                        $html  .= '<td style="width: 5%"></td>';
                        $html  .= '<td>' . $LM1 . '</td>';
                        $html  .= '<td>' . $LM2 . '</td>';
                        $html  .= '<td>' . $STS . '</td>';
                        $html  .= '</tr>';
                    }
                    $html  .= '</table>';
                    $html  .= '</div>';
                    $html  .= '</div>';

                    $html  .= '<div class="row">';
                    $html  .= '<table>';
                    $html  .= '<tr>';
                    $html  .= '<td style="width: 32%">';
                    $html  .= '<table border="0.75" style="text-align: center; font-family: helvetica; font-size: 13px;">';
                    $html  .= '<tr>';
                    $html  .= '<th colspan="3" style="line-height: 1.5; font-weight: bold; width: 95%">Nilai Praktek</th>';   
                    $html  .= '</tr>';
                    
                    $queryNilaiPraktek = "SELECT `kategori_praktek`, `nilai` 
                    FROM `nilai_praktek` 
                    WHERE 
                    `id_tahun_Ajar` = '$idTahunAjar' AND 
                    `semester`='$semester' AND 
                    `id_siswa`='$idSiswa';";

                    $i = 1;
                    $nilaiPraktek = mysqli_query($conn, $queryNilaiPraktek);
                    while ($rowPraktek = mysqli_fetch_array($nilaiPraktek)) {
                        $kategoriPraktek = $rowPraktek['kategori_praktek'];
                        $nilaiPrak = $rowPraktek['nilai'];
                        $html  .= '<tr>';
                        $html  .= '<td style="width: 15%">' . $i++ . '</td>';
                        $html  .= '<td style="width: 55%; text-align: left;"> ' . $kategoriPraktek . '</td>';
                        $html  .= '<td style="width: 25%">' . $nilaiPrak  . '</td>';   
                        $html  .= '</tr>';

                    }
                    $html  .= '</table>';
                    $html  .= '</td>';

                    $html  .= '<td style="width: 35%">';
                    $html  .= '<table border="0.75" style="text-align: center; font-family: helvetica; font-size: 13px;">';
                    $html  .= '<tr>';
                    $html  .= '<th colspan="3" style="line-height: 1.5; font-weight: bold; width: 95%">Kepribadian</th>'; 
                    $html  .= '</tr>';

                    $queryNilaiKepribadian = "SELECT `kategori_kepribadian`, `nilai` 
                    FROM `nilai_kepribadian` 
                    WHERE 
                    `id_tahun_Ajar` = '$idTahunAjar' AND 
                    `semester`='$semester' AND 
                    `id_siswa`='$idSiswa';";

                    $i = 1;
                    $nilaiKepribadian = mysqli_query($conn, $queryNilaiKepribadian);
                    while ($rowKepribadian = mysqli_fetch_array($nilaiKepribadian)) {
                        $kategoriKepribadian = $rowKepribadian['kategori_kepribadian'];
                        $nilaiKeprib = $rowKepribadian['nilai'];
                        $html  .= '<tr>';
                        $html  .= '<td style="width: 15%">' . $i++ . '</td>';
                        $html  .= '<td style="width: 55%; text-align: left;"> ' . $kategoriKepribadian . '</td>';
                        $html  .= '<td style="width: 25%">' . $nilaiKeprib  . '</td>';   
                        $html  .= '</tr>';

                    }
                    $html  .= '</table>'; 

                    $html  .= '</td>';
                    $html  .= '<td style="width: 30%">';
                    $html  .= '<table border="0.75" style="text-align: center; font-family: helvetica; font-size: 13px;">';
                    $html  .= '<tr>';
                    $html  .= '<th colspan="3"  style="line-height: 1.5; font-weight: bold; width: 80%">Absensi Siswa</th>';
                    $html  .= '</tr>';

                    $queryAbsensi = "SELECT absen, COUNT(absen) AS count
                    FROM absensi                    
                    WHERE 
                    `id_tahun_Ajar` = '$idTahunAjar' AND 
                    `semester`='$semester' AND 
                    `id_siswa`='$idSiswa'
                    GROUP BY absen 
                    ;";

                    $i = 1;
                    $absensiSiswa = mysqli_query($conn, $queryAbsensi);
                    while ($rowAbsensi = mysqli_fetch_array($absensiSiswa)) {
                        $kategoriAbsen = $rowAbsensi['absen'];
                        $jumlahAbsen = $rowAbsensi['count'];
                        $html  .= '<tr>';
                        $html  .= '<td style="width: 15%">' . $i++ . '</td>';
                        $html  .= '<td style="width: 40%; text-align: left;"> ' . $kategoriAbsen . '</td>';
                        $html  .= '<td style="width: 25%">' . $jumlahAbsen  . '</td>';   
                        $html  .= '</tr>';

                    }

                    $html  .= '</table>';                    
                    $html  .= '</td>';
                    $html  .= '</tr>';
                    $html  .= '</table><br><br>';

                    $html  .= '<table style="text-align: center; font-family: helvetica; font-size: 13px;">'; 
                    $html  .= '<tr>';
                    $html  .= '<td style="width: 91%">';

                    $html  .= '<table>'; 
                    $html  .= '<tr>';
                    $html .= '<td style="width: 10%;"></td>';
                    $html .= '<td  border="0.75" style="line-height: 1.5; font-weight: bold; width: 80%">CATATAN SISWA';                    
                    $html .= '</td>';
                    $html .= '<td style="width: 10%;"></td>';                    
                    $html  .= '</tr>';
                    $html  .= '<tr>';
                    $html  .= '<td style="width: 10%"></td>';

                    $queryCatatan = "SELECT catatan
                    FROM nilai_catatan                  
                    WHERE 
                    `id_tahun_Ajar` = '$idTahunAjar' AND 
                    `semester`='$semester' AND 
                    `id_siswa`='$idSiswa'
                    ;";

                    $catatanSiswa = mysqli_query($conn, $queryCatatan);
                    while ($rowCatatan = mysqli_fetch_array($catatanSiswa)){
                        $Catatan = $rowCatatan['catatan'];
                        
                        $html  .= '<td  border="0.75" style="line-height: 3; font-style: italic; width: 80%">"' . $Catatan . '"</td>';
                    }
                    $html  .= '<td style="width: 10%"></td>';
                    $html  .= '</tr>';
                    $html  .= '</table>';
                    $html  .= '</td>';
                    $html  .= '</tr>';
                    $html  .= '</table><br><br><br>'; 

                    $html .= '<table style="font-family: helvetica; font-size: 13px;">';
                    $html .= '<tr>';
                    $html .= '<td style="width: 5%"></td>';
                    $tahun = date("Y"); 
                    $html .= '<td style="width: 55%">Ditandatangani tgl, ......................' . $tahun . '</td>';
                    $html .= '<td style="width: 35%">Diberikan di Rogojampi</td>';
                    $html .= '</tr>';
                    $html .= '<tr>';
                    $html .= '<td style="width: 5%"></td>';
                    $html .= '<td style="width: 55%">Orang Tua / Wali Siswa</td>';
                    // setlocale(LC_TIME, 'id_ID');
                    // $tanggal = strftime("%d %B %Y");
                    // $html .= '<td style="width: 35%">Tanggal ' . $tanggal . '</td>';
                    $tanggal = date('d F Y');
                    $bulan = [
                        'January' => 'Januari',
                        'February' => 'Februari',
                        'March' => 'Maret',
                        'April' => 'April',
                        'May' => 'Mei',
                        'June' => 'Juni',
                        'July' => 'Juli',
                        'August' => 'Agustus',
                        'September' => 'September',
                        'October' => 'Oktober',
                        'November' => 'November',
                        'December' => 'Desember'
                    ];

                    foreach ($bulan as $english => $indonesian) {
                        $tanggal = str_replace($english, $indonesian, $tanggal);
                    }
                    
                    $html .= '<td style="width: 35%">Tanggal ' . $tanggal . '</td>';
                    $html .= '</tr>';
                    $html .= '<tr>';
                    $html .= '<td style="width: 5%"></td>';
                    $html .= '<td style="width: 55%"></td>';
                    $html .= '<td style="width: 35%">Wali Kelas </td>';
                    $html .= '</tr>';
                    $html .= '<tr>';
                    $html .= '<td style="line-height: 4;width: 5%"></td>';
                    $html .= '<td style="line-height: 4;width: 55%"></td>';
                    $html .= '<td style="line-height: 4;width: 35%"></td>';
                    $html .= '</tr>';
                    $html .= '<tr>';
                    $html .= '<td style="width: 5%"></td>';
                    $html .= '<td style="text-decoration: underline; width: 55%">........................................</td>';
                    $html .= '<td style="font-weight: bold; text-decoration: underline; width: 35%">' . $waliKelas . '</td>';
                    $html .= '</tr>';
                    $html .= '<tr>';
                    $html .= '<td style="width: 5%"></td>';
                    $html .= '<td style="width: 55%"></td>';
                    $html .= '<td style="width: 35%"> NIY. ' . $nipWaliKelas . '</td>';
                    $html .= '</tr>';
                    $html .= '</table><br>';

                    $html .= '<table style="text-align: center; font-family: helvetica; font-size: 13px;">';                   
                    $html .= '<tr>';
                    $html .= '<td style="width: 20%"></td>';
                    $html .= '<td style="width: 55%">Mengetahui</td>';
                    $html .= '<td style="width: 20%"></td>';
                    $html .= '</tr>';
                    $html .= '<tr>';
                    $html .= '<td style="width: 20%"></td>';
                    $html .= '<td style="width: 55%">Kepala Sekolah</td>';
                    $html .= '<td style="width: 20%"></td>';
                    $html .= '</tr>';
                    $html .= '<tr>';
                    $html .= '<td style="line-height: 4;width: 20%"></td>';
                    $html .= '<td style="line-height: 4;width: 55%"></td>';
                    $html .= '<td style="line-height: 4;width: 20%"></td>';
                    $html .= '</tr>';
                    $html .= '<tr>';
                    $html .= '<td style="width: 20%"></td>';
                    $html .= '<td style="font-weight: bold; text-decoration: underline; width: 55%">' . $kepsek . '</td>';
                    $html .= '<td style="width: 20%"></td>';
                    $html .= '</tr>';
                    $html .= '<tr>';
                    $html .= '<td style="width: 20%"></td>';
                    $html .= '<td style="width: 55%">' . $nipKepsek . '</td>';
                    $html .= '<td style="width: 20%"></td>';
                    $html .= '</tr>';
                    $html .= '</table><br>';

                    $html .= '</div><br><br>';

                    
                    
// print a block of text using Write()
$pdf->Write(0, $txt, '', 0, 'C', true, 0, false, false, 0);
$pdf->writeHTML($html, true, false, false, false, '');

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('laporan_umum.pdf', 'I');

?>