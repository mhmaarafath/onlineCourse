<?php 
require('connection.php');
include('inc/header.php');
?>
    <div>
    <?php
    if(isset($_GET['id'])){
        $invoice_id = $_GET['id'];
        $sql1="SELECT 
        invoice_item.*,
        batch.name AS batch_name,
        subject.name AS subject_name,
        course.name AS course_name
        FROM invoice_item
        INNER JOIN batch ON invoice_item.batch_id = batch.id
        INNER JOIN subject ON batch.subject_id = subject.id
        INNER JOIN course ON subject.course_id = course.id
        WHERE invoice_id = '$invoice_id'
        AND payment_amount != '0'";

        $query1=mysqli_query($connect,$sql1); 
        while($row1= mysqli_fetch_array($query1)){
            $batch_id = $row1['batch_id'];
    ?>
            <div class="alert alert-success">
                <span class="alert alert-warning"><?=$row1['batch_name']?></span>
                <span class="alert alert-warning"><?=$row1['subject_name']?></span>
                <span class="alert alert-warning"><?=$row1['course_name']?></span>
                <span class="alert alert-primary"><?=$row1['payment_amount']?></span>
            </div>
    <?php                            
        }        
    }
    ?>
    </div>

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Name</th>
                <th>Email</th>
                <th>Currency</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sql="SELECT * FROM invoice WHERE status = 'Success'";
        $query=mysqli_query($connect,$sql); 
        while($row= mysqli_fetch_array($query)){
            $invoice_id = $row['id'];
            $student_id = $row['student_id'];
            $row_student = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM student WHERE id = '$student_id'"));
        ?>
            <tr>
                <td><?=$row['id']?></td>
                <td><?=$row['time']?></td>
                <td><?=$row_student['name']?></td>
                <td><?=$row_student['email']?></td>
                <td><?=$row['currency']?></td>
                <td><?=$row['amount']?></td>
                <td><?=ucfirst($row['status'])?></td>
                <td><a class="btn btn-primary btn-sm" href="?id=<?=$invoice_id?>">View</a></td>
            </tr>
        <?php
        }
        ?>
        </tbody>
    </table>


<?php include('inc/footer.php');?>

