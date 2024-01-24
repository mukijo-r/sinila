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
        $this->Ln();  
        $this->SetFont('helvetica', '', 12);
        $this->Cell(0, 8, 'LAPORAN HASIL BELAJAR', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln(); 
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 8, '(RAPOR)', 0, false, 'C', 0, '', 0, false, 'M', 'M');
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
$pdf->SetMargins(10, 30, 0);

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

if ($semester == 'Ganjil') {
    $stringSemester = '1 (Satu)';
} elseif ($semester == 'Genap') {
    $stringSemester = '2 (Dua)';
}

$kelas = $_POST['kelas'];

if ($kelas == 1 | $kelas == 2) {
    $fase = 'A';
} elseif ($kelas == 3 | $kelas == 4) {
    $fase = 'B';
} elseif ($kelas == 5 | $kelas == 6) {
    $fase = 'C';
}

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

EOD;
$pdf->SetFont('times', '', 12);
// $pdf->SetMargins(15, 15, 0);
$pdf->SetCellMargins(0, 0, 0, 0);
// $pdf->SetCellPaddings(1, 1, 1, 1);

$html = '<div><table style="font-family: times; font-size: 12px;"><tr><td style="width: 60%">

    <table>
        <tr>
            <th style="width: 35%">Nama Peserta Didik</th>
            <th style="width: 6%">:</th>
            <th style="width: 45%">' . $namaSiswa . '</th>       
        </tr>
        <tr>
            <th style="width: 35%">NISN</th>
            <th style="width: 6%">:</th>
            <th style="width: 45%">' . $nisn. '</th>       
        </tr>
        <tr>
            <th style="fwidth: 35%">Sekolah</th>
            <th style="width: 6%">:</th>
            <th style="width: 45%">SD Katolik Bhakti Rogojampi</th>       
        </tr>
        <tr>
            <th style="width: 35%">Alamat</th>
            <th style="width: 6%">:</th>
            <th style="width: 45%">Jl. Ki Hajar Dewantoro</th>       
        </tr>
    </table></td><td>

    <table>
        <tr>
            <th style="width: 30%">Kelas</th>
            <th style="width: 6%">:</th>
            <th style="width: 45%">' . $stringKelas . '</th>       
        </tr>
        <tr>
            <th style="width: 30%">Fase</th>
            <th style="width: 6%">:</th>
            <th style="width: 45%">' . $fase . '</th>       
        </tr>
        <tr>
            <th style="width: 30%">Semester</th>
            <th style="width: 6%">:</th>
            <th style="width: 45%">' . $stringSemester . '</th>       
        </tr>
        <tr>
            <th style="width: 30%">Tahun Pelajaran</th>
            <th style="width: 6%">:</th>
            <th style="width: 45%">' . $tahunAjar . '</th>       
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

$html  .= '<table border="1" style="padding-top: 5x; padding-bottom: 5px; width: 100%; text-align: center; font-family: times; font-size: 12px">';
$html  .= '<tr>';
$html  .= '<th style="line-height: 1.5; width: 5%; text-align: center; vertical-align: middle; font-weight: bold;"><br>No.</th>';
$html  .= '<th style="line-height: 1.5; width: 25%; text-align: center; vertical-align: middle; font-weight: bold;"><br>Muatan Pelajaran</th>';
$html  .= '<th style="line-height: 1.5; width: 7%; text-align: center; font-weight: bold;">Nilai Akhir</th>';
$html  .= '<th style="line-height: 1.5; width: 55%; text-align: center; vertical-align: middle; font-weight: bold;"><br>Capaian Kompetensi</th>';
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

    $queryDeskripsi1 = "SELECT GROUP_CONCAT(a.deskripsi SEPARATOR ', ') AS combined_deskripsi1
                        FROM
                        `capaian_kompetensi` ck
                        LEFT JOIN asesmen a ON ck.id_asesmen = a.id_asesmen  
                        LEFT JOIN siswa s ON ck.id_siswa = s.id_siswa                                  
                        WHERE
                        a.semester = '$semester' AND
                        ck.id_siswa = '$idSiswa' AND
                        ck.capaian = '1' AND
                        a.id_kelas = '$kelas' AND
                        a.id_mapel = '$idMapel';";

    $result1 = mysqli_query($conn, $queryDeskripsi1);

    if ($result1) {
        $row1 = mysqli_fetch_assoc($result1);
        $combinedDeskripsi1 = $row1['combined_deskripsi1'];
    }

    $queryDeskripsi0 = "SELECT GROUP_CONCAT(a.deskripsi SEPARATOR ', ') AS combined_deskripsi0
    FROM
    `capaian_kompetensi` ck
    LEFT JOIN asesmen a ON ck.id_asesmen = a.id_asesmen  
    LEFT JOIN siswa s ON ck.id_siswa = s.id_siswa                                  
    WHERE
    a.semester = '$semester' AND
    ck.id_siswa = '$idSiswa' AND
    ck.capaian = '0' AND
    a.id_kelas = '$kelas' AND
    a.id_mapel = '$idMapel';";

    $result0 = mysqli_query($conn, $queryDeskripsi0);

    if ($result0) {
        $row0 = mysqli_fetch_assoc($result0);
        $combinedDeskripsi0 = $row0['combined_deskripsi0'];
    }                        

    $html  .= '<tr>';
    $html  .= '<td rowspan="2">' . $i++ . '</td>';
    $html  .= '<td rowspan="2" style="text-align: left;">' . $mapel . '</td>';
    $html  .= '<td rowspan="2">' . $nilaiRapot . '</td>';
    if ($combinedDeskripsi1 <> '') {
        if ($nilaiRapot > 74){
            $html  .= '<td style="text-align: left;">'. $namaSiswa . ' mampu ' . $combinedDeskripsi1 .'.</td>';
        } else {
            $html  .= '<td style="text-align: left;">'. $namaSiswa . ' membutuhkan bantuan dalam ' . $combinedDeskripsi1 .'.</td>';
        } 
    } else {
        $html  .= '<td style="text-align: left;"></td>';
    }
    $html  .= '</tr>';
    $html  .= '<tr>';
    if ($combinedDeskripsi0 <> '') {
            $html  .= '<td style="text-align: left;">'. $namaSiswa . ' membutuhkan bantuan dalam ' . $combinedDeskripsi0 .'.</td>';
    } else {
        $html  .= '<td style="text-align: left;"></td>';
    }
    $html  .= '</tr>';
}

$html  .= '</table><br><br>';

$html  .= '<table border="1" style=".. font-family: times; font-size: 12px">';
$html  .= '<tr>';
$html  .= '<th style="line-height: 1.5; width: 5%; text-align: center; vertical-align: middle; font-weight: bold;"><br>No.</th>';
$html  .= '<th style="line-height: 1.5; width: 25%; text-align: center; vertical-align: middle; font-weight: bold;"><br>Ekstrakurikuler</th>';
$html  .= '<th style="line-height: 1.5; width: 62%; text-align: center; vertical-align: middle; font-weight: bold;"><br>Keterangan</th>';
$html  .= '</tr>';

$queryCatatanEkstra = "SELECT
    ek.nama_ek,
    catatan
    FROM nilai_catatan_ekstrakurikuler nce
    LEFT JOIN ekstrakurikuler ek ON nce.id_ek = ek.id_ek
    WHERE id_siswa = $idSiswa";

    $i = 1;
    $catatanSiswa = mysqli_query($conn, $queryCatatanEkstra);

    while ($rowCatatanEkstra = mysqli_fetch_array($catatanSiswa)) {
    $namaEkstra = $rowCatatanEkstra['nama_ek'];
    $catatan = $rowCatatanEkstra['catatan'];
    $html  .= '<tr>';
    $html  .= '<td style="line-height: 1.5; width: 5%; text-align: center; vertical-align: middle;">' . $i . '</td>';
    $html  .= '<td style="line-height: 1.5; width: 25%; text-align: center; vertical-align: middle;">' . $namaEkstra .'</td>';
    $html  .= '<td style="line-height: 1.5; width: 62%; text-align: center; vertical-align: middle;">' . $catatan . '</td>';
    $html  .= '</tr>';
    }



$html  .= '</table><br><br>';

$html .= '<table style="font-family: times; font-size: 13px; border-collapse: separate;">';
$html  .= '<tr>';
$html .= '<td style="width: 5%"></td>';
$html .= '<td style="width: 47%">';

$html .= '<table border="1" style="text-align: left; font-family: helvetica; font-size: 11px; border-collapse: separate;">';

$html  .= '<tr>';
$html  .= '<th colspan="3" style="text-align: center; line-height: 1.5; font-weight: bold; width: 80%">Ketidakhadiran</th>';
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
    $html .= '<tr>';
    $html .= '<td style="width: 51%; border-left: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black; padding: 8px; padding-left: 12px;">  ' . $kategoriAbsen . '</td>';
    $html .= '<td style="width: 9%; text-align: left; padding: 8px; border-top: 1px solid black; border-bottom: 1px solid black; border-left: none; border-right: none;">:   ' . $jumlahAbsen . '</td>';
    $html .= '<td style="width: 20%; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; padding: 8px;">hari</td>';
    $html .= '</tr>';
}

$html  .= '</table>';


$html .='</td>';
$html .= '<td style="width: 40%">';

if ($semester == 'Genap'){
    //Naik tidak naik
    $html .= '<table style="border-collapse: collapse;">'; 
    $html .= '<tr>';
    $html .= '<td style="line-height: 1.5; font-weight: normal; width: 90%; border-top: 0.75px solid black; border-right: 0.75px solid black; border-left: 0.75px solid black">  Berdasarkan pencapaian seluruh kompetensi, ';                    
    $html .= '</td>';                    
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<td style="line-height: 1.5; font-weight: normal; width: 90%; border-right: 0.75px solid black; border-left: 0.75px solid black">  peserta didik dinyatakan :';                    
    $html .= '</td>';                    
    $html .= '</tr>';
    $html .= '<tr>';

    $queryKenaikan = "SELECT `status`
    FROM kenaikan_kelas                 
    WHERE 
    `id_tahun_Ajar` = '$idTahunAjar' AND 
    `semester`='$semester' AND 
    `id_siswa`='$idSiswa'
    ;";

    $kenaikanSiswa = mysqli_query($conn, $queryKenaikan);
    while ($rowKenaikan = mysqli_fetch_array($kenaikanSiswa)){
        $kenaikan = $rowKenaikan['status'];                        

        if ($kenaikan == 'Naik'){
            $tampilKenaikan = 'Naik ke kelas ' . ($kelas + 1);
        } elseif ($kenaikan == 'Tidak Naik'){
            $tampilKenaikan = 'Tidak Naik ke kelas ' . ($kelas + 1);
        } else {
            $tampilKenaikan = 'status belum diinput';
        } 

        $html .= '<td style="text-align: center; line-height: 3; font-style: italic; width: 90%; border-bottom: 0.75px solid black; border-left: 0.75px solid black; border-right: 0.75px solid black;">' . $tampilKenaikan . '</td>';
    }

    $html .= '</tr>';
    $html .= '</table>';    
} elseif ($semester == 'Genap' && $kelas == 6) {
    //Naik tidak naik
    $html .= '<table style="border-collapse: collapse;">'; 
    $html .= '<tr>';
    $html .= '<td style="line-height: 1.5; font-weight: normal; width: 90%; border-top: 0.75px solid black; border-right: 0.75px solid black; border-left: 0.75px solid black">  Berdasarkan pencapaian seluruh kompetensi, ';                    
    $html .= '</td>';                    
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<td style="line-height: 1.5; font-weight: normal; width: 90%; border-right: 0.75px solid black; border-left: 0.75px solid black">  peserta didik dinyatakan :';                    
    $html .= '</td>';                    
    $html .= '</tr>';
    $html .= '<tr>';

    $queryKenaikan = "SELECT `status`
    FROM kenaikan_kelas                 
    WHERE 
    `id_tahun_Ajar` = '$idTahunAjar' AND 
    `semester`='$semester' AND 
    `id_siswa`='$idSiswa'
    ;";

    $kenaikanSiswa = mysqli_query($conn, $queryKenaikan);
    while ($rowKenaikan = mysqli_fetch_array($kenaikanSiswa)){
        $kenaikan = $rowKenaikan['status'];                        

        if ($kenaikan == 'Naik'){
            $tampilKenaikan = 'Naik ke kelas ' . ($kelas + 1);
        } elseif ($kenaikan == 'Tidak Naik'){
            $tampilKenaikan = 'Tidak Naik ke kelas ' . ($kelas + 1);
        } else {
            $tampilKenaikan = 'status belum diinput';
        } 

        $html .= '<td style="text-align: center; line-height: 3; font-style: italic; width: 90%; border-bottom: 0.75px solid black; border-left: 0.75px solid black; border-right: 0.75px solid black;">' . $tampilKenaikan . '</td>';
    }

    $html .= '</tr>';
    $html .= '</table>'; 
}

$html .='</td>';
$html  .= '</tr>';
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