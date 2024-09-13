<?php
// Database connection
$host = "localhost";
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "login"; // Your database name

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to check if email exists (for sign-up)
function emailExists($conn, $email) {
    $sql = "SELECT email FROM user_data WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// Initialize message variables for sign-up and sign-in
$signup_message = '';
$login_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['signup'])) {
        // Sign-up process
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $dob = $_POST['dob'];
        $blood_group = $_POST['blood_group'];
        $location = $_POST['location'];
        $phone_number = $_POST['phone_number'];
        $date_of_registration = date("Y-m-d");

        // Check if any field is empty
        if (empty($name) || empty($email) || empty($password) || empty($dob) || empty($blood_group) || empty($location) || empty($phone_number)) {
            $signup_message = "<div class='alert alert-danger'>All fields are required.</div>";
        } else {
            // Check if email already exists
            if (emailExists($conn, $email)) {
                $signup_message = "<div class='alert alert-danger'>Email already exists.</div>";
            } else {
                // Insert data into the database
                $sql = "INSERT INTO user_data (name, email, password, dob, blood_group, location, phone_number, date_of_registration) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssss", $name, $email, $password, $dob, $blood_group, $location, $phone_number, $date_of_registration);

                if ($stmt->execute()) {
                    $signup_message = "<div class='alert alert-success'>Sign-up successful! You can now sign in.</div>";
                } else {
                    $signup_message = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
                }
            }
        }
    } elseif (isset($_POST['signin'])) {
        // Sign-in process
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Check if any field is empty
        if (empty($email) || empty($password)) {
            $login_message = "<div class='alert alert-danger'>Both fields are required.</div>";
        } else {
            // Check if email and password are correct
            $sql = "SELECT * FROM user_data WHERE email = ? AND password = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $email, $password);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $login_message = "<div class='alert alert-success'>Welcome back, " . $user['name'] . "!</div>";
                header("Location: home.html"); // Redirect to home page
                exit();
            } else {
                $login_message = "<div class='alert alert-danger'>Invalid email or password.</div>";
            }
        }
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <title>Sign-Up & Login</title>
</head>
<body>
    <div class="container" id="container">
        <!-- Sign-Up Form -->
        <div class="form-container sign-up">
            <form action="" method="POST">
                <h1>Create Account</h1>
                <span>or use email for registration</span>
                <input type="text" name="name" placeholder="Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="date" name="dob" placeholder="Date of Birth" required>
                <input type="text" name="blood_group" placeholder="Blood Group" required>
                <input type="text" name="location" placeholder="Location" required>
                <input type="text" name="phone_number" placeholder="Phone Number" required>
                <button type="submit" name="signup">Sign Up</button>
                <!-- Display sign-up message -->
                <?php echo $signup_message; ?>
            </form>
        </div>

        <!-- Sign-In Form -->
<div class="form-container sign-in">
    <form action="" method="POST">
        <h1>SIGN IN</h1>
        <span>or use your email and password</span>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <a href="#">Forgot Your Password?</a>
        <button type="submit" name="signin">Sign In</button>
        <!-- Display sign-in message -->
        <?php echo $login_message; ?>
    </form>
</div>


        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Welcome Back!</h1>
                    <p>To use all features, please sign in with your personal details</p>
                    <button class="hidden" id="login">Sign In</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Hello Friends!</h1>
                    <p>Register with your details to start using the system</p>
                    <button class="hidden" id="register">Sign Up</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <script src="main.js"></script>
</body>
</html>
