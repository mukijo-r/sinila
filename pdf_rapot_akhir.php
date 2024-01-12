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
$pdf->SetTitle('Rapot Akhir');
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
AKHIR SEMESTER  $semCap
TAHUN PELAJARAN $tahunAjar


EOD;
$pdf->SetFont('times', '', 12);
$pdf->SetMargins(15, 15, 0);
$pdf->SetCellMargins(0, 0, 0, 0);
$pdf->SetCellPaddings(1, 1, 1, 1);

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

$queryNilai = "SELECT m.id_mapel, m.mapel,                    
    MAX(CASE WHEN nul.lingkup_materi = 'LM 1' THEN nul.nilai ELSE 0 END) AS LM1,
    MAX(CASE WHEN nul.lingkup_materi = 'LM 2' THEN nul.nilai ELSE 0 END) AS LM2,
    MAX(CASE WHEN nul.lingkup_materi = 'LM 3' THEN nul.nilai ELSE 0 END) AS LM3,
    MAX(CASE WHEN nul.lingkup_materi = 'LM 4' THEN nul.nilai ELSE 0 END) AS LM4,                        
    MAX(CASE WHEN nuj.ujian = 'STS' THEN nuj.nilai ELSE 0 END) AS STS,
    MAX(CASE WHEN nuj.ujian = 'SAS' THEN nuj.nilai ELSE 0 END) AS SAS
    FROM mapel m                    
    LEFT JOIN nilai_ulangan nul ON m.id_mapel = nul.id_mapel
    LEFT JOIN nilai_ujian nuj ON m.id_mapel = nuj.id_mapel
    WHERE 
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

$nilai = mysqli_query($conn, $queryNilai);

$html  .= '<table border="1" style="width: 100%; text-align: center; font-family: helvetica; font-size: 12px">';
$html  .= '<tr>';
$html  .= '<th style="line-height: 1.5; width: 5%; height: 22px; text-align: center; font-weight: bold;">No.</th>';
$html  .= '<th style="line-height: 1.5; width: 25%; height: 22px; text-align: center; font-weight: bold;">Mata Pelajaran</th>';
$html  .= '<th style="line-height: 1.5; width: 7%; height: 22px; text-align: center; font-weight: bold;">Nilai</th>';
$html  .= '<th style="line-height: 1.5; width: 55%; height: 22px; text-align: center; font-weight: bold;">Deskripsi</th>';
$html  .= '</tr>';

$i = 1;
                
while ($row = mysqli_fetch_array($nilai)) {
    $mapel = $row['mapel'];
    $LM1 = $row['LM1'];
    $LM2 = $row['LM2'];
    $LM3 = $row['LM3'];
    $LM4 = $row['LM4'];
    $rerataUlangan = ($LM1 + $LM2 + $LM3 + $LM4)/4;
    $STS = $row['STS'];
    $SAS = $row['SAS'];
    $nilaiRapot = ((2 * $rerataUlangan) + $STS + (2 * $SAS)) / 5;

    $queryMapel = mysqli_query($conn, "SELECT id_mapel
    FROM mapel                  
    WHERE                                         
    `mapel` = '$mapel';");

    while ($rowMapel = mysqli_fetch_array($queryMapel)){
    $idMapel = $rowMapel['id_mapel'];
    }

    $queryDeskripsi = "SELECT GROUP_CONCAT(deskripsi SEPARATOR ', ') AS combined_deskripsi
    FROM asesmen                                   
    WHERE 
    id_mapel = '$idMapel' AND 
    semester = '$semester' AND
    id_kelas = '$kelas'";

    $result = mysqli_query($conn, $queryDeskripsi);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $combinedDeskripsi = $row['combined_deskripsi'];
    }
    $html  .= '<tr>';
    $html  .= '<td style="padding-top: 10px;">' . $i++ . '</td>';
    $html  .= '<td style="text-align: left;">' . $mapel . '</td>';
    $html  .= '<td>' . $nilaiRapot . '</td>';
    if ($nilaiRapot > 74){
        $html  .= '<td style="text-align: left;">'. $namaSiswa . ' mampu ' . $combinedDeskripsi .'</td>';
    } else {
        $html  .= '<td style="text-align: left;">'. $namaSiswa . ' membutuhkan bantuan dalam ' . $combinedDeskripsi .'</td>';
    }
    $html  .= '</tr>';
}

$html  .= '</table><br><br>';                    

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

// Tambahkan teks ke dokumen
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
                    
// print a block of text using Write()
$pdf->Write(0, $txt, '', 0, 'C', true, 0, false, false, 0);
$pdf->writeHTML($html, true, false, false, false, '');

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('laporan_akhir.pdf', 'I');

?>