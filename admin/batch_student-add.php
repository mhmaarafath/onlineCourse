<?php
require('connection.php');

if(isset($_GET['id'])){
    $id = $_GET['id'];
    
}

if(isset($_GET['batch_id'])){
    $batch_id = $_GET['batch_id'];
}


if(isset($_GET['id'])){
    
    $sql="SELECT 
    batch_student.*,
    student.email AS student_email,
    course.id AS course_id
    FROM batch_student 
    INNER JOIN batch ON batch_student.batch_id = batch.id
    INNER JOIN subject ON batch_student.subject_id = subject.id
    INNER JOIN course ON subject.course_id = course.id
    INNER JOIN student ON batch_student.student_id = student.id
    WHERE batch_student.id = '$id'";
    
} else if(isset($_GET['batch_id'])){

    $sql="SELECT 
    batch.*,
    course.id AS course_id
    FROM batch
    INNER JOIN subject ON batch.subject_id = subject.id
    INNER JOIN course ON subject.course_id = course.id
    WHERE batch.id = '$batch_id'";
}

$query = mysqli_query($connect,$sql); 
$row = mysqli_fetch_array($query);

$fees_amount = ($row['fees_amount'])? $row['fees_amount'] : 0 ;
$paid_amount = ($row['paid_amount'])? $row['paid_amount'] : 0 ;
$student_id = ($row['student_id'])? $row['student_id'] : "";
$email = ($row['student_email'])? $row['student_email'] : "";

$course_id = $row['course_id'];
$subject_id = $row['subject_id'];
$batch_id = ($_GET['batch_id']) ? $_GET['batch_id'] : $row['batch_id'];


if(isset($_POST["submit"])){

    $location = ($_GET['batch_id']) ? "batch_student-add.php?batch_id=$batch_id" : "batch_student.php";
    
    $batch_id = $_POST["batch_id"];
    $course_id = $_POST["course_id"];
    $subject_id = $_POST["subject_id"];
    $email = $_POST["email"];
    $remark = $_POST["remark"];
    $fees_amount = $_POST["fees_amount"];
    $paid_amount = $_POST["paid_amount"];


    $student_id = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM student WHERE email = '$email' LIMIT 1"))['id'];

    if($student_id !=""){
        if(isset($_GET['id'])){
            $sql = "UPDATE batch_student SET 
            batch_id = '$batch_id',
            subject_id = '$subject_id', 
            remark = '$remark',
            fees_amount = '$fees_amount'
            WHERE id='$id'";
            if(($_POST['batch_id'] == $row['batch_id']) || (!mysqli_num_rows(mysqli_query($connect, "SELECT * FROM batch_student WHERE batch_id = '$batch_id' AND student_id = '$student_id' AND deleted != '1'")))){
                mysqli_query($connect, $sql);
                $active = ($paid_amount >= $fees_amount) ? '2' : '1';
                mysqli_query($connect, "UPDATE batch_student set active = '$active' WHERE id = '$id'");
                header("Location: $location");
            } else {
                header("Location: $location");
                $message = "Already Registered to Batch";
            }
        } else {
            if(!mysqli_num_rows(mysqli_query($connect, "SELECT * FROM batch_student WHERE batch_id = '$batch_id' AND student_id = '$student_id' AND deleted != '1'"))){
                mysqli_query($connect, "INSERT INTO batch_student (batch_id, subject_id, student_id, remark, active) VALUES ('$batch_id', '$subject_id', $student_id, '$remark', '2')");
                header("Location: $location");
            } else {
                $message = "Already Registered to Selected Batch";
            }
        }
    } else {
        $message = "Email Not Registered";
    }

}

if(isset($_POST["upload"])){

    if($_FILES['product_file']['name']){
        $filename = explode(".", $_FILES['product_file']['name']);
        if(end($filename) == "csv"){
            $handle = fopen($_FILES['product_file']['tmp_name'], "r");
            while($data = fgetcsv($handle)){
                
                $email = mysqli_real_escape_string($connect, $data[0]);
                $remark = mysqli_real_escape_string($connect, $data[1]);
                
                $batch_id = $_GET['batch_id'];
                $student_id = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM student WHERE email = '$email' LIMIT 1"))['id'];
                $subject_id = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM batch WHERE id = '$batch_id'"))['subject_id'];

                if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    if(!mysqli_num_rows(mysqli_query($connect, "SELECT * FROM batch_student WHERE batch_id = '$batch_id' AND student_id = '$student_id' AND deleted != '1'"))){
                        mysqli_query($connect, "INSERT INTO batch_student (batch_id, subject_id, student_id, remark, active) VALUES ('$batch_id', '$subject_id', $student_id, '$remark', '2')");                    
                    }
                }
            }
            fclose($handle);
            header("Location: batch_student-add.php?batch_id=$batch_id");    
        }else{
            $message = '<label class="text-danger">Please Select CSV File only</label>';
        }
    }else{
        $message = '<label class="text-danger">Please Select File</label>';
    }
}


if($_GET['id']){
    $lock_email = "readonly"; $lock_fees = "";
} else {
    $lock_email = ""; $lock_fees = "readonly";
}

?>
<?php include('inc/header.php');?>
<?php
if($_GET['batch_id']){
?>
<form class="form-inline" method="post" enctype='multipart/form-data'>
    <input type="submit" name="upload" class="btn btn-info mr-2" value="Upload" />
    <input type="file" name="product_file" /></p>
</form>
<?php
}
?>
<form action="" method="POST" class="col-md-6 offset-md-3">
    <div class="card">
        <div class="card-header">Batch</div>
        <div class="card-body">
            <?php if($message){?>
                <div class="alert alert-warning alert-dismissible"><?=$message?></div>
            <?php } ?>
            <?php
                require('connection.php');
                $batch_id = ($_GET['batch_id']) ? $_GET['batch_id'] : $row['batch_id'];
            ?>
            <select name="course_id" class ="form-control mb-2" required>
                <option value="">Select Course</option>
                <?php
                    $sql_course="SELECT * FROM course WHERE deleted != '1'";
                    $query_course=mysqli_query($connect,$sql_course); 
                    while($row_course= mysqli_fetch_array($query_course)){
                        $selected = ($row_course['id'] == $course_id) ? "selected" : "";
                ?>
                        <option <?=$selected?> value="<?=$row_course['id']?>"><?=$row_course['name']?></option>
                <?php
                    }
                ?>
            </select>
            <select name="subject_id" class = "form-control mb-2" reqired>
                <?php
                    $sql_subject="SELECT * FROM subject WHERE deleted != '1'";
                    $query_subject=mysqli_query($connect,$sql_subject); 
                    while($row_subject= mysqli_fetch_array($query_subject)){
                        $selected = ($row_subject['id'] == $subject_id) ? "selected" : "";
                ?>
                        <option <?=$selected?> value="<?=$row_subject['id']?>"><?=$row_subject['name']?></option>
                <?php
                    }
                ?>

            </select>
            <select name="batch_id"  class = "form-control mb-2" required>
                <?php
                    $sql_batch="SELECT * FROM batch WHERE deleted != '1'";
                    $query_batch=mysqli_query($connect,$sql_batch); 
                    while($row_batch= mysqli_fetch_array($query_batch)){
                        $selected = ($row_batch['id'] == $batch_id) ? "selected" : "";
                ?>
                        <option <?=$selected?> value="<?=$row_batch['id']?>"><?=$row_batch['name']?></option>
                <?php
                    }
                ?>
            </select>
             

            <input <?=$lock_email?> type="email" name="email" value="<?=$email?>" placeholder="Email" class="form-control mb-2" required>
            <input type="text" name="remark" value="<?=$row['remark']?>" placeholder="Remark" class="form-control mb-2"> 
            <input <?=$lock_fees?> type="text" name="fees_amount" value="<?=$fees_amount?>" class="form-control mb-2"> 
            <input readonly type="text" name="paid_amount" value="<?=$paid_amount?>" class="form-control mb-2"> 
        </div>
        <div class="card-footer">
            <input type="submit" name="submit" value="Update" class="btn btn-primary mt-2">  
        </div>
    </div>
</form>

<?php
if($_GET['batch_id']){
    $batch_id = $_GET['batch_id'];
?>
<table class="table table-striped table-hover mt-2">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Fees</th>
            <th>Paid</th>
            <th>Balance</th>
            <th>Remark</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php
    require('connection.php');

    $sql="SELECT 
        batch_student.*,
        student.name AS student_name, 
        student.email AS student_email
        FROM batch_student 
        INNER JOIN student ON batch_student.student_id = student.id
        WHERE batch_student.deleted != '1' 
        AND batch_student.batch_id = '$batch_id'
        ORDER BY batch_student.id DESC";
    $query=mysqli_query($connect,$sql); 
    while($row= mysqli_fetch_array($query)){
    ?>
        <tr>
            <td><?=$row['student_name']?></td>
            <td><?=$row['student_email']?></td>
            <td><?=($row['fees_amount'])?$row['fees_amount'] : "0"?></td>
            <td><?=($row['paid_amount'])?$row['paid_amount'] : "0"?></td>
            <td><?=($row['fees_amount'] - $row['paid_amount'])? ($row['fees_amount'] - $row['paid_amount']) : "0"?></td>
            <td><?=$row['remark']?></td>
            <td>
                <a href="batch_student-add.php?batch_id=<?=$batch_id?>&id=<?=$row['id']?>" class="btn btn-warning btn-sm">Edit</a>
                <?php if(!($row['paid_amount'] > 0)){?>
                    <a href="delete.php?tbl=batch_student&id=<?=$row['id']?>&return=batch_id&return_id=<?=$batch_id?>" class="btn btn-danger btn-sm">Delete</a>
                <?php } ?>
            </td>
        </tr>
    <?php
    }
    ?>
    </tbody>
</table>
<?php
}
?>


<?php include('inc/footer.php');?>
