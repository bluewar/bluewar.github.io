<?php
 
// nama file
 
$namaFile = "report.xls";
 
// Function penanda awal file (Begin Of File) Excel
 
function xlsBOF() {
echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
return;
}
 
// Function penanda akhir file (End Of File) Excel
 
function xlsEOF() {
echo pack("ss", 0x0A, 0x00);
return;
}
 
// Function untuk menulis data (angka) ke cell excel
 
function xlsWriteNumber($Row, $Col, $Value) {
echo pack("sssss", 0x203, 14, $Row, $Col, 0x0);
echo pack("d", $Value);
return;
}
 
// Function untuk menulis data (text) ke cell excel
 
function xlsWriteLabel($Row, $Col, $Value ) {
$L = strlen($Value);
echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L);
echo $Value;
return;
}
 
// header file excel
 
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
 
// header untuk nama file
/*header("Content-Disposition: attachment;
 filename="$namaFile"");*/
 
	// isi $excel akan bernilai true apabila ditemukan parameter get dengan nama 'excel'
$excel = isset($_GET['lihat']);
if($excel):
 // buat nama file unique untuk di download
 $filename = 'export-'.date('YmdHis');
 // dengan perintah di bawah ini akan memunculkan dialog download di browser anda
 header("Content-type: application/x-msdownload");
 // perintah di bawah untuk menentukan nama file yang akan di download
 header("Content-Disposition: attachment; filename=".$filename.".xls");
 else
	 echo "<br />"
 
header("Content-Transfer-Encoding: binary ");
 
// memanggil function penanda awal file excel
xlsBOF();
 
// ------ membuat kolom pada excel --- //
 
// mengisi pada cell A1 (baris ke-0, kolom ke-0)
xlsWriteLabel(0,0,"NO");
 
// mengisi pada cell A2 (baris ke-0, kolom ke-1)
xlsWriteLabel(0,1,"Tanggal");
 
// mengisi pada cell A3 (baris ke-0, kolom ke-2)
xlsWriteLabel(0,2,"Kasir Name");
 
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
	
    // Cara penggunaan function ubahTgl
    $ubahtglterbit = ubahformatTgl($tglterbit);
	$ubahtgltutup = ubahformatTgl($tgltutup);
	
// -------- menampilkan data --------- //
 
// koneksi ke mysql
 
$connection = mysqli_connect("localhost", "root", "5JJILWwWWx8C5JD9xOXqsZKAtzf/xw7Aw=VuFHIRhM0j");
 
// query menampilkan semua data
 
$query = "select tanggal, kasir_name from pos.initial where tanggal between '$ubahtglterbit' and '$ubahtgltutup'";
$hasil = mysqli_query($connection, $query);
 
// nilai awal untuk baris cell
$noBarisCell = 1;
 
// nilai awal untuk nomor urut data
$noData = 1;
 
while ($data = mysqli_fetch_array($hasil))
{
 // menampilkan no. urut data
 xlsWriteNumber($noBarisCell,0,$noData);
 
// menampilkan data nim
 xlsWriteLabel($noBarisCell,1,$data['tanggal']);
 
// menampilkan data nama mahasiswa
 xlsWriteLabel($noBarisCell,2,$data['kasir_name']);

// increment untuk no. baris cell dan no. urut data
 $noBarisCell++;
 $noData++;
}
 
// memanggil function penanda akhir file excel
xlsEOF();
exit();
 
?>