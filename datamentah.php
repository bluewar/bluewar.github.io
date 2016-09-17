<table border="1">
	<tr>
		<th>NO.</th>
		<th>Tanggal</th>
		<th>Kasir_name</th>
		
	</tr>
	<?php
	//koneksi ke database
	$connection = mysqli_connect("localhost", "root", "5JJILWwWWx8C5JD9xOXqsZKAtzf/xw7Aw=VuFHIRhM0j");
	
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

	
	//query menampilkan data
	$sql = mysqli_query($connection,"SELECT tanggal, kasir_name FROM pos.initial where tanggal between '$ubahtglterbit' and '$ubahtgltutup'");
	$no = 1;
	while($data = mysqli_fetch_assoc($sql)){
		echo '
		<tr>
			<td>'.$no.'</td>
			<td>'.$data['tanggal'].'</td>
			<td>'.$data['kasir_name'].'</td>
		</tr>
		';
		$no++;
	}
	?>
</table>