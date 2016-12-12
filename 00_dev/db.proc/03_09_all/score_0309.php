<?php

/**
 * @Func:       匯入學生成績資料-semester_score資料表
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

    $file_date=`date -d "1 day ago" +%Y%m%d`;
    $file_date=rtrim($file_date);

$path ="/home/ftproot/schoolsoft/SCORE.$file_date.zip";
if(!is_file($path))
exit(0);

    $sql = "CREATE TABLE semester_score_$file_date LIKE semester_score;";
    $sql .= "INSERT semester_score_$file_date SELECT * FROM semester_score;"; 
    $sql .= "truncate table semester_score";

if (mysqli_multi_query($con, $sql)) {
    echo "Update Table Successfully";
} else {
    echo "Error: " . mysqli_error($con);
}

while(mysqli_next_result($con)){;}

    $cmd1="/bin/cp /home/ftproot/schoolsoft/SCORE.$file_date.zip /home/shan/alle/03_09_all/";
    shell_exec($cmd1);
    $cmd2="/usr/bin/unzip /home/shan/alle/03_09_all/SCORE.$file_date.zip -d /home/shan/alle/03_09_all/";
    shell_exec($cmd2);

    // use prepare statement for insert query
    $st = mysqli_prepare($con, 'INSERT INTO semester_score(idno, schno, stdyear, semester, stdlib, score) VALUES (?, ?, ?, ?, ?, ?)');

if($st === FALSE){ die(mysqli_error($con)); }

    // bind variables to insert query params
    mysqli_stmt_bind_param($st, 'ssssss', $idno, $schno, $stdyear, $semester, $stdlib, $score);

    // read json file
    $filename = "/home/shan/alle/03_09_all/SCORE.$file_date.json";
    $json = file_get_contents($filename);

    //convert json object to php associative array
    $data = json_decode($json, true);

    // loop through the array
    foreach ((array)$data as $row) {
        // get the employee details
        $idno = $row['idno'];
        $schno = $row['schno'];
        $stdyear = $row['stdyear'];
        $semester = $row['semester'];
        $stdlib = json_encode ($row['stdlib'], JSON_UNESCAPED_UNICODE);
        $stdlib = preg_replace('/"|\'/', '', $stdlib);
        if(isset($row['score']))
        $score = $row['score'];

        // execute insert query
        mysqli_stmt_execute($st);
    }

    //close connection
    mysqli_close($con);
?>
