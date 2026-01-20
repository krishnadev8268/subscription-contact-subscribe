<?php
/**
 * Sidebar Navigation
 */
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <h2>Central Dashboard</h2>
    </div>
    
    <nav class="sidebar-nav">
        <ul>
            <li>
                <a href="dashboard.php" class="<?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">
                    <span>📊</span> Dashboard
                </a>
            </li>
            <li>
                <a href="websites.php" class="<?php echo $currentPage === 'websites.php' ? 'active' : ''; ?>">
                    <span>🌐</span> Websites
                </a>
            </li>
            <li>
                <a href="subscribers.php" class="<?php echo $currentPage === 'subscribers.php' ? 'active' : ''; ?>">
                    <span>📧</span> Subscribers
                </a>
            </li>
            <li>
                <a href="contacts.php" class="<?php echo $currentPage === 'contacts.php' ? 'active' : ''; ?>">
                    <span>💬</span> Contacts
                </a>
            </li>
            <li>
                <a href="logout.php">
                    <span>🚪</span> Logout
                </a>
            </li>
        </ul>
    </nav>
</aside>
