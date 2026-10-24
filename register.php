<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $raw_password = $_POST['password'];
    $role = $_POST['role'];
    
    $pattern = '/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{8,}$/';

    // 1. Check password strength first
    if (!preg_match($pattern, $raw_password)) {
        $error = "Password must be at least 8 characters long, contain at least 1 uppercase letter, 1 number, and 1 special character.";
    } else {
        // 2. ONLY if password is good, check for duplicate usernames
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        
        if ($stmt->fetch()) {
            $error = "Username already exists. Please choose another.";
        } else {
            // 3. ONLY if password is good AND username is unique, insert the user
            $password = password_hash($raw_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role, status) VALUES (?, ?, ?, 'pending')");
            $stmt->execute([$username, $password, $role]);
            $success = "Registration successful! Please wait for an admin to approve your account.";
        }
    }
}
?>
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - Blog System</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 300px; text-align: center; }
        input, select { width: 90%; padding: 12px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background: #28a745; color: white; border: none; padding: 12px; border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px; margin-top: 10px;}
        button:hover { background: #218838; }
        .error { color: #e74c3c; margin-bottom: 15px; font-weight: bold; }
        .success { color: #27ae60; margin-bottom: 15px; font-weight: bold; }
        a { color: #007bff; text-decoration: none; display: block; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Create Account</h2>
        <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>
        <?php if(isset($success)) echo "<div class='success'>$success</div>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Choose a Username" required>
            <input type="password" name="password" placeholder="Create a Password" required>
            <select name="role" required>
                <option value="user">Reader (User)</option>
                <option value="employee">Writer (Employee)</option>
            </select>
            <button type="submit">Register</button>
        </form>
        <a href="login.php">Already have an account? Login here.</a>
    </div>
</body>
</html>