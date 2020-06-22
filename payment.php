<?php
require('connection.php');
$merchant_id         = $_POST['merchant_id'];
$order_id             = $_POST['order_id'];
$payhere_amount     = $_POST['payhere_amount'];
$payhere_currency    = $_POST['payhere_currency'];
$status_code         = $_POST['status_code'];
$md5sig                = $_POST['md5sig'];

$merchant_secret = '4Es2mHerDoh8QnYWwMXIv48hix4tRssvR4Ur9PLgsnN0'; 

$local_md5sig = strtoupper (md5 ( $merchant_id . $order_id . $payhere_amount . $payhere_currency . $status_code . strtoupper(md5($merchant_secret)) ) );

if($status_code == 2){
    $status = "Approved";
} else if($status_code == 0){
    $status = "Pending";
} else if($status_code == -1){
    $status = "Canceled";
} else if($status_code == -2){
    $status = "Failed";
} else if($status_code == -3){
    $status = "ChargedBack";
}

mysqli_query($connect, "UPDATE invoice SET currency = '$payhere_currency', amount = '$payhere_amount' WHERE id='$order_id'");

if (($local_md5sig === $md5sig) AND ($status_code == 2) ){
    mysqli_query($connect, "UPDATE invoice SET status = 'Success' WHERE id = '$order_id'");
    
    $currency = ($payhere_currency == "LKR") ? "price_lkr" : "price_usd";

    $query_invoice = mysqli_query($connect, "SELECT * FROM invoice_item WHERE invoice_id = '$order_id'");

    while($row_invoice = mysqli_fetch_array($query_invoice)){

        $subject_id = $row_invoice['subject_id'];
        $batch_id = $row_invoice['batch_id'];
        $paid_amount = $row_invoice['payment_amount'];
        $fees_amount = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM subject WHERE id = '$subject_id'"))[$currency];
        $student_id = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM invoice WHERE id = '$order_id'"))['student_id'];


        $query_subject = mysqli_query($connect, "SELECT * FROM batch_student WHERE student_id = '$student_id' AND subject_id = '$subject_id' AND active !='2'");
        if(mysqli_num_rows($query_subject)){
            $row_subject = mysqli_fetch_array($query_subject);
            $batch_student_id = $row_subject['id'];
            $fees_amount = $row_subject['fees_amount'];
            $paid_amount = $row_invoice['payment_amount'] + $row_subject['paid_amount'];
            mysqli_query($connect, "UPDATE batch_student SET paid_amount = '$paid_amount' WHERE id = '$batch_student_id'");
            if($paid_amount >= $fees_amount){
                mysqli_query($connect, "UPDATE batch_student SET active = '2' WHERE id = '$batch_student_id'");
            }
        } else {
            $active = ($paid_amount >= $fees_amount) ? '2' : '0';
            mysqli_query($connect, "INSERT INTO batch_student (batch_id, subject_id, student_id, remark, currency, fees_amount, paid_amount, active) VALUES ('$batch_id', '$subject_id', $student_id, '$remark', '$payhere_currency', '$fees_amount', '$paid_amount', '$active')");
        }
    }
} else {
    mysqli_query($connect, "UPDATE invoice SET status = '$status' WHERE id='$order_id'");
}

?>