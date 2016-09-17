<html>
<head>
    <link type="text/css" rel="stylesheet" href="development-bundle/themes/ui-lightness/ui.all.css" />
    
    <script src="development-bundle/jquery-1.8.0.min.js"></script>
    <script src="development-bundle/ui/ui.core.js"></script>
    <script src="development-bundle/ui/ui.datepicker.js"></script>
    <script src="development-bundle/ui/i18n/ui.datepicker-id.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $("#tglterbit").datepicker({
                dateFormat : "dd/mm/yy",
                changeMonth : true,
                changeYear : true
				 });				
        });
	</script>
	<script type="text/javascript">	
        $(document).ready(function(){
            $("#tgltutup").datepicker({
                dateFormat : "dd/mm/yy",
                changeMonth : true,
                changeYear : true				
            });			
        });
    </script>
</head>

<?php
$connect=mysqli_connect('192.168.10.182', 'root', 'ventrinet', 'db_washokusato_ho');
$sql = mysqli_query($connect, "SELECT DISTINCT outlet FROM `tbl_sales` where outlet !=' ' ")
?>

<body style="font-size:85%;">
    <h3>Report Bapeda</h3>

<form method="POST" action="exportcsvbapeda.php">
<select name="outlet[]" >
<option> == Pilih Outlet == </option>
<?php if (mysqli_num_rows($sql)>0){
	while ($row=mysqli_fetch_array($sql)) {?>
<option type="text"><?php echo $row['outlet'] ?></option>
<?php } ?>
<?php } ?>
		</select>

        <table>
        <tr><td>Tanggal mulai :</td> <td> <input name="tglterbit" id="tglterbit" type="text" /> Tanggal akhir : <input name="tgltutup" id="tgltutup" type="text" /></td></tr>
        <tr><td colspan="2"><input type="submit" name="submit" value="Download CSV" /></td></tr>
    </form>	
</body>
</html>