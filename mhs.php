<?php
//header untuk format XML, jika dihilangkan maka akan berbentuk data string
header('Content-type: text/xml; charset=ISO-8859-1');
include "koneksi.php";

//check for the path elements
$path = $_SERVER['SCRIPT_FILENAME'];
if ($path != null) {
	$path_params = spliti ("/", $path);
}

//metode request untuk GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$path_params = isset($_POST['path_params']);
	if ($path_params[1] != null) {
		$query = "select
			nim,
			nama,
			alamat,
			prodi
			from mahasiswa where nim = $path_params[1]";
	} else {
		$query = "select
			nim,
			nama,
			alamat,
			prodi
			from mahasiswa";
	}
	$result = mysql_query($query) or die('Query Failed: ' . mysql_error());

	echo "<data>";
	while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
		echo "<mahasiswa>";
		foreach ($line as $key => $col_value) {
			echo "<$key>$col_value</$key>";
		}
		echo "</mahasiswa>";
	}
	echo "</data>";

	mysql_free_result($result);
}

//metode request untuk POST
else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$input = file_get_contents("php://input");
	$xml = simplexml_load_string($input);
	foreach ($xml->mahasiswa as $mahasiswa) {
		$querycek = "select * from mahasiswa where nim ='$mahasiswa->nim'";
		$num_rows = mysql_num_rows($querycek);
		if ($num_rows == 0)
		{
			$query = "insert into mahasiswa (
				nim,
				nama,
				alamat,
				prodi)
				values (
				'$mahasiswa->nim',
				'$mahasiswa->nama',
				'$mahasiswa->alamat',
				'$mahasiswa->prodi'
				)";
		}
		else if ($num_rows == 1)
		{
			$query = "update mahasiswa set
				nim = '$mahasiswa->nim',
				nama = '$mahasiswa->nama',
				alamat = '$mahasiswa->alamat',
				prodi = '$mahasiswa->prodi'
				where nim = '$mahasiswa->nim'";
		}
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	}
}

//metode request untuk delete
mysql_close($link);

?>