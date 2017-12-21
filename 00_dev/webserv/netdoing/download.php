<?php
//echo 'Current PHP version: ' . phpversion();
//Current PHP version: 7.1.7

$Serverip = $_SERVER["REMOTE_ADDR"];
if($Serverip!="211.75.1.128"&&$Serverip!="120.127.252.14")
{
	echo "IP無授權處理!!";
}
else{
	//connect DB請調整
	$serverName = "192.168.0.166";  
	$connectionInfo = array( "Database"=>"moodle", "UID"=>"sa", "PWD"=>"04271129", "CHARACTERSET"=>"utf-8");

	$sql = "SELECT fullname, name, lastname, firstname, timemodified, finalgrade, classcode, openid , dataid FROM view_student_learning where timemodified is not null ";
	$ccode =  $_POST["classcode"] ; 
	if($ccode!=""){
		$sql = $sql. " and classcode in (" . $ccode . ")";
	}

	$lid =  $_POST["lid"] ; 
	if($lid!=""){
		$sql = $sql. " and dataid > " . $lid;
	}
	$col = ["fullname", "name", "lastname", "firstname", "timemodified", "finalgrade", "classcode", "openid", "dataid"];
	//print_r($col);

	//開conn
	$conn = sqlsrv_connect( $serverName, $connectionInfo);  
	if( $conn === false ) {
		die( print_r( sqlsrv_errors(), true));//中斷程式
	}
	else{
		$stmt = sqlsrv_query( $conn, $sql );
		if( $stmt === false) {
			die( print_r( sqlsrv_errors(), true) );
		}else{
			while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
				//print_r($row);
				$row_value = "";
				foreach ($col as $key => $value) {
					$row_value .= "'$row[$value]',";
				}

				$row_value= substr($row_value , 0, -1);//去掉最後一個
				echo"$row_value\r\n";
			}
		}
		sqlsrv_free_stmt($stmt);
	}


	//關conn
	sqlsrv_close($conn);
}



?>

