<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reservation_id = $_POST['reservation_id'];

    $sql = "UPDATE Reservations SET status = 'Cancelled' WHERE reservation_id = $reservation_id";
    if (mysqli_query($conn, $sql)) {
        echo "Reservation $reservation_id cancelled successfully.";
    } else {
        echo "Error cancelling reservation: " . mysqli_error($conn);
    }
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cancel Reservation</title>
</head>
<body>
    <h1>Cancel Reservation</h1>
    <form method="POST">
        <label>Reservation ID:</label><input type="number" name="reservation_id" required><br>
        <input type="submit" value="Cancel">
    </form>
    <a href="index.php">Back to Home</a>
</body>
</html>