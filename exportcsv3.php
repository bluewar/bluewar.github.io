<?php

function table_to_csv($query,$filename='namafile.csv')
{
//
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=".$filename);
header("Pragma: no-cache");
header("Expires: 0");
//
$output=fopen('php://output', 'w');
$connect=mysqli_connect('192.168.10.182', 'root', 'ventrinet', 'db_washokusato_ho');

$result=mysqli_query($connect, $query);
$num_fields=mysqli_num_fields($result);
fputcsv($output, array('jobTitle','name'));
$headers=array();
for ($i=2;$i<$num_fields;$i++)
{
$headers[]=mysqli_fetch_field($result,$i);
}
fputcsv($output,$headers);
while($row=mysqli_fetch_row($result))
{
fputcsv($output,array_values($row));
}
fclose($output);
}
table_to_csv('select jobTitle,name from tbl_employees');
?>