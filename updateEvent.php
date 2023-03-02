<?php     
// Include database configuration file 
require_once 'dbConfig.php'; 

$postData = $statusMsg = $valErr = ''; 
$status = 'danger'; 

// If the form is submitted 
if(isset($_POST['submit'])){ 
     
    // Get event info  
    $title = !empty($_POST['title'])?trim($_POST['title']):''; 
    $description = !empty($_POST['description'])?trim($_POST['description']):''; 
    $location = !empty($_POST['location'])?trim($_POST['location']):''; 
    $date = !empty($_POST['date'])?trim($_POST['date']):''; 
    $time_from = !empty($_POST['time_from'])?trim($_POST['time_from']):''; 
    $time_to = !empty($_POST['time_to'])?trim($_POST['time_to']):''; 
    $updateID = !empty($_POST['updateID'])?trim($_POST['updateID']):''; 

     
    // Validate form input fields 
    if(empty($title)){ 
        $valErr .= 'Please enter event title.<br/>'; 
    } 
    if(empty($date)){ 
        $valErr .= 'Please enter event date.<br/>'; 
    } 
     
    // Check whether user inputs are empty 
    if(empty($valErr)){ 
        //$db->autocommit(false);
        // Insert data into the database 

        $sqlQ = "UPDATE events SET title=?,description=?,location=?,date=?,time_from=?,time_to=?
        WHERE id=?"; 
        $stmt = $db->prepare($sqlQ); 
        $stmt->bind_param("ssssssi", $title, $description, $location, $date, $time_from, $time_to,$updateID); 
        $update = $stmt->execute(); 
         
        if($update){    
            // Store event ID in session 
            $_SESSION['updateID'] = $updateID; 
             
            header("Location: $googleOauthURL"); 
            exit(); 
        }else{ 
            $statusMsg = 'Something went wrong, please try again after some time.'; 
        } 
    }else{ 
        $statusMsg = '<p>Please fill all the mandatory fields:</p>'.trim($valErr, '<br/>'); 
    } 
}else{ 
    $statusMsg = 'Form submission failed!'; 
} 
 
$_SESSION['status_response'] = array('status' => $status, 'status_msg' => $statusMsg); 
 
header("Location: index.php"); 
exit(); 
?>