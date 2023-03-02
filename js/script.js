$(document).ready(function() {
    $('.edit').click(function(){
        console.log('clicked')
        $.ajax({
            type: 'POST',
            url: 'updateModal.php',
            data: {'updateID':$(this).attr('id')},
            success:function(response) {
                console.log(response);
                $('#updateModalSpace').append(response)
                $('#editmodal').modal('show');
            }
        });
    })
    $('.delete').click(function(){
        return confirm('do you really want to delete this event?');
    })
});