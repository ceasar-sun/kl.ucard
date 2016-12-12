<?php

/**
 * @Func:       匯入學生借閱資料-semester_bookrec資料表
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

$path ="/home/ftproot/schoolsoft/BOOKREC.$file_date.zip";
if(!is_file($path))
exit(0);

    $sql = "CREATE TABLE semester_bookrec_$file_date LIKE semester_bookrec;";
    $sql .= "INSERT semester_bookrec_$file_date SELECT * FROM semester_bookrec;";
    $sql .= "truncate table semester_bookrec";

if (mysqli_multi_query($con, $sql)) {
    echo "Update Table Successfully";
} else {
    echo "Error: " . mysqli_error($con);
}

while(mysqli_next_result($con)){;}

    $cmd1="/bin/cp /home/ftproot/schoolsoft/BOOKREC.$file_date.zip /home/shan/alle/03_09_all/";
    shell_exec($cmd1);
    $cmd2="/usr/bin/unzip /home/shan/alle/03_09_all/BOOKREC.$file_date.zip -d /home/shan/alle/03_09_all/";
    shell_exec($cmd2);

    // use prepare statement for insert query
    $st = mysqli_prepare($con, 'INSERT INTO semester_bookrec(idno, schno, date_out, book, bk_grp) VALUES (?, ?, ?, ?, ?)');

if($st === FALSE){ die(mysqli_error($con)); }

    // bind variables to insert query params
    mysqli_stmt_bind_param($st, 'sssss', $idno, $schno, $date_out, $book, $bk_grp);

    // read json file
    $filename = "/home/shan/alle/03_09_all/BOOKREC.$file_date.json";
    $json = file_get_contents($filename);

    //convert json object to php associative array
    $data = json_decode($json, true);

    // loop through the array
    foreach ((array)$data as $row) {
        // get the employee details
        $idno = $row['idno'];
        $schno = $row['schno'];
        $date_out = $row['date_out'];
        $book = json_encode ($row['book'], JSON_UNESCAPED_UNICODE);
        $book = preg_replace('/"|\'/', '', $book);
        $bk_grp = $row['bk_grp'];

        // execute insert query
        mysqli_stmt_execute($st);

//        if(mysqli_stmt_affected_rows($st)==1){
//        echo "Insert Success";
//        }else{
//        echo "Insert Failed";
//        }

//        mysqli_stmt_close($st);

    }
    
    //close connection
    mysqli_close($con);
?>
