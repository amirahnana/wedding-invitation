<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Upright:wght@300;400;500;600;700&family=Itim&family=Lobster&family=Mr+De+Haviland&family=Playwrite+GB+S:ital,wght@0,100..400;1,100..400&display=swap" rel="stylesheet">
    <link rel="icon" href="assets/gallery4.jpeg">

    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f0f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .cormorant-upright-bold {
            font-family: "Cormorant Upright", serif;
            font-weight: 700;
            font-style: normal;
            font-size: 20px;
        }
        .container {
            background-color: #fafff7;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 350px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        input {
            width: 100%;
            max-width: 300px;
            padding: 10px;
            margin: 10px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            text-align: center;
        }
        button {
            width: 100%;
            padding: 15px;
            background-color: #E75480;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px;
            margin-top: 10px;
        }
        button:hover {
            background-color: #e91e63;
        }
        .link {
            text-align: center;
            margin-top: 15px;
        }
        .link a {
            color: #e91e63;
            text-decoration: underline;
            transition: color 0.3s;
        }
        .link a:hover {
            color: black;
        }
        .error {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
        .top-right-button {
            position: absolute;
            top: 30px;
            right: 30px;
        }
        .btn {
            padding: 10px 20px;
            background-color: #d81b60;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #e91e63;
        }
    </style>
</head>
<body class="cormorant-upright-bold">
    <div class="top-right-button">
        <a href="index.html" class="btn">Go to Main Page</a>
    </div>

    <div class="container">
        <h2>Login</h2>
        
        <!-- Display error message if it exists -->
        <?php
        session_start();
        if (isset($_SESSION['error'])): ?>
            <div class="error">
                <?= htmlspecialchars($_SESSION['error']); ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="POST" action="user.php">
            <input type="email" name="login_email" placeholder="Email" required>
            <input type="password" name="login_password" placeholder="Password" required>
            <button class="cormorant-upright-bold" type="submit" name="login">Login</button>
        </form>
        
        <div class="link">
            <a href="register.php">Don't have an account? Register here</a>
        </div>
    </div>
</body>
</html>
