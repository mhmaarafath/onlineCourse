<?php 
require('connection.php');
$student_id = $_COOKIE['auth'];
if($_GET['order_id']){
    setcookie("sweet_alert","success", 0);
    header("Location: orders.php");        
}
include('inc/header.php');
?>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Currency</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sql="SELECT * FROM invoice WHERE student_id = '$student_id' AND status != 'cancel'";
        $query=mysqli_query($connect,$sql); 
        while($row= mysqli_fetch_array($query)){
            $invoice_id = $row['id'];
        ?>
            <tr>
                <td><?=$row['id']?></td>
                <td><?=$row['time']?></td>
                <td><?=$row['currency']?></td>
                <td><?=$row['amount']?></td>
                <td><?=ucfirst($row['status'])?></td>

            </tr>
            <tr>
                <td colspan="5">
                    <table class="table table-dark">
        <?php

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
                        AND payment_amount !='0'";

                        $query1=mysqli_query($connect,$sql1); 
                        while($row1= mysqli_fetch_array($query1)){
        ?>
                            <tr>
                                <td><?=$row1['id']?></td>
                                <td><?=$row1['batch_name']?></td>
                                <td><?=$row1['subject_name']?></td>
                                <td><?=$row1['course_name']?></td>
                                <td><?=$row1['payment_amount']?></td>
                            </tr>
        <?php                            
                        }
        ?>
                    </table>
                </td>
            </tr>
        <?php
        }
        ?>
        </tbody>
    </table>


<?php include('inc/footer.php');?>

