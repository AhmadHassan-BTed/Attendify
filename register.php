<?php

$message = "Login status will go here"; 

$messageClass = "hideMessage"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "attendance_portal_beenishtasaffar";

    $conn = new mysqli($servername, $username, $password, $dbname, ,3316);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $email = $_POST['email'];
    $userName = $_POST['username'];
    $regId = $_POST['regId'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("INSERT INTO login(th_regId, l_username, l_password, l_recmail) VALUES (?, ?, ?, ?)");

    $stmt->bind_param("ssss", $regId, $userName, $password, $email);

    if ($stmt->execute()) {

        echo "<script>
                alert('The user has Successfully Registered');
                window.location.href = 'login.php';
              </script>";
        exit();
    } else {

        $message = "Error: " . $stmt->error;
        $messageClass = "showMessage"; 

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
    <title>Register - GC Attendance Portal</title>
    
    <link rel="stylesheet" href=".\css\register.css">
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
        <div class="grey-box register-box">
          <div class="login-content">
            <div class="one">
              <h1 class="login-heading">Teacher Register</h1>
              <h5 class="au-heading">Welcome to GC Attendance Portal</h5>
            </div>
            
            <span class="<?php echo $messageClass; ?>"><?php echo $message; ?></span>
            
            <div class="form register-form-container">
                <form action="register.php" method="POST" class="login-form">
                  <div class="fields">
                    <input type="email" name="email" id="email" class="login-input1" placeholder="   Email" required />
                    <input type="text" name="username" id="username" class="login-input2" placeholder="   Username" required />
                    <input type="text" name="regId" id="regId" class="login-input2" placeholder="   RegId" required />
                    <input type="password" name="password" id="password" class="login-input2" placeholder="   Password" required />
                  </div>
                  
                  <div class="login-section" style="margin-top: 2vw;">
                    <div class="login-button-section">
                      <button type="submit" class="login-button"> Register </button>
                    </div>
                  </div>
                </form>
            </div>
            
            <div class="last">
              <div class="lastBox">
                <p class="last-heading"> Already a member?</p>
                <a href="login.php" class="signup"> <span style="font-weight: 700; text-decoration: underline;"> Sign in </span> here </a>
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