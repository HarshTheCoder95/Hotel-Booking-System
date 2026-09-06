<?php 
require_once 'includes/header.php'; 

// Redirect to dashboard if the user is already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: dashboard.php");
    }
    exit;
}
?>

<div class="auth-container">
    <h2>Create an Account</h2>
    
    <?php if (isset($_SESSION['error_msg'])): ?>
        <div class="alert error">
            <?php 
                echo $_SESSION['error_msg']; 
                unset($_SESSION['error_msg']); 
            ?>
        </div>
    <?php endif; ?>

    <form action="actions/auth.php" method="POST" class="auth-form">
        <input type="hidden" name="action" value="register">
        
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required placeholder="e.g., John Doe">
        </div>
        
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required placeholder="you@example.com">
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required minlength="6" placeholder="At least 6 characters">
        </div>
        
        <button type="submit" class="btn primary">Register</button>
        
        <p class="auth-link">
            Already have an account? <a href="login.php">Login here</a>.
        </p>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>