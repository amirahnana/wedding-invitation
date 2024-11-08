<?php
session_start();

// Database connection details
$host = 'localhost'; 
$port = 3307;
$dbName = 'user_auth'; 
$user = 'root';
$password = '';

//data source name
$dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8";

// CREATE PDO
try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

// Handle edit and delete guest operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit_guest'])) {
        $id = $_POST['id'];
        $rsvp_status = $_POST['rsvp_status'];
        $no_of_guest = $_POST['no_of_guest'];

        if ($rsvp_status === "No") {
            $no_of_guest = 0;
        } else {
        
            if ($no_of_guest < 1) {
                $no_of_guest = 1;
            }
        }

        $sql = "UPDATE guest SET rsvp_status = ?, no_of_guest = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$rsvp_status, $no_of_guest, $id]);
    }

    if (isset($_POST['delete_guest'])) {
        $id = $_POST['id'];

        $sql = "DELETE FROM guest WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
    }
}

$sql = "SELECT id, fullname, email, rsvp_status, no_of_guest FROM guest";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$guests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total number of guests
$totalGuests = 0;
foreach ($guests as $guest) {
    $totalGuests += $guest['no_of_guest'];
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

    <title>Bride & Groom Page</title>
    <style>
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #fff1f5;
            margin: 0;
            padding: 20px;
        }

        .cormorant-upright-bold {
            font-family: "Cormorant Upright", serif;
            font-weight: 700;
            font-style: normal;
            font-size: 20px;
        }

        h1 {
            color: #e91e63;
            text-align: center;
        }
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 6px 9px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #d81b60;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tr:hover {
            background-color: #e9ecef;
        }
        .button {
            padding: 5px 10px;
            background-color: #c126646e;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        .button:hover {
            background-color: #d81b60;
        }
        .delete-button {
            background-color: #dc3545;
        }
        .delete-button:hover {
            background-color: #c82333;
        }

        /* Popup Modal Styles */
        #deleteModal {
            display: none;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 10;
            width: 300px;
            text-align: center;
        }

        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 5;
        }

        .confirm-delete {
            background-color: #dc3545;
            color: white;
        }

        .cancel-delete {
            background-color: #6c757d;
            color: white;
        }
     
        #editForm {
            display: none;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background-color: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            z-index: 10;
            width: 350px;
            text-align: center;
        }

        #editForm h2 {
            font-size: 24px;
            color: #e91e63;
            margin-bottom: 20px;
            font-family: "Cormorant Upright", serif;
        }

        #editForm label {
            font-size: 16px;
            color: #555;
            font-weight: 500;
        }

        #editForm input,
        #editForm select {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 15px;
        }

        #editForm button {
            padding: 10px 20px;
            background-color: #4caf50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 10px;
            font-size: 16px;
            width: 90%;
            transition: background-color 0.3s;
        }

        #editForm button:hover {
            background-color: #388e3c;
        }

        #editForm .close-button {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: transparent;
            color: #888;
            font-size: 20px;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }

        #editForm .close-button:hover {
            color: #e91e63;
        }

        input, select, button {
            display: block;
            margin: 5px auto;
            padding: 10px;
            width: 80%;
            font-size: 16px;
        }
        button {
            width: auto;
        }

        /* Background overlay */
        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 5;
        }

        footer {
            /* background-color: #f8d7da; */
            color: #333;
            padding: 10px;
            text-align: center;
            display: flex;         /* Make the footer a flex container */
            justify-content: flex-end; /* Align items to the right */
            width: 100%;
            box-sizing: border-box; /* Ensures padding doesn’t alter the width */
        }

        .footer-buttons {
            display: flex;
            gap: 20px; /* Space between the buttons */
            align-items: center;
            padding-right: 20px;
        }

        /* Adjust buttons to align in the footer */
        .logout-button,
        .share-button {
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            color: white;
            border-radius: 5px;
        }
        .logout-button:hover {
            background-color: #e91e63;
        }

        .share-button:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
        }

        .logout-button {
            background-color: #d81b60;
            margin-right: auto; /* Moves the button to the far left within flex container */
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            color: white;
            border-radius: 5px;
        }

        .share-button {
            background-color: #4caf50;
        }

        .share-button:hover {
            background-color: #45a049;
        }

        /* Style for the share options */
        .share-container {
        position: relative;
        display: inline-block;
        }

        .share-options {
        display: none;
        position: absolute;
        top: 100%; /* Positions below the button */
        right: 0;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        z-index: 10;
        }

        .share-options a {
            display: block;
            padding: 8px 0;
            text-decoration: none;
            color: #333;
        }
        .share-options a:hover {
            color: #e91e63;
        }

        /* Style for the "Link copied!" pop-up */
        .shared-message {
            display: none;
            position: fixed;
            bottom: 100px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #4caf50;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }


    </style>
</head>
<body class= "cormorant-upright-bold">
    <h1>Bride & Groom Page</h1>

    <h2>Guest List (Total Guests: <?= htmlspecialchars($totalGuests) ?>)</h2>

    <table>
        <thead>
            <tr>
                <!-- <th>ID</th> -->
                <th>Name</th>
                <th>RSVP Status</th>
                <th>Number of Guests</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($guests) > 0): ?>
                <?php foreach ($guests as $row): ?>
                    <tr>
                        
                        <td><?= htmlspecialchars($row['fullname']) ?></td>
                        <td><?= htmlspecialchars($row['rsvp_status']) ?></td>
                        <td><?= htmlspecialchars($row['no_of_guest']) ?></td>
                        <td>
                            <button class="button" onclick="showEditForm('<?= htmlspecialchars($row['id']) ?>', '<?= htmlspecialchars($row['rsvp_status']) ?>', '<?= htmlspecialchars($row['no_of_guest']) ?>')">Edit</button>
                            <button class="button delete-button" onclick="showDeleteModal('<?= htmlspecialchars($row['id']) ?>')">Delete</button>                       
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No guest information available</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div id="deleteModal">
        <h2>Confirm Delete</h2>
        <p>Are you sure you want to delete this guest?</p>
        <form id="deleteForm" method="POST">
            <input type="hidden" name="id" id="deleteId">
            <button type="button" class="button cancel-delete" onclick="hideDeleteModal()">Cancel</button>
            <button type="submit" name="delete_guest" class="button confirm-delete">Delete</button>
        </form>
    </div>

    <!-- Background overlay -->
    <div id="overlay" onclick="hideDeleteModal()"></div>

    <!-- Edit form (pop-up) -->
    <div id="editForm">
    <button class="close-button" onclick="hideEditForm()">×</button>
    <h2>Edit Guest</h2>
    <form method="POST">
        <input type="hidden" name="id" id="editId">
        <label for="editRsvpStatus">RSVP Status:</label>
        <select name="rsvp_status" id="editRsvpStatus" required>
            <option value="Yes">Yes</option>
            <option value="No">No</option>
            <option value="Maybe">Maybe</option>
        </select>
        <label for="editNoOfGuest">Number of Guests:</label>
        <input type="number" name="no_of_guest" id="editNoOfGuest" required min="0">
        <button type="submit" name="edit_guest">Save Changes</button>
    </form>
</div>


    <br>
    <br>
    <br>


    <!-- Background overlay -->
    <div id="overlay" onclick="hideEditForm()"></div>

    <script>
        // Show the edit form as a modal
        function showEditForm(id, rsvp_status, no_of_guest) {
            document.getElementById('editId').value = id;
            document.getElementById('editRsvpStatus').value = rsvp_status;
            document.getElementById('editNoOfGuest').value = no_of_guest;
            document.getElementById('editForm').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        // Hide the edit form
        function hideEditForm() {
            document.getElementById('editForm').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }

        
        // Show the delete modal
        function showDeleteModal(id) {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteModal').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        // Hide the delete modal
        function hideDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }
 

        function toggleShareOptions() {
    const shareOptions = document.getElementById('shareOptions');
    const isDisplayed = shareOptions.style.display === 'block';
    shareOptions.style.display = isDisplayed ? 'none' : 'block';
}

// Hide the share options if clicking outside
document.addEventListener('click', function(event) {
    const shareOptions = document.getElementById('shareOptions');
    const shareButton = document.querySelector('.share-button');
    if (!shareOptions.contains(event.target) && event.target !== shareButton) {
        shareOptions.style.display = 'none';
    }
});

        function showPopup() {
            document.getElementById('sharedMessage').style.display = 'block';
            setTimeout(function() {
                document.getElementById('sharedMessage').style.display = 'none';
            }, 2000);
        }

        function copyRSVPLink() {
            const link = "https://amirahnana/wedding-invitation";
            navigator.clipboard.writeText(link).then(() => {
                showPopup();
            }).catch(err => {
                console.error('Could not copy text: ', err);
            });
        }

    </script>

<footer>
    <div class="footer-buttons">
        <a href="logout.php" class="logout-button">Logout</a>
    </div> 

    <div class="share-container">
        <button onclick="toggleShareOptions()" class="share-button">Share RSVP</button>  
    <!-- Share options next to share button -->
        <div id="shareOptions" class="share-options">
            <a href="https://wa.me/?text=You're%20invited%20to%20Remy%20and%20Melati's%20wedding!%20RSVP%20here:%20https://amirahnana/wedding-invitation" target="_blank">WhatsApp</a>
            <a href="mailto:?subject=Wedding RSVP&body=Please RSVP to our wedding at https://amirahnana/wedding-invitation">Email</a>
            <a href="#" onclick="copyRSVPLink()">Copy link</a>
        </div>
    </div>

    <div id="sharedMessage" class="shared-message">Link copied!</div>
</footer>


</body>
</html>