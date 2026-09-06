<?php require_once 'includes/header.php'; ?>

<div class="auth-container">
    <h2>Login now</h2>
    <p class="subtitle">Please sign in to your account</p>
    
    <form action="actions/auth.php" method="POST" class="auth-form">
        <input type="hidden" name="action" value="login">
        
        <div class="form-group">
            <label for="email">Email</label>
            <div class="input-wrapper">
                <i class="fa-regular fa-envelope"></i>
                <input type="email" id="email" name="email" class="with-icon" required placeholder="Enter your email">
            </div>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <div class="input-wrapper">
                <i class="fa-solid fa-lock"></i>
                <input type="password" id="password" name="password" class="with-icon" required placeholder="Enter your password">
            </div>
            <!-- Forgot Password Link -->
            <a href="forgot_password.php" class="forgot-link">Forgot password?</a>
        </div>
        
        <button type="submit" class="btn primary">Sign in</button>
        
        <p class="auth-link">
            Don't have an account? <a href="register.php">Sign up</a>
        </p>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>