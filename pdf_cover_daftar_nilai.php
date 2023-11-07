<?php
// Include the main TCPDF library (search for installation path).
require_once('TCPDF-main/tcpdf.php');
$conn = mysqli_connect("localhost:3306","root","","sdk");

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {
    public function Header() {

        $this->SetLineWidth(0);
        if ($this->getPage() == 1) {
        $backgroundImage = K_PATH_IMAGES.'cover_nilai.jpg';

        $this->Image($backgroundImage, 8, 10, 210, 297, '', '', '', false, 300, 'F', false, true, true);

    }
    }
}
// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Mukijo');
$pdf->SetTitle('Daftar Nillai');
$pdf->SetSubject('TCPDF');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
//$pdf->SetMargins(0, 0, 0);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set font
$pdf->SetFont('helvetica', '', 10);

// add a page
$pdf->AddPage();

$idTahunAjar = $_POST['idTahunAjar'];
$semester = $_POST['semester'];
if ($semester == 'Ganjil') {
    $semester = 1;
    $semesterString = '1 (Satu)';
} elseif ($semester == 'Genap') {
    $semester = 2;
    $semesterString = '2 (Dua)';
}
$kelas = $_POST['kelas'];

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

EOD;
$pdf->SetFont('times', '', 12);
$pdf->SetMargins(70, 15, 0);
$pdf->SetCellMargins(0, 0, 0, 0);
$html = '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Nomor Statistik Sekolah  :
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<table border="0.75">
    <tr>
        <th rowspan="2" style="width: 5%"></th>
        <th rowspan="2" style="width: 5%"></th>
        <th rowspan="2" style="width: 5%"></th>
        <th rowspan="2" style="width: 5%"></th>
        <th rowspan="2" style="width: 5%"></th>
        <th rowspan="2" style="width: 5%"></th>
        <th rowspan="2" style="width: 5%"></th>
        <th rowspan="2" style="width: 5%"></th>
        <th rowspan="2" style="width: 5%"></th>
        <th rowspan="2" style="width: 5%"></th>
        <th rowspan="2" style="width: 5%"></th>
        <th rowspan="2" style="width: 5%"></th>        
    </tr>
</table><br><br><br><br><br><br><br><br>
';

$html .= '<table>
<tr>
    <th style="width: 25%"></th>
    <th style="width: 70%">DAFTAR NILAI SEMESTER ' . $semester . '</th>
    <th style="width: 10%"></th>       
</tr>
<tr>
    <th style="width: 27%"></th>
    <th style="width: 70%">KURIKULUM MERDEKA</th>
    <th style="width: 10%"></th>       
</tr>
</table><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>';

$pdf->SetMargins(30, 15, 0);

$html .= '<table>
<tr>
    <th style="width: 32%">Nama Sekolah</th>
    <th style="width: 10%">:</th>
    <th style="width: 55%">SD KATOLIK BHAKTI ROGOJAMPI</th>       
</tr>
<tr>
    <th style="width: 32%">Status Sekolah</th>
    <th style="width: 10%">:</th>
    <th style="width: 55%">Swasta</th>       
</tr>
<tr>
    <th style="width: 32%">Alamat</th>
    <th style="width: 10%">:</th>
    <th style="width: 55%">Jalan Ki Hajar Dewantara</th>       
</tr>
<tr>
    <th style="width: 32%">Desa/Kelurahan</th>
    <th style="width: 10%">:</th>
    <th style="width: 55%">Gitik</th>       
</tr>
<tr>
    <th style="width: 32%">Kecamatan</th>
    <th style="width: 10%">:</th>
    <th style="width: 55%">SD Rogojampi</th>       
</tr>
<tr>
    <th style="width: 32%">Kabupaten</th>
    <th style="width: 10%">:</th>
    <th style="width: 55%">Banyuwangi</th>       
</tr>
</table><br><br><br><br><br><br><br><br><br><br><br><br><br>';

$html .= '<table>
<tr>
    <th style="width: 15%"></th>
    <th style="width: 25%">Kelas</th>
    <th style="width: 10%">:</th>
    <th style="width: 45%">' . $kelas  .'</th>       
</tr>
<tr>
    <th style="width: 15%"></th>
    <th style="width: 25%">Semester</th>
    <th style="width: 10%">:</th>
    <th style="width: 45%">' . $semesterString . '</th>       
</tr>
<tr>
    <th style="width: 15%"></th>    
    <th style="width: 25%">Tahun Pelajaran</th>
    <th style="width: 10%">:</th>
    <th style="width: 45%">' . $tahunAjar . '</th>       
</tr>
</table><br><br><br><br><br><br><br><br><br><br>';



$html .= '<table>
<tr>
    <th style="text-align: center; width: 80%; font-size: 24px;">Identitas</th>   
</tr></table><br><br>';



$html .= '<table>
<tr>
    <th style="width: 25%; font-size: 16px;">Kepala Sekolah</th>
    <th style="width: 10%; font-size: 16px;">:</th>
    <th style="width: 45%; font-size: 16px;">' . $kepsek   .'</th>       
</tr>
<tr>
    <th style="width: 25%; font-size: 8px;"></th>
    <th style="width: 10%; font-size: 8px;"></th>
    <th style="width: 45%; font-size: 8px;"></th>       
</tr>
<tr>
    <th style="width: 25%; font-size: 16px;">NIP</th>
    <th style="width: 10%; font-size: 16px;">:</th>
    <th style="width: 45%; font-size: 16px;">' . $nipKepsek . '</th>       
</tr>
<tr>
    <th style="width: 25%; font-size: 8px;"></th>
    <th style="width: 10%; font-size: 8px;"></th>
    <th style="width: 45%; font-size: 8px;"></th>       
</tr>
<tr>
    <th style="width: 25%; font-size: 16px;">Wali Kelas</th>
    <th style="width: 10%; font-size: 16px;">:</th>
    <th style="width: 45%; font-size: 16px;">' . $waliKelas . '</th>       
</tr>
<tr>
    <th style="width: 25%; font-size: 8px;"></th>
    <th style="width: 10%; font-size: 8px;"></th>
    <th style="width: 45%; font-size: 8px;"></th>       
</tr>
<tr>
    <th style="width: 25%"; font-size: 16px;>NIP</th>
    <th style="width: 10%; font-size: 16px;">:</th>
    <th style="width: 45%; font-size: 16px;">' . $nipWaliKelas . '</th>       
</tr>
</table>';

// print a block of text using Write()
$pdf->Write(0, $txt, '', 0, 'C', true, 0, false, false, 0);
$pdf->writeHTML($html, true, false, false, false, '');

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('laporan_umum.pdf', 'I');

?>