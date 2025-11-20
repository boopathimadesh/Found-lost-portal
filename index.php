<?php
require_once 'includes/auth.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$pageTitle = "Home";
?>
<?php include 'includes/header.php'; ?>

<h1>Welcome to KEC Campus Finder</h1>
<p>The official lost and found system for KEC campus.</p>

<div style="display: flex; gap: 20px; margin-top: 30px; flex-wrap: wrap;">
    <div style="flex: 1; min-width: 300px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h2 style="color: var(--primary-blue);">For Students & Staff</h2>
        <p>Report lost items or found items on campus. Our system helps reunite lost items with their owners.</p>
        <a href="login.php" class="btn">Get Started</a>
    </div>
    
    <div style="flex: 1; min-width: 300px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h2 style="color: var(--primary-blue);">How It Works</h2>
        <ol style="margin-left: 20px;">
            <li>Create an account with your @kongu.edu email</li>
            <li>Report lost or found items with details</li>
            <li>Our system matches similar items</li>
            <li>Get notified when your item is found</li>
        </ol>
    </div>
</div>

<div style="margin-top: 40px; background: var(--primary-blue); color: white; padding: 30px; border-radius: 8px;">
    <h2>Why Use KEC Campus Finder?</h2>
    <div style="display: flex; gap: 20px; margin-top: 20px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 250px;">
            <h3><i class="fas fa-shield-alt"></i> Secure</h3>
            <p>Only KEC community members with valid email addresses can access the system.</p>
        </div>
        <div style="flex: 1; min-width: 250px;">
            <h3><i class="fas fa-bolt"></i> Efficient</h3>
            <p>Our matching algorithm helps quickly connect lost items with their owners.</p>
        </div>
        <div style="flex: 1; min-width: 250px;">
            <h3><i class="fas fa-clock"></i> Automated</h3>
            <p>Items are automatically expired after 2 years to keep the database clean.</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>