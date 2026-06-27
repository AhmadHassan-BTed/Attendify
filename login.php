<?php
session_start();

$loginStatus = "";
$statusHolder = "hideMessage";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "attendance_portal_beenishtasaffar";

    $conn = new mysqli($servername, $username, $password, $dbname,3316);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $loginUserName = $_POST['loginUserName'];
    $loginPassword = $_POST['loginPassword'];

    $stmt = $conn->prepare("SELECT th_regId FROM login WHERE l_username = ? AND l_password = ?");
    $stmt->bind_param("ss", $loginUserName, $loginPassword);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $_SESSION['isLoggedIn'] = true;
        $_SESSION['LoggedUserId'] = $row['th_regId'];

        header("Location: portal.php"); 
        exit();
    } else {

        $loginStatus = "Credentials Don't Exist";
        $statusHolder = "showMessage";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Teacher Login - GC Attendance Portal</title>
    
    <link rel="stylesheet" href=".\css\login.css">
    <style>
        body { margin: 0; padding: 0; font-family: 'Manrope', sans-serif; }
        a { text-decoration: none; color: inherit; }
    </style>
</head>
<body>
    <div>
      <div class="background-image-div">
        <img class="background-image" src=".\images\login_background.jpg" alt="backgroundpic" />
      </div>
      <div class="login-container">
        <div class="grey-box">
          <div class="login-content">
            <div class="one">
              <h1 class="login-heading">Teacher Login</h1>
              <h5 class="au-heading">Welcome to GC Attendance Portal</h5>
            </div>
            
            <span class="<?php echo $statusHolder; ?>"><?php echo $loginStatus; ?></span>
            
            <div class="form">
                <form action="login.php" method="POST" class="login-form">
                  <div class="fields">
                    <input type="text" name="loginUserName" id="username" class="login-input1" placeholder="Username" required />
                    <input type="password" name="loginPassword" id="password" class="login-input2" placeholder="Password" required />
                  </div>
                  <div class="fieldBottomLines">

                    <div class="rememberMeDiv">
                      <input type="checkbox" id="rememberMe" name="rememberMe" class="remember-me-checkbox" />
                      <label for="rememberMe" class="remember-me-label"></label>
                      <label for="rememberMe" class="remember-me-text">Remember me</label>
                    </div>

                    <a href="#" class="forget-password-link">Forget Password?</a>
                  </div>
                  <div class="login-section">
                    <div class="login-button-section">
                      <button type="submit" class="login-button"> Login </button>
                    </div>
                    <h5 class="signin-heading">or sign in with</h5>
                  </div>
                </form>
            </div>
            
            <div class="last">
              <div class="loginOptions">
                <button type="button" class="google-logo-button">
                  <img src=".\images\google_logo.png" alt="Google Logo" class="image-google" />
                </button>
                <button type="button" class="uni-logo-button">
                  <img src=".\images\unilogo.jpg" alt="University Logo" class="image-uni" />
                </button>
              </div>
              <div class="lastBox">
                <p class="last-heading"> Dont have an account?</p>
                <a href="register.php" class="signup"> <span style="font-weight: 700; text-decoration: underline;"> Sign up </span> here </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const statusSpan = document.querySelector('.showMessage');
            if (statusSpan && statusSpan.textContent.trim() !== "") {
                setTimeout(() => {
                    statusSpan.classList.remove('showMessage');
                    statusSpan.classList.add('hideMessage');
                }, 4000);
            }
        });
    </script>
</body>
</html>