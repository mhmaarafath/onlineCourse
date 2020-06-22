<?php 
require('connection.php');

if(!(isset($_COOKIE['admin']) && $_COOKIE['admin'] == "1")){
    header("Location: index.php");
}

include('inc/header.php');
?>
    <a href="user-add.php" class="btn btn-primary mb-2" id="add">ADD</a>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        require('connection.php');
        $sql="SELECT * FROM user WHERE deleted != '1'";
        $query=mysqli_query($connect,$sql); 
        while($row= mysqli_fetch_array($query)){
        ?>
            <tr>
                <td><?=$row['id']?></td>
                <td><?=$row['email']?></td>
                <td>
                    <a href="user-add.php?id=<?=$row['id']?>" class="btn btn-warning btn-sm">Edit</a>
                    <!-- <a href="delete.php?tbl=subject&id=<?=$row['id']?>" class="btn btn-danger btn-sm">Delete</a> -->
                </td>
            </tr>
        <?php
        }
        ?>
        </tbody>
    </table>


<?php include('inc/footer.php');?>

