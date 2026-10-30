<!-- ==================================================
vulnerable to SQL injection: NO
tunnel login: YES
hashing: NO
================================================== -->

<?php
session_start();
$teacher_password = "TeacherGate2026!Secure";

if (!isset($_SESSION['teacher_ok'])) {
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['teacher'])) {
        if ($_POST['teacher'] === $teacher_password) {
            $_SESSION['teacher_ok'] = true;
        } else {
            echo "Wrong password<br>";
        }
    }
    if (!isset($_SESSION['teacher_ok'])) {
        echo '<form method="post"><input type="password" name="teacher" placeholder="Teacher password"><button type="submit">Enter</button></form>';
        exit;
    }
}
?>

<form method="post">
    <!-- name -->
    <label for="">username
        <input type="text" name="username">
    </label>

    <!-- password -->
    <label for="">password
        <input type="password" name="password">
    </label>

    <!-- button -->
    <button type="submit">Submit</button>
</form>

<?php
// connecting to DB
$conn = new mysqli("192.168.2.20", "appUser", "appPass123", "lab");
if ($conn->connect_error) {
    die("fail: " . $conn->connect_error);
}

// The query is only checked when an actual request was sent
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["username"])) {
    $stmt = $conn->prepare("SELECT username FROM users WHERE username = ? AND password = ?");
    
    // Let's put the 2 string-type variables that we got from the input in the place of the placeholders in the query
    $stmt->bind_param("ss", $_POST["username"], $_POST["password"]);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        print("YES");
    }
    else{
        print("NO");
    }
} 
    
?>