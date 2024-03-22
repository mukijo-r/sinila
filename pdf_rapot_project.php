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
    
        // // Logo
        // $image_file = K_PATH_IMAGES.'logo.jpg';
        // $this->Image($image_file, 30, 8, 20, '', 'JPG', '', 'T', true, 50, '', false, false, 0, false, false, false);
    
        // Geser ke posisi baru untuk tulisan
        $this->SetXY(10, 14);
    
        // Tambahkan baris baru

        $this->SetCellPaddings(0, 2);
    
        // $this->SetFont('helvetica', '', 20);
        // $this->Cell(0, 0, 'SD KATOLIK BHAKTI', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        // $this->Ln();  
        // $this->SetFont('times', 'B', 12);
        // $this->Cell(0, 8, 'RAPOR PROJEK PENGUATAN PROFIL PELAJAR PANCASILA', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        // $this->Ln(); 
    }    
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
$pdf->SetAutoPageBreak(true, 15); 

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Mukijo');
$pdf->SetTitle('Rapot Project');
$pdf->SetSubject('TCPDF');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetFont('times', '', 14);
// set margins
$pdf->SetMargins(12, 10, 0);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// add a page
$pdf->AddPage();

$idTahunAjar = $_POST['idTahunAjar'];
$kelas = $_POST['kelas'];
$fase = $_POST['kelas'];

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
$idProject = $_POST['idProject'];
$tanggalCetak = $_POST['tanggalCetak'];

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
RAPOR PROJEK PENGUATAN PROFIL PELAJAR PANCASILA


EOD;
$pdf->SetFont('times', '', 12);
// $pdf->SetMargins(15, 15, 0);
//$pdf->SetCellMargins(0, 0, 0, 0);
// $pdf->SetCellPaddings(1, 1, 1, 1);

    $html = '<table>
                <tr>
                    <td style="width: 60%">
                        <table>
                            <tr>
                                <th style="width: 31%">Nama Sekolah</th>
                                <th style="width: 5%">:</th>
                                <th style="width: 645%">SD KATOLIK BHAKTI</th>       
                            </tr>
                            <tr>
                                <th style="width: 31%">Alamat Sekolah</th>
                                <th style="width: 5%">:</th>
                                <th style="width: 64%">Jl. Ki. Hajar Dewantoro Rogojampi</th>       
                            </tr>
                            <tr>
                                <th style="width: 31%">Nama</th>
                                <th style="width: 5%">:</th>
                                <th style="width: 64%">' . $namaSiswa . '</th>       
                            </tr>
                            <tr>
                                <th style="width: 31%">No.Induk/NISN</th>
                                <th style="width: 5%">:</th>
                                <th style="width: 64%">' . $nis . '/' .$nisn . '</th>       
                            </tr>
                        </table>
                    </td>
                    <td style="width: 40%">
                        <table>
                            <tr>
                                <th style="width: 30%">Kelas</th>
                                <th style="width: 5%">:</th>
                                <th style="width: 65%">' . $stringKelas . '</th>       
                            </tr>
                            <tr>
                                <th style="width: 30%">No.Absen</th>
                                <th style="width: 5%">:</th>
                                <th style="width: 65%">' . $nomorUrut . '</th>       
                            </tr>
                            <tr>
                                <th style="width: 30%">Fase</th>
                                <th style="width: 5%">:</th>
                                <th style="width: 65%">' . $fase . '</th>       
                            </tr>
                            <tr>
                                <th style="width: 30%">Tahun Ajar</th>
                                <th style="width: 5%">:</th>
                                <th style="width: 65%">' .$tahunAjar . '</th>       
                            </tr>
                        </table>
                    </td>
                </tr>
            </table><br>';

$queryNamaProject = mysqli_query($conn, "SELECT `nama_project`,`deskripsi_project` 
FROM `p5_project` WHERE `id_project` = '$idProject';");
while ($rowProject = mysqli_fetch_array($queryNamaProject)) {
    $namaProject = $rowProject['nama_project'];
    $deskripsiProject = $rowProject['deskripsi_project'];
}

$queryNilaiProject = "SELECT
dimensi,
capaian,
MAX(CASE WHEN nilai = 'BB' THEN 1 ELSE 0 END) AS BB,
MAX(CASE WHEN nilai = 'MB' THEN 1 ELSE 0 END) AS MB,
MAX(CASE WHEN nilai = 'BSH' THEN 1 ELSE 0 END) AS BSH,
MAX(CASE WHEN nilai = 'SB' THEN 1 ELSE 0 END) AS SB
FROM
    p5_penilaian pp
    LEFT JOIN p5_capaian pc ON pp.id_capaian = pc.id_capaian
    LEFT JOIN p5_sub_elemen pse ON pc.id_sub_elemen = pse.id_sub_elemen
    LEFT JOIN p5_elemen pe ON pse.id_elemen = pe.id_elemen
    LEFT JOIN p5_dimensi pd ON pe.id_dimensi = pd.id_dimensi
WHERE
    pp.id_tahun_ajar = '$idTahunAjar' AND
    pp.kelas = '$kelas' AND
    pp.id_siswa = '$idSiswa' AND
    pp.id_project = '$idProject'
GROUP BY dimensi, capaian;
";  

$nilaiProject = mysqli_query($conn, $queryNilaiProject);

$html .= '<h4>  Nama Proyek : ' . $namaProject . '</h4>';
$html .= '<table border="1" style="text-align: justify; width: 94%;">';
$html .= '<tr><td colspan="5" style="height: 50px;">' . $deskripsiProject . '</td></tr>';
$html .= '<tr><td colspan="5" style="line-height : 0.5;"></td></tr>';
$html .= '<tr>
        <th style="font-weight: bold; width: 60%; line-height: 1.5;">' . $namaProject . '</th>
        <th style="text-align: center; width: 10%; line-height: 1.5;">BB</th>
        <th style="text-align: center; width: 10%; line-height: 1.5;">MB</th>
        <th style="text-align: center; width: 10%; line-height: 1.5;">BSH</th>
        <th style="text-align: center; width: 10%; line-height: 1.5;">SB</th>
        </tr>';

while ($row = mysqli_fetch_array($nilaiProject)) {
    $dimensi = $row['dimensi'];
    $capaian = $row['capaian'];
    $BB = $row['BB'];
    $MB = $row['MB'];
    $BSH = $row['BSH'];
    $SB = $row['SB'];

    $html .= '<tr><td colspan="5" style="background-color: #A9A9A9; line-height : 1.5;">' . $dimensi . '</td></tr>';

    $html .= '<tr>';
    $html .= '<td>' . $capaian . '</td>';
    $html .= '<td style="text-align: center; height: 50px; vertical-align: middle;">&nbsp;<br/>' . ($BB == 1 ? '<span style="font-family:zapfdingbats;">4</span>' : '') . '</td>';
    $html .= '<td style="text-align: center; height: 50px; vertical-align: middle;">&nbsp;<br/>' . ($MB == 1 ? '<span style="font-family:zapfdingbats;">4</span>' : '') . '</td>';
    $html .= '<td style="text-align: center; height: 50px; vertical-align: middle;">&nbsp;<br/>' . ($BSH == 1 ? '<span style="font-family:zapfdingbats;">4</span>' : '') . '</td>';
    $html .= '<td style="text-align: center; height: 50px; vertical-align: middle;">&nbsp;<br/>' . ($SB == 1 ? '<span style="font-family:zapfdingbats;">4</span>' : '') . '</td>';
    $html .= '</tr>';                       

}

$query = "SELECT 
`catatan` 
FROM `p5_penilaian_catatan` 
WHERE 
`id_tahun_ajar` = '$idTahunAjar' AND
`kelas` = '$kelas' AND
`id_siswa` = '$idSiswa' AND
`id_project` = '$idProject';";

$queryCatatanProject = mysqli_query($conn, $query);


while ($rowCatatanProject = mysqli_fetch_array($queryCatatanProject)) {
    $catatanProject = $rowCatatanProject['catatan'];
}

$html .= '<tr><td colspan="5"></td></tr>';
$html .= '<tr><td colspan="5"><h4>Catatan proses :</h4></td></tr>';
$html .= '<tr><td colspan="5">' . $catatanProject . '</td></tr>';
$html .= '</table>';
$html .= '<br><br>';
$pdf->SetMargins(12, 18, 0);


$html .= '<table border="1" style="width: 94%; text-align: center;" nobr="true">';
$html .= '<tr nobr="true"><th colspan="4"><h5>Keterangan Capaian Siswa :</h5></th></tr>
<tr>
    <th>BB</th>
    <th>MB</th>
    <th>BSH</th>
    <th>SB</th>
</tr>
<tr>
    <td>Belum Berkembang</td>
    <td>Mulai Berkembang</td>
    <td>Berkembang Sesuai Harapan</td>
    <td>Sangat Berkembang</td>
</tr>
<tr>
    <td>Siswa masih membutuhkan bimbingan dalam mengembangkan kemampuan.</td>
    <td>Siswa mulai mengembangkan kemampuan namun masih belum ajek.</td>
    <td>Siswa telah mengembangkan kemampuan hingga berada dalam tahap ajek.</td>
    <td>Siswa mengembangkan kemampuannya melampaui harapan.</td>
</tr>
</table><br><br>';

$html .= '<table nobr="true"><tr nobr="true"><td>';

$html .= '<table style="font-family: helvetica; font-size: 13px;" nobr="true">';
$html .= '<tr>';
$html .= '<td style="width: 5%"></td>';
$tahun = date("Y"); 
$html .= '<td style="width: 55%">Ditandatangani tgl, ......................' . $tahun . '</td>';
$html .= '<td style="width: 35%">Diberikan di Rogojampi</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<td style="width: 5%"></td>';
$html .= '<td style="width: 55%">Orang Tua / Wali Siswa</td>';

$tanggal = date("d F Y", strtotime($tanggalCetak));
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

$html .= '<table style="text-align: center; font-family: helvetica; font-size: 13px;" nobr="true">';                   
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

$html .= '</td></tr></table><br>';  
                    
// print a block of text using Write()
$pdf->Write(0, $txt, '', 0, 'C', true, 0, false, false, 0);
$pdf->writeHTML($html, true, false, false, false, '');

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('rapot_project.pdf', 'I');

?>