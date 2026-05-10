<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    if ($_SESSION['role'] === 'admin') {
        redirect('/retail-ease-store/admin/dashboard.php');
    } else {
        redirect('/retail-ease-store/user/dashboard.php');
    }
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);

        if ($check->rowCount() > 0) {
            $error = "This email is already registered.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
            $stmt->execute([$name, $email, $hashedPassword]);

            $success = "Account created successfully. You can now login.";
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="form-box">
        <h2>Create Account</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" onsubmit="return validateRegisterForm();">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" id="name" placeholder="Enter your name">
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" id="email" placeholder="Enter your email">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" id="password" placeholder="Enter password">
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm password">
            </div>

            <button type="submit" class="full-btn">Register</button>
        </form>

        <p class="form-link">
            Already have an account?
            <a href="login.php">Login here</a>
        </p>
    </div>
</div>

<script>
function validateRegisterForm() {
    let name = document.getElementById("name").value.trim();
    let email = document.getElementById("email").value.trim();
    let password = document.getElementById("password").value.trim();
    let confirmPassword = document.getElementById("confirm_password").value.trim();

    if (name === "" || email === "" || password === "" || confirmPassword === "") {
        alert("Please fill all fields.");
        return false;
    }

    if (password.length < 6) {
        alert("Password must be at least 6 characters long.");
        return false;
    }

    if (password !== confirmPassword) {
        alert("Passwords do not match.");
        return false;
    }

    return true;
}
</script>

<?php include 'includes/footer.php'; ?>