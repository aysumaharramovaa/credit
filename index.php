<?php
include 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fin_code = $_POST['fin_code'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $father_name = $_POST['father_name'];
    $birth_date = $_POST['birth_date'];
    $workplace = $_POST['workplace'];
    $official_salary = $_POST['official_salary'];
    $registration_address = $_POST['registration_address'];
    $family_member_count = $_POST['family_member_count'];

    $sql = "INSERT INTO credit_tablee
    (fin_code, first_name, last_name, father_name, birth_date, workplace, official_salary, registration_address, family_member_count)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssdsi",
        $fin_code, $first_name, $last_name, $father_name, $birth_date,
        $workplace, $official_salary, $registration_address, $family_member_count
    );

    if ($stmt->execute()) {
        $message = "Müştəri uğurla əlavə olundu!";
    } else {
        $message = "Xəta: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <title>Müştəri Əlavə Et (Credit)</title>
</head>
<body>
    <h1>Müştəri Əlavə Et (Credit Table)</h1>
    <?php if ($message) echo "<p>$message</p>"; ?>

    <form method="post">
        <label>FİN kod: <input type="text" name="fin_code" required></label><br><br>
        <label>Ad: <input type="text" name="first_name" required></label><br><br>
        <label>Soyad: <input type="text" name="last_name" required></label><br><br>
        <label>Ata adı: <input type="text" name="father_name" required></label><br><br>
        <label>Doğum tarixi: <input type="date" name="birth_date" required></label><br><br>
        <label>İş yeri: <input type="text" name="workplace"></label><br><br>
        <label>Rəsmi maaş: <input type="number" step="0.01" name="official_salary"></label><br><br>
        <label>Qeydiyyat ünvanı: <input type="text" name="registration_address" required></label><br><br>
        <label>Ailə üzvlərinin sayı: <input type="number" name="family_member_count" value="0"></label><br><br>
        <button type="submit">Əlavə et</button>
    </form>
</body>
</html>