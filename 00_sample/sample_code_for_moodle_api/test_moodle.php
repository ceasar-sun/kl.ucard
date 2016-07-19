<?php
include "moodle.php";
$token = '851fc9fb3410e174ff156b65689f6922';
$server = 'http://moodle.nchc.org.tw';
$dir = '/moodle'; // May be null if moodle runs in the root directory in the server.

// To do things with Moodle, we create a new Moodle class, initialize it, and then call its functions.
$moodle = new Moodle();

// Initialize the class.
$fields = array('token'=>$token, 'server'=>$server, 'dir'=>$dir);
$moodle->init($fields);


// Enroll a user in a course.
$user_id = 5;
$course_id = 2;
$enrolled = $moodle->enrollUser($user_id, $course_id);
if ($enrolled)
  var_dump($enrolled);      // Success, normal result.
else
  var_dump($moodle->error); // Error.
?>
