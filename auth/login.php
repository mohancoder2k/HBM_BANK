<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HBM Bank - Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 420px;
            padding: 40px;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            color: #667eea;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .logo p {
            color: #6b7280;
            font-size: 14px;
            margin-top: 5px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            color: #374151;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.2s;
            background: #f9fafb;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .error-message {
            background: #fee;
            color: #c00;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            border: 1px solid #fcc;
        }

        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        button:active {
            transform: translateY(0);
        }

        .links {
            text-align: center;
            margin-top: 20px;
        }

        .links a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s;
        }

        .links a:hover {
            color: #764ba2;
        }

        .divider {
            margin: 20px 0;
            text-align: center;
            color: #9ca3af;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>HBM Bank</h1>
            <p>Secure Banking Portal</p>
        </div>

        <?php
        session_start();
        require '../config/db.php';

        $error = '';

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Invalid email format";
            } else {
                try {
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
                    $stmt->execute([$email]);
                    $user = $stmt->fetch();

                    if ($user && password_verify($password, $user['password'])) {
                        session_regenerate_id(true);
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_name'] = $user['name'];
                        header("Location: ../dashboard/index.php");
                        exit();
                    } else {
                        $error = "Invalid email or password";
                    }
                } catch (PDOException $e) {
                    $error = "An error occurred. Please try again.";
                    error_log($e->getMessage());
                }
            }
        }
        ?>

        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required 
                       placeholder="Enter your email" 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required 
                       placeholder="Enter your password">
            </div>

            <button type="submit">Sign In</button>
        </form>

        <div class="divider">or</div>

        <div class="links">
            <a href="register.php">Create an account</a> • 
            <a href="forgot-password.php">Forgot password?</a>
        </div>
    </div>
</body>
</html>