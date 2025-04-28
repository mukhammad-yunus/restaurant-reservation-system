<?php
require 'db_connect.php';

// View all reservations
$sql = "SELECT r.reservation_id, r.reservation_date, r.reservation_time, r.party_size, r.status,
               c.first_name, c.last_name
        FROM Reservations r
        JOIN Customers c ON r.customer_id = c.customer_id
        WHERE r.status != 'Cancelled'";
$result = mysqli_query($conn, $sql);

echo "<h2>All Reservations</h2>";
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "ID: " . $row['reservation_id'] . " | Customer: " . $row['first_name'] . " " . $row['last_name'] . 
             " | Date: " . $row['reservation_date'] . " | Time: " . $row['reservation_time'] . 
             " | Party: " . $row['party_size'] . " | Status: " . $row['status'] . "<br>";
    }
} else {
    echo "No active reservations.";
}

// Assign tables
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reservation_id = $_POST['reservation_id'];
    $table_id = $_POST['table_id'];

    $sql = "INSERT INTO Table_Assignments (reservation_id, table_id) 
            VALUES ($reservation_id, $table_id)";
    if (mysqli_query($conn, $sql)) {
        echo "Table assigned successfully.";
    } else {
        echo "Error assigning table: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Reservations</title>
</head>
<body>
    <h2>Assign Table to Reservation</h2>
    <form method="POST">
        <label>Reservation ID:</label><input type="number" name="reservation_id" required><br>
        <label>Table:</label>
        <select name="table_id" required>
            <?php
            $sql = "SELECT table_id, table_number, capacity FROM Tables";
            $result = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='" . $row['table_id'] . "'>" . $row['table_number'] . " (Capacity: " . $row['capacity'] . ")</option>";
            }
            ?>
        </select><br>
        <input type="submit" value="Assign Table">
    </form>
    <a href="index.php">Back to Home</a>
    <?php mysqli_close($conn); ?>
</body>
</html>