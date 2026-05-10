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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            logActivity($pdo, $user['id'], 'Login', $user['name'] . ' logged in.');

            if ($user['role'] === 'admin') {
                redirect('/retail-ease-store/admin/dashboard.php');
            } else {
                redirect('/retail-ease-store/user/dashboard.php');
            }
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="form-box">
        <h2>Login</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" onsubmit="return validateLoginForm();">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" id="email" placeholder="Enter your email">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" id="password" placeholder="Enter your password">
            </div>

            <button type="submit" class="full-btn">Login</button>
        </form>

        <p class="form-link">
            Do not have an account?
            <a href="register.php">Register here</a>
        </p>
    </div>
</div>

<script>
function validateLoginForm() {
    let email = document.getElementById("email").value.trim();
    let password = document.getElementById("password").value.trim();

    if (email === "" || password === "") {
        alert("Please enter email and password.");
        return false;
    }

    return true;
}
</script>

<?php include 'includes/footer.php'; ?>