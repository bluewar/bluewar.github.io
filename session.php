<?php
// Membangun Koneksi dengan Server dengan nama server, user_id dan password sebagai parameter
$connection = mysqli_connect("localhost", "root", "hTwZzYKcrIzc6zPLyuN66wIBw6n3dTCFM=1wsRVhQVNA");
// Seleksi Database
$db = mysqli_select_db($connection,"pos");
session_start();// Memulai Session
// Menyimpan Session
$user_check=$_SESSION['login_user'];
// Ambil nama karyawan berdasarkan username karyawan dengan mysql_fetch_assoc
$ses_sql=mysqli_query($connection, "select nama from passtoko where nik='$user_check'");
$row = mysqli_fetch_assoc($ses_sql);
$login_session =$row['nama'];
if(!isset($login_session)){
	mysqli_close($connection); // Menutup koneksi
	header('Location: index.php'); // Mengarahkan ke Home Page
}
?>