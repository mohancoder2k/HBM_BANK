<?php
require '../config/db.php';
$uid = $_SESSION['user_id'];

if ($_POST) {
    $amt = $_POST['amount'];
    if ($amt > 0) {
        $pdo->beginTransaction();
        $pdo->prepare("UPDATE accounts SET balance = balance + ? WHERE user_id=?")
            ->execute([$amt,$uid]);
        $pdo->prepare("INSERT INTO transactions (user_id,type,amount,description)
                       VALUES (?,?,?,?)")
            ->execute([$uid,'DEPOSIT',$amt,'Self Deposit']);
        $pdo->commit();
        header("Location: ../dashboard/index.php");
    }
}
?>

<form method="post">
<input name="amount" type="number" step="0.01" required>
<button>Deposit</button>
</form>
