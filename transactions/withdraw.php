<?php
require '../config/db.php';
$uid = $_SESSION['user_id'];

if ($_POST) {
    $amt = $_POST['amount'];
    $bal = $pdo->query("SELECT balance FROM accounts WHERE user_id=$uid")->fetchColumn();

    if ($amt > 0 && $amt <= $bal) {
        $pdo->beginTransaction();
        $pdo->prepare("UPDATE accounts SET balance = balance - ? WHERE user_id=?")
            ->execute([$amt,$uid]);
        $pdo->prepare("INSERT INTO transactions VALUES (NULL,?,?,?,NOW())")
            ->execute([$uid,'WITHDRAW',$amt]);
        $pdo->commit();
        header("Location: ../dashboard/index.php");
    } else {
        echo "Insufficient Balance";
    }
}
?>
<form method="post">
<input name="amount" type="number" step="0.01">
<button>Withdraw</button>
</form>
