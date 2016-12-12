<?php

/**
 * @Func:       匯入學生基本資料-semester_student資料表
 * @License:    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @Author:     Hui-Shan Chen 
 * @Note:
 *
*/

    // open mysql connection
    $host = "localhost";
    $username = "shan";
    $password = "shan@mysql@ucard";
    $dbname = "ucard";
    $con = mysqli_connect($host, $username, $password, $dbname) or die('Error in Connecting: ' . mysqli_error($con));

    $file_all=`date -d "1 day ago" +%m%d`;
    $file_all=rtrim($file_all);
    
    if($file_all=="0306" || $file_all=="0906")
    exit(0);

    $file_date=`date -d "1 day ago" +%Y%m%d`;
    $file_date=rtrim($file_date);

$path ="/home/ftproot/schoolsoft/BASIC.$file_date.zip";
if(!is_file($path))
exit(0);

    //echo $file_date;
    $cmd1="/bin/cp /home/ftproot/schoolsoft/BASIC.$file_date.zip /home/shan/alle/BASIC/";
    shell_exec($cmd1);
    $cmd2="/usr/bin/unzip /home/shan/alle/BASIC/BASIC.$file_date.zip -d /home/shan/alle/BASIC/";
    shell_exec($cmd2);
    // use prepare statement for insert query
    $st1 = mysqli_prepare($con, 'INSERT INTO semester_student(idno, schno, stdno, name, rfid_keyout, rfid_key16) VALUES (?, ?, ?, ?, ?, ?)');
    // bind variables to insert query params
    mysqli_stmt_bind_param($st1, 'ssssss', $idno, $schno, $stdno, $name, $rfid_keyout, $rfid_key16);

    // use prepare statement for update
    $st2 = mysqli_prepare($con, 'UPDATE semester_student SET idno = ?, schno = ?, stdno = ?, name = ?, rfid_keyout = ?, rfid_key16 = ?');
    // bind variables to insert query params
    mysqli_stmt_bind_param($st2, 'ssssss', $idno, $schno, $stdno, $name, $rfid_keyout, $rfid_key16);

    // read json file
    $filename = "/home/shan/alle/BASIC/BASIC.$file_date.json";
    $json = file_get_contents($filename);

    //convert json object to php associative array
    $data = json_decode($json, true);

    // loop through the array
    foreach ((array)$data as $row) {
        // get the employee details
        $idno = $row['idno'];
        $schno = $row['schno'];
        $stdno = $row['stdno'];
        $name = json_encode ($row['name'], JSON_UNESCAPED_UNICODE);
        $name = preg_replace('/"|\'/', '', $name);
        $rfid_keyout = $row['rfid_keyout'];
        $rfid_key16 = $row['rfid_key16'];

   $sql = "SELECT idno FROM `semester_student` WHERE `idno` LIKE '$idno'";
   $result = mysqli_query($con,$sql);

if(mysqli_num_rows($result) == 0){
	 // execute insert query
        mysqli_stmt_execute($st1);
	//echo "insert";
}
else{
	// execute update
	mysqli_stmt_execute($st2);
	//echo "update";
}

    } //foreach

    //close connection
    mysqli_close($con);
?>
