<?php
require('connection.php');

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM student WHERE email = '$email' AND password = '$password'";
    $query = mysqli_query($connect, $sql);

    if(mysqli_num_rows($query) > 0){
        $student_id = mysqli_fetch_array($query)['id'];
        setcookie("auth", $student_id, 0);
        setcookie("sweet_alert","valid", 0);
        header("Location: index.php");        
    } else {
        setcookie("sweet_alert","invalid", 0);
        header("Location: index.php");        
    }
}

include('inc/header.php');
?>

    <div class="fixed-top header h4">
            <?=strtoupper("Achievers Online Course")?>
    </div>
    <div class="container">
        <div class="row min-vh-100 justify-content-center align-items-center">
            <div class="col-12 col-md-6 text-right d-none d-sm-block" style="border-right:3px solid #ed1c24">
                <img class="img-fluid" src="admin/img/login_banner.png" alt="" sizes="">
            </div>
            <div class="col-12 col-md-6">
                <form action="" method="post">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-user-o" aria-hidden="true"></i></span>
                        </div>
                        <input type="email" name="email" placeholder="Email" class="form-control">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-key" aria-hidden="true"></i></span>
                        </div>
                        <input type="password" name="password" placeholder="Password" class="form-control">
                    </div>
                    <input type="submit" name="login" value="Login" class="btn btn-primary d-block w-100 btn-lg">
                    <div class="text-center mt-2">
                        <p>Don't have an Account</p>
                        <a href="register.php" class="text-warning"><b>Register</b></a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="fixed-bottom footer">
        <?=strtoupper("The largest college for CIMA and ACCA")?>
    </div>        

<?php 
include('inc/footer.php');
?>        
