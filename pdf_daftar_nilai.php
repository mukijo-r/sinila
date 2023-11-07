<?php
// Include the main TCPDF library (search for installation path).
require_once('TCPDF-main/tcpdf.php');
$conn = mysqli_connect("localhost:3306","root","","sdk");



// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        // Logo
        // $image_file = K_PATH_IMAGES.'logo.jpg';
        // $this->Image($image_file, 30, 12, 20, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

        // Tambahkan baris baru
        $this->SetCellPaddings(0, 0.2);
       
        $this->Ln(); // Ini akan membuat baris baru
        $this->SetLineWidth(0); // Atur ketebalan garis
        $this->Ln(); // Ini akan membuat baris baru

    }
}

// create new PDF document
$pdf = new MYPDF('LANDSCAPE', PDF_UNIT, 'A4', true, 'UTF-8', false);


// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Mukijo');
$pdf->SetTitle('Laporan Keuangan Umum');
$pdf->SetSubject('TCPDF');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(9, 10, 10);
//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set font
$pdf->SetFont('helvetica', '', 10);

// add a page
$pdf->AddPage();

$idTahunAjar = $_POST['idTahunAjar'];
$queryTahunAjar = mysqli_query($conn, "SELECT tahun_ajar FROM tahun_ajar WHERE id_tahun_ajar = '$idTahunAjar'");
$rowTahunAjar = mysqli_fetch_array($queryTahunAjar);
$tahunAjar = $rowTahunAjar['tahun_ajar'];

$semester = $_POST['semester'];
if ($semester == 'Ganjil'){
    $semesterRomawi = 'I';
} elseif ($semester == 'Genap') {
    $semesterRomawi = 'II';
}
$kelas = $_POST['kelas'];

$idMapel = $_POST['idMapel'];

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
DAFTAR NILAI SEMESTER $semesterRomawi
SD KATOLIK BHAKTI ROGOJAMPI
TAHUN PELAJARAN $tahunAjar



EOD;
$pdf->SetFont('times', '', 9);
$pdf->SetCellMargins(0, 1, 0, 0);
$html = '<table border="0.75" cellpadding="1" style="text-align: center;">';
$html .= '<tr>';
$html .= '<th rowspan="3" style="line-height: 2; width: 3%">No. Urut</th>';
$html .= '<th rowspan="3" style="line-height: 2; width: 4%">No. Induk</th>';
$html .= '<th rowspan="3" style="line-height: 5; width: 7%">NISN</th>';
$html .= '<th rowspan="3" style="line-height: 5; width: 13%">Nama</th>';
$html .= '<th colspan="16" style="width: 48%">FORMATIF</th>';
$html .= '<th rowspan="2" colspan="5" style="width: 15.5%">NILAI SUMATIF LINGKUP MATERI</th>';
$html .= '<th rowspan="2" colspan="2" style="width: 6%">NILAI SUMATIF</th>';
$html .= '<th rowspan="3" style="line-height: 5; width: 4%">NR</th>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th colspan="4" style="width: 12%">Lingkup Materi 1</th>';
$html .= '<th colspan="4" style="width: 12%">Lingkup Materi 2</th>';
$html .= '<th colspan="4" style="width: 12%">Lingkup Materi 3</th>';
$html .= '<th colspan="4" style="width: 12%">Lingkup Materi 4</th>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<th style="width: 3%">TP1</th>';
$html .= '<th style="width: 3%">TP2</th>';
$html .= '<th style="width: 3%">TP3</th>';
$html .= '<th style="width: 3%">TP4</th>';
$html .= '<th style="width: 3%">TP1</th>';
$html .= '<th style="width: 3%">TP2</th>';
$html .= '<th style="width: 3%">TP3</th>';
$html .= '<th style="width: 3%">TP4</th>';
$html .= '<th style="width: 3%">TP1</th>';
$html .= '<th style="width: 3%">TP2</th>';
$html .= '<th style="width: 3%">TP3</th>';
$html .= '<th style="width: 3%">TP4</th>';
$html .= '<th style="width: 3%">TP1</th>';
$html .= '<th style="width: 3%">TP2</th>';
$html .= '<th style="width: 3%">TP3</th>';
$html .= '<th style="width: 3%">TP4</th>';
$html .= '<th style="width: 3%">LM1</th>';
$html .= '<th style="width: 3%">LM2</th>';
$html .= '<th style="width: 3%">LM3</th>';
$html .= '<th style="width: 3%">LM4</th>';
$html .= '<th style="width: 3.5%">RATA</th>';
$html .= '<th style="width: 3%">STS</th>';
$html .= '<th style="width: 3%">SAS</th>';
$html .= '</tr>'; 

$query = mysqli_query($conn, "SELECT s.nis, s.nisn, s.nama,
    SUM(CASE WHEN nm.lingkup_materi = 'LM 1' AND nm.tujuan_pembelajaran = 'TP1' THEN nm.nilai ELSE 0 END) AS LM1_TP1,
    SUM(CASE WHEN nm.lingkup_materi = 'LM 1' AND nm.tujuan_pembelajaran = 'TP2' THEN nm.nilai ELSE 0 END) AS LM1_TP2,
    SUM(CASE WHEN nm.lingkup_materi = 'LM 1' AND nm.tujuan_pembelajaran = 'TP3' THEN nm.nilai ELSE 0 END) AS LM1_TP3,
    SUM(CASE WHEN nm.lingkup_materi = 'LM 1' AND nm.tujuan_pembelajaran = 'TP4' THEN nm.nilai ELSE 0 END) AS LM1_TP4,
    SUM(CASE WHEN nm.lingkup_materi = 'LM 2' AND nm.tujuan_pembelajaran = 'TP1' THEN nm.nilai ELSE 0 END) AS LM2_TP1,
    SUM(CASE WHEN nm.lingkup_materi = 'LM 2' AND nm.tujuan_pembelajaran = 'TP2' THEN nm.nilai ELSE 0 END) AS LM2_TP2,
    SUM(CASE WHEN nm.lingkup_materi = 'LM 2' AND nm.tujuan_pembelajaran = 'TP3' THEN nm.nilai ELSE 0 END) AS LM2_TP3,
    SUM(CASE WHEN nm.lingkup_materi = 'LM 2' AND nm.tujuan_pembelajaran = 'TP4' THEN nm.nilai ELSE 0 END) AS LM2_TP4,
    SUM(CASE WHEN nm.lingkup_materi = 'LM 3' AND nm.tujuan_pembelajaran = 'TP1' THEN nm.nilai ELSE 0 END) AS LM3_TP1,
    SUM(CASE WHEN nm.lingkup_materi = 'LM 3' AND nm.tujuan_pembelajaran = 'TP2' THEN nm.nilai ELSE 0 END) AS LM3_TP2,
    SUM(CASE WHEN nm.lingkup_materi = 'LM 3' AND nm.tujuan_pembelajaran = 'TP3' THEN nm.nilai ELSE 0 END) AS LM3_TP3,
    SUM(CASE WHEN nm.lingkup_materi = 'LM 3' AND nm.tujuan_pembelajaran = 'TP4' THEN nm.nilai ELSE 0 END) AS LM3_TP4,
    SUM(CASE WHEN nm.lingkup_materi = 'LM 4' AND nm.tujuan_pembelajaran = 'TP1' THEN nm.nilai ELSE 0 END) AS LM4_TP1,
    SUM(CASE WHEN nm.lingkup_materi = 'LM 4' AND nm.tujuan_pembelajaran = 'TP2' THEN nm.nilai ELSE 0 END) AS LM4_TP2,
    SUM(CASE WHEN nm.lingkup_materi = 'LM 4' AND nm.tujuan_pembelajaran = 'TP3' THEN nm.nilai ELSE 0 END) AS LM4_TP3,
    SUM(CASE WHEN nm.lingkup_materi = 'LM 4' AND nm.tujuan_pembelajaran = 'TP4' THEN nm.nilai ELSE 0 END) AS LM4_TP4,
    SUM(CASE WHEN nul.lingkup_materi = 'LM 1' THEN nul.nilai ELSE 0 END) AS LM1,
    SUM(CASE WHEN nul.lingkup_materi = 'LM 2' THEN nul.nilai ELSE 0 END) AS LM2,
    SUM(CASE WHEN nul.lingkup_materi = 'LM 3' THEN nul.nilai ELSE 0 END) AS LM3,
    SUM(CASE WHEN nul.lingkup_materi = 'LM 4' THEN nul.nilai ELSE 0 END) AS LM4,                        
    SUM(CASE WHEN nuj.ujian = 'STS' THEN nuj.nilai ELSE 0 END) AS STS,
    SUM(CASE WHEN nuj.ujian = 'SAS' THEN nuj.nilai ELSE 0 END) AS SAS
FROM siswa s
LEFT JOIN nilai_mapel nm ON s.id_siswa = nm.id_siswa
LEFT JOIN nilai_ulangan nul ON s.id_siswa = nul.id_siswa
LEFT JOIN nilai_ujian nuj ON s.id_siswa = nuj.id_siswa
WHERE nm.kelas = '$kelas' AND
    nm.semester = '$semester' AND
    nm.id_mapel = '$idMapel' AND
    nm.id_tahun_ajar = '$idTahunAjar'
GROUP BY s.nis, s.nisn, s.nama;
");

$i = 1;

while ($row = mysqli_fetch_array($query)) {
    $nis = $row['nis'];
    $nisn = $row['nisn'];
    $namaSiswa = $row['nama'];
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

    $html .= '<tr>';
    $html .= '<td style="width: 3%">' . $i++ . '</td>';
    $html .= '<td style="width: 4%">' . $nis . '</td>';
    $html .= '<td style="width: 7%">' . $nisn . '</td>';
    $html .= '<td style="width: 13%; text-align: left;">' . $namaSiswa . '</td>';
    $html .= '<td>' . $LM1_TP1 . '</td>';
    $html .= '<td>' . $LM1_TP2 . '</td>';
    $html .= '<td>' . $LM1_TP3 . '</td>';
    $html .= '<td>' . $LM1_TP4 . '</td>';
    $html .= '<td>' . $LM2_TP1 . '</td>';
    $html .= '<td>' . $LM2_TP2 . '</td>';
    $html .= '<td>' . $LM2_TP3 . '</td>';
    $html .= '<td>' . $LM2_TP4 . '</td>';
    $html .= '<td>' . $LM3_TP1 . '</td>';
    $html .= '<td>' . $LM3_TP2 . '</td>';
    $html .= '<td>' . $LM3_TP3 . '</td>';
    $html .= '<td>' . $LM3_TP4 . '</td>';
    $html .= '<td>' . $LM4_TP1 . '</td>';
    $html .= '<td>' . $LM4_TP2 . '</td>';
    $html .= '<td>' . $LM4_TP3 . '</td>';
    $html .= '<td>' . $LM4_TP4 . '</td>';
    $html .= '<td>' . $LM1 . '</td>';
    $html .= '<td>' . $LM2 . '</td>';
    $html .= '<td>' . $LM3 . '</td>';
    $html .= '<td>' . $LM4 . '</td>';
    $html .= '<td>' . $rerataUlangan . '</td>';
    $html .= '<td>' . $STS . '</td>';
    $html .= '<td>' . $SAS . '</td>';
    $html .= '<td>' . $nilaiRapot . '</td>';
    $html .= '</tr>';
}

$html .= '</table><br><br><br><br>';

$html .= '<table>';
$html .= '<tr>';
$html .= '<td style="width: 10%"></td>';
$html .= '<td style="width: 60%">Kepala Sekolah</td>';
$html .= '<td style="width: 30%">Guru Kelas</td>';
$html .= '</tr>';
$html .= '</table><br><br><br><br>';

$html .= '<table>';
$html .= '<tr>';
$html .= '<td style="width: 10%"></td>';
$html .= '<td style="width: 60%"><u>' . $kepsek . '</u></td>';
$html .= '<td style="width: 30%"><u>' . $waliKelas . '</u></td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<td style="width: 10%"></td>';
$html .= '<td style="width: 60%">NIP. ' . $nipKepsek . '</td>';
$html .= '<td style="width: 30%">NIP. ' . $nipWaliKelas . '</td>';
$html .= '</tr>';
$html .= '</table><br><br>';


// print a block of text using Write()
$pdf->Write(0, $txt, '', 0, 'C', true, 0, false, false, 0);
$pdf->writeHTML($html, true, false, false, false, '');

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('laporan_umum.pdf', 'I');

?>