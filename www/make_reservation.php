<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['phone'], $_POST['reservation_date'], $_POST['reservation_time'], $_POST['party_size'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $reservation_date = $_POST['reservation_date'];
    $reservation_time = $_POST['reservation_time'];
    $party_size = $_POST['party_size'];

    // Insert customer
    $sql = "INSERT INTO Customers (first_name, last_name, email, phone) 
            VALUES ('$first_name', '$last_name', '$email', '$phone')";
    // this if block is executed if customer got added successfully.
    if (mysqli_query($conn, $sql)) {
      $customer_id = mysqli_insert_id($conn); //mysqli_insert_id() returns last inserted item's id. id on the table should be assigned AUTO_INCREMENT

      // Check table availability (simplified: check capacity)
      $sql = "SELECT table_id FROM Tables WHERE capacity >= $party_size 
                AND table_id NOT IN (
                    SELECT table_id FROM Table_Assignments ta
                    JOIN Reservations r ON ta.reservation_id = r.reservation_id
                    WHERE r.reservation_date = '$reservation_date'
                    AND r.reservation_time = '$reservation_time'
                    AND r.status != 'Cancelled'
                ) LIMIT 1";
      $result = mysqli_query($conn, $sql);
      //This if block is executed if the query finds any table_id that is not booked on the selected date and time and result status not be "Cancelled"
      if (mysqli_num_rows($result) > 0) {
        $table = mysqli_fetch_assoc($result);//mysqli_fetch_assoc function takes the result set and returns one row from it. the row is returned in a associative array, column names are the keys, values are the values of the columns for the row
        $table_id = $table['table_id'];
        $sql = "INSERT INTO Reservations (customer_id, reservation_date, reservation_time, party_size, `status`) 
                    VALUES ($customer_id, '$reservation_date', '$reservation_time', $party_size, 'Pending')";
        if (mysqli_query($conn, $sql)) {
          $reservation_id = mysqli_insert_id($conn);

          // Assign table
          $sql = "INSERT INTO Table_Assignments (reservation_id, table_id) 
                    VALUES ($reservation_id, $table_id)";
          if (mysqli_query($conn, $sql)) {
            echo "Reservation successful! Your Reservation ID is: $reservation_id";
          } else {
            echo "Error assigning table: " . mysqli_error($conn);
          }
        } else {
          echo "Error creating reservation: " . mysqli_error($conn);
        }
      } else {
        echo "No tables available for this party size and time.";
      }
    } else {
      echo "Error creating customer: " . mysqli_error($conn);
    }
    mysqli_close($conn);
  }
}
?>
<!DOCTYPE html>
<html>

<head>
  <title>Make a Reservation</title>
</head>

<body>
  <h1>Make a Reservation</h1>
  <form method="POST">
    <label>First Name:</label><input type="text" name="first_name" required><br>
    <label>Last Name:</label><input type="text" name="last_name" required><br>
    <label>Email:</label><input type="email" name="email" required><br>
    <label>Phone:</label><input type="text" name="phone" required><br>
    <label>Date:</label><input type="date" name="reservation_date" required><br>
    <label>Time:</label><input type="time" name="reservation_time" required><br>
    <label>Party Size:</label><input type="number" name="party_size" min="1" required><br>
    <input type="submit" value="Reserve">
  </form>
  <a href="index.php">Back to Home</a>
</body>

</html>