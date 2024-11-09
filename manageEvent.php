<?php
// Database connection details
$host = 'localhost';
$port = 3307; // Ensure this port matches if used
$dbName = 'user_auth';
$user = 'root';
$password = '';

// Create the connection using MySQLi
$connection = mysqli_connect($host, $user, $password, $dbName, $port);

// Check if the connection is successful
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Retrieve existing event details
$query = "SELECT venue, dates, times FROM event_details LIMIT 1";
$result = mysqli_query($connection, $query);

// Check if the query executed successfully
if ($result) {
    $event = mysqli_fetch_assoc($result);
    // Set default values if no event data is found
    $venue = $event['venue'] ?? 'Venue not set';
    $dates = $event['dates'] ?? 'Date not set';
    $times = $event['times'] ?? 'Time not set';
} else {
    // In case the query failed
    die("Error executing query: " . mysqli_error($connection));
}

// Handle the form submission if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize user inputs to prevent SQL injection
    $venue = mysqli_real_escape_string($connection, $_POST['venue']);
    $dates = mysqli_real_escape_string($connection, $_POST['dates']);
    $times = mysqli_real_escape_string($connection, $_POST['times']);

    // Update the event details in the database (ensure that the columns are VARCHAR in the DB)
    $update_query = "UPDATE event_details SET venue='$venue', dates='$dates', times='$times' WHERE id=1"; // Assuming event ID is 1
    if (mysqli_query($connection, $update_query)) {
        echo "<p style='color: green;'>Event details updated successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error updating event: " . mysqli_error($connection) . "</p>";
    }}    

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Event</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h2 {
            color: #333;
        }
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: 0 auto;
        }
        label {
            font-size: 1.1em;
            margin-bottom: 5px;
            display: block;
        }
        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
        }
        .top-right-button {
            position: absolute;
            top: 30px;
            right: 30px;
        }
        .top-right-button a {
            display: inline-block;
            background-color: #E75480; /* Change this color as needed */
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 1.1em;
            transition: background-color 0.3s;
        }

        .top-right-button a:hover {
            background-color: #e91e63; 
        }
        .message {
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>

    <div class="top-right-button">
        <a href="index.php" class="btn">Go to Main Page</a>
    </div>

    <h2>Manage Event</h2>

    <form method="POST" action="manageEvent.php">
        <label for="venue">Venue:</label>
        <input type="text" name="venue" id="venue" value="<?php echo htmlspecialchars($venue); ?>" required><br>

        <label for="dates">Date:</label>
        <!-- You can use the formatted date here -->
        <input type="text" name="dates" id="dates" value="<?php echo htmlspecialchars($dates); ?>" required><br>

        <label for="times">Time:</label>
        <input type="text" name="times" id="times" value="<?php echo htmlspecialchars($times); ?>" required><br>

        <button type="submit">Update Event</button>
        
    </form>

</body>
</html>