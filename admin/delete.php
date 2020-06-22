<?php
require('connection.php');
if(isset($_POST["yes"])){
    $table = $_GET['tbl'];
    $id = $_GET['id'];
    $sql = "UPDATE $table SET deleted ='1' WHERE id='$id'";

    if(mysqli_query($connect, $sql)){
        if($_GET['return_id']){
            $return_id = $_GET['return_id'];
            $return = $_GET['return'];
            header("Location: $table-add.php?$return=$return_id");
        } else {
            header('Location: ' . $table.'.php');
        }
    }
}
?>
<?php include('inc/header.php');?>
    <div class="jumbotron">
        <h1 class="mb-3">Are you sure you want to delete ?</h1>
        <form action="" method="POST">
            <input type="submit" name="yes" value="YES" class="btn btn-lg btn-danger">
            <input type="submit" name="no" value="NO" class="btn btn-lg btn-primary">
        </form>
    </div>
<?php include('inc/footer.php');?>
