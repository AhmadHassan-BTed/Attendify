<?php

$message = "Login status will go here"; 

$messageClass = "hideMessage"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "air_attendance_portal_byted";

    $conn = new mysqli($servername, $username, $password, $dbname);

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
    <title>Register - AU Attendance Portal</title>

    <link rel="stylesheet" href="css/App.css">
    <link rel="stylesheet" href="css/register.css">

    <style>

        body { font-family: 'Manrope', sans-serif; }
        .hideMessage { display: none; }
        .showMessage { display: block; color: red; text-align: center; margin-bottom: 10px; }
        a { text-decoration: none; color: inherit; }
    </style>
</head>
<body>

    <div>
      <h3>Register</h3>

      <form action="register.php" method="POST">

        <span class="<?php echo $messageClass; ?>"><?php echo $message; ?></span>

        <div class="inputdiv">
          <label for="email">Email</label>
            <div class="inputFlex">
              <input type="email" id="email" name="email" placeholder="Enter email" required>
          </div>
        </div>

        <div class="inputdiv">
          <label for="username">Username</label>
            <div class="inputFlex">
              <input type="text" id="username" name="username" placeholder="Enter username" required>
          </div>
        </div>

        <div class="inputdiv">
          <label for="regId">RegId</label>
            <div class="inputFlex">
              <input type="text" id="regId" name="regId" placeholder="Enter RegId" required>
          </div>
        </div>

        <div class="inputdiv">
          <label for="password">Password</label>
            <div class="inputFlex">
              <input type="password" id="password" name="password" placeholder="Enter password" required>
          </div>
        </div>

        <span class="loginButton">
          <button type="submit" class="loginButton"> Register </button>
        </span>

      </form>

      <br/>
      <a href="login.php">Already a member</a>

    </div>

</body>
</html>