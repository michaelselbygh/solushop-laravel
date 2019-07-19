<?php 

	include("databaseconnection.php");


	//updating counts
	$sql = "UPDATE count SET ProdIDCount = 0, CusIDCount = 0, OrdIDCount = 0";
	$conn->query($sql)


?>