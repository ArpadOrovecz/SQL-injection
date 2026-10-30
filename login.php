<!-- ==================================================
vulnerable to SQL injection: YES
tunnel login: NO
hashing: NO
================================================== -->

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
$conn = new mysqli("127.0.0.1", "root", "mysql", "lab");
if ($conn->connect_error) {
    die("fail: " . $conn->connect_error);
}

// The query is only checked when an actual request was sent
if ($_SERVER["REQUEST_METHOD"] === "POST"){
    // mySQL command to search the DB for the text we have from HTML input
    $sql = "SELECT username FROM users WHERE username = '" . $_POST["username"] . "' AND password = '" . $_POST["password"] . "'";
    $result = $conn->query($sql);

    // // prepare() asks mySQL to get ready to run that command later, and remembers the locations of the ? placeholders
    // $stmt = $conn->prepare("SELECT username FROM users WHERE username = ? AND password = ?");

    // // bind-param() takes 3 parameters to substitute in the placeholders: type of variable, and the 2 actual variables
    // $stmt->bind_param("ss", $_POST["username"], $_POST["password"]);

    // $stmt->execute();
    // $result = $stmt->get_result();

    // If the request was successful print YES, if not NO
    if ($result->num_rows == 1) {
        print("YES");
    }
    else{
        print("NO");
    }
}

?>