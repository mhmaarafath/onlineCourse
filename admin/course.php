<?php 
require('connection.php');
include('inc/header.php');
?>
    <a href="course-add.php" class="btn btn-primary mb-2" id="add">ADD</a>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sql="SELECT * FROM course WHERE deleted != '1' ORDER BY name ASC";
        $query=mysqli_query($connect,$sql); 
        while($row= mysqli_fetch_array($query)){
        ?>
            <tr>
                <td><?=$row['name']?></td>
                <td>
                    <a href="course-add.php?id=<?=$row['id']?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="subject-add.php?course_id=<?=$row['id']?>" class="btn btn-secondary btn-sm"><i class="fa fa-plus" aria-hidden="true"></i> View Subject</a>
                    <a href="delete.php?tbl=course&id=<?=$row['id']?>" class="btn btn-danger btn-sm">Delete</a>
                </td>
            </tr>
        <?php
        }
        ?>
        </tbody>
    </table>
<?php include('inc/footer.php');?>

