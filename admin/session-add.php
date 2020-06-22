<?php
require('connection.php');
if(isset($_GET['id'])){
    $id = $_GET['id'];
    $sql = "SELECT * FROM session WHERE id='$id'";
    $query = mysqli_query($connect,$sql); 
	$row = mysqli_fetch_array($query);
}

if(isset($_POST["submit"])){
    $name = $_POST["name"];
    $link = $_POST["link"];
    $completed = (isset($_POST['completed']))? "1" : "0";

    $batch_id = ($_GET['batch_id']) ? $_GET['batch_id'] : $_POST['batch_id'];
    $location = ($_GET['batch_id']) ? "session-add.php?batch_id=$batch_id" : "session.php";
    if(isset($_GET['id'])){
        $sql = "UPDATE session SET name ='$name', batch_id = '$batch_id', link = '$link', completed = '$completed' WHERE id='$id'";
    } else {
        $sql = "INSERT INTO session (name, batch_id, link, completed) VALUES ('$name','$batch_id', '$link', '$completed')";
    }
    mysqli_query($connect, $sql);
    header("Location: $location");
}
?>
<?php include('inc/header.php');?>

<form action="" method="POST" class="col-md-6 offset-md-3">
    <div class="card">
        <div class="card-header">Session</div>
        <div class="card-body">
            <?php 
                $batch_id = $_GET['batch_id'];
                $sql1="SELECT 
                    batch.*,
                    subject.name AS subject_name
                    FROM batch 
                    INNER JOIN subject ON batch.subject_id = subject.id
                    WHERE batch.id = '$batch_id'";
                $query1=mysqli_query($connect,$sql1); 
                $row1 = mysqli_fetch_array($query1);
            ?>
            <input type="text" readonly value="<?=$row1['subject_name']?>" class="form-control mb-2">
            <input type="text" readonly value="<?=$row1['name']?>" class="form-control mb-2">
            <input type="hidden" name="batch_id" value="<?=$row1['id']?>" class="form-control mb-2">
            <input type="text" name="name" value="<?=$row['name']?>" placeholder="Session" class="form-control mb-2" required>
            <input type="text" name="link" value="<?=$row['link']?>" placeholder="Link" class="form-control mb-2" required>
            <input <?=($row['completed']==1)?"checked":""?> type="checkbox" name="completed" value="1"> Completed
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
            <th>ID</th>
            <th>Name</th>
            <th>Course</th>
            <th>Subject</th>
            <th>Batch</th>
            <th>Link</th>
            <th>Completed</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php
    require('connection.php');
    $sql="SELECT 
        session.*,
        course.name AS course_name,
        subject.name AS subject_name,
        batch.name AS batch_name
        FROM session 
        INNER JOIN batch ON session.batch_id = batch.id
        INNER JOIN subject ON batch.subject_id = subject.id
        INNER JOIN course ON subject.course_id = course.id
        WHERE session.deleted != '1' AND session.batch_id = '$batch_id'";
    $query=mysqli_query($connect,$sql); 
    while($row= mysqli_fetch_array($query)){
    ?>
        <tr>
            <td><?=$row['id']?></td>
            <td><?=$row['name']?></td>
            <td><?=$row['course_name']?></td>
            <td><?=$row['subject_name']?></td>
            <td><?=$row['batch_name']?></td>
            <td><?=$row['link']?></td>
            <td><?=($row['completed'])?"Completed":"Pending"?></td>
            <td>
                <a href="session-add.php?batch_id=<?=$batch_id?>&id=<?=$row['id']?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="delete.php?tbl=session&id=<?=$row['id']?>&return=batch_id&return_id=<?=$batch_id?>" class="btn btn-danger btn-sm">Delete</a>
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
