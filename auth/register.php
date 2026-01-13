<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HBM Bank - Register</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .register-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 420px;
            padding: 40px;
        }
        .logo { text-align: center; margin-bottom: 30px; }
        .logo h1 { color: #667eea; font-size: 32px; font-weight: 700; }
        .logo p { color: #6b7280; font-size: 14px; margin-top: 5px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; color: #374151; font-size: 14px; font-weight: 500; margin-bottom: 8px; }
        input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 15px;
            background: #f9fafb;
            transition: all 0.2s;
        }
        input:focus {
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
            transition: transform 0.2s;
        }
        button:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3); }
        .links { text-align: center; margin-top: 20px; font-size: 14px; color: #6b7280; }
        .links a { color: #667eea; text-decoration: none; }
        .links a:hover { color: #764ba2; }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo">
            <h1>HBM Bank</h1>
            <p>Create Your Account</p>
        </div>

        <?php
        session_start();
        require '../config/db.php';
        require '../config/csrf.php';

        $error = '';

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if ($_POST['token'] !== $_SESSION['token']) {
                $error = "Security token mismatch";
            } else {
                $name = trim($_POST['name']);
                $email = trim($_POST['email']);
                $phone = trim($_POST['phone']);
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $account = "HBM" . rand(100000, 999999);

                try {
                    $stmt = $pdo->prepare("SELECT id FROM users WHERE email=?");
                    $stmt->execute([$email]);
                    if ($stmt->rowCount() > 0) {
                        $error = "Email already exists";
                    } else {
                        $pdo->beginTransaction();
                        $pdo->prepare("INSERT INTO users (full_name,email,phone,password,account_number) VALUES (?,?,?,?,?)")
                            ->execute([$name, $email, $phone, $password, $account]);

                        $uid = $pdo->lastInsertId();
                        $pdo->prepare("INSERT INTO accounts (user_id,balance) VALUES (?,0)")
                            ->execute([$uid]);

                        $pdo->commit();
                        header("Location: login.php");
                        exit();
                    }
                } catch (PDOException $e) {
                    $pdo->rollBack();
                    $error = "Registration failed. Please try again.";
                    error_log($e->getMessage());
                }
            }
        }
        ?>

        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
            
            <div class="form-group">
                <label for="name">Full Name</label>
                <input id="name" name="name" placeholder="John Doe" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input id="email" name="email" type="email" placeholder="john@example.com" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input id="phone" name="phone" placeholder="+1234567890" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" placeholder="Min. 8 characters" required>
            </div>

            <button type="submit">Create Account</button>
        </form>

        <div class="links">
            Already have an account? <a href="login.php">Sign in</a>
        </div>
    </div>
</body>
</html>