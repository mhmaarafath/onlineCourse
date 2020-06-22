<?php
require('connection.php');
if(isset($_GET['id'])){
    $id = $_GET['id'];
    $sql = "SELECT * FROM student WHERE id='$id'";
    $query = mysqli_query($connect,$sql); 
	$row = mysqli_fetch_array($query);
}

if(isset($_POST["submit"])){
    $name = mysqli_real_escape_string($connect, $_POST['name']);
    $address = mysqli_real_escape_string($connect, $_POST['address']);
    $email = mysqli_real_escape_string($connect, $_POST['email']);
    $password = mysqli_real_escape_string($connect, $_POST['password']);
    $reconfirm_password = mysqli_real_escape_string($connect, $_POST['reconfirm_password']);
    $cima_acca_register_number = mysqli_real_escape_string($connect, $_POST['cima_acca_register_number']);
    $contact_number = mysqli_real_escape_string($connect, $_POST['contact_number']);
    $whatsapp_number = mysqli_real_escape_string($connect, $_POST['whatsapp_number']);
    $nic_passport_number = mysqli_real_escape_string($connect, $_POST['nic_passport_number']);
    $dob = mysqli_real_escape_string($connect, $_POST['dob']);
    $gender = mysqli_real_escape_string($connect, $_POST['gender']);
    $school = mysqli_real_escape_string($connect , $_POST['school']);
    $how_did_get = implode(", ", mysqli_real_escape_string($connect, $_POST['how_did_get']));
    $remark = mysqli_real_escape_string($connect, $_POST['remark']);

    if(isset($_GET['id'])){
        $sql = "UPDATE student SET 
                name = '$name',
                address = '$address',
                email = '$email',
                password = '$password',
                cima_acca_register_number = '$cima_acca_register_number',
                contact_number = '$contact_number',
                whatsapp_number = '$whatsapp_number',
                nic_passport_number = '$nic_passport_number',
                dob = '$dob',
                gender = '$gender',
                school = '$school',
                how_did_get = '$how_did_get',
                remark = '$remark'                
                WHERE id='$id'";
    } else {
        $sql = "INSERT student (name, address, email, password, cima_acca_register_number, contact_number, whatsapp_number, nic_passport_number, dob, gender, school, how_did_get, remark) VALUES ('$name','$address','$email','$password','$cima_acca_register_number','$contact_number','$whatsapp_number','$nic_passport_number','$dob','$gender','$school','$how_did_get','$remark')";
    }
    mysqli_query($connect, $sql);
    header("Location: student.php");
}



?>
<?php include('inc/header.php');?>

<div class="row">

<form action="" method="POST" class="col-md-6" id="form">
    <div class="card">
        <div class="card-header">Registration</div>
        <div class="card-body">
            <input type="hidden" name="id" id="id" value="<?=($_GET['id']?$_GET['id']:"insert")?>">
            <input type="text" name="name" value="<?=$row['name']?>" placeholder="Name" class="form-control mb-2" required/>
            <input type="text" name="address" value="<?=$row['address']?>" placeholder="Address" class="form-control mb-2" required/>
            <input type="email" name="email" value="<?=$row['email']?>" placeholder="Email" class="form-control mb-2" id="email" required/>
            <input type="password" name="password" value="<?=$row['password']?>" placeholder="Password" class="form-control mb-2" id="password" required/>
            <input type="password" name="reconfirm_password" value="<?=$row['password']?>" placeholder="Password" class="form-control mb-2" required/>
            <input type="text" name="cima_acca_register_number" value="<?=$row['cima_acca_register_number']?>" placeholder="CIMA / ACCA Registered Number" class="form-control mb-2"/>
            <input type="text" name="contact_number" value="<?=$row['contact_number']?>" placeholder="Contact Number" class="form-control mb-2" required/>
            <input type="text" name="whatsapp_number" value="<?=$row['whatsapp_number']?>" placeholder="WhatsApp Number" class="form-control mb-2"/>
            <input type="text" name="nic_passport_number" value="<?=$row['nic_passport_number']?>" placeholder="NIC / Passport Number" class="form-control mb-2"/>
            <input type="text" name="dob" value="<?=$row['dob']?>" placeholder="Date of Birth" class="form-control mb-2" required/>

            <select name="gender" class="form-control mb-2" required>
                <option value="">Gender</option>
                <option <?=($row['gender'] == "m") ? "selected" : ""?> value="m">Male</option>
                <option <?=($row['gender'] == "f") ? "selected" : ""?> value="m">Female</option>
            </select> 
            <input type="text" name="school" value="<?=$row['school']?>" placeholder="School" class="form-control mb-2" required/>
            <div class="card mb-2">
                <div class="card-header">
                    How did you get to know us? 
                </div>
                <div class="card-body">
                    <?php
                        $sql1="SELECT * FROM how_get";
                        $query1=mysqli_query($connect,$sql1);
                        while($row1= mysqli_fetch_array($query1)){
                            $checked = (in_array($row1['id'], explode(", ", $row['how_did_get']))) ? "checked = \"checked\"" : ""; 
                    ?>
                            <div class="form-check float-left w-25">
                                <input <?=$checked?> class="form-check-input" type="checkbox" name="how_did_get[]" value="<?=$row1['id']?>">
                                <label class="form-check-label">
                                    <?=$row1['name']?>
                                </label>
                            </div>
                    <?php
                        }
                    ?>
                </div>
            </div>
            <input type="text" name="remark" value="<?=$row['remark']?>" placeholder="Remark" class="form-control mb-2"/>
        </div>
        <div class="card-footer">
            <input type="submit" name="submit" value="Register" class="btn btn-primary d-block w-100 btn-lg">
            <div class="text-center mt-2">
                <a href="index.php" class="text-warning"><b>Login</b></a>
            </div>
        </div>
    </div>
</form>

<div class="col-md-6">
    <table class="table">
    <?php
    $sql="SELECT 
    batch_student.*,
    batch.name AS batch_name,
    subject.name AS subject_name,
    course.name AS course_name
    FROM batch_student 
    INNER JOIN batch ON batch_student.batch_id = batch.id
    INNER JOIN subject ON batch_student.subject_id = subject.id
    INNER JOIN student ON batch_student.student_id = student.id
    INNER JOIN course ON subject.course_id = course.id
    WHERE batch_student.deleted != '1'
    AND batch_student.student_id = '$id'";
    $query = mysqli_query($connect, $sql);
    while($row = mysqli_fetch_array($query)){
    ?>
        <tr>
            <td><?=$row['batch_name']?></td>
            <td><?=$row['subject_name']?></td>
            <td><?=$row['course_name']?></td>
        </tr>
    <?php
    }
    ?>
    </table>
</div>

</div>

<?php include('inc/footer.php');?>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>

<script>

$(document).ready(function(){
    $('#form').validate({
        rules : {
            reconfirm_password: {
                equalTo: "#password",
            },

            email: {
                remote: {
                    url: "../load_data.php",
                    type: "post",
                    data: {
                        action : "check",
                        email: function() {
                            return $("#email").val();
                        },
                        id: function() {
                            return $("#id").val();
                        }
                    }
                }                
            }
        },
        messages : {
            reconfirm_password: {
                equalTo: "Passwords not match", 
            },
            email : {
                remote : "Email already in use"
            },
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
});

</script>
