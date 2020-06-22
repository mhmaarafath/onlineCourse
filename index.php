<?php
require('connection.php');

if(isset($_COOKIE['auth'])){
    $student_id = ($_COOKIE['auth']);
} elseif(isset($_COOKIE['tmp_auth'])){
    $student_id = ($_COOKIE['tmp_auth']);
} else {
    $student_id = uniqid("s-");
    setcookie("tmp_auth", $student_id, 0);
}

if(isset($_COOKIE['auth']) && isset($_COOKIE['tmp_auth'])){
    $auth = $_COOKIE['auth'];
    $tmp_auth = $_COOKIE['tmp_auth'];
    $check_auth = mysqli_query($connect, "SELECT * FROM invoice WHERE student_id = '$auth' AND status = 'processing'");
    if(mysqli_num_rows($check_auth)){
        $chk_invoice_id = mysqli_fetch_array($check_auth)['id'];
        mysqli_query($connect, "UPDATE invoice SET status = 'cancel' WHERE id = '$chk_invoice_id'");
    }
    mysqli_query($connect, "UPDATE invoice SET student_id ='$auth' WHERE student_id='$tmp_auth'");
    setcookie("tmp_auth", "", -3600);
    header("Location: index.php");
} else {
    $invoice_id_sql = "SELECT * FROM invoice WHERE student_id = '$student_id' AND status = 'processing'";
    $invoice_id_query = mysqli_query($connect, $invoice_id_sql);
    if(mysqli_num_rows($invoice_id_query)){
        $invoice_id = mysqli_fetch_array($invoice_id_query)['id'];
    } else {
        mysqli_query($connect,"INSERT INTO invoice (student_id, time, status) VALUES ('$student_id', SYSDATE(), 'processing')");
        $invoice_id= mysqli_insert_id($connect);
    }
}

$batch_array = array();
$subject_array = array();
$course_array = array();


$sql="SELECT 
    invoice_item.*,
    batch.subject_id AS subject_id,
    subject.course_id AS course_id
    FROM invoice_item
    INNER JOIN batch ON invoice_item.batch_id = batch.id
    INNER JOIN subject ON invoice_item.subject_id = subject.id
    WHERE invoice_id = '$invoice_id'
    AND batch_student_id = ''";

$query=mysqli_query($connect,$sql); 
while($row= mysqli_fetch_array($query)){
    $batch_id = $row['batch_id'];
    $subject_id = $row['subject_id'];
    $course_id = $row['course_id'];
    array_push($batch_array, $batch_id);
    array_push($subject_array, $subject_id);
    array_push($course_array, $course_id);
}

?>
<?php include('inc/header.php');?>


<div class="row">
    <div id="order_summery" class="col-12 col-md-6 order-md-12 mb-2">
        <?php
        $currency = ($_COOKIE['currency'] == "LKR") ? "price_lkr" : "price_usd";
        $original_price = ($_COOKIE['currency'] == "LKR") ? "original_price_lkr" : "original_price_usd";
        $re_price = ($_COOKIE['currency'] == "LKR") ? "re_price_lkr" : "re_price_usd";
        $symbol = ($_COOKIE['currency'] == "LKR") ? "Rs " : "$ ";
        $total = 0;
        $sql="SELECT 
        invoice_item.*,
        batch.name AS batch_name,
        subject.name AS subject_name,
        subject.$currency AS price,
        subject.$original_price AS original_price,
        subject.$re_price AS re_price,
        course.name AS course_name
        FROM invoice_item
        INNER JOIN batch ON invoice_item.batch_id = batch.id
        INNER JOIN subject ON batch.subject_id = subject.id
        INNER JOIN course ON subject.course_id = course.id
        WHERE invoice_id = '$invoice_id'
        AND batch_student_id = ''";
        $query=mysqli_query($connect,$sql); 
        if(mysqli_num_rows($query)){
        ?>
            <table class="table table-dark">
        <?php
            while($row= mysqli_fetch_array($query)){
                $original_total = $original_total + $row['original_price'];
                $subject_id = $row['subject_id'];
                $price = (isset($_COOKIE['auth']) && (mysqli_num_rows(mysqli_query($connect, "SELECT * FROM batch_student WHERE subject_id = '$subject_id' AND student_id = '$student_id' AND active = '2'")))) ? $row['re_price'] : $row['price'];
                $total = $total + $price;
        ?>
                <tr>
                    <td>
                        <?=$row['subject_name']?><br>
                        <span style="font-size:.8em"><?=$row['batch_name']?></span>
                    </td>
                    <td class="text-right" style="text-decoration: line-through;"><?=$symbol?><?=$row['original_price']?></td>
                    <td class="text-right"><?=$symbol?><?=$price?></td>
                </tr>
        <?php
            }
                    $sql_student="SELECT * FROM student WHERE id = '$student_id'";
                    $query_student=mysqli_query($connect,$sql_student); 
                    $row_student= mysqli_fetch_array($query_student);
        
        ?>
                <tr>
                    <td>Total</td>
                    <td class="text-right" style="text-decoration: line-through;"><b><?=$symbol?><?=$original_total?></b></td>
                    <td class="text-right"><b><?=$symbol?><?=$total?></b></td>
                </tr>
            </table>
            <div>
                <?php if(isset($_COOKIE['auth'])){ ?>
                    <a href="payout.php?id=<?=$invoice_id?>" class="btn btn-primary float-right">Make Payment</a>   
                <?php } else { ?>
                    <a href="login.php" class="btn btn-primary float-right">Login - Register to Place Order</a>   
                <?php } ?>
            </div> 
        <?php
        }
        ?>      
    </div>
    <form action="" method="POST" class="col-12 col-md-6 order-md-1">
        <input type="hidden" name="invoice_id" value="<?=$invoice_id?>">
        <div class="accordion" id="accordionExample">
            <?php
            $sql="SELECT * FROM course WHERE deleted != '1'";
            $query=mysqli_query($connect,$sql); 
            while($row= mysqli_fetch_array($query)){
                $course_id = $row['id'];
            ?>
                <div class="card">
                    <div class="card-header" id="heading-<?=$course_id?>" data-toggle="collapse" data-target="#collapse-<?=$course_id?>" aria-expanded="true" aria-controls="collapse-<?=$course_id?>">
                        <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>
                        <?=$row['name']?>
                    </div> <!--End card Header-->
                    <div id="collapse-<?=$course_id?>" class="collapse card-body <?=(in_array($course_id, $course_array))? "show":""?>" aria-labelledby="heading-<?=$course_id?>" data-parent="#accordionExample">
            <?php
                    $sql1="SELECT * FROM subject WHERE deleted != '1' AND course_id='$course_id'";
                    $query1=mysqli_query($connect,$sql1); 
                    while($row1= mysqli_fetch_array($query1)){                            
                        $subject_id = $row1['id'];
            ?>
                        <div>
                            <input class="mb-2 mr-2" <?=(in_array($subject_id, $subject_array))? "checked":""?> type="checkbox" name="subject_id" value="<?=$subject_id?>"><?=$row1['name']?>
                            <br>
                            <select name="batch_id" class="form-control my-2"> 
                                <option value="">Select Batch</option>                      
            <?php
                                $sql2="SELECT * FROM batch WHERE deleted != '1' AND subject_id='$subject_id'";
                                $query2=mysqli_query($connect,$sql2); 
                                while($row2= mysqli_fetch_array($query2)){
                                    $batch_id = $row2['id'];
            ?>
                                    <option <?=(in_array($batch_id, $batch_array))? "selected":""?> value="<?=$batch_id?>"><?=$row2['name']?></option>
            <?php                            
                                    }
            ?>
                            </select>
                            <div class="alert alert-warning" style="font-size:.8em">
            <?php
                            $sql3="SELECT * FROM batch WHERE deleted != '1' AND subject_id='$subject_id'";
                            $query3=mysqli_query($connect,$sql3); 
                            while($row3= mysqli_fetch_array($query3)){
                                $batch_id = $row3['id'];
            ?>
                                <div class="<?=(in_array($batch_id, $batch_array))? "d-block":"d-none"?>" value="<?=$batch_id?>"><?=$row3['time']?></div>
            <?php
                            }
            ?>
                            </div>
                        </div> <!--End Container Div-->
            <?php
                    }
            ?>
                    </div> <!--End Card Body-->
                </div> <!--End Card-->
                <?php
            }
            ?>
        </div> <!--End Accordian-->
    </form> <!--End Form-->


</div> <!--End Row-->



<?php include('inc/footer.php');?>
