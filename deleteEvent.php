<?php 
session_start();
// get the id of the record
$_SESSION['deleteID'] = $_GET['deleteID'];
     
// Include database configuration file 
require_once 'dbConfig.php'; 

// google configurations
require_once 'config.php';

// now get the event id from db
$id = $_SESSION['deleteID'];
$sqlQ = "SELECT `google_calendar_event_id` FROM events WHERE id = ?"; 
$stmt = $db->prepare($sqlQ);  
$stmt->bind_param("i", $id);
$stmt->execute(); 
$result = $stmt->get_result();
$event_id = 0;
while($data = $result->fetch_assoc()) {
    $event_id = $data['google_calendar_event_id'];
}


$_SESSION['event_id'] = $event_id;

header("Location: $googleOauthURL");

