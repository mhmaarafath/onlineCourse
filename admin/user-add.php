<?php
require('connection.php');

if(!(isset($_COOKIE['admin']) && $_COOKIE['admin'] == "1")){
    header("Location: index.php");
}

if(isset($_GET['id'])){
    $id = $_GET['id'];
    $sql = "SELECT * FROM user WHERE id='$id'";
    $query = mysqli_query($connect,$sql); 
	$row = mysqli_fetch_array($query);
}

if(isset($_POST["submit"])){
    $email = $_POST["email"];
    $password = $_POST["password"];

    if(isset($_GET['id'])){
        $sql = "UPDATE user SET email ='$email', password = '$password' WHERE id='$id'";
    } else {
        $sql = "INSERT INTO user (email, password) VALUES ('$email', '$password')";
    }
    mysqli_query($connect, $sql);
    header("Location: user.php");
}
?>
<?php include('inc/header.php');?>

<form action="" method="POST" class="col-md-6 offset-md-3">
    <div class="card">
        <div class="card-header">User</div>
        <div class="card-body">
            <input type="email" name="email" value="<?=$row['email']?>" placeholder="Email" class="form-control mb-2" required>
            <input type="password" name="password" value="<?=$row['password']?>" placeholder="Password" class="form-control mb-2" required>
        </div>
        <div class="card-footer">
            <input type="submit" name="submit" value="Update" class="btn btn-primary mt-2">  
        </div>
    </div>
</form>

<?php include('inc/footer.php');?>
