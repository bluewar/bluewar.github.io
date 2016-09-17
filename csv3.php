<?php
 
// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=data.csv');


// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// output the column headings
fputcsv($output, array('tanggal','nobonbill','jmlbill','gross','diskon','servicecharge','dpp','pajak','dpp/100*10'));

$connect=mysqli_connect('192.168.10.182', 'root', 'ventrinet', 'db_washokusato_ho');
$rows = mysqli_query($connect, "SELECT
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
         AND DATE(s.`date`) BETWEEN '2016-02-01' AND '2016-02-02'
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
   WHERE DATE(`date`) BETWEEN '2016-02-01' AND '2016-02-02' AND invoiceno IS NOT NULL
   AND voidcheck IS FALSE
   AND s.outlet = 'Washokusato CP'
   GROUP BY DATE(`date`)
 ) b ON a.tanggal = b.tanggal");

$num_fields=mysqli_num_fields($rows);

// loop over the rows, outputting them
while ($row = mysqli_fetch_row($rows))
{
fputcsv($output,array_values($row));
}
 
 exit();
?>