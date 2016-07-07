<?php
    // open mysql connection
    $host = 'localhost';
    $username = 'mmc';
    $password = 'mmc@mysql@ucard';
    $dbname = 'ucard';
    $con = mysqli_connect($host, $username, $password, $dbname) or die('Error in Connecting: ' . mysqli_error($con));

    // use prepare statement for insert query
    $st = mysqli_prepare($con, 'INSERT INTO game_rec(idno, schno, result, dtime) VALUES (?, ?, ?, ?)');

    // bind variables to insert query params
    mysqli_stmt_bind_param($st, 'ssss', $idno, $schno, $result, $dtime);

    // read json file
   // $filename = 'BASIC.20160519.json';
   // $json = file_get_contents($filename);   

  //   $record = $argv[1];
  //   $record = $_GET['record'];
   $record = $_POST['record'];

    //convert json object to php associative array
    $row = json_decode($record, true);

    // loop through the array
//    foreach ($data as $row) {
        // get the employee details
        $idno = $row['idno'];
        $schno = $row['schno'];
        $result = $row['result'];
        $dtime = $row['dtime'];

        // execute insert query 
        mysqli_stmt_execute($st);

	if(mysqli_stmt_affected_rows($st)==1){
    	echo "Insert Success";
	}else{
    	echo "Insert Failed";
 	}

	mysqli_stmt_close($st);

//   }
   
    //close connection
    mysqli_close($con);
?>
