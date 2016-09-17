<?php
 

// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// output the column headings
fputcsv($output, array('Category','Gross','Discount','Service Charge','DPP','Pajak'));
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
header("Content-Disposition: attachment; filename=Sato SalesPerCat $tglterbit s/d $tgltutup $aoutlet.csv");

// fetch the data
$connect=mysqli_connect('192.168.10.182', 'root', 'ventrinet', 'db_washokusato_ho');
$rows = mysqli_query($connect, "select l.category
, sum((l.quantity-l.voidQuantity)*l.unitPrice) as gross
, sum(l.discountAmount) as discount
, sum(l.serviceChargeAmount) as servicecharge
, sum((l.quantity-l.voidQuantity)*l.unitPrice)+sum(l.serviceChargeAmount) as dpp
, sum(l.tax1Amount) as pajak
from tbl_sales s inner join tbl_sales_lines l on s.id = l.sales_id
where 1
and s.voidCheck is false
and (s.invoiceNo is not null or s.invoiceNo != '')
and l.`type` = '1'
and l.quantity > 0
and l.quantity-l.voidQuantity > 0
and date(s.`date`) between '$ubahtglterbit' and '$ubahtgltutup'
and s.outlet = '$aoutlet'
group by l.category;");
 
$num_fields=mysqli_num_fields($rows);

// loop over the rows, outputting them
while ($row = mysqli_fetch_row($rows))
{
fputcsv($output,array_values($row));
}
 
 exit();
?>