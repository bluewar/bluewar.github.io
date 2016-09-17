<?php
include('session.php');
?>
<!DOCTYPE html>
<html>
	<head>
	  <title>Nyekrip Halaman Khusus</title>
	  <link href="style.css" rel="stylesheet" type="text/css">
	</head>
<body>
	<div id="profile">
	  <b id="welcome">Selamat Datang : <i><?php echo $login_session; ?></i></b>
	  <b id="logout"><a href="logout.php">Log Out</a></b>
	</div>
	
	<!-- <p><a href="cobapdf.php">report PDF download</a></p> -->
	<p><a href="calendar.php">Bapeda Report</a></p>
	<p><a href="salespercat.php">Sales per Category Report</a></p>
	<p><a href="audit.php">Audit Report</a></p>
<!-- 	<p><a href="datamentah.php">lihat</a></p> -->
</body>
</html>


