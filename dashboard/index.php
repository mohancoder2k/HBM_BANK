<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HBM Bank - Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f3f4f6;
            min-height: 100vh;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .nav-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo { font-size: 24px; font-weight: 700; }
        .user-info { display: flex; align-items: center; gap: 20px; }
        .logout-btn {
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            padding: 8px 20px;
            border-radius: 6px;
            color: white;
            text-decoration: none;
            transition: all 0.2s;
        }
        .logout-btn:hover { background: rgba(255,255,255,0.3); }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }
        .hero-section {
            background: white;
            border-radius: 12px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .welcome-text h1 { color: #111827; font-size: 32px; margin-bottom: 8px; }
        .welcome-text p { color: #6b7280; font-size: 16px; }
        .balance-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 16px;
            padding: 30px;
            margin: 30px 0;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }
        .balance-label { font-size: 14px; opacity: 0.9; margin-bottom: 8px; }
        .balance-amount { font-size: 48px; font-weight: 700; margin-bottom: 20px; }
        .account-number {
            background: rgba(255,255,255,0.2);
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .feature-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        .feature-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 16px;
        }
        .feature-title { font-size: 18px; font-weight: 600; color: #111827; margin-bottom: 8px; }
        .feature-desc { font-size: 14px; color: #6b7280; line-height: 1.5; }
        .actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin: 30px 0;
        }
        .action-btn {
            background: white;
            border: 2px solid #667eea;
            color: #667eea;
            padding: 16px;
            border-radius: 10px;
            text-align: center;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.2s;
        }
        .action-btn:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }
        .transactions-section {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .section-title {
            font-size: 22px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f3f4f6;
        }
        .transaction-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px;
            border-bottom: 1px solid #f3f4f6;
            transition: background 0.2s;
        }
        .transaction-item:hover { background: #f9fafb; }
        .transaction-item:last-child { border-bottom: none; }
        .transaction-type {
            font-weight: 600;
            color: #374151;
            text-transform: capitalize;
        }
        .transaction-amount {
            font-weight: 700;
            font-size: 16px;
        }
        .amount-positive { color: #10b981; }
        .amount-negative { color: #ef4444; }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <?php
    session_start();
    require '../config/db.php';
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../auth/login.php");
        exit();
    }

    $uid = $_SESSION['user_id'];
    $user = $pdo->query("SELECT * FROM users WHERE id=$uid")->fetch();
    $bal = $pdo->query("SELECT balance FROM accounts WHERE user_id=$uid")->fetchColumn();
    $txns = $pdo->prepare("SELECT * FROM transactions WHERE user_id=? ORDER BY id DESC LIMIT 10");
    $txns->execute([$uid]);
    ?>

    <nav class="navbar">
        <div class="nav-content">
            <div class="logo">HBM Bank</div>
            <div class="user-info">
                <span>Welcome, <?= htmlspecialchars(explode(' ', $user['full_name'])[0]) ?></span>
                <a href="../auth/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="hero-section">
            <div class="welcome-text">
                <h1>Welcome Back, <?= htmlspecialchars($user['full_name']) ?></h1>
                <p>Your trusted partner for seamless digital banking experience</p>
            </div>

            <div class="balance-card">
                <div class="balance-label">Available Balance</div>
                <div class="balance-amount">₹<?= number_format($bal, 2) ?></div>
                <div class="account-number">Account: <?= htmlspecialchars($user['account_number']) ?></div>
            </div>

            <div class="actions">
                <a href="../transactions/deposit.php" class="action-btn">💰 Deposit Funds</a>
                <a href="../transactions/withdraw.php" class="action-btn">💸 Withdraw Cash</a>
                <a href="../transactions/transfer.php" class="action-btn">🔄 Transfer Money</a>
            </div>
        </div>

        <div class="features">
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <div class="feature-title">Bank-Grade Security</div>
                <div class="feature-desc">Your funds are protected with enterprise-level encryption and multi-layer security protocols ensuring complete peace of mind.</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <div class="feature-title">Instant Transactions</div>
                <div class="feature-desc">Experience lightning-fast transfers and deposits. Your money moves at the speed of life, available 24/7 without delays.</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <div class="feature-title">Real-Time Tracking</div>
                <div class="feature-desc">Monitor every transaction in real-time with detailed insights. Stay informed about your financial activities instantly.</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🌍</div>
                <div class="feature-title">Global Access</div>
                <div class="feature-card">Bank from anywhere in the world with our secure online platform. Your finances are always within reach, anytime.</div>
            </div>
        </div>

        <div class="transactions-section">
            <h2 class="section-title">Recent Activity</h2>
            <?php if ($txns->rowCount() > 0): ?>
                <?php foreach($txns as $t): ?>
                    <div class="transaction-item">
                        <div>
                            <div class="transaction-type"><?= htmlspecialchars($t['type']) ?></div>
                            <div style="font-size: 13px; color: #9ca3af; margin-top: 4px;">
                                <?= date('M d, Y - h:i A', strtotime($t['created_at'])) ?>
                            </div>
                        </div>
                        <div class="transaction-amount <?= in_array($t['type'], ['deposit', 'transfer_in']) ? 'amount-positive' : 'amount-negative' ?>">
                            <?= in_array($t['type'], ['deposit', 'transfer_in']) ? '+' : '-' ?>₹<?= number_format($t['amount'], 2) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <p>No transactions yet. Start by making a deposit!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>