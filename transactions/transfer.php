<?php
require '../config/db.php';
$uid = $_SESSION['user_id'];

if ($_POST) {
    $to = $_POST['account'];
    $amt = $_POST['amount'];

    $pdo->beginTransaction();

    $fromBal = $pdo->query("SELECT balance FROM accounts WHERE user_id=$uid")->fetchColumn();
    if ($amt > $fromBal) die("Insufficient balance");

    $toUser = $pdo->prepare("SELECT id FROM users WHERE account_number=?");
    $toUser->execute([$to]);
    $toId = $toUser->fetchColumn();

    $pdo->prepare("UPDATE accounts SET balance=balance-? WHERE user_id=?")->execute([$amt,$uid]);
    $pdo->prepare("UPDATE accounts SET balance=balance+? WHERE user_id=?")->execute([$amt,$toId]);

    $pdo->prepare("INSERT INTO transactions VALUES (NULL,?,?,?,NOW())")
        ->execute([$uid,'TRANSFER',$amt]);

    $pdo->commit();
    header("Location: ../dashboard/index.php");
}
?>

<form method="post">
<input name="account" placeholder="Receiver Account No" required>
<input name="amount" type="number" step="0.01">
<button>Transfer</button>
</form>
