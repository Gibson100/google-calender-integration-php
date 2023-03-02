<?php 
// Include Google calendar api handler class 
include_once 'GoogleCalendarApi.class.php'; 
     
// Include database configuration file 
require_once 'dbConfig.php'; 
 
$statusMsg = ''; 
$status = 'danger'; 
if(isset($_GET['code'])){ 
    // Initialize Google Calendar API class 
    $GoogleCalendarApi = new GoogleCalendarApi(); 

    // check if delete request and get access token
    // delete the event from google calendar at last

    if (isset($_SESSION['deleteID'])) {

        $data = $GoogleCalendarApi->GetAccessToken(GOOGLE_CLIENT_ID, REDIRECT_URI, GOOGLE_CLIENT_SECRET, $_GET['code']); 
        $access_token = $data['access_token']; 

        $event_id = $_SESSION['event_id'];

        $res = $GoogleCalendarApi->DeleteCalendarEvent($event_id, 'primary', $access_token);
    
        if ($res['type'] == 'success') {
            // deleted data from db if successfull
            $id = $_SESSION['deleteID'];
            $sqlQ = "DELETE FROM events WHERE id = $id"; 
            $stmt = $db->prepare($sqlQ);  
            $stmt->execute(); 
            $result = $stmt->get_result();
        }
         
        $_SESSION['status_response'] = array('status' => $res['type'], 'status_msg' => $res['message']); 
        // uset the session
        unset($_SESSION['deleteID']);
        unset($_SESSION['event_id']);

        header("Location: index.php"); 
        exit(); 
    }
    
    // if user is updating event
    $updateID = $_SESSION['updateID'];

    if ($updateID) {
        // echo $updateID;
        // exit;
        // Fetch event details from database 
        $sqlQ = "SELECT * FROM events WHERE id = ?"; 
        $stmt = $db->prepare($sqlQ);  
        $stmt->bind_param("i", $_SESSION['updateID']);
        $stmt->execute(); 
        $result = $stmt->get_result(); 
        //$eventData = $result->fetch_assoc(); 
        $event_id;
        $calendar_event = $event_datetime = array();

            while($data = $result->fetch_assoc()) {
                $event_id = $data['google_calendar_event_id'];
        
                $calendar_event = array( 
                    'summary' => $data['title'], 
                    'location' => $data['location'], 
                    'description' => $data['description'] 
                ); 
                
                $event_datetime = array( 
                    'event_date' => $data['date'], 
                    'start_time' => $data['time_from'], 
                    'end_time' => $data['time_to'] 
                ); 
            }

            $data = $GoogleCalendarApi->GetAccessToken(GOOGLE_CLIENT_ID, REDIRECT_URI, GOOGLE_CLIENT_SECRET, $_GET['code']);
            $access_token = $data['access_token'];
            $_SESSION['google_access_token'] = $access_token;


            // updating event on the primary calendar
            $calendar_id = 'primary';
             
            if(!empty($access_token)){
                try { 
                    //get timezone of user 
                    $user_timezone = $GoogleCalendarApi->GetUserCalendarTimezone($access_token);

                    $results = $GoogleCalendarApi->UpdateCalendarEvent($event_id, $calendar_id, $calendar_event, 0, $event_datetime, $user_timezone, $access_token);

                    if($results['type'] == 'success'){ 
                        $db->commit();
                        // Update google event reference in the database 
                         
                        unset($_SESSION['updateID']); 
                        unset($_SESSION['google_access_token']); 
                         
                        $_SESSION['status_response'] = array('status' => $results['type'], 'status_msg' => $results['message']); 
                        
                        header("Location: index.php"); 
                        exit(); 
                    } 
                    else {
                        $db->rollback();
                        
                        $_SESSION['status_response'] = array('status' => 'error', 'status_msg' => 'Failed to save the changes in google calender.'); 
                        
                        header("Location: index.php"); 
                        exit(); 
                    }
                } catch(Exception $e) { 
                    //header('Bad Request', true, 400); 
                    //echo json_encode(array( 'error' => 1, 'message' => $e->getMessage() )); 
                    $statusMsg = $e->getMessage(); 
                } 
            }
            else{ 
                $statusMsg = 'Failed to fetch access token!'; 
            } 
        }
        else{ 
            $statusMsg = 'Event data not found!'; 
        } 

     

    // Get event ID from session 
    $event_id = $_SESSION['last_event_id']; 
 
    if(!empty($event_id)){ 
         
        // Fetch event details from database 
        $sqlQ = "SELECT * FROM events WHERE id = ?"; 
        $stmt = $db->prepare($sqlQ);  
        $stmt->bind_param("i", $db_event_id); 
        $db_event_id = $event_id; 
        $stmt->execute(); 
        $result = $stmt->get_result(); 
        $eventData = $result->fetch_assoc(); 
         
        if(!empty($eventData)){ 
            $calendar_event = array( 
                'summary' => $eventData['title'], 
                'location' => $eventData['location'], 
                'description' => $eventData['description'] 
            ); 
             
            $event_datetime = array( 
                'event_date' => $eventData['date'], 
                'start_time' => $eventData['time_from'], 
                'end_time' => $eventData['time_to'] 
            ); 
             
            // Get the access token 
            $access_token_sess = $_SESSION['google_access_token']; 
            if(!empty($access_token_sess)){ 
                $access_token = $access_token_sess; 
            }else{ 
                $data = $GoogleCalendarApi->GetAccessToken(GOOGLE_CLIENT_ID, REDIRECT_URI, GOOGLE_CLIENT_SECRET, $_GET['code']); 
                $access_token = $data['access_token']; 
                $_SESSION['google_access_token'] = $access_token; 
            } 
             
            if(!empty($access_token)){ 
                try { 
                    // Get the user's calendar timezone 
                    $user_timezone = $GoogleCalendarApi->GetUserCalendarTimezone($access_token); 
                 
                    // Create an event on the primary calendar 
                    $google_event_id = $GoogleCalendarApi->CreateCalendarEvent($access_token, 'primary', $calendar_event, 0, $event_datetime, $user_timezone); 
                     
                    //echo json_encode([ 'event_id' => $event_id ]); 
                     
                    if($google_event_id){ 
                        // Update google event reference in the database 
                        $sqlQ = "UPDATE events SET google_calendar_event_id=? WHERE id=?"; 
                        $stmt = $db->prepare($sqlQ); 
                        $stmt->bind_param("si", $db_google_event_id, $db_event_id); 
                        $db_google_event_id = $google_event_id; 
                        $db_event_id = $event_id; 
                        $update = $stmt->execute(); 
                         
                        unset($_SESSION['last_event_id']); 
                        unset($_SESSION['google_access_token']); 
                         
                        $status = 'success'; 
                        $statusMsg = '<p>Event #'.$event_id.' has been added to Google Calendar successfully!</p>'; 
                        $statusMsg .= '<p><a href="https://calendar.google.com/calendar/" target="_blank">Open Calendar</a>'; 
                    } 
                } catch(Exception $e) { 
                    //header('Bad Request', true, 400); 
                    //echo json_encode(array( 'error' => 1, 'message' => $e->getMessage() )); 
                    $statusMsg = $e->getMessage(); 
                } 
            }else{ 
                $statusMsg = 'Failed to fetch access token!'; 
            } 
        }else{ 
            $statusMsg = 'Event data not found!'; 
        } 
    }else{ 
        $statusMsg = 'Event reference not found!'; 
    } 
     
    $_SESSION['status_response'] = array('status' => $status, 'status_msg' => $statusMsg); 
     
    header("Location: index.php"); 
    exit(); 

}
?>