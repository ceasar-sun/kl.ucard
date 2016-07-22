<?php
 $host = 'localhost';
 $username = '';
 $password = '';
 $dbname = '';
 $con = mysqli_connect($host, $username, $password, $dbname) or die('Error in Connecting: ' . mysqli_error($con));

// $idno = $argv[1];
// $idno = $_GET['idno'];
   $idno = $_POST['idno'];
   $sql = "select * from game_src where idno like '$idno'";
   $result = mysqli_query($con,$sql);

if(mysqli_num_rows($result) == 0){
  echo "No this idno";
}
else{
//while($row = mysqli_fetch_array($result))
while($row = mysqli_fetch_assoc($result))
{
   if( strlen(trim($row['idno'])) != 0 )
        echo json_encode($row);
   else
        echo "data fragment";
}
}

//   echo "idno = ".$row['idno']."\n";
//   echo "schno = ".$row['schno']."\n";
//   echo "level = ".$row['level']."\n";
//   echo "dtime = ".$row['dtime']."\n";

    //close connection
    mysqli_close($con);

?>
