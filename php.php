<?php
// Database Configuration
$host = "127.0.0.1";
$username = "root";
$password = ""; // Replace with your database password
$dbname = "forsti3";

// Establishing Connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to Fetch Job Postings
function fetchJobPostings($conn) {
    $sql = "SELECT jp.job_posting_id, jp.title, jp.description, jp.salary, jp.location, jp.posting_date, e.company_name 
            FROM job_posting jp
            JOIN employer e ON jp.employer_id = e.employer_id
            ORDER BY jp.posting_date DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='job-posting'>
                <h3>" . htmlspecialchars($row['title']) . "</h3>
                <p><strong>Company:</strong> " . htmlspecialchars($row['company_name']) . "</p>
                <p>" . htmlspecialchars($row['description']) . "</p>
                <p><strong>Salary:</strong> $" . htmlspecialchars($row['salary']) . "</p>
                <p><strong>Location:</strong> " . htmlspecialchars($row['location']) . "</p>
                <p><strong>Posted on:</strong> " . htmlspecialchars($row['posting_date']) . "</p>
            </div><hr>";
        }
    } else {
        echo "<p>No job postings available.</p>";
    }
}

// Handling User Login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        echo "<p>Welcome, " . htmlspecialchars($user['username']) . "!</p>";
    } else {
        echo "<p>Invalid email or password.</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forsti Job Portal</title>
</head>
<body>
    <h1>Forsti Job Portal</h1>

    <!-- Login Form -->
    <form method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit" name="login">Login</button>
    </form>

    <h2>Job Postings</h2>
    <div>
        <?php
        // Reconnect to the database to fetch job postings
        $conn = new mysqli($host, $username, $password, $dbname);
        fetchJobPostings($conn);
        ?>
    </div>
</body>
</html>
