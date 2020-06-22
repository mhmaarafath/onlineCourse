<?php 
require('connection.php');
include('inc/header.php');
?>
    <a href="batch-add.php" class="btn btn-primary mb-2" id="add">ADD</a>
    <table class="table table-striped table-hover">
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
            WHERE batch.deleted != '1' 
            ORDER BY batch.name ASC";
        $query=mysqli_query($connect,$sql); 
        while($row= mysqli_fetch_array($query)){
            $section_id = $row['id'];
        ?>
            <tr>
                <td><?=$row['name']?></td>
                <td><?=$row['subject_name']?></td>
                <td><?=$row['course_name']?></td>
                <td><?=$row['time']?></td>
                <td>
                    <a href="batch-add.php?id=<?=$row['id']?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="batch_student-add.php?batch_id=<?=$row['id']?>" class="btn btn-secondary btn-sm">Add Student</a>
                    <a href="session-add.php?batch_id=<?=$row['id']?>" class="btn btn-secondary btn-sm">Add Session</a>
                    <a href="delete.php?tbl=batch&id=<?=$row['id']?>" class="btn btn-danger btn-sm">Delete</a>
                </td>
            </tr>
        <?php
        }
        ?>
        </tbody>
    </table>


<?php include('inc/footer.php');?>

