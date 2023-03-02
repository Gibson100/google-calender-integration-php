<?php
include_once 'dbConfig.php';

if(isset($_POST['updateID'])) {
$id = $_POST['updateID'];

// get the record from DB
$sqlQ = "SELECT * FROM events WHERE id=?"; 
$stmt = $db->prepare($sqlQ);  
$stmt->bind_param('i',$id);
$stmt->execute(); 
$result = $stmt->get_result(); 
if ($result) {
    while($postData = $result->fetch_assoc()) :

?>
<!-- Modal -->
<div id="editmodal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Update Event</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form method="post" action="updateEvent.php" class="form">
                            <div class="form-group">
                                <label>Event Title</label>
                                <input type="text" class="form-control" name="title"
                                    value="<?php echo !empty($postData['title'])?$postData['title']:''; ?>" required="">
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
                                    value="<?php echo !empty($postData['date'])?$postData['date']:''; ?>" required="">
                            </div>
                            <div class="form-group time">
                                <label>Time</label>
                                <input type="time" name="time_from" class="form-control"
                                    value="<?php echo !empty($postData['time_from'])?$postData['time_from']:''; ?>">
                                <span>TO</span>
                                <input type="time" name="time_to" class="form-control"
                                    value="<?php echo !empty($postData['time_to'])?$postData['time_to']:''; ?>">
                            </div>
                            <input type="hidden" name="updateID" value="<?php echo $postData['id'] ?>">
                            <div class="form-group">
                                <input type="submit" class="form-control btn-primary" name="submit" value="Update Event" />
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

<?php endwhile; } } ?>

<!-- <script>
    $('#editmodal').modal('show');
</script> -->