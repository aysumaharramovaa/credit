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
    $official_salary = $_POST['official_salary'] ?: 0;
    $registration_address = $_POST['registration_address'];
    $family_member_count = $_POST['family_member_count'] ?: 0;

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
        header("Location: apply_credit.php?fin_code=$fin_code");
        exit;
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
<title>Müştəri Əlavə Et</title>
<style>
body { font-family: Arial; padding: 20px; background: #f5f5f5; }
form { background: #fff; padding: 20px; border-radius: 8px; max-width: 400px; }
input, button { width: 100%; padding: 8px; margin-top: 10px; }
button { background: #007bff; color: #fff; border: none; cursor: pointer; }
button:hover { background: #0056b3; }
p { padding: 10px; background: #e2e3e5; border-radius: 6px; }
</style>
</head>
<body>
<h1>Müştəri Əlavə Et</h1>
<?php if ($message) echo "<p>$message</p>"; ?>

<form method="post">
    <label>FİN kod: <input type="text" name="fin_code" required></label>
    <label>Ad: <input type="text" name="first_name" required></label>
    <label>Soyad: <input type="text" name="last_name" required></label>
    <label>Ata adı: <input type="text" name="father_name" required></label>
    <label>Doğum tarixi: <input type="date" name="birth_date" required></label>
    <label>İş yeri: <input type="text" name="workplace"></label>
    <label>Rəsmi maaş: <input type="number" step="0.01" name="official_salary"></label>
    <label>Qeydiyyat ünvanı: <input type="text" name="registration_address" required></label>
    <label>Ailə üzvlərinin sayı: <input type="number" name="family_member_count" value="0"></label>
    <button type="submit">Əlavə et və Kredit Müraciətinə Keç</button>
</form>
</body>
</html>