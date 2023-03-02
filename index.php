<?php 
// Include configuration file 
include_once 'config.php'; 
include_once 'dbConfig.php';

$sqlQ = "SELECT * FROM events"; 
$stmt = $db->prepare($sqlQ);  
$stmt->execute(); 
$result = $stmt->get_result(); 
// print_r($eventData);
 
$postData = ''; 
if(!empty($_SESSION['postData'])){ 
    $postData = $_SESSION['postData']; 
    unset($_SESSION['postData']); 
} 
 
$status = $statusMsg = ''; 
if(!empty($_SESSION['status_response'])){ 
    $status_response = $_SESSION['status_response']; 
    $status = $status_response['status']; 
    $statusMsg = $status_response['status_msg']; 
} 
?>
<!-- Status message -->
<?php if(!empty($statusMsg)){ ?>
<div class="alert alert-<?php echo $status; ?>"><?php echo $statusMsg; ?></div>
<?php } ?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <title>Add Event to Google Calendar using PHP</title>
    <meta charset="utf-8">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
    .center_div {
        margin: 0 auto;
        width: 60%
            /* value of your choice which suits your alignment */
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Add
                Event</button>
        </div>
        <div class="row">
            <table class="table table-responsive">
                <tr>
                    <td>Event Title</td>
                    <td>Event Description</td>
                    <td>Location</td>
                    <td>Date</td>
                    <td>From</td>
                    <td>To</td>
                    <td>Action</td>
                </tr>
                <?php while($eventData = $result->fetch_assoc()) : ?>
                <tr>

                    <td><?php echo $eventData['title'] ?></td>
                    <td><?php echo $eventData['description'] ?></td>
                    <td><?php echo $eventData['location'] ?></td>
                    <td><?php echo $eventData['date'] ?></td>
                    <td><?php echo $eventData['time_from'] ?></td>
                    <td><?php echo $eventData['time_to'] ?></td>
                    <td>
                        <div class="d-flex"><button class="btn btn-primary btn-sm edit"
                                id='<?php echo $eventData['id'] ?>'>Edit</button>
                            <a class="btn btn-danger btn-sm delete" href="deleteEvent.php?deleteID=<?php echo $eventData['id'] ?>">Delete</a>
                        </div>
                    </td>

                </tr>

                <?php endwhile;
                ?>
            </table>
        </div>
    </div>
    <!-- Modal -->
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add Event</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form method="post" action="addEvent.php" class="form">
                                <div class="form-group">
                                    <label>Event Title</label>
                                    <input type="text" class="form-control" name="title"
                                        value="<?php echo !empty($postData['title'])?$postData['title']:''; ?>"
                                        required="">
                                </div>
                                <div class="form-group">
                                    <label>Event Description</label>
                                    <textarea name="description"
                                        class="form-control"><?php echo !empty($postData['description'])?$postData['description']:''; ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Location</label>
                                    <input type="text" name="location" class="form-control"
                                        value="<?php echo !empty($postData['location'])?$postData['location']:''; ?>">
                                </div>
                                <div class="form-group">
                                    <label>Date</label>
                                    <input type="date" name="date" class="form-control"
                                        value="<?php echo !empty($postData['date'])?$postData['date']:''; ?>"
                                        required="">
                                </div>
                                <div class="form-group time">
                                    <label>Time</label>
                                    <input type="time" name="time_from" class="form-control"
                                        value="<?php echo !empty($postData['time_from'])?$postData['time_from']:''; ?>">
                                    <span>TO</span>
                                    <input type="time" name="time_to" class="form-control"
                                        value="<?php echo !empty($postData['time_to'])?$postData['time_to']:''; ?>">
                                </div>
                                <div class="form-group">
                                    <input type="submit" class="form-control btn-primary" name="submit"
                                        value="Add Event" />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>

        </div>
    </div>

    <!-- update modal -->
    <div id="updateModalSpace">

    </div>
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <script src="./js/script.js"></script>
</body>

</html>