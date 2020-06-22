</div>
</body>
</html>


<script>
$(document).ready(function(){
    $('table').DataTable({
        stateSave: true
    });


    $('[name="course_id"]').click(function() {
        var course_id = $('[name="course_id"]').val();   
        $.ajax({  
            url:"load_data.php",  
            method:"POST",  
            data:{course_id: course_id},  
            success:function(data){  
            $('[name="subject_id"]').find('option').remove();  
            $('[name="subject_id"]').html(data);  
            }  
        }); 
    }); 

    $('[name="subject_id"]').click(function() {
        var subject_id = $('[name="subject_id"]').val();  
        $.ajax({  
            url:"load_data.php",  
            method:"POST",  
            data:{subject_id: subject_id},  
            success:function(data){  
                $('[name="batch_id"]').find('option').remove();  
                $('[name="batch_id"]').html(data);  
            }  
        }); 

    }); 


});



</script>
