<?php
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Dashboard';

// Get statistics
$websites = readJSON(WEBSITES_FILE);
$subscribers = readJSON(SUBSCRIBERS_FILE);
$contacts = readJSON(CONTACTS_FILE);

$totalWebsites = count($websites);
$totalSubscribers = count($subscribers);
$totalContacts = count($contacts);

// Get recent activity (last 10 items combined)
$recentActivity = [];

foreach ($subscribers as $sub) {
    $recentActivity[] = [
        'type' => 'subscriber',
        'data' => $sub,
        'timestamp' => $sub['created_at']
    ];
}

foreach ($contacts as $contact) {
    $recentActivity[] = [
        'type' => 'contact',
        'data' => $contact,
        'timestamp' => $contact['created_at']
    ];
}

// Sort by timestamp descending
usort($recentActivity, function($a, $b) {
    return strtotime($b['timestamp']) - strtotime($a['timestamp']);
});

// Take only last 10
$recentActivity = array_slice($recentActivity, 0, 10);

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<div class="main-content">
    <div class="top-header">
        <h1>Dashboard</h1>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
        </div>
    </div>
    
    <div class="content-area">
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h4>Total Websites</h4>
                <div class="stat-value"><?php echo $totalWebsites; ?></div>
            </div>
            
            <div class="stat-card success">
                <h4>Total Subscribers</h4>
                <div class="stat-value"><?php echo $totalSubscribers; ?></div>
            </div>
            
            <div class="stat-card warning">
                <h4>Total Contacts</h4>
                <div class="stat-value"><?php echo $totalContacts; ?></div>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="card">
            <div class="card-header">
                <h3>Recent Activity</h3>
            </div>
            <div class="card-body">
                <?php if (empty($recentActivity)): ?>
                    <p class="text-center text-muted">No recent activity</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Details</th>
                                    <th>Website</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentActivity as $activity): ?>
                                    <tr>
                                        <td>
                                            <?php if ($activity['type'] === 'subscriber'): ?>
                                                <span class="badge badge-success">Subscriber</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">Contact</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($activity['type'] === 'subscriber'): ?>
                                                <?php echo htmlspecialchars($activity['data']['email']); ?>
                                                <?php if (!empty($activity['data']['country'])): ?>
                                                    (<?php echo htmlspecialchars($activity['data']['country']); ?>)
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?php echo htmlspecialchars($activity['data']['name']); ?> - 
                                                <?php echo htmlspecialchars($activity['data']['email']); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($activity['data']['website_name']); ?>
                                            <small class="text-muted">
                                                (<?php echo htmlspecialchars($activity['data']['website_type']); ?>)
                                            </small>
                                        </td>
                                        <td>
                                            <?php echo date('M d, Y H:i', strtotime($activity['timestamp'])); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Quick Links -->
        <div class="card">
            <div class="card-header">
                <h3>Quick Actions</h3>
            </div>
            <div class="card-body">
                <div class="flex gap-10">
                    <a href="websites.php" class="btn btn-primary">Manage Websites</a>
                    <a href="subscribers.php" class="btn btn-success">View Subscribers</a>
                    <a href="contacts.php" class="btn btn-secondary">View Contacts</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
