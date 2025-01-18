<?php
include '../components/connect.php';

// Current logged-in tutor ID is retrieved from the POST request
$current_teacher_id = $_POST['tutor_id'] ?? null; // Default to null if not provided

// Handle delete request
if (isset($_GET['delete'])) {
    $id_to_delete = $_GET['delete'];

    // Prevent self-deletion
    if ($id_to_delete !== $current_teacher_id) {
        try {
            // Begin transaction to delete tutor and associated data
            $conn->beginTransaction();

            // Delete associated playlists
            $delete_playlists = $conn->prepare("DELETE FROM playlist WHERE tutor_id = ?");
            $delete_playlists->execute([$id_to_delete]);

            // Delete tutor
            $delete_tutor = $conn->prepare("DELETE FROM tutors WHERE id = ?");
            $delete_tutor->execute([$id_to_delete]);

            $conn->commit(); // Commit the transaction

            $message = 'Teacher and associated playlists deleted successfully!';
        } catch (PDOException $e) {
            $conn->rollBack(); // Rollback transaction on failure
            $message = 'Error: ' . $e->getMessage();
        }
    } else {
        $message = 'You cannot delete your own account.';
    }
}

// Fetch all tutors from the database
$teachers_query = $conn->query("SELECT * FROM tutors");
$teachers = $teachers_query->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Teachers</title>
    <style>
    /* General Reset */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f6f9;
    color: #333;
    line-height: 1.6;
    animation: backgroundFade 10s infinite alternate;
}

/* Page Title */
h2 {
    text-align: center;
    margin: 20px 0;
    font-size: 24px;
    color: #2c3e50;
}

/* Table Styling */
table {
    width: 95%;
    margin: 20px auto;
    border-collapse: collapse;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    background-color: #ffffff;
    border-radius: 10px;
    overflow: hidden;
}

th, td {
    padding: 18px;
    text-align: left;
    border-bottom: 1px solid #eaeaea;
}

th {
    background-color: #34495e;
    color: white;
    text-transform: uppercase;
    font-size: 14px;
    letter-spacing: 1px;
}

tr:nth-child(even) {
    background-color: #f8f9fa;
}

tr:hover {
    background-color: #eaeff3;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

td {
    font-size: 14px;
    color: #606060;
}

/* Links */
a {
    text-decoration: none;
    font-weight: 600;
    color: #2980b9;
    transition: color 0.3s ease;
}

a:hover {
    color: #1a5276;
}

/* Delete Button */
.delete-btn {
    color: #e74c3c;
    font-weight: bold;
    border: 1px solid #e74c3c;
    padding: 5px 10px;
    border-radius: 5px;
    background-color: transparent;
    transition: all 0.3s ease;
}

.delete-btn:hover {
    color: #ffffff;
    background-color: #e74c3c;
    text-decoration: none;
}

/* Message Styling */
p {
    text-align: center;
    margin: 15px auto;
    font-size: 16px;
}

p.success {
    color: #27ae60;
    font-weight: bold;
}

p.error {
    color: #c0392b;
    font-weight: bold;
}

/* Background Fade Animation */
@keyframes backgroundFade {
    0% {
        background-color:rgb(26, 205, 236);
    }
    50% {
        background-color:rgb(65, 93, 216);
    }
    100% {
        background-color:rgb(146, 165, 194);
    }
}

    </style>
</head>
<body>
    <h2>Manage Teachers</h2>

    <!-- Display Message -->
    <?php if (isset($message)): ?>
        <p style="color: green;"><?= htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <!-- Hidden form to pass the current tutor ID -->
    <form method="POST" id="current-tutor-form">
        <input type="hidden" name="tutor_id" value="<?= htmlspecialchars($current_teacher_id); ?>">
    </form>

    <table>
        <tr>
            <th>Name</th>
            <th>Profession</th>
            <th>Email</th>
            <th>Action</th>
        </tr>
        <?php foreach ($teachers as $teacher): ?>
            <tr>
                <td><?= htmlspecialchars($teacher['name']); ?></td>
                <td><?= htmlspecialchars($teacher['profession']); ?></td>
                <td><?= htmlspecialchars($teacher['email']); ?></td>
                <td>
                    <?php if ($teacher['id'] !== $current_teacher_id): ?>
                        <a class="delete-btn" href="manage_teachers.php?delete=<?= $teacher['id']; ?>" 
                           onclick="return confirm('Are you sure you want to delete this teacher? This will also delete all associated playlists.');">
                           Delete
                        </a>
                    <?php else: ?>
                        <span>(Yourself)</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
