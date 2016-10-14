<?php

/**
 * @Func:       ucard 函式庫 
 * @License:    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @Author:     Thomas Tsai, Ceasar Sun 
 * @Note:              
 *
*/


class UCard {

    public  $lastError;         // Holds the last error
    public  $lastQuery;         // Holds the last query
    public  $result;            // Holds the MySQL query result
    public  $records;           // Holds the total number of records returned
    public  $affected;          // Holds the total number of records affected
    public  $rawResults;        // Holds raw 'arrayed' results
    public  $arrayedResult;     // Holds an array of the result

    private $hostname;          // MySQL Hostname
    private $username;          // MySQL Username
    private $password;          // MySQL Password
    private $database;          // MySQL Database
    private $databaseLink;      // Database Connection Link
    private $moodle_token = null;
    private $moodle_server = null;
    private $moodle_path = null;


    /* *******************
     * Class Constructor *
     * *******************/

    function __construct($database, $username, $password, $hostname='localhost', $port=3306, $persistant = false){
	$this->database = $database;
	$this->username = $username;
	$this->password = $password;
	//	$this->hostname = $hostname.':'.$port;
	$this->hostname = $hostname;

	$this->Connect($persistant);
    }

    /* *******************
     * Class Destructor  *
     * *******************/

    function __destruct(){
	$this->closeConnection();
    }

    /* *******************
     * Private Functions *
     * *******************/

    // Connects class to database
    // $persistant (boolean) - Use persistant connection?
    private function Connect($persistant = false){
	$this->CloseConnection();

	$this->databaseLink = mysqli_connect($this->hostname, $this->username, $this->password);

	if(!$this->databaseLink){
	    $this->lastError = 'Could not connect to server: ' . mysqli_error($this->databaseLink);
	    return false;
	}

	if(!$this->UseDB()){
	    $this->lastError = 'Could not connect to database: ' . mysqli_error($this->databaseLink);
	    return false;
	}

	$this->setCharset(); // TODO: remove forced charset find out a specific management
	return true;
    }

    // Select database to use
    private function UseDB(){
	if(!mysqli_select_db($this->databaseLink, $this->database)){
	    $this->lastError = 'Cannot select database: ' . mysqli_error($this->databaseLink);
	    return false;
	}else{
	    return true;
	}
    }

    private function SecureData($data, $types=array()){
	if(is_array($data)){
	    $i = 0;
	    foreach($data as $key=>$val){
		if(!is_array($data[$key])){
		    $data[$key] = mysqli_real_escape_string($data[$key], $this->databaseLink);
		    $i++;
		}
	    }
	}else{
	    $data = mysqli_real_escape_string($this->databaseLink, $data);
	}
	return $data;
    }

    private function setCharset( $charset = 'UTF8' ) {
	return mysqli_set_charset ($this->databaseLink, $this->SecureData($charset,'string'));
    }

    private function commit(){
	return mysqli_query($this->databaseLink, "COMMIT");
    }

    // Closes the connections
    private function closeConnection(){
	if($this->databaseLink){
	    // Commit before closing just in case :)
	    $this->commit();
	    mysqli_close($this->databaseLink);
	}
    }

    // Executes MySQL query
    private function executeSQL($query){
	$this->lastQuery = $query;
	if($this->result = mysqli_query($this->databaseLink, $query)){
	    #var_dump($query);
	    #var_dump($this->result);
	    if ($this->result == TRUE) {
		$this->records  = @mysqli_num_rows($this->result);
	    } else {
		$this->records  = 0;
	    }
	    $this->affected = @mysqli_affected_rows($this->databaseLink);
	    if($this->records > 0){
		$this->arrayResults();
		return $this->arrayedResult;
	    }else{
		return NULL; // Fix by Ceasar
		#return true;
	    }
	}else{
	    $this->lastError = mysqli_error($this->databaseLink);
	    return false;
	}
    }

    // execute Moodle API
    private function executeMoodleAPI($api, $params){
	//var_dump($params);
	$request = xmlrpc_encode_request($api, $params, array('encoding'=>'UTF-8'));

	//var_dump($request);  // In case you want to see XML.
// ssl issue, this not work
//
//	$context = stream_context_create(array(
//			'http' => array(
//				'method' => "POST",
//				'header' => "Content-Type: text/xml",
//				'content' => $request
//				),
//			'ssl' => array(
//				"verify_peer"=>false,
//				"verify_peer_name"=>false
//				)
//			));
//
//	$path = $this->moodle_server.$this->moodle_path."/webservice/xmlrpc/server.php?wstoken=".$this->moodle_token;
//	// Send XML to server and get a reply from it.
//	$file = file_get_contents($path, false, $context); // $file is the reply from server.


// replace with curl
	$server_url = $this->moodle_server.$this->moodle_path."/webservice/xmlrpc/server.php?wstoken=".$this->moodle_token;
	$req = curl_init($server_url);
	// Using the cURL extension to send it off,  first creating a custom header block
	$headers = array();
	array_push($headers,"Content-Type: text/xml");
	array_push($headers,"Content-Length: ".strlen($request));
	array_push($headers,"\r\n");

	//URL to post to
	curl_setopt($req, CURLOPT_URL, $server_url);

	//Setting options for a secure SSL based xmlrpc server
	curl_setopt( $req, CURLOPT_CUSTOMREQUEST, 'POST' );
	curl_setopt($req, CURLOPT_HTTPHEADER, $headers );
	curl_setopt( $req, CURLOPT_POSTFIELDS, $request );
	curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($req, CURLOPT_SSL_VERIFYPEER, true);
	curl_setopt($req, CURLOPT_SSL_VERIFYHOST, 2);

	//Finally run
	$file = curl_exec($req);

	// Decode the reply.
	$response = xmlrpc_decode($file);

	// This is our normal exit (returning an array of user properties).
	//var_dump($response);
	return $response;

    }

    // 'Arrays' a single result
    private function arrayResult(){
	$this->arrayedResult = mysqli_fetch_assoc($this->result) or die (mysqli_error($this->databaseLink));
	return $this->arrayedResult;
    }
    // 'Arrays' multiple result
    private function arrayResults(){
	/*if($this->records == 1){
	    return $this->arrayResult();
	}
	*/
	$this->arrayedResult = array();
	while ($data = mysqli_fetch_assoc($this->result)){
	    $this->arrayedResult[] = $data;
	}
	return $this->arrayedResult;
    }
    // 'Arrays' multiple results with a key
    private function arrayResultsWithKey($key='id'){
	if(isset($this->arrayedResult)){
	    unset($this->arrayedResult);
	}
	$this->arrayedResult = array();
	while($row = mysqli_fetch_assoc($this->result)){
	    foreach($row as $theKey => $theValue){
		$this->arrayedResult[$row[$key]][$theKey] = $theValue;
	    }
	}
	return $this->arrayedResult;
    }

    /* *******************
     * Public Functions  *
     * *******************/

    public function init_moodle($token, $server, $path){
	$this->moodle_token  = $token;
	$this->moodle_server = $server;
	$this->moodle_path   = $path;
    }

    public function runSQL($query){
	$data = $this->executeSQL($query);
	return $data;
    }

    public function getID($rfid_key16){
	$query = "select idno from semester_student where rfid_key16=\"$rfid_key16\"";
	$data = $this->executeSQL($query);
	if ($data != false){
	    return $data[0]['idno'];
	}else{
	    return null;
	}
    }

    public function getStudentID($rfid_key16){ // for moodle idnumber
	$idno = $this->getID($rfid_key16);
	if ($idno != null){
	    return hash('sha256', $idno);
	}else{
	    return null;
	}
    }

    public function logCardID($moodleid, $rfid_key16, $location){

	$query = "INSERT INTO cardlog (`id`, `moodleid`, `rfid_key16`, `location`, `dtime`) VALUES (NULL, $moodleid, \"$rfid_key16\", $location, CURRENT_TIMESTAMP)";
	$data = $this->executeSQL($query);
    }

    public function listUserCardLogs($moodleid, $limit = 10){
	$query = "select * from cardlog where `moodleid`=$moodleid ORDER BY dtime DESC limit $limit";
	$data = $this->executeSQL($query);
	return $data;
    }

    public function listCardLogs($limit = 10){
	$query = "select * from cardlog ORDER BY dtime DESC limit $limit";
	$data = $this->executeSQL($query);
	return $data;
    }

    public function getStudentLevel($idno){
	$query = "SELECT max(stdyear) as stdyer FROM semester_score WHERE idno=\"$idno\"";
	$data = $this->executeSQL($query);
	if ($data != false){
	    return $data[0]['stdyer'];
	}else{
	    return null;
	}

    }

    public function getRFIDKeyOut($rfid_key16){
	$query = "select * from semester_student where rfid_key16=\"$rfid_key16\"";
	$data = $this->executeSQL($query);
	if ($data != false){
	    return $data[0]['rfid_keyout'];
	}else{
	    return null;
	}

    }
    public function logRunningCourse($moodleid, $rfid_keyout, $location, $courseid, $level){

	$testquery = "SELECT * FROM `running_course` WHERE `moodleid`=$moodleid and `courseid`=$courseid";
	$testdata = $this->executeSQL($testquery);
	if ($testdata == false){
	    $query = "INSERT INTO running_course (`id`, `moodleid`, `rfid_keyout`, `location`, `courseid`, `level`, `dtime`, `status`) VALUES (NULL, $moodleid, \"$rfid_keyout\", $location, $courseid, $level, CURRENT_TIMESTAMP, 0)";
	    $data = $this->executeSQL($query);
	}
    }

    public function getRunningCourse($moodleid, $location = 0, $status = true){
	if ($location != 0){
	    $query = "SELECT * FROM `running_course` WHERE `moodleid`=$moodleid and `location`=$location";
	}else{
	    $query = "SELECT * FROM `running_course` WHERE `moodleid`=$moodleid";
	}
	if ($status == true){
	     $query .= " and `status`=0";
	}
	$data = $this->executeSQL($query);
	$courses=array();
	for ($i = 0; $i < count($data); $i++){
	    array_push($courses, $data[$i]['courseid']);
	}
	return $courses;
    }
    public function upgradeRunningCourse($moodleid, $courseid){

	$query = "UPDATE `running_course` SET `status` = '1' WHERE `moodleid`=$moodleid and `courseid`=$courseid and status=0";
	$data = $this->executeSQL($query);
    }

    public function getMoodleIDbyOID($identity){
	$identity = rtrim(preg_replace("(^https?://)", "", $identity), "/");
        $search['key']="username";
        $search['value']=$identity;
        $api='core_user_get_users';
        $params = array(array($search));
        $response = $this->executeMoodleAPI($api, $params);
        //var_dump($response);
        $data = $response['users'][0];
        $moodleid = $data['id'];
        return $moodleid;
    }

    public function getMoodleIDbyEmail($email){
	$search['key']="email";
	$search['value']=$email;
	$api='core_user_get_users';
	$params = array(array($search));
	$response = $this->executeMoodleAPI($api, $params);
        //var_dump($response);
	$data = $response['users'][0];
	$moodleid = $data['id'];
	return $moodleid;
    }

    public function getMoodleUserbyStudentID($sid){
	$search['key']="idnumber";
	$search['value']=$sid;
	$api='core_user_get_users';
	$params = array(array($search));
	$response = $this->executeMoodleAPI($api, $params);
        //var_dump($response);
	$data = $response['users'][0];
	return $data;
    }

    public function getMoodleIDbyStudentID($sid){
	$response = $this->getMoodleUserbyStudentID($sid);
	$moodleid = $response['id'];
	return $moodleid;
    }

    public function registCourse($moodleid, $courseid){
	$params = array(array(array('roleid'=>'5', 'userid'=>$moodleid, 'courseid'=>$courseid))); // roleid 5 is "student".
	$api='enrol_manual_enrol_users';
	$response = $this->executeMoodleAPI($api, $params);
    }

    public function getPassGrades($courseid){
	$params = (int)$courseid;
	$api='core_grades_get_grades';
	$response = $this->executeMoodleAPI($api, $params);
	$items = $response['items'];
	$grade=array();
	for ($i = 0; $i < count($items); $i++){
	    array_push($grade, $items[$i]['gradepass']);
	}
	return max($grade);

    }

    public function getUserGrades($moodleid, $courseid){
	$params = array((int)$courseid, (int)$moodleid);
	$api = "gradereport_user_get_grades_table";
	$response = $this->executeMoodleAPI($api, $params);

	if (count($response)>0){
	    $tables = $response['tables'];
	    $data = $tables[0]['tabledata'];
	    $total = array_pop($data);
	    if ($total['grade']['content'] == "-"){
		return -1;
	    }
	    return $total['grade']['content'];
	}else{
	    return -1;
	}

    }

    public function getUserCourses($moodleid){
	$params = $moodleid;
	$api='core_enrol_get_users_courses';
	$response = $this->executeMoodleAPI($api, $params);
	$all_course = $response;
	$courses=array();
	for ($i = 0; $i < count($all_course); $i++){
	    array_push($courses, $all_course[$i]['id']);
	}
	return $courses;
    }

    public function getNextLevelbyCourse($courseid){
	$params = array($courseid);
	$api='course_level_get_next_level';
	$response = $this->executeMoodleAPI($api, $params);
	return $response;
    }

    public function getLevelbyCourse($courseid){
	$params = array($courseid);
	$api='course_level_get_level';
	$response = $this->executeMoodleAPI($api, $params);
	return $response;
    }

    public function getCoursesbyLevelLocation($level, $location){
	$params = array($location, $level);
	$api='course_level_id_by_level_location';
	$response = $this->executeMoodleAPI($api, $params);
	$all_course = $response;
	//var_dump($response);
	$courses=array();
	for ($i = 0; $i < count($all_course); $i++){
	    array_push($courses, $all_course[$i]['id']);
	}
	return $courses;
    }

    public function getCompletionStatus($course, $user){

	$params = array($course, $user);
	$api='core_completion_get_course_completion_status';
	$response = $this->executeMoodleAPI($api, $params);
	if (array_key_exists('faultCode', $response)){
	    #echo "課程 $course 異常: $response[faultString]($response[faultCode])<br>\n";
	    return "ERROR";
	}
	return $response['completionstatus']['completed'];

    }

    public function getNameofCourse($courseid){

	$params = array(array('ids'=>array($courseid)));
	$api='core_course_get_courses';
	$response = $this->executeMoodleAPI($api, $params);
	$name = $response[0]['fullname']."-".$response[0]['shortname'];

	return $name;
    }
    public function getTrackbyCourse($courseid){

	$params = array(array('ids'=>array($courseid)));
	$api='core_course_get_courses';
	$response = $this->executeMoodleAPI($api, $params);
	$name = $response[0]['categoryid'];

	return $name;
    }

    public function getNextCourse($courseid){
	$params = array($courseid);
	$api='course_level_get_next_course';
	$response = $this->executeMoodleAPI($api, $params);
	return $response;
    }

    public function getLastCourse($level, $track){
	$params = array($level, $track);
	$api='course_level_get_last_course';
	$response = $this->executeMoodleAPI($api, $params);
	return $response;
    }


    public function upgradeCourse($moodleid, $courseid, $location){

	$newcourseid = $this->getNextCourse($courseid);
	$this->registCourse($moodleid, $newcourseid);
	return $newcourseid;

    }

    public function getLocationID($location){
	$search_name["key"]="name";
	$search_name["value"]=$location;
	$search_parent["key"]="parent";
	$search_parent["value"]="0";
	$params = array(array($search_name, $search_parent),'0'); 

	$api='core_course_get_categories';
	$response = $this->executeMoodleAPI($api, $params);

	return $response[0]['id'];
    }

}
?>
