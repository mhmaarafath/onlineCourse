<?php
require('connection.php');
if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM user WHERE email = '$email' AND password = '$password' AND deleted !='1'";
    $query = mysqli_query($connect, $sql);

    if(mysqli_num_rows($query) > 0){
        $user_id = mysqli_fetch_array($query)['id'];
        setcookie("admin", $user_id, 0);
        header("Location: index.php");        
    } else {
        header("Location: login.php");        
    }
}

 include('inc/header.php');
?>
        <div class="row">
            <div class="col col-md-4 offset-md-4">
                <form action="" method="post">
                <div class="card mt-5">
                    <div class="card-header h3">Admin Login</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="">
                                <i class="fa fa-envelope-o" aria-hidden="true"></i>
                                Email
                            </label>
                            <input type="email" name="email" placeholder="Email" class="form-control">
                        </div>                
                        <div class="form-group">
                            <label for="">
                                <i class="fa fa-key" aria-hidden="true"></i>
                                Password
                            </label>
                            <input type="password" name="password" placeholder="Password" class="form-control">
                        </div>                    
                    </div>
                    <div class="card-footer text-sm-right">
                        <!-- <a href="register.php" class="float-left">New Admin - Register</a> -->
                        <input type="submit" name="login" value="Login" class="btn btn-primary">
                    </div>
                </div>
                </form>
            </div>
        </div>

<?php 
include('inc/footer.php');
?>        
