<?php
require('connection.php');
if(isset($_GET['id'])){
    $id = $_GET['id'];
    $sql = "SELECT * FROM course WHERE id='$id'";
    $query = mysqli_query($connect,$sql); 
	$row = mysqli_fetch_array($query);
}

if(isset($_POST["submit"])){
    $name = $_POST["name"];
    if(isset($_GET['id'])){
        $sql = "UPDATE course SET name ='$name' WHERE id='$id'";
    } else {
        $sql = "INSERT INTO course (name) VALUES ('$name')";
    }
    mysqli_query($connect, $sql);
    header("Location: course.php");
}
?>
<?php include('inc/header.php');?>

<form action="" method="POST" class="col-md-6 offset-md-3">
    <div class="card">
        <div class="card-header">Course</div>
        <div class="card-body">
            <input type="text" name="name" value="<?=$row['name']?>" placeholder="Course" class="form-control mb-2" required>
        </div>
        <div class="card-footer">
            <input type="submit" name="submit" value="Update" class="btn btn-primary mt-2">  
        </div>
    </div>
</form>

<?php include('inc/footer.php');?>
