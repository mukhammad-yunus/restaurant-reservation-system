<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reservation_id = $_POST['reservation_id'];

    $sql = "SELECT r.reservation_id, r.reservation_date, r.reservation_time, r.party_size, r.status,
                   c.first_name, c.last_name, c.email, c.phone,
                   t.table_number
            FROM Reservations r
            JOIN Customers c ON r.customer_id = c.customer_id
            LEFT JOIN Table_Assignments ta ON r.reservation_id = ta.reservation_id
            LEFT JOIN Tables t ON ta.table_id = t.table_id
            WHERE r.reservation_id = $reservation_id";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo "<h2>Reservation Details</h2>";
        echo "Reservation ID: " . $row['reservation_id'] . "<br>";
        echo "Customer: " . $row['first_name'] . " " . $row['last_name'] . "<br>";
        echo "Email: " . $row['email'] . "<br>";
        echo "Phone: " . $row['phone'] . "<br>";
        echo "Date: " . $row['reservation_date'] . "<br>";
        echo "Time: " . $row['reservation_time'] . "<br>";
        echo "Party Size: " . $row['party_size'] . "<br>";
        echo "Table: " . ($row['table_number'] ? $row['table_number'] : "Not assigned") . "<br>";
        echo "Status: " . $row['status'] . "<br>";
    } else {
        echo "No reservation found with ID: $reservation_id";
    }
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Reservation</title>
</head>
<body>
    <h1>View Reservation</h1>
    <form method="POST">
        <label>Reservation ID:</label><input type="number" name="reservation_id" required><br>
        <input type="submit" value="View">
    </form>
    <a href="index.php">Back to Home</a>
</body>
</html>