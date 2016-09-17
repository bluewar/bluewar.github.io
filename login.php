<?php
session_start(); // Memulai Session
$error=''; // Variabel untuk menyimpan pesan error
if (isset($_POST['submit'])) {
	if (empty($_POST['username']) || empty($_POST['password'])) {
			$error = "Username or Password is invalid";
	}
	else
	{
		// Variabel username dan password
		$username=$_POST['username'];
		$password=$_POST['password'];
		// Membangun koneksi ke database
		$connection = mysqli_connect("localhost:3306", "root", "hTwZzYKcrIzc6zPLyuN66wIBw6n3dTCFM=1wsRVhQVNA");
		// Mencegah MySQL injection 
		$username = stripslashes($username);
		$password = stripslashes($password);
		//	$username = mysqli_real_escape_string($username);
		//	$password = mysqli_real_escape_string($password);
		// Seleksi Database
		//	$db = mysqli_select_db("pos", $connection);
		// SQL query untuk memeriksa apakah karyawan terdapat di database?
		$query = mysqli_query($connection,"select nik, pass from pos.passtoko where pass='$password' AND nik='$username';");
		$rows = mysqli_num_rows($query);
			if ($rows == 1) {
				$_SESSION['login_user']=$username; // Membuat Sesi/session
				header("location: profile.php"); // Mengarahkan ke halaman profil
				} else {
				$error = "Username atau Password belum terdaftar";
				}
				mysqli_close($connection); // Menutup koneksi
	}
}
?>