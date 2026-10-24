<?php
session_start();
require 'db.php';

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') header("Location: admin.php");
    elseif ($_SESSION['role'] === 'employee') header("Location: employee.php");
    else header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$_POST['username']]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($_POST['password'], $user['password'])) {
        if ($user['status'] === 'pending') {
            $error = "Your account is still pending admin approval.";
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            
            if ($user['role'] === 'admin') header("Location: admin.php");
            elseif ($user['role'] === 'employee') header("Location: employee.php");
            else header("Location: index.php");
            exit;
        }
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Blog System</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 300px; text-align: center; }
        input { width: 90%; padding: 12px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background: #007bff; color: white; border: none; padding: 12px; border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px; margin-top: 10px;}
        button:hover { background: #0056b3; }
        .error { color: #e74c3c; margin-bottom: 15px; font-weight: bold; }
        a { color: #007bff; text-decoration: none; display: block; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>System Login</h2>
        <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <a href="register.php">Need an account? Register here.</a>
    </div>
</body>
</html>