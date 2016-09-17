<?php
$link = mysqli_connect('192.168.10.182', 'root', 'ventrinet', 'db_washokusato_ho');
if (!$link) {
   die('Could not connect: ' . mysql_error());
}
echo 'Connected successfully';
$nourut = mysqli_query($link,"SET @row_number:=0");
$sql=mysqli_query($link,"SELECT
	@row_number:=@row_number+1 AS row_number,
	a.tanggal
	,CONCAT(CONCAT('INV',LPAD(b.mininv,6,'0')),' - ',CONCAT('INV',LPAD(b.maxinv,6,'0'))) AS nobonbill
	,b.jmlbill
	-- ,(a.gross + b.roundingAmount) as gross
	,a.gross AS gross
	,a.diskon
	,a.servicecharge
	,a.dpp
	,a.pajak 
	,a.pajak2 AS 'dpp/100*10'
	 FROM (
	   SELECT `date` AS tanggal
	   ,SUM(gross) AS gross
	   ,SUM(diskon) AS diskon
	   ,SUM(servicecharge) AS servicecharge
	   ,SUM(dpp) AS dpp
	   ,ROUND(SUM(pajak)) AS pajak
	   ,ROUND(SUM(dpp) /100 * 10) AS pajak2
	   FROM (
	      SELECT `date`,SUM(gross) AS gross, FLOOR(SUM(diskon)) AS diskon
	      ,FLOOR(SUM(servicecharge)) AS servicecharge
	      ,FLOOR(SUM(dpp)) AS dpp, SUM(pajak) AS pajak
	      FROM (
		 SELECT DATE(s.`date`) AS `date`, l.unitprice*l.quantity AS gross
		 , l.discountAmount AS diskon
		 , l.servicechargeamount AS servicecharge
		 , (l.unitprice*l.quantity)+(l.servicechargeamount) AS dpp
		 , l.tax1amount AS pajak
		 FROM tbl_sales s, tbl_sales_lines l
		 WHERE
		 s.id=l.sales_id
		 AND s.voidcheck IS FALSE
		 AND DATE(s.`date`) BETWEEN '2015-11-02' AND '2016-02-09'
		 AND l.`type` = 1
		 AND s.invoiceno IS NOT NULL
		 AND s.outlet = 'Washokusato CP'
	      ) a GROUP BY `date`
	   ) a GROUP BY `date`
	 ) a
	 LEFT JOIN (
	   SELECT DATE(`date`) AS tanggal, MAX(invoiceno) AS maxinv,MIN(invoiceno) AS mininv
	   , COUNT(invoiceno) AS jmlbill, SUM(roundingAmount) AS roundingAmount
	   FROM tbl_sales s
	   WHERE DATE(`date`) BETWEEN '2015-11-02' AND '2016-02-09' AND invoiceno IS NOT NULL
	   AND voidcheck IS FALSE
	   AND s.outlet = 'Washokusato CP'
	   GROUP BY DATE(`date`)
	 ) b ON a.tanggal = b.tanggal");
$data=array();
while ($row= mysqli_fetch_assoc($sql)){
	array_push($data, $row);
} 

ob_start();
//memanggil fpdf
require_once ("fpdf/fpdf.php");
$pdf = new FPDF();
$pdf->AddPage();

//mengisi judul dan header tabel
$judul = "Bapeda";
$header = array(
array("label"=>"no", "length"=>8, "align"=>"L"),
array("label"=>"tanggal", "length"=>20, "align"=>"L"),
array("label"=>"nobill", "length"=>40, "align"=>"L"),
array("label"=>"jml bill", "length"=>12, "align"=>"L"),
array("label"=>"gross", "length"=>18, "align"=>"L"),
array("label"=>"diskon", "length"=>18, "align"=>"L"),
array("label"=>"service charge", "length"=>22	, "align"=>"L"),
array("label"=>"dpp", "length"=>18, "align"=>"L"),
array("label"=>"pajak", "length"=>18, "align"=>"L"),
array("label"=>"dpp/100*100", "length"=>21, "align"=>"L"),
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
$pdf->SetFont('Arial','','8.5');
$pdf->SetFillColor(139, 69, 19); //warna dalamdalam kolom header
$pdf->SetTextColor(255); //warna tulisan putih
$pdf->SetDrawColor(222, 184, 135); //warna border
foreach ($header as $kolom) {
    $pdf->Cell($kolom['length'], 5, $kolom['label'], 1, '0', $kolom['align'], true);
}
$pdf->Ln();

//menampilkan data table
$pdf->SetFillColor(245, 222, 179); //warna dalam kolom `
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

mysqli_close($link);
?>