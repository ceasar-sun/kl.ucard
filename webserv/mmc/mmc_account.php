<?php
 $host = 'localhost';
 $username = 'mmc';
 $password = 'mmc@mysql@ucard';
 $dbname = 'ucard';
 $con = mysqli_connect($host, $username, $password, $dbname) or die('Error in Connecting: ' . mysqli_error($con));

// $idno = $argv[1];
// $idno = $_GET['idno'];
   $idno = $_POST['idno'];
   $sql = "select schno from semester_student where idno like '$idno'";
   $result = mysqli_query($con,$sql);
 
if(mysqli_num_rows($result) == 0){
  echo "Login Faild";
}
else{
//while($row = mysqli_fetch_array($result))
while($row = mysqli_fetch_assoc($result))
{
   if( strlen(trim($row['schno'])) != 0 )
	echo json_encode($row);
   else
	echo "data fragment";
}
}

//close connection
mysqli_close($con);

?>
