<?php
// Database connection
$host = 'localhost';
$port = 3307;
$dbName = 'user_auth'; // Database name
$user = 'root';
$password = '';

$dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8";

try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Start session
session_start();

// Guest registration process
if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    try {
        // Check if the email already exists in the users table
        $sql = "SELECT * FROM users WHERE email = :email"; // Updated table name
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            $_SESSION['error'] = 'Email is already registered.';
            header("Location: register.html");
            exit();
        } else {
            // Insert new user into the users table
            $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)"; // Updated table name
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['username' => $username, 'email' => $email, 'password' => $password]);

            header("Location: login.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: register.html");
        exit();
    }
}

// Guest login process (for updating RSVP)
if (isset($_POST['login']) && !isset($_POST['staff_login'])) {
    $login_email = trim($_POST['login_email']);
    $login_password = $_POST['login_password'];

    try {
        // Fetch user from the users table
        $sql = "SELECT * FROM users WHERE email = :email"; // Updated table name
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $login_email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($login_password, $user['password'])) {
            $_SESSION['id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            session_regenerate_id(true); 

            header("Location: profile.php");
            exit();
        } else {
            $_SESSION['error'] = 'Account is not found. Please try again or create an account.'; // Updated error message
            header("Location: login.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: login.php");
        exit();
    }
}

// Staff login process (for admin, bride, and groom)
if (isset($_POST['login']) && isset($_POST['staff_login'])) {
    $staff_email = trim($_POST['login_email']);
    $staff_password = $_POST['login_password'];

    try {
        $sql = "SELECT * FROM staff WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $staff_email]);

        $staff = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($staff && password_verify($staff_password, $staff['password'])) {
            $_SESSION['id'] = $staff['id'];
            $_SESSION['email'] = $staff['email'];
            $_SESSION['role'] = $staff['role'];
            session_regenerate_id(true); 

            // Redirect based on role
            if ($staff['role'] === 'admin') {
                header("Location: admin.php");
                exit();
            } elseif ($staff['role'] === 'bride' || $staff['role'] === 'groom') {
                header("Location: Bride-Groom.php");
                exit();
            }
        } else {
            $_SESSION['error'] = 'Invalid email or password.';
            header("Location: stafflogin.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: stafflogin.php");
        exit();
    }
}
?>
