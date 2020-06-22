<?php 
require('connection.php');
include('inc/header.php');
?>
    <a href="subject-add.php" class="btn btn-primary mb-2" id="add">ADD</a>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Name</th>
                <th>Course</th>
                <th>Original USD</th>
                <th>Original LKR</th>
                <th>Promo USD</th>
                <th>Promo LKR</th>
                <th>Re USD</th>
                <th>Re LKR</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        require('connection.php');
        $sql="SELECT 
            subject.*,
            course.name AS course_name 
            FROM subject 
            INNER JOIN course ON subject.course_id = course.id
            WHERE subject.deleted != '1' 
            ORDER BY course.name ASC";
        $query=mysqli_query($connect,$sql); 
        while($row= mysqli_fetch_array($query)){
            $subject_id = $row['id'];
        ?>
            <tr>
                <td><?=$row['name']?></td>
                <td><?=$row['course_name']?></td>
                <td><?=$row['price_usd']?></td>
                <td><?=$row['price_lkr']?></td>
                <td><?=$row['original_price_usd']?></td>
                <td><?=$row['original_price_lkr']?></td>
                <td><?=$row['re_price_usd']?></td>
                <td><?=$row['re_price_lkr']?></td>
                <td>
                    <a href="subject-add.php?id=<?=$row['id']?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="batch-add.php?subject_id=<?=$row['id']?>" class="btn btn-secondary btn-sm"><i class="fa fa-plus" aria-hidden="true"></i> View Batch</a>
                    <a href="delete.php?tbl=subject&id=<?=$row['id']?>" class="btn btn-danger btn-sm">Delete</a>
                </td>
            </tr>
        <?php
        }
        ?>
        </tbody>
    </table>


<?php include('inc/footer.php');?>

