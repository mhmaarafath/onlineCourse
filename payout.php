<?php
require('connection.php');

if(isset($_COOKIE['auth'])){
    $student_id = ($_COOKIE['auth']);
    $student = mysqli_fetch_array(mysqli_query($connect,"SELECT * FROM student WHERE id = '$student_id'"));
} else {
    header("Location: index.php");
}

if($_GET['id']){
    $invoice_id = $_GET['id'];
    if(!mysqli_num_rows(mysqli_query($connect, "SELECT * FROM invoice WHERE id = '$invoice_id' AND status = 'processing'"))){
        header("Location: index.php");
    }
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

$query_batch_sql = mysqli_query($connect, "SELECT * FROM batch_student WHERE student_id = '$student_id' AND active !='2' AND deleted !='1'");
if(mysqli_num_rows($query_batch_sql)){
    while($row_batch_sql = mysqli_fetch_array($query_batch_sql)){
        $batch_student_id = $row_batch_sql['id'];
        $batch_id = $row_batch_sql['batch_id'];
        $subject_id = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM batch WHERE id = '$batch_id'"))['subject_id'];
        //stop refresh adding
        if(!mysqli_num_rows(mysqli_query($connect, "SELECT * FROM invoice_item WHERE invoice_id = '$invoice_id' AND subject_id = '$subject_id' AND batch_student_id != ''"))){
            mysqli_query($connect, "INSERT INTO invoice_item (invoice_id, subject_id, batch_id, batch_student_id) VALUES ('$invoice_id', '$subject_id', '$batch_id', '$batch_student_id')");
        }
    }
}


$query_invoice = mysqli_query($connect, "SELECT * FROM invoice_item WHERE invoice_id = '$invoice_id' AND batch_student_id = ''");
while($row_invoice = mysqli_fetch_array($query_invoice)){
    $invoice_item_id = $row_invoice['id'];
    $subject_id = $row_invoice['subject_id'];
    if(mysqli_num_rows(mysqli_query($connect, "SELECT * FROM invoice_item WHERE invoice_id = '$invoice_id' AND subject_id = '$subject_id' AND batch_student_id != ''"))){
        mysqli_query($connect, "DELETE FROM invoice_item WHERE id = '$invoice_item_id'");
    }
}

$sql="SELECT 
invoice_item.*,
batch.name AS batch_name,
subject.name AS subject_name,
course.name AS course_name
FROM invoice_item
INNER JOIN batch ON invoice_item.batch_id = batch.id
INNER JOIN subject ON batch.subject_id = subject.id
INNER JOIN course ON subject.course_id = course.id
WHERE invoice_id = '$invoice_id'";

$query=mysqli_query($connect,$sql); 


?>
<?php include('inc/header.php');?>


<div class="row">
    <div id="order_summery" class="col-12">
    <table class="table table-responsive">
        <tr>
            <td>Price</td>
            <td class="text-right">Paid</td>
            <td class="text-right">Balance</td>
            <td class="text-right">Payment</th>
        </tr>
    <?php
        while($row= mysqli_fetch_array($query)){
            $currency = '';
            $fees_amount = 0;
            $paid_amount = 0;
            $balance_amount = 0;
            
            if($row['batch_student_id'] != ''){
                $batch_student_id = $row['batch_student_id'];
                $row_batch_student = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM batch_student WHERE id = '$batch_student_id'"));
                
                $currency = $row_batch_student['currency'];
                $readonly = ($currency != $_COOKIE['currency']) ? "readonly=\"readonly\"" : "";
                
                $fees_amount = $row_batch_student['fees_amount'];
                $paid_amount = $row_batch_student['paid_amount'];
                $balance_amount = $fees_amount - $paid_amount;
                
            } else {
                $subject_id = $row['subject_id'];

                $currency = $_COOKIE['currency'];
                $readonly = "";

                $price = (isset($_COOKIE['auth']) && (mysqli_num_rows(mysqli_query($connect, "SELECT * FROM batch_student WHERE subject_id = '$subject_id' AND student_id = '$student_id' AND active = '2'")))) ? "re_price" : "price";
                $_currency = ($_COOKIE['currency'] == "LKR") ? "_lkr" : "_usd";
                $price_currency = $price.$_currency;

                
                $fees_amount = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM subject WHERE id = '$subject_id'"))[$price_currency];
                $paid_amount = 0;
                $balance_amount = $fees_amount - $paid_amount;
            }
            $currency = ($currency == "LKR") ? "RS " : "$ "


    ?>
            <tr class="align-middle">
                <td colspan="5">
                    <span class="badge badge-secondary">
                        <?=$row['subject_name']?> - <?=$row['batch_name']?> -  <?=$row['course_name']?>
                    </span>
                </td>
            </tr>
            <tr>
                <td class="align-middle" style="white-space: nowrap;"><?=$currency?><?=$fees_amount?></td>
                <td class="text-right align-middle"><?=$paid_amount?></td>
                <td class="text-right align-middle"><?=$balance_amount?></td>
                <td class="text-right align-middle">
                    <input <?=$readonly?> id="<?=$row['id']?>" type="number" step="0.01" name="payment_amount" value="<?=$row['payment_amount']?>" placeholder="Payment Amount" class="form-control mb-2">                            
                </td>
            </tr>
    <?php
        }
    ?>
            <tr>
                <td colspan="3">Total</td>
                <td class="text-right"><input readonly id="total" type="text" class="form-control mb-2"></td>
            </tr>
        </table>
        <form id="payment" method="post" action="https://sandbox.payhere.lk/pay/checkout">
            <input class="btn btn-primary btn-lg col-sm-12 col-md-3" type="submit" value="Buy Now">   
            <input type="hidden" name="merchant_id" value="1213558">    <!-- Replace your Merchant ID -->
            <input type="hidden" name="return_url" value="http://course.ultimomart.com/orders.php">
            <input type="hidden" name="cancel_url" value="http://course.ultimomart.com/courses.php">
            <input type="hidden" name="notify_url" value="http://course.ultimomart.com/payment.php">  
            <input type="hidden" name="order_id" value="<?=$invoice_id?>">
            <input type="hidden" name="items" value="Achievers Online Course"><br>
            <input type="hidden" name="currency" value="<?=$_COOKIE['currency']?>">
            <input type="hidden" name="amount" value="">  
            <input type="hidden" name="first_name" value="<?=$student['name']?>">
            <input type="hidden" name="last_name" value="_"><br>
            <input type="hidden" name="email" value="<?=$student['email']?>">
            <input type="hidden" name="phone" value="<?=$student['contact_number']?>"><br>
            <input type="hidden" name="address" value="<?=$student['address']?>">
            <input type="hidden" name="city" value="Colombo">
            <input type="hidden" name="country" value="Sri Lanka"><br><br> 
        </form> 
    </div>

</div> <!--End Row-->



<?php include('inc/footer.php');?>
