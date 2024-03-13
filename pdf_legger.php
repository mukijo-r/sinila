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
        $this->SetCellPaddings(0, 0.5);
       
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
$pdf->SetTitle('Legger');
$pdf->SetSubject('TCPDF');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(15, 10, 10);
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
LEGGER SEMESTER $semesterRomawi
SD KATOLIK BHAKTI ROGOJAMPI
TAHUN PELAJARAN $tahunAjar



EOD;
$pdf->SetFont('times', '', 9);
$pdf->SetCellMargins(0, 1, 0, 0);

$html = '<table border="0.75" cellpadding="1" style="text-align: center;">';
$html .= '<tr>';
$html .= '<th style="vertical-align: middle; font-weight: bold; width: 3%">No.</th>';
$html .= '<th style="vertical-align: middle; font-weight: bold; width: 4%">No. Induk</th>';
$html .= '<th style="vertical-align: middle; font-weight: bold; width: 7%">NISN</th>';
$html .= '<th style="vertical-align: middle; font-weight: bold; width: 13%">Nama</th>';

$subjectsQuery = "SELECT id_mapel, mapel FROM mapel ORDER BY id_mapel;";
$subjectsResult = mysqli_query($conn, $subjectsQuery);

while ($subjectRow = mysqli_fetch_assoc($subjectsResult)) {
    $idMapel = $subjectRow['id_mapel'];
    $mapel = $subjectRow['mapel'];

    if ($kelas == 1 | $kelas == 2 | $kelas == 3) {
        if ($mapel == 'Bahasa Using') {
            continue;
        }
    }
    $html .= '<th style="vertical-align: middle; font-weight: bold; width: 6.5%">' . $mapel . '</th>';
}

$html .= '</tr>';

// Fetch student data
$query = "
    SELECT s.nis, s.nisn, s.nama
    FROM siswa s
    WHERE id_kelas = $kelas
    GROUP BY s.nis, s.nisn, s.nama;
";
$studentResult = mysqli_query($conn, $query);

$i = 1;

while ($studentRow = mysqli_fetch_assoc($studentResult)) {
    $nis = $studentRow['nis'];
    $nisn = $studentRow['nisn'];
    $namaSiswa = $studentRow['nama'];

    $html .= '<tr>';
    $html .= '<td style="width: 3%">' . $i++ . '</td>';
    $html .= '<td style="width: 4%">' . $nis . '</td>';
    $html .= '<td style="width: 7%">' . $nisn . '</td>';

    $html .= '<td> <table><tr>';
    $html .= '<td style="text-align: left;">' . $namaSiswa . '</td>';
    $html .= '</tr></table></td>';

    // Fetch and display nilaiRapot for each subject
    mysqli_data_seek($subjectsResult, 0); // Reset the subjects result pointer

    while ($subjectRow = mysqli_fetch_assoc($subjectsResult)) {
        $idMapel = $subjectRow['id_mapel'];
        $mapel = $subjectRow['mapel'];
        if ($kelas == 1 | $kelas == 2 | $kelas == 3) {
            if ($mapel == 'Bahasa Using') {
                continue;
            }
        }
    
        $query = "
            SELECT
                MAX(CASE WHEN nul.lingkup_materi = 'LM 1' THEN nul.nilai ELSE 0 END) AS LM1,
                MAX(CASE WHEN nul.lingkup_materi = 'LM 2' THEN nul.nilai ELSE 0 END) AS LM2,
                MAX(CASE WHEN nul.lingkup_materi = 'LM 3' THEN nul.nilai ELSE 0 END) AS LM3,
                MAX(CASE WHEN nul.lingkup_materi = 'LM 4' THEN nul.nilai ELSE 0 END) AS LM4,
                MAX(CASE WHEN nuj.ujian = 'STS' THEN nuj.nilai ELSE 0 END) AS STS,
                MAX(CASE WHEN nuj.ujian = 'SAS' THEN nuj.nilai ELSE 0 END) AS SAS
            FROM siswa s
            LEFT JOIN nilai_ulangan nul ON s.id_siswa = nul.id_siswa
            LEFT JOIN nilai_ujian nuj ON s.id_siswa = nuj.id_siswa
            WHERE
                nul.kelas = '$kelas' AND
                nul.semester = '$semester' AND
                nul.id_mapel = '$idMapel' AND
                nul.id_tahun_ajar = '$idTahunAjar' AND
                nuj.kelas = '$kelas' AND
                nuj.semester = '$semester' AND
                nuj.id_mapel = '$idMapel' AND
                nuj.id_tahun_ajar = '$idTahunAjar' AND
                s.nis = '$nis'
            GROUP BY s.nis;
        ";
    
        $queryResult = mysqli_query($conn, $query);
    
        // Check if there is data
        if ($queryResult && mysqli_num_rows($queryResult) > 0) {
            $row = mysqli_fetch_assoc($queryResult);
    
            $LM1 = $row['LM1'];
            $LM2 = $row['LM2'];
            $LM3 = $row['LM3'];
            $LM4 = $row['LM4'];
            $rerataUlangan = ($LM1 + $LM2 + $LM3 + $LM4) / 4;
            $STS = $row['STS'];
            $SAS = $row['SAS'];
            $nilaiRapot = ((2 * $rerataUlangan) + $STS + (2 * $SAS)) / 5;
    
            $html .= '<td style="width: 6.5%">' . $nilaiRapot . '</td>';
        } else {
            $html .= '<td style="width: 6.5%">-</td>';
        }
    }   

    $html .= '</tr>';
}

$html .= '</table><br><br><br>';

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
$pdf->Output('legger.pdf', 'I');

?>