<?php
include 'db.php';

$fin_code = '7ABCD12';
$first_name = 'Aysu';
$last_name = 'Maharramova';
$father_name = 'Elman';
$birth_date = '2005-04-12';
$workplace = 'Kapital Bank';
$official_salary = 1200.00;
$registration_address = 'Bakı şəhəri, Nəsimi rayonu';
$family_member_count = 4;


$sql = "INSERT INTO customers 
(fin_code, first_name, last_name, father_name, birth_date, workplace, official_salary, registration_address, family_member_count)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ssssssdsi", 
    $fin_code, 
    $first_name, 
    $last_name, 
    $father_name, 
    $birth_date, 
    $workplace, 
    $official_salary, 
    $registration_address, 
    $family_member_count
);


if ($stmt->execute()) {
    echo "Müştəri uğurla əlavə olundu!";
} else {
    echo "Xəta: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>