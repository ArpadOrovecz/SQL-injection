<?php
session_start();
?>

<!-- 
 ===========================================================================================================
                                            FILE DESCRIPTION
===========================================================================================================
1 - CSS

2 - TEACHER LOGIN
        
2 - VULNERABLE BACKEND
    * vulnerable to SQL injection: YES
    * tunnel login: YES
    * hashing: NO
    
3 - SECURE BACKEND
    * vulnerable to SQL injection: NO
    * tunnel login: YES
    * hashing: YES 

4 - STATE CHOOSER & LOGIN FRONTEND

5 - HOME BUTTON CLICKED 
-->



<!-- 
===========================================================================================================
                                                    1 - CSS
=========================================================================================================== 
-->
<style>
    body {
        font-family: system-ui, sans-serif;
        background: #f4f4f4;
        margin: 0;
        padding: 40px 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-height: 100vh;
    }
    #login-container, .card {
        background: #fff;
        padding: 40px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        width: fit-content;
        max-width: 420px;
        text-align: center;
    }
    #login-container {
        margin-top: 20vh;
    }
    form {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        margin: 20px 0;
    }
    input, button {
        padding: 10px 16px;
        font-size: 16px;
        border-radius: 4px;
        border: 1px solid #ccc;
    }
    button {
        background: #333;
        color: #fff;
        border: none;
        cursor: pointer;
        margin-top: 8px;
    }
    button:hover {
        background: #555;
    }
    .error {
        color: #c00;
        font-weight: bold;
        margin: 0 0 6px 0;
        text-align: left;
        width: 100%;
    }
    .form-column form {
        align-items: flex-start;
    }
    img {
        display: block;
        margin: 20px auto;
        max-width: 280px;
    }
    ol {
        text-align: left;
        margin: 20px auto;
        max-width: 400px;
    }
    .choice-buttons {
        display: flex;
        gap: 16px;
        justify-content: center;
    }
    .instructions {
        max-width: 380px;
    }
    .instructions p {
        margin: 0 0 8px 0;
        text-align: left;
    }
    .success-screen {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 70vh;
    }
    html {
        scroll-snap-type: y mandatory;
        scroll-behavior: smooth;
    }
    .choice-section,
    .login-area {
        scroll-snap-align: center;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        box-sizing: border-box;
    }
    .choice-section {
        background: #e8e8e8;
        padding: 30px;
        border-radius: 8px;
        margin-bottom: 40px;
    }
    .login-area {
        background: #fff;
        padding: 50px 60px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        max-width: 900px;
        width: 90%;
    }
    .form-column {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .scroll-hint {
        position: absolute;
        left: 0;
        right: 0;
        text-align: center;
        margin: 0;
        padding: 0;
        line-height: 1;
        font-size: 1em;
        z-index: 2;
    }
    .login-area > .scroll-hint {
        top: 10px;
    }
    .scroll-hint .arrow,
    .scroll-hint div {
        margin: 0;
        padding: 0;
        line-height: 1;
        display: block;
    }
    .scroll-hint .arrow {
        font-size: 1.3em;
    }
    .choice-section > p {
        margin-top: -100px;
    }
</style>





<?php
// ===========================================================================================================
//                                   2 - TEACHER LOGIN
// ===========================================================================================================
$teacher_password = "TeacherGate2026!Secure";

// flag that remembers whether this browser already entered the correct teacher password
if (!isset($_SESSION['teacher_ok'])) {
    $error = "";
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['teacher'])) {
        if ($_POST['teacher'] === $teacher_password) {
            $_SESSION['teacher_ok'] = true;
        } else {
            $error = "Wrong password";
        }
    }

    // teacher login HTML
    if (!isset($_SESSION['teacher_ok'])) {
        echo '
        <div class="success-screen">
            <div id="login-container">
                <p class="error">' . $error . '</p>
                <form method="post">
                    <input type="password" name="teacher" placeholder="Teacher password">
                    <button type="submit">Enter</button>
                </form>
            </div>
        </div>';
    exit;}
}


// connecting to DB
// $conn = new mysqli("192.168.2.20", "appUser", "appPass123", "lab");
$conn = new mysqli("127.0.0.1", "root", "mysql", "lab");
if ($conn->connect_error) {
    die("fail: " . $conn->connect_error);
}





// ===========================================================================================================
//                                             3 - HOME BUTTON CLICKED 
// ===========================================================================================================
if (isset($_POST["home-button"])) {
    unset($_SESSION['version']);
}




// ===========================================================================================================
//                                             4 - VULNERABLE BACKEND
// ===========================================================================================================
if (isset($_SESSION['version']) && $_SESSION['version'] === 'vulnerable') {
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["username"])) {
        $sql = "SELECT username FROM users WHERE username = '" . $_POST["username"] . "' AND password = '" . $_POST["password"] . "'";
        try {
            $result = $conn->query($sql);
            if ($result && $result->num_rows == 1) {
                $login_message = "Welcome. You are now logged in to this top-secret database.";
            } else {
                $login_message = "Login failed.";
            }
        } catch (mysqli_sql_exception $e) {
            $login_message = "Login failed.";
        }
    }
}



// ================================================================================================================
//                                              5 - SECURE BACKEND
// ================================================================================================================
if (isset($_SESSION['version']) && $_SESSION['version'] === 'secure') {
    // The query is only checked when an actual request was sent
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["username"])) {
        // Since SQL cannot handle hashing, we need to do that manually. For that, we need the password column selected. 
        // When we will ask for the whole row with fetch_assoc(), it will contain this column.
        // Then we can handle its hashing manually
        $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->bind_param("s", $_POST["username"]);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            // fetch_assoc() pulls the single matching row out of that result and turns it into the array ($row).    
            $row = $result->fetch_assoc();

            // hashing
            if (password_verify($_POST["password"], $row["password"])) {
                $login_message = "Welcome. You are now logged in to this top-secret database.";
            } 
            else {
                $login_message = "Login failed.";
            }
        }

        else {
            $login_message = "Login failed.";
        }
    } 
}




?>
<!-- 
===========================================================================================================
                                        6 - STATE CHOOSER & LOGIN FRONTEND
=========================================================================================================== 
-->
<?php
if (!isset($login_message) || $login_message !== "Welcome. You are now logged in to this top-secret database.") {
?>

<div class="choice-section">
    <p>Welcome attacker! <br>Are you in the mood for compromising a webapp by SQL injection? You are at the best place! Choose your target.</p>
    <img src="hacker.png" alt="Cybercriminal" style="display:block; margin:20px auto; max-width:320px;">
    <div class="choice-buttons">
        <form method="post">
            <input type="hidden" name="version" value="vulnerable">
            <button type="submit">Vulnerable login</button>
        </form>

        <form method="post">
            <input type="hidden" name="version" value="secure">
            <button type="submit">Secure login</button>
        </form>
    </div>
</div>

<?php
}
?>

<?php
if (isset($_POST["version"])) {
    $_SESSION['version'] = $_POST["version"];
}

// Home button clicked
if (isset($login_message) && $login_message === "Welcome. You are now logged in to this top-secret database.") {
    echo '
    <p>' . $login_message . '</p>

    <img src="topsecret.png" alt="TOP SECRET" style="display:block; margin:20px auto; max-width:300px;">
    
    <form method="post">
        <input type="hidden" name="home-button">
        <button type="submit">Home</button>
    </form>';
}

else if (isset($_SESSION['version'])) {
    echo '
    <div class="login-area">
        <div class="scroll-hint">
            <div class="arrow">ˆ</div>
            <div>scroll up to choose login</div>
        </div>

        <div style="display:flex; gap:80px; align-items:flex-start; width:100%; justify-content:center;">
            <div class="instructions">
                <p>Try the ' . $_SESSION['version'] . ' login below:</p>
                <ol>
                    <li>Try to log in with random credentials to test the authentication.</li>
                    <br>
                    <li>' . ($_SESSION['version'] == 'secure' ? 'Archibald' : 'Mortimer') . ' has access already to the top-secret database. Log in as ' . ($_SESSION['version'] == 'secure' ? 'Archibald' : 'Mortimer') . ':
                        <ul>
                            <li>Username: 
                            ' . ($_SESSION['version'] == 'secure' ? 'Archibald' : 'Mortimer') . '
                            </li>
                            <li>Password: ' . ($_SESSION['version'] == 'secure' ? 'topSecret' : 'letMeInPlease') . '</li>
                        </ul>
                    </li>
                    <br>
                    <li>Then use a malicious SQL code snippet to get in the naughty way: 
                        <u>\' OR \'1\'=\'1\' LIMIT 1 -- </u> (there is a space at the end)
                    </li>
                </ol>
            </div>
            <div class="form-column">
                <form method="post">
                    ' . (isset($login_message) ? '<p class="error">' . $login_message . '</p>' : '') . '
                    <label>username
                        <input type="text" name="username">
                    </label>
                    <label>password
                        <input type="password" name="password">
                    </label>
                    <button type="submit">Submit</button>
                </form>
            </div>
        </div>
    </div>';
}
?>


<script>
    const loginArea = document.querySelector('.login-area');
    const failed = document.querySelector('.error');

    if (loginArea) {
        loginArea.scrollIntoView({
            behavior: failed ? 'instant' : 'smooth',
            block: 'center'
        });
    }
</script>
