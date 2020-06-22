<?php 
require('connection.php');
if(isset($_POST['submit'])){
    $batch_student_id = $_POST['batch_student_id'];
    mysqli_query($connect, "UPDATE batch_student SET active = '1' WHERE id = '$batch_student_id'");
    header('Refresh: 0');
}
include('inc/header.php');
?>
    <a href="batch_student-add.php" class="btn btn-primary mb-2" id="add">ADD</a>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Batch</th>
                <th>Subject</th>
                <th>Email</th>
                <th>Currency</th>
                <th>Fees</th>
                <th>Payment</th>
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
            batch.name AS batch_name,
            subject.name AS subject_name,
            student.email 
            FROM batch_student 
            INNER JOIN batch ON batch_student.batch_id = batch.id
            INNER JOIN subject ON batch_student.subject_id = subject.id
            INNER JOIN student ON batch_student.student_id = student.id
            WHERE batch_student.deleted != '1' 
            ORDER BY batch.name ASC";
        $query=mysqli_query($connect,$sql); 
        while($row= mysqli_fetch_array($query)){
            $batch_student_id = $row['id'];
        ?>
            <tr>
                <td><?=$row['batch_name']?></td>
                <td><?=$row['subject_name']?></td>
                <td><?=$row['email']?></td>
                <td><?=$row['currency']?></td>
                <td><?=$row['fees_amount']?></td>
                <td><?=$row['paid_amount']?></td>
                <td><?=$row['fees_amount'] - $row['paid_amount']?></td>
                <td><?=$row['remark']?></td>
                <td>
                    <a href="batch_student-add.php?id=<?=$batch_student_id?>" class="btn btn-warning btn-sm">Edit</a>  
                    <?php if($row['active'] == '0'){?>

                    <form class="form-inline" action="" method="post">
                        <input type="hidden" name="batch_student_id" value="<?=$batch_student_id?>">
                        <input class="btn btn-success" type="submit" name="submit" value="Approve">
                    </form>                
                    <?php } ?>
                    <!-- <a href="batch_student-add.php?badge_id=<?=$row['id']?>" class="btn btn-secondary btn-sm"><i class="fa fa-plus" aria-hidden="true"></i> View Section</a> -->
                    <!-- <a href="delete.php?tbl=subject&id=<?=$row['id']?>" class="btn btn-danger btn-sm">Delete</a> -->
                </td>
            </tr>
        <?php
        }
        ?>
        </tbody>
    </table>


<?php include('inc/footer.php');?>

