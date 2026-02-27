<?php
include 'db.php'; // DB bağlantısı

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fin_code = $_POST['fin_code'];
    $loan_amount = $_POST['loan_amount'];
    $loan_term = $_POST['loan_term'];

    // 1️⃣ FİN kodunu yoxla
    $check_sql = "SELECT * FROM credit_tablee WHERE fin_code = ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("s", $fin_code);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        // 2️⃣ Kredit müraciətini əlavə et
        $insert_sql = "INSERT INTO credit_applications (fin_code, loan_amount, loan_term_months) VALUES (?, ?, ?)";
        $stmt_insert = $conn->prepare($insert_sql);
        $stmt_insert->bind_param("sdi", $fin_code, $loan_amount, $loan_term);

        if ($stmt_insert->execute()) {
            $message = "Kredit müraciəti uğurla əlavə olundu!";
        } else {
            $message = "Xəta: " . $stmt_insert->error;
        }
        $stmt_insert->close();
    } else {
        $message = "Belə FİN kodlu müştəri tapılmadı!";
    }

    $stmt_check->close();
}
?>

<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <title>Kredit Müraciəti</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        form { background: #fff; padding: 20px; border-radius: 8px; max-width: 400px; }
        input, select, button { width: 100%; padding: 8px; margin-top: 10px; }
        button { background: #007bff; color: #fff; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        p { padding: 10px; background: #e2e3e5; border-radius: 6px; }
    </style>
</head>
<body>
    <h1>Kredit Müraciəti</h1>
    <?php if ($message) echo "<p>$message</p>"; ?>

    <form method="post">
        <label>FİN kod:
            <input type="text" name="fin_code" required placeholder="Məs: 7ABCD12">
        </label>
        <label>Kredit məbləği:
            <input type="number" step="0.01" name="loan_amount" required placeholder="Məs: 1200">
        </label>
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
</body>
</html>