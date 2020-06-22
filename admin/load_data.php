<?php  
 require('connection.php');
 $output_subject = '';  
 $output_batch = '';  
 
 if(isset($_POST["course_id"])){ 
    $course_id = $_POST["course_id"];
    $sql_course = "SELECT * FROM subject WHERE course_id = '$course_id' AND deleted != '1'";  
    $query_course = mysqli_query($connect, $sql_course); 
    $output_subject .= '<option value="">Select Subject</option>';
    while($row_course = mysqli_fetch_array($query_course)){  
        $output_subject .= '<option value="'.$row_course['id'].'">'.$row_course['name'].'</option>';
    }  
    echo $output_subject;  
} 

 if(isset($_POST["subject_id"])){ 
    $subject_id = $_POST["subject_id"];
    $sql_subject = "SELECT * FROM batch WHERE subject_id = '$subject_id' AND deleted != '1'";  
    $query_subject = mysqli_query($connect, $sql_subject); 
    $output_batch .= '<option value="">Select Batch</option>';
    while($row_subject = mysqli_fetch_array($query_subject)){  
        $output_batch .= '<option value="'.$row_subject['id'].'">'.$row_subject['name'].'</option>';
    }  
    echo $output_batch;  
} 
 ?>  