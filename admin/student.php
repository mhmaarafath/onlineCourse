<?php 
require('connection.php');

if(isset($_POST["upload"])){
    if($_FILES['product_file']['name']){
        $filename = explode(".", $_FILES['product_file']['name']);
        if(end($filename) == "csv"){
            $handle = fopen($_FILES['product_file']['tmp_name'], "r");
            while($data = fgetcsv($handle)){
                $name = mysqli_real_escape_string($connect, $data[0]);
                $address = mysqli_real_escape_string($connect, $data[1]);
                $email = mysqli_real_escape_string($connect, $data[2]);
                $password = mysqli_real_escape_string($connect, $data[3]);
                $cima_acca_register_number = mysqli_real_escape_string($connect, $data[4]);
                $contact_number = mysqli_real_escape_string($connect, $data[5]);
                $whatsapp_number = mysqli_real_escape_string($connect, $data[6]);
                $nic_passport_number = mysqli_real_escape_string($connect, $data[7]);
                $dob = mysqli_real_escape_string($connect, $data[8]);
                $gender = strtolower(mysqli_real_escape_string($connect, $data[9]));
                $school = mysqli_real_escape_string($connect, $data[10]);
                $how_did_get = mysqli_real_escape_string($connect, $data[11]);
                $remark = mysqli_real_escape_string($connect, $data[12]);
                
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    if(!mysqli_num_rows(mysqli_query($connect, "SELECT * FROM student WHERE email = '$email'"))){
                        $sql4 = "INSERT student (name, address, email, password, cima_acca_register_number, contact_number, whatsapp_number, nic_passport_number, dob, gender, school, how_did_get, remark) VALUES ('$name','$address','$email','$password','$cima_acca_register_number','$contact_number','$whatsapp_number','$nic_passport_number','$dob','$gender','$school','$how_did_get','$remark')";
                        mysqli_query($connect, $sql4);
                    }
                }
            }
            fclose($handle);
            header("Location: student.php");    
        }else{
            $message = '<label class="text-danger">Please Select CSV File only</label>';
        }
    }else{
        $message = '<label class="text-danger">Please Select File</label>';
    }
}


// $sql5 = "SELECT * FROM student"; 

// $filename = "export.xls";
// if(isset($_POST['export'])){
//     $output;
//     header("Content-Type: application/xls");
//     header("Content-Disposition: attachment; filename=\"$filename\"");
//     $output .= "ID \t Name \t Address \t Email \t Contact \t WhatsApp \t Price \t DDate \t Deliver \t Waybill  \n";
//         while($row5 = mysqli_fetch_array($query5)){
//             $iid = $row5['id'];
//             $item = "";
//             $price = 0;
//             $sql6 = "SELECT
//             invoice_item.*, 
//             model.name AS model_name,
//             brand.name AS brand_name,
//             cover.name AS cover_name,
//             variation.name AS variation_name
//             FROM invoice_item 
//             INNER JOIN product ON invoice_item.product_id = product.id 
//             INNER JOIN model ON product.model_id = model.id 
//             INNER JOIN cover ON product.cover_id = cover.id 
//             INNER JOIN brand ON model.brand_id = brand.id 
//             INNER JOIN variation ON product.variation_id = variation.id 
//             WHERE invoice_item.invoice_id = '$iid' AND invoice_item.deleted = '0'";
//             $query6 = mysqli_query($connect, $sql6);
//             while($row6 = mysqli_fetch_array($query6)){
//                 $item .= $row6['brand_name']." ".$row6['model_name']." ".$row6['cover_name']." ".$row6['variation_name'].", ";
//                 $price += $row6['price'];
//             }
//             $output .= $row5['id']."\t";
//             $output .= $row5['date']."\t";
//             $output .= ucwords(preg_replace( "/\r|\n/", "", $row5['customer_name'] ))."\t";
//             $output .= ucwords(preg_replace( "/\r|\n/", "", $row5['customer_address'] ))."\t";
//             $output .= $row5['customer_contact']."\t";
//             $output .= ucwords(strtolower($item))."\t";
//             $output .= $price."\t";
//             $output .= ""."\t";
//             $output .= ""."\t";
//             $output .= ""."\n";
//         }
//     echo $output;
//     exit();
// }


include('inc/header.php');
?>
    <form class="form-inline mb-3" method="post" enctype='multipart/form-data'>
        <input type="submit" name="upload" class="btn btn-info mr-2" value="Upload" />
        <?=$message?>
        <input type="file" name="product_file" /></p>
        <!-- <input type="submit" name="export" class="btn btn-info mr-2" value="Export" /> -->
    </form>

    <a href="student-add.php" class="btn btn-primary mb-3" id="add">Single</a>

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Address</th>
                <th>Email</th>
                <th>Contact Number</th>
                <th>Whatsapp Number</th>
                <th>NIC-Passport Number</th>
                <th>Remark</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        require('connection.php');
        $sql="SELECT * FROM student WHERE deleted != '1'";
        $query=mysqli_query($connect, $sql); 
        while($row= mysqli_fetch_array($query)){
        ?>
            <tr>
                <td><?=$row['id']?></td>
                <td><?=$row['name']?></td>
                <td><?=$row['address']?></td>
                <td><?=$row['email']?></td>
                <td><?=$row['contact_number']?></td>
                <td><?=$row['whatsapp_number']?></td>
                <td><?=$row['nic_passport_number']?></td>
                <td><?=$row['remark']?></td>                
                <td>
                    <a href="student-add.php?id=<?=$row['id']?>" class="btn btn-warning btn-sm">Edit</a>
                    <!-- <a href="student-add.php?student_id=<?=$row['id']?>" class="btn btn-secondary btn-sm">Add Student</a> -->
                    <a href="delete.php?tbl=student&id=<?=$row['id']?>" class="btn btn-danger btn-sm">Delete</a>
                </td>
            </tr>
        <?php
        }
        ?>
        </tbody>
    </table>


<?php include('inc/footer.php');?>

