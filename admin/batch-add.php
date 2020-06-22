<?php
require('connection.php');
if(isset($_GET['id'])){
    $id = $_GET['id'];
    $sql = "SELECT * FROM batch WHERE id='$id'";
    $query = mysqli_query($connect,$sql); 
	$row = mysqli_fetch_array($query);
}

if(isset($_POST["submit"])){
    $name = $_POST["name"];
    $time = $_POST["time"];
    $subject_id = (isset($_GET['subject_id'])) ? $_GET['subject_id'] : $_POST["subject_id"];
    $location = ($_GET['subject_id']) ? "batch-add.php?subject_id=$subject_id" : "batch.php";
    if(isset($_GET['id'])){
        $sql = "UPDATE batch SET name ='$name', subject_id = '$subject_id', time = '$time' WHERE id='$id'";
    } else {
        $sql = "INSERT INTO batch (name,subject_id, time) VALUES ('$name','$subject_id', '$time')";
    }
    mysqli_query($connect, $sql);
    header("Location: $location");
}
?>
<?php include('inc/header.php');?>

<form action="" method="POST" class="col-md-6 offset-md-3">
    <div class="card">
        <div class="card-header">Batch</div>
        <div class="card-body">
            <?php
                require('connection.php');
                $subject_id = ($_GET['subject_id']) ? $_GET['subject_id'] : $row['subject_id'];
                $disabled = ($_GET['subject_id']) ? "disabled" : "";
            ?>
            <select <?=$disabled?> name="subject_id" class="form-control mb-2" required>
                    <option value="">Select Subject</option>
                <?php 
                    $sql1="SELECT * FROM subject WHERE deleted != '1'";
                    $query1=mysqli_query($connect,$sql1); 
                    while($row1= mysqli_fetch_array($query1)){
                        $selected = ($subject_id == $row1['id']) ? "selected" : "";
                ?>
                        <option <?=$selected?> value="<?=$row1['id']?>"><?=$row1['name']?></option>
                <?php
                    }
                ?>
            </select>            
            <input type="text" name="name" value="<?=$row['name']?>" placeholder="Batch" class="form-control mb-2" required>
            <input type="text" name="time" value="<?=$row['time']?>" placeholder="Time" class="form-control mb-2" required>
        </div>
        <div class="card-footer">
            <input type="submit" name="submit" value="Update" class="btn btn-primary mt-2">  
        </div>
    </div>
</form>

<?php
if($_GET['subject_id']){
    $subject_id = $_GET['subject_id'];
?>
<table class="table table-striped table-hover mt-2">
    <thead>
        <tr>
            <th>Name</th>
            <th>Subject</th>
            <th>Course</th>
            <th>Time</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php
    require('connection.php');
    $sql="SELECT 
        batch.*,
        subject.name AS subject_name,
        course.name AS course_name 
        FROM batch 
        INNER JOIN subject ON batch.subject_id = subject.id
        INNER JOIN course ON subject.course_id = course.id
        WHERE batch.deleted != '1' AND batch.subject_id = '$subject_id'
        ORDER BY batch.id DESC";
    $query=mysqli_query($connect,$sql);
    while($row= mysqli_fetch_array($query)){
        $batch_id = $row['id'];
    ?>
        <tr>
            <td><?=$row['name']?></td>
            <td><?=$row['subject_name']?></td>
            <td><?=$row['course_name']?></td>
            <td><?=$row['time']?></td>
            <td>
                <a href="batch-add.php?subject_id=<?=$subject_id?>&id=<?=$row['id']?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="batch_student-add.php?batch_id=<?=$row['id']?>" class="btn btn-secondary btn-sm"><i class="fa fa-plus" aria-hidden="true"></i> View Student</a>
                <a href="session-add.php?batch_id=<?=$row['id']?>" class="btn btn-secondary btn-sm"><i class="fa fa-plus" aria-hidden="true"></i> View Session</a>
                <a href="delete.php?tbl=batch&id=<?=$row['id']?>&return=subject_id&return_id=<?=$subject_id?>" class="btn btn-danger btn-sm">Delete</a>
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
