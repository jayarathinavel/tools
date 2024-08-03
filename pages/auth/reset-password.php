<?php
$rootPath = $_SERVER['DOCUMENT_ROOT'];
require_once ($rootPath. '/config/config.php');
isLoggedIn();
$conn = initDb();
$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";
if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(empty(trim($_POST["new_password"]))){
        $new_password_err = "Please enter the new password.";
    } elseif(strlen(trim($_POST["new_password"])) < 4){
        $new_password_err = "Password must have atleast 4 characters.";
    } else{
        $new_password = trim($_POST["new_password"]);
    }
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm the password.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    if(empty($new_password_err) && empty($confirm_password_err)){
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "si", $param_password, $param_id);
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_id = $_SESSION["id"];
            if(mysqli_stmt_execute($stmt)){
                session_destroy();
                header("location: /pages/auth");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($conn);
}
?>
<?php
    $pageTitle = "Reset Password";
    require_once $rootPath . '/pages/includes/admin-pages/header.php';
?>
<div style="display: flex;flex-wrap: wrap;justify-content: center;">
    <div class="">
        <h4 class="text-center">Reset Password</h4>
        <p>Please fill out this form to reset your password.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" class="form-control
                    <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>"
                        value="<?php echo $new_password; ?>">
                <span class="invalid-feedback"><?php echo $new_password_err; ?></span>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control
                    <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group mt-2">
                <input type="submit" class="btn btn-success" value="Submit">
                <a class="btn btn-danger ms-2" href="/pages/admin/dashboard.php">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php
    require_once $rootPath . '/pages/includes/admin-pages/footer.php';
?>
