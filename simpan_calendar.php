<?php
    // Fungsi untuk Merubah susunan format tanggal
    function ubahformatTgl($tanggal) {
        $pisah = explode('/',$tanggal);
        $urutan = array($pisah[2],$pisah[1],$pisah[0]);
        $satukan = implode('-',$urutan);
        return $satukan;
    }
    
    // Ambil variabel dari form
   
    $tglterbit = $_POST['tglterbit'];
    $tgltutup = $_POST['tgltutup'];
	
    // Cara penggunaan function ubahTgl
    $ubahtglterbit = ubahformatTgl($tglterbit);
	$ubahtgltutup = ubahformatTgl($tgltutup);
    
    $konek = mysqli_connect("localhost","root","5JJILWwWWx8C5JD9xOXqsZKAtzf/xw7Aw=VuFHIRhM0j","pos");
    // $query = "select tanggal, kasir_name from initial where tanggal between '$tglterbit' and '$tgltutup'";
	//            VALUES('$judul','$pengarang','$ubahtgl')";
    // $input = mysqli_query($konek, $query);
 
	
	$sql=mysqli_query($konek,"select tanggal, kasir_name from pos.initial where tanggal between '$ubahtglterbit' and '$ubahtgltutup'");
	$data=array();
	while ($row= mysqli_fetch_assoc($sql)){
	array_push($data, $row);
} 


	//mengisi judul dan header tabel
		$judul = "daftar inisial";
	$header = array(
	array("label"=>"tanggal", "length"=>30, "align"=>"L"),
	array("label"=>"kasir_name", "length"=>50, "align"=>"L")
	//array("label"=>"Pass", "length"=>30, "align"=>"L"),
	);

	ob_start();
	//memanggil fpdf
	require_once ("fpdf/fpdf.php");
	$pdf = new FPDF();
	$pdf->AddPage();
   
   //tampilan Judul Laporan
	$pdf->SetFont('Arial','B','16'); //Font Arial, Tebal/Bold, ukuran font 16
	$pdf->Cell(0,20, $judul, '0', 1, 'C');
 
	//Header Table
	$pdf->SetFont('Arial','','10');
	$pdf->SetFillColor(139, 69, 19); //warna dalamdalam kolom header
	$pdf->SetTextColor(255); //warna tulisan putih
	$pdf->SetDrawColor(222, 184, 135); //warna border
	foreach ($header as $kolom) {
    $pdf->Cell($kolom['length'], 5, $kolom['label'], 1, '0', $kolom['align'], true);
}
$pdf->Ln();

//menampilkan data table
$pdf->SetFillColor(245, 222, 179); //warna dalam kolom data
$pdf->SetTextColor(0); //warna tulisan hitam
$pdf->SetFont('');
$fill=false;
foreach ($data as $baris) {
$i = 0;
foreach ($baris as $cell) {
$pdf->Cell($header[$i]['length'], 5, $cell, 1, '0', $kolom['align'], $fill);
$i++;
}
$fill = !$fill;
$pdf->Ln();
}
 
//output file pdf
$pdf->Output();
ob_end_flush(); 

      
?>