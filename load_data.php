<?php  
require('connection.php');
 
$batch_id = $_POST['batch_id'];
$subject_id = $_POST['subject_id'];
$invoice_id = $_POST['invoice_id'];


if($_POST["action"]=="insert" && $batch_id !=""){
    if(mysqli_num_rows(mysqli_query($connect, "SELECT * FROM invoice_item WHERE invoice_id = '$invoice_id' AND subject_id = '$subject_id'"))){
        mysqli_query($connect, "DELETE FROM invoice_item WHERE subject_id='$subject_id' AND invoice_id='$invoice_id'");
    }
    $sql = "INSERT INTO invoice_item (invoice_id, subject_id, batch_id) VALUES ('$invoice_id', '$subject_id', '$batch_id')";  
    $query = mysqli_query($connect, $sql); 
} 

if($_POST["action"]=="delete"){
    $sql = "DELETE FROM invoice_item WHERE subject_id='$subject_id' AND invoice_id='$invoice_id'";  
    $query = mysqli_query($connect, $sql); 
} 

function check_email(){
    global $email, $connect;
    $sql = "SELECT * FROM student WHERE email = '$email'";  
    $query = mysqli_query($connect, $sql); 
    $return = (mysqli_num_rows($query)>0) ? "false" : "true";
    return $return;
}

if($_POST["action"]=="check"){
    $email = $_POST['email'];
    $id = $_POST['id'];

    if($id == "insert"){
        echo check_email();
    } else {
        $row_email = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM student WHERE id = '$id'"))['email'];
        if($email == $row_email){
            echo "true";
        } else {
            echo check_email();
        }
    }
} 

if($_POST["action"]=="update"){
    $id = $_POST['id'];
    $payment_amount = ($_POST['payment_amount'] != "")? $_POST['payment_amount']:0;
    mysqli_query($connect, "UPDATE invoice_item SET payment_amount = '$payment_amount' WHERE id = '$id'");
    echo $payment_amount;
} 

// if($_POST["action"]=="search"){
//     $sql = "DELETE FROM invoice_item WHERE subject_id='$subject_id' AND invoice_id='$invoice_id'";  
//     $time = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM batch WHERE id = '$batch_id'"))['time'];
//     echo $time;
// } 




?>  