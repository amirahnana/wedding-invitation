<?php
session_start();

// Ensure the admin is logged in before accessing the dashboard
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header("Location: stafflogin.php");
    exit;
}

// Database connection
$host = 'localhost'; 
$port = 3307;
$dbName = 'user_auth'; 
$user = 'root';
$password = '';

$dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8";

try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

// Initialize error messages
$fullnameError = '';
$emailError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_report'])) {
    $reportType = $_POST['report_type'];
    if ($reportType === 'rsvp_report') {
        // Fetch RSVP report data
    } elseif ($reportType === 'guest_list_report') {
        // Fetch guest list data
    }
}





// Handle adding a new guest
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_guest'])) {
    $fullname = strtoupper(trim($_POST['fullname']));
    $email = trim($_POST['email']);
    $rsvp_status = $_POST['rsvp_status'] ?? 'Maybe';
    // $rsvp_status = isset($_POST['rsvp_status']) ? $_POST['rsvp_status'] : 'Maybe';
$no_of_guest = ($_POST['no_of_guest'] !== '') ? (int)$_POST['no_of_guest'] : 0;
    // $rsvp_status = $_POST['rsvp_status'];
    // $no_of_guest = ($_POST['no_of_guest'] !== '') ? (int)$_POST['no_of_guest'] : 0;

    // Check for duplicate fullname
    $sql = "SELECT COUNT(*) FROM guest WHERE fullname = :fullname";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['fullname' => $fullname]);
    $fullnameExists = $stmt->fetchColumn() > 0;

    // Check for duplicate email
    $sql = "SELECT COUNT(*) FROM guest WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    $emailExists = $stmt->fetchColumn() > 0;

    if ($fullnameExists) {
        $fullnameError = "This user already exists.";
    }
    if ($emailExists) {
        $emailError = "This email already exists.";
    }

    // Validate no_of_guest based on rsvp_status
    if ($rsvp_status === 'Yes' && $no_of_guest < 1) {
        $fullnameError = "Number of guests must be at least 1 for 'Yes'.";
    } elseif ($rsvp_status === 'No' && $no_of_guest !== 0) {
        $fullnameError = "Number of guests must be 0 for 'No'.";
    }

    // Only insert if there are no duplicate entries and no validation errors
    if (!$fullnameExists && !$emailExists && !$fullnameError) {
        // Insert guest into the database
        $sql = "INSERT INTO guest (fullname, email, rsvp_status, no_of_guest) VALUES (:fullname, :email, :rsvp_status, :no_of_guest)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'fullname' => $fullname,
            'email' => $email,
            'rsvp_status' => $rsvp_status,
            'no_of_guest' => $no_of_guest
        ]);
    }
}

// Handle deleting a guest
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    $id = (int)$_POST['guest_id'];

    // Delete guest from the database
    $sql = "DELETE FROM guest WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
}

// Handle editing a guest
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_guest'])) {
    $id = (int)$_POST['guest_id'];
    $fullname = strtoupper(trim($_POST['fullname']));
    $email = trim($_POST['email']);
    $rsvp_status = $_POST['rsvp_status'];
    $no_of_guest = ($_POST['no_of_guest'] !== '') ? (int)$_POST['no_of_guest'] : 0;

    // Validate no_of_guest based on rsvp_status
    if ($rsvp_status === 'Yes' && $no_of_guest < 1) {
        $fullnameError = "Number of guests must be at least 1 for 'Yes'.";
    } elseif ($rsvp_status === 'No' && $no_of_guest !== 0) {
        $fullnameError = "Number of guests must be 0 for 'No'.";
    }

    // Update guest in the database only if there are no validation errors
    if (!$fullnameError) {
        $sql = "UPDATE guest SET fullname = :fullname, email = :email, rsvp_status = :rsvp_status, no_of_guest = :no_of_guest WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'fullname' => $fullname,
            'email' => $email,
            'rsvp_status' => $rsvp_status,
            'no_of_guest' => $no_of_guest,
            'id' => $id
        ]);
    }
}

// Query to fetch guest data sorted by fullname
$sql = "SELECT id, fullname, email, rsvp_status, no_of_guest FROM guest ORDER BY fullname ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$guests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total number of guests
$totalGuests = 0;
foreach ($guests as $guest) {
    $totalGuests += $guest['no_of_guest'];
}

// Report generation and document management logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['generate_report'])) {
        $reportType = $_POST['report_type'];
        header('Content-Type: text/csv');
        header("Content-Disposition: attachment; filename=$reportType.csv");
        
        $output = fopen('php://output', 'w');
        if ($reportType === 'rsvp_report') {
            fputcsv($output, ['Name', 'RSVP Status']);
            $sql = "SELECT fullname, rsvp_status FROM guest";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($output, $row);
            }
        } elseif ($reportType === 'guest_list_report') {
            fputcsv($output, ['Name', 'Email', 'Number of Guests']);
            $sql = "SELECT fullname, email, no_of_guest FROM guest";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($output, $row);
            }
        }
        fclose($output);
        exit;
    }

    if (isset($_POST['upload_document'])) {
        $documentType = $_POST['document_type'];
        $documentDir = 'uploaded_documents';
        
        if (!is_dir($documentDir)) {
            mkdir($documentDir, 0777, true);
        }

        $documentName = $documentType . '_' . basename($_FILES['document']['name']);
        $targetFile = $documentDir . '/' . $documentName;

        if (move_uploaded_file($_FILES['document']['tmp_name'], $targetFile)) {
            echo "<p>Document uploaded successfully.</p>";
        } else {
            echo "<p>Failed to upload document.</p>";
        }
    }
}

// CSV Generation for RSVP Report
if (isset($_POST['download_rsvp_report'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="rsvp_report.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Name', 'RSVP Status']);
    
    $query = "SELECT fullname, rsvp_status FROM guest";
    $stmt = $pdo->query($query);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [$row['fullname'], $row['rsvp_status']]);
    }
    fclose($output);
    exit;
}

// CSV Generation for Guest List Report
if (isset($_POST['download_guest_list_report'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="guest_list_report.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Name', 'Email', 'Number of Guests']);
    
    $query = "SELECT fullname, email, no_of_guest FROM guest";
    $stmt = $pdo->query($query);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [$row['fullname'], $row['email'], $row['no_of_guest']]);
    }
    fclose($output);
    exit;
}

// if (isset($_POST['reportType'])) {
//     $reportType = $_POST['reportType'];

//     if ($reportType === 'guest_list') {
//         // Fetch Guest List data
//         $sql = "SELECT fullname, email, no_of_guest FROM guest";
//         $result = $conn->query($sql);

//         echo "<h2>Guest List Report</h2>";
//         echo "<table border='1'>";
//         echo "<tr><th>Full Name</th><th>Email</th><th>Number of Guests</th></tr>";

//         if ($result->num_rows > 0) {
//             // Output data of each row
//             while($row = $result->fetch_assoc()) {
//                 echo "<tr><td>" . $row["fullname"] . "</td><td>" . $row["email"] . "</td><td>" . $row["no_of_guest"] . "</td></tr>";
//             }
//         } else {
//             echo "<tr><td colspan='3'>No data available</td></tr>";
//         }
//         echo "</table>";

//     } elseif ($reportType === 'rsvp') {
//         // Fetch RSVP data
//         $sql = "SELECT fullname, rsvp_status FROM guest";
//         $result = $conn->query($sql);

//         echo "<h2>RSVP Report</h2>";
//         echo "<table border='1'>";
//         echo "<tr><th>Full Name</th><th>RSVP Status</th></tr>";

//         if ($result->num_rows > 0) {
//             // Output data of each row
//             while($row = $result->fetch_assoc()) {
//                 echo "<tr><td>" . $row["fullname"] . "</td><td>" . $row["rsvp_status"] . "</td></tr>";
//             }
//         } else {
//             echo "<tr><td colspan='2'>No data available</td></tr>";
//         }
//         echo "</table>";
//     }
// }

// Display reports as tables based on the selected report type
if (isset($_POST['reportType'])) {
    $reportType = $_POST['reportType'];

    if ($reportType === 'guest_list') {
        // Fetch Guest List data from the database
        $sql = "SELECT fullname, email, no_of_guest FROM guest ORDER BY fullname ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $guests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Display Guest List Report as a table
        echo "<h2>Guest List Report</h2>";
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>Full Name</th><th>Email</th><th>Number of Guests</th></tr>";

        if ($guests) {
            foreach ($guests as $guest) {
                echo "<tr><td>" . htmlspecialchars($guest['fullname']) . "</td>";
                echo "<td>" . htmlspecialchars($guest['email']) . "</td>";
                echo "<td>" . htmlspecialchars($guest['no_of_guest']) . "</td></tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No data available</td></tr>";
        }
        echo "</table>";

    } elseif ($reportType === 'rsvp') {
        // Fetch RSVP data from the database
        $sql = "SELECT fullname, rsvp_status FROM guest ORDER BY fullname ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $guests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Display RSVP Report as a table
        echo "<h2>RSVP Report</h2>";
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>Full Name</th><th>RSVP Status</th></tr>";

        if ($guests) {
            foreach ($guests as $guest) {
                echo "<tr><td>" . htmlspecialchars($guest['fullname']) . "</td>";
                echo "<td>" . htmlspecialchars($guest['rsvp_status']) . "</td></tr>";
            }
        } else {
            echo "<tr><td colspan='2'>No data available</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Invalid report type selected.</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Upright:wght@300;400;500;600;700&family=Itim&family=Lobster&family=Mr+De+Haviland&family=Playwrite+GB+S:ital,wght@0,100..400;1,100..400&display=swap" rel="stylesheet">
    <link rel="icon" href="assets/gallery4.jpeg">

    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f0f6;
            text-align: center;
            margin: 0;
            padding-bottom: 60px;
            position: relative;
        }

        .cormorant-upright-bold {
            font-family: "Cormorant Upright", serif;
            font-weight: 700;
            font-style: normal;
            font-size: 20px;
        }

        h1 {
            color: #E75480;
        }
        table {
            margin: 0 auto;
            border-collapse: collapse;
            width: 80%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #E75480;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        .logout-button {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background-color: #d81b60;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            text-decoration: none;
            border-radius: 5px;
            z-index: 10;
        }
        .logout-button:hover {
            background-color: #e91e63;
        }
        .error {
            color: red;
        }
        footer {
            position: absolute;
            bottom: 0;
            right: 0;
            padding: 10px;
            background-color: #fff; /* Optional: to match the body */
            box-shadow: 0 -1px 5px rgba(0,0,0,0.1);
        }
        .delete-button {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }
        .delete-button:hover {
            background-color: darkred;
        }
        .edit-button {
            background-color: #2F2F3D;
            color: #FFFFFF;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }
        .edit-button:hover {
            background-color: #4B4B5E;
        }
        /* Modal Styles */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgba(0,0,0,0.4); 
            padding-top: 60px; 
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 80%; 
            text-align: center;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .modal-button {
            padding: 10px 15px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .modal-button.confirm {
            background-color: #4CAF50; /* Yes button in green */
            color: white;
        }
        .modal-button.confirm:hover {
            background-color: #45a049; /* Darker green on hover */
        }
        .modal-button.cancel {
            background-color: #f0f0f0; /* Cancel button */
            color: #333;
        }
        .modal-button.cancel:hover {
            background-color: #ddd;
        }
    </style>
    <script>
        function openEditModal(id, fullname, email, rsvp_status, no_of_guest) {
            document.getElementById("modal").style.display = "block";
            document.getElementById("guest_id").value = id;
            document.getElementById('edit_fullname').value = fullname;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_rsvp_status').value = rsvp_status;
            document.getElementById('edit_no_of_guests').value = no_of_guest;
            updateEditNoOfGuests(); // Call to set read-only status based on RSVP status
        }

        function closeEditModal() {
            document.getElementById("modal").style.display = "none";
        }

        function openDeleteModal(id) {
    document.getElementById("deleteModal").style.display = "block";
    document.getElementById("delete_guest_id").value = id;
}

function closeDeleteModal() {
    document.getElementById("deleteModal").style.display = "none";
}

function updateNoOfGuests() {
    var rsvpStatus = document.getElementById('rsvp_status').value;
    var noOfGuestsInput = document.getElementById('no_of_guest');

    if (rsvpStatus === 'Yes') {
        noOfGuestsInput.min = 1; // Minimum for Yes
        noOfGuestsInput.value = ''; // Clear value for manual input
        noOfGuestsInput.readOnly = false; // Allow input
    } else if (rsvpStatus === 'No') {
        noOfGuestsInput.value = 0; // Default value for No
        noOfGuestsInput.readOnly = true; // Make input read-only
    } else if (rsvpStatus === 'Maybe') {
        noOfGuestsInput.readOnly = false; // Allow input for Maybe
        noOfGuestsInput.min = 0; // Minimum is 0 for Maybe
        noOfGuestsInput.value = ''; // Clear value for manual input
    }
}

function updateEditNoOfGuests() {
    var rsvpStatus = document.getElementById('edit_rsvp_status').value;
    var noOfGuestsInput = document.getElementById('edit_no_of_guests');

    if (rsvpStatus === 'Yes') {
        noOfGuestsInput.value = 1; // Default value for Yes
        noOfGuestsInput.min = 1; // Minimum for Yes
        noOfGuestsInput.readOnly = false; // Allow input
    } else if (rsvpStatus === 'No') {
        noOfGuestsInput.value = 0; // Default value for No
        noOfGuestsInput.readOnly = true; // Make input read-only
    } else if (rsvpStatus === 'Maybe') {
        noOfGuestsInput.readOnly = false; // Allow input for Maybe
        noOfGuestsInput.min = 0; // Minimum is 0 for Maybe
        noOfGuestsInput.value = ''; // Clear value for manual input
    }
}

// Inside the openEditModal function, you already have this line:
updateEditNoOfGuests(); // Call to set read-only status based on RSVP status

function printReport() {
    const printContents = document.querySelector('table').outerHTML;
    const originalContents = document.body.innerHTML;

    document.body.innerHTML = `<h1>Guest Report</h1>${printContents}`;
    window.print();
    document.body.innerHTML = originalContents;
}


    </script>
</head>
<body class="cormorant-upright-bold">
    <h1>Admin Dashboard</h1>
    
    <form method="POST" action="" style="width: 80%; margin: 20px auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
    <h2 style="color: black;">Add New Guest</h2>
    
    <div style="margin-bottom: 15px;">
        <input type="text" name="fullname" placeholder="Full Name" required style="width: calc(100% - 20px); padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 16px;">
        <span class="error" style="color: red; font-size: 14px;"><?php echo $fullnameError; ?></span>
    </div>
    
    <div style="margin-bottom: 15px;">
        <input type="email" name="email" placeholder="Email" required style="width: calc(100% - 20px); padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 16px;">
        <span class="error" style="color: red; font-size: 14px;"><?php echo $emailError; ?></span>
    </div>
    
    <div style="margin-bottom: 15px;">
        <select name="rsvp_status" id="rsvp_status" onchange="updateNoOfGuests()" required style="width: calc(100% - 20px); padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 16px;">
            <option value="" disabled selected>Select RSVP Status</option>
            <option value="Yes">Yes</option>
            <option value="Maybe" selected>Maybe</option>
            <option value="No">No</option>
        </select>
    </div>

    <div style="margin-bottom: 15px;">
        <input type="number" name="no_of_guest" placeholder="Number of Guests" id="no_of_guest" min="0" required style="width: calc(100% - 20px); padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 16px;" readonly>
    </div>

    <button class="cormorant-upright-bold" type="submit" name="add_guest" style="width: calc(100% - 20px); padding: 10px; background-color: #E75480; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;">Add Guest</button>
    </form>

    <br>
    <br>
    <br>

    <h2>Guest List</h2><p></p>
    <h4>Total Guests: <?= htmlspecialchars($totalGuests) ?></h4>
    <!-- <p>Total Guests: <?= htmlspecialchars($totalGuests) ?></p> -->
<table>
    <tr>
        <th>Full Name</th>
        <th>Email</th>
        <th>RSVP Status</th>
        <th>No. of Guests</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($guests as $guest): ?>
    <tr>
        <td><?php echo htmlspecialchars($guest['fullname']); ?></td>
        <td><?php echo htmlspecialchars($guest['email']); ?></td>
        <td><?php echo htmlspecialchars($guest['rsvp_status']); ?></td>
        <td><?php echo htmlspecialchars($guest['no_of_guest']); ?></td>
        <td>
            <button class="edit-button" onclick="openEditModal(<?php echo $guest['id']; ?>, '<?php echo addslashes($guest['fullname']); ?>', '<?php echo addslashes($guest['email']); ?>', '<?php echo addslashes($guest['rsvp_status']); ?>', <?php echo $guest['no_of_guest']; ?>)">Edit</button>
            <button type="button" class="delete-button" onclick="openDeleteModal(<?php echo $guest['id']; ?>)">Delete</button>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeDeleteModal()">&times;</span>
        <h2>Delete Guest</h2>
        <p>Are you sure you want to delete this guest?</p>
        <form method="POST" action="">
            <input type="hidden" name="guest_id" id="delete_guest_id">
            <button class="cormorant-upright-bold" type="submit" class="modal-button confirm" name="confirm_delete">Yes</button>
            <button class="cormorant-upright-bold" type="button" class="modal-button cancel" onclick="closeDeleteModal()">Cancel</button>
        </form>
    </div>
</div>

<div id="modal" class="modal">
    <div class="modal-content" style="background-color: #fff; border-radius: 8px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); padding: 20px; width: 400px; margin: auto;">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2 style="color: #E75480; text-align: center;">Edit Guest Information</h2>
        <form method="POST" action="" style="display: flex; flex-direction: column; gap: 15px;">
            <input type="hidden" name="guest_id" id="guest_id">
            <input type="text" name="fullname" id="edit_fullname" placeholder="Full Name" required style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            <input type="email" name="email" id="edit_email" placeholder="Email" required style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            <select name="rsvp_status" id="edit_rsvp_status" onchange="updateEditNoOfGuests()" required style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                <option value="" disabled selected>Select RSVP Status</option>
                <option value="Yes">Yes</option>
                <option value="Maybe">Maybe</option>
                <option value="No">No</option>
            </select>
            <input type="number" name="no_of_guest" id="edit_no_of_guests" min="0" placeholder="Number of Guests" required style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            <button class="cormorant-upright-bold" type="submit" name="edit_guest" style="background-color: #E75480; color: white; border: none; padding: 10px; border-radius: 5px; cursor: pointer;">Save Changes</button>
        </form>
    </div>
</div>

    <br>
    <br>
    <br>

    <footer>
        <a href="logout.php" class="logout-button">Logout</a>
    </footer>

<!-- Report Generation Section -->
<h3>Generate Reports</h3>
<form method="post" action="admin.php">
    <label for="report_type">Select Report Type:</label>
    <select name="report_type" id="report_type">
        <option value="rsvp_report">RSVP Report</option>
        <option value="guest_list_report">Guest List Report</option>
    </select>
    <button type="submit" name="generate_report">Generate Report</button>
</form>







<!-- Document Management Section -->
<h3>Upload Documents</h3>
<form method="POST" action="admin.php" enctype="multipart/form-data">
    <select name="document_type">
        <option value="invitation_template">Invitation Template</option>
        <option value="seating_arrangement">Seating Arrangement</option>
    </select>
    <input type="file" name="document" required>
    <button type="submit" name="upload_document">Upload Document</button>
</form>

<h3>Access Uploaded Documents</h3>
<ul>
    <?php
    // Display links to uploaded documents in each category
    $documentDir = 'uploaded_documents';
    if (is_dir($documentDir)) {
        $files = scandir($documentDir);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                echo "<li><a href=\"$documentDir/$file\" download>$file</a></li>";
            }
        }
    }
    ?>
</ul>
<div style="text-align: center; margin-top: 20px;">
<!-- <form method="post" style="display: inline;">
    <button type="submit" name="download_rsvp_report" class="button">Download RSVP Report (CSV)</button>
</form>
<form method="post" style="display: inline;">
    <button type="submit" name="download_guest_list_report" class="button">Download Guest List Report (CSV)</button>
</form> -->

    <form method="post" style="display: inline;">
        <button type="submit" name="download_rsvp_report" class="button">Download RSVP Report (CSV)</button>
    </form>
    <form method="post" style="display: inline;">
        <button type="submit" name="download_guest_list_report" class="button">Download Guest List Report (CSV)</button>
    </form>
    <button onclick="printReport()" class="button">Print & Save as PDF</button>
</div>

</body>
</html>
