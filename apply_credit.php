<?php
include 'db.php';
$message = '';
$payment_schedule = [];

$fin_code = $_GET['fin_code'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fin_code = $_POST['fin_code'];
    $loan_amount = $_POST['loan_amount'];
    $loan_term = $_POST['loan_term'];

    $sql = "SELECT * FROM credit_tablee WHERE fin_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $fin_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $message = "Belə FİN kodlu müştəri tapılmadı!";
    } else {
        $customer = $result->fetch_assoc();
        $salary = $customer['official_salary'] ?? 0;
        $family_count = $customer['family_member_count'] ?? 0;

        $remaining = $salary - 196 - ($family_count * 88);

        $interest_rates = [6=>0.27,12=>0.24,18=>0.21,24=>0.17,36=>0.13];
        $annual_rate = $interest_rates[$loan_term] ?? 0.2;
        $monthly_rate = $annual_rate / 12;

        $n = $loan_term;
        $P = $loan_amount;

        $monthly_payment = $monthly_rate > 0
            ? $P * ($monthly_rate * pow(1+$monthly_rate, $n)) / (pow(1+$monthly_rate, $n) - 1)
            : $P / $n;

        if ($monthly_payment <= $remaining) {
            $message = "Kredit təsdiqləndi! Aylıq ödəniş: ".number_format($monthly_payment,2)." AZN";

            $balance = $P;
            for ($i=1; $i<=$n; $i++) {
                $interest = $balance * $monthly_rate;
                $principal = $monthly_payment - $interest;
                $balance -= $principal;
                if ($balance < 0) $balance = 0;

                $payment_schedule[] = [
                    'month'=>$i,
                    'monthly_payment'=>round($monthly_payment,2),
                    'interest'=>round($interest,2),
                    'principal'=>round($principal,2),
                    'balance'=>round($balance,2)
                ];
            }

            $insert_sql = "INSERT INTO credit_applications (fin_code, loan_amount, loan_term_months) VALUES (?, ?, ?)";
            $stmt_insert = $conn->prepare($insert_sql);
            $stmt_insert->bind_param("sdi",$fin_code,$loan_amount,$loan_term);
            $stmt_insert->execute();
            $stmt_insert->close();
        } else {
            $message = "Kredit rədd edildi! Aylıq ödəniş qalan məbləğdən çoxdur.";
        }
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="az">
<head>
<meta charset="UTF-8">
<title>Kredit Müraciəti</title>
<style>
body { font-family: Arial; padding: 20px; background: #f5f5f5; }
form { background: #fff; padding: 20px; border-radius: 8px; max-width: 400px; margin-bottom:20px; }
input, select, button { width: 100%; padding: 8px; margin-top: 10px; }
button { background: #007bff; color: #fff; border: none; cursor: pointer; }
button:hover { background: #0056b3; }
table { border-collapse: collapse; margin-top: 20px; width: 100%; background: #fff; }
th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
th { background: #007bff; color: #fff; }
p { padding: 10px; background: #e2e3e5; border-radius: 6px; }
</style>
</head>
<body>
<h1>Kredit Müraciəti</h1>
<?php if ($message) echo "<p>$message</p>"; ?>

<form method="post">
<label>FİN kod: <input type="text" name="fin_code" value="<?= htmlspecialchars($fin_code) ?>" required></label>
<label>Kredit məbləği: <input type="number" step="0.01" name="loan_amount" required></label>
<label>Kredit müddəti:
<select name="loan_term" required>
<option value="6">6 ay</option>
<option value="12">12 ay</option>
<option value="18">18 ay</option>
<option value="24">24 ay</option>
<option value="36">36 ay</option>
</select>
</label>
<button type="submit">Müraciət et</button>
</form>

<?php if (!empty($payment_schedule)) : ?>
<h2>Ödəniş Cədvəli</h2>
<table>
<tr><th>Ay</th><th>Aylıq ödəniş</th><th>Əsas borc</th><th>Qalan borc</th></tr>
<?php foreach($payment_schedule as $p): ?>
<tr>
<td><?= $p['month'] ?></td>
<td><?= $p['monthly_payment'] ?></td>
<td><?= $p['principal'] ?></td>
<td><?= $p['balance'] ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>
</body>
</html>