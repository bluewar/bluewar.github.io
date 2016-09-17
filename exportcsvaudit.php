<?php

ini_set('max_execution_time', 300); //300 seconds = 5 minutes



//merubah format tanggal
	    function ubahformatTgl($tanggal) {
        $pisah = explode('/',$tanggal);
        $urutan = array($pisah[2],$pisah[1],$pisah[0]);
        $satukan = implode('-',$urutan);
        return $satukan;
    }
    
    // Ambil variabel dari form
    $tglterbit = $_POST['tglterbit'];
    $tgltutup = $_POST['tgltutup'];
	$aoutlet = implode($_POST['outlet']);
	// echo $aoutlet;
	
    // Cara penggunaan function ubahTgl
	
    $ubahtglterbit = ubahformatTgl($tglterbit);
	$ubahtgltutup = ubahformatTgl($tgltutup);
	
	// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header("Content-Disposition: attachment; filename=Sato Audit $tglterbit s/d $tgltutup $aoutlet.csv");
$output = fopen('php://output', 'w');
	
// fetch the data
$connect=mysqli_connect('192.168.10.182', 'root', 'ventrinet', 'db_washokusato_ho');
$rows=mysqli_query($connect,"SELECT
 DISTINCT l.paymentMethodName
 FROM tbl_sales s INNER JOIN tbl_sales_lines l ON s.id = l.sales_id
 WHERE DATE(s.`date`) BETWEEN '$ubahtglterbit' AND '$ubahtgltutup'
 AND l.`type` = '3'
 AND s.voidCheck IS FALSE AND s.invoiceNo IS NOT NULL
 AND s.outlet = 'WASHOKUSATO MOI'
 GROUP BY DATE(s.date),l.paymentMethodName
 ORDER BY l.paymentMethodName;");
 
 $header1='';
// loop over the rows, outputting them
while ($row3 = mysqli_fetch_row($rows))
{
	$satukan3 = implode($row3);
	$header1 .= "$satukan3,"; 
}
 

$rows2=mysqli_query($connect,"select
 distinct l.discountName
 from tbl_sales s inner join tbl_sales_lines l on s.id = l.sales_id
 where date(s.`date`) between '$ubahtglterbit' AND '$ubahtgltutup'
 and l.`type` = '2'
 and s.voidCheck is false and s.invoiceNo is not null
 and s.outlet = 'WASHOKUSATO MOI'
 group by date(s.`date`), l.discountName
 order by l.discountName;");

$header2='';
// loop over the rows, outputting them
while ($row4 = mysqli_fetch_row($rows2))
{
	$satukan4 = implode($row4);
	$header2 .= "$satukan4,"; 
}
 
$string = "Tanggal,Total Inv,Total Cust,Subtotal,Disc Amount,Net Sales,Service Charge,Tax1,Rounding,Total,Tanggal,$header1 tanggal,$header2"; 
$arr=explode(',',$string);
fputcsv($output,array_values($arr));


$rowspayment=mysqli_query($connect,"SELECT
 DISTINCT l.paymentMethodName
 FROM tbl_sales s INNER JOIN tbl_sales_lines l ON s.id = l.sales_id
 WHERE DATE(s.`date`) BETWEEN '$ubahtglterbit' AND '$ubahtgltutup'
 AND l.`type` = '3'
 AND s.voidCheck IS FALSE AND s.invoiceNo IS NOT NULL
 AND s.outlet = 'WASHOKUSATO MOI'
 GROUP BY DATE(s.date),l.paymentMethodName
 ORDER BY l.paymentMethodName;");

 $cetak ='';
// loop over the rows, outputting them
while ($row = mysqli_fetch_row($rowspayment))
{
	 $satukan = implode($row);
$cetak .= ",MAX(IF(paymentMethodName = '$satukan',amount, 0)) as '$satukan' ";
//echo "$cetak";
}


$rowsdisc=mysqli_query($connect,"select
 distinct l.discountName
 from tbl_sales s inner join tbl_sales_lines l on s.id = l.sales_id
 where date(s.`date`) between '$ubahtglterbit' AND '$ubahtgltutup'
 and l.`type` = '2'
 and s.voidCheck is false and s.invoiceNo is not null
 and s.outlet = 'WASHOKUSATO MOI'
 group by date(s.`date`), l.discountName
 order by l.discountName;");
 
 $cetak2 ='';
// loop over the rows, outputting them
while ($row2 = mysqli_fetch_row($rowsdisc))
{
	 $satukan2 = implode($row2);
$cetak2 .= ",MAX(IF(discountName = '$satukan2',amount, 0)) as '$satukan2' ";
//echo "$cetak2";
}

 

$rows3=mysqli_query($connect,"SELECT
 revenue.*,payment.*,discount.*
 FROM (
 SELECT DATE(`date`) AS tanggal,COUNT(*) AS total_invoice, SUM(pax) AS total_customer, SUM(subtotal) AS subtotal, SUM(discountAmount) AS discountAmount, SUM(subtotal)-SUM(discountAmount) AS Net_Sales
 , ROUND(SUM(serviceChargeAmount)) AS ServiceCharge
 , ROUND(SUM(tax1Amount)) AS Tax1
 , SUM(total) - (SUM(subtotal)-SUM(discountAmount)+ROUND(SUM(serviceChargeAmount))+ROUND(SUM(tax1Amount))) AS Rounding
 , SUM(total) AS Total
 FROM tbl_sales WHERE DATE(`date`) BETWEEN '$ubahtglterbit' AND '$ubahtgltutup'
 AND outlet = '$aoutlet'
 AND voidCheck IS FALSE AND invoiceNo IS NOT NULL
 GROUP BY DATE(`date`)
 ) revenue
 LEFT JOIN
 (
 SELECT tanggal
$cetak
 FROM (
 SELECT DATE(s.date) AS tanggal, l.paymentMethodCode, l.paymentMethodName, SUM(l.Amount-l.changeAmount) AS amount
 FROM tbl_sales s INNER JOIN tbl_sales_lines l ON s.id = l.sales_id
 WHERE DATE(s.`date`) BETWEEN '$ubahtglterbit' AND '$ubahtgltutup'
 AND l.`type` = '3'
 AND s.voidCheck IS FALSE AND s.invoiceNo IS NOT NULL
 AND s.outlet = '$aoutlet'
 GROUP BY DATE(s.date),l.paymentMethodName
 ORDER BY DATE(s.date),l.paymentMethodName
 ) a
 GROUP BY tanggal
 ) payment ON revenue.tanggal = payment.tanggal
 LEFT JOIN
 (
 SELECT tanggal
$cetak2
 FROM (
 SELECT DATE(s.`date`) AS tanggal, l.discountCode, l.discountName, SUM(l.Amount) AS amount
 FROM tbl_sales s INNER JOIN tbl_sales_lines l ON s.id = l.sales_id
 WHERE DATE(s.`date`) BETWEEN '$ubahtglterbit' AND '$ubahtgltutup'
 AND l.`type` = '2'
 AND s.voidCheck IS FALSE AND s.invoiceNo IS NOT NULL
 AND s.outlet = '$aoutlet'
 GROUP BY DATE(s.`date`), l.discountName
 ORDER BY DATE(s.`date`)
 ) a GROUP BY tanggal
 ) discount ON revenue.tanggal = discount.tanggal;
");
 
$num_fields=mysqli_num_fields($rows3);

// loop over the rows, outputting them
while ($row3 = mysqli_fetch_row($rows3))
{
fputcsv($output,array_values($row3));

}

 
 
 exit();

?>