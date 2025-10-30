<?php
include 'includes/header.php';
require_once "includes/classes/admin-class.php";

// Ensure only admins can access this page
if ($_SESSION['user_role'] !== 'admin') {
    // Redirect non-admin users to the dashboard
    echo '<script>window.location.href = "index.php";</script>';
    exit;
}

$admins = new Admins($dbh);
$monitoring_data = $admins->getEmployerMonitoringData();
?>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<style>
    :root {
        --primary-blue: #3498db;
        --primary-green: #2ecc71;
        --primary-red: #e74c3c;
        --dark-green: #27ae60;
        --dark-red: #c0392b;
        --text-dark: #2c3e50;
        --text-medium: #34495e;
        --text-light: #7f8c8d;
        --border-color: #ecf0f1;
        --shadow-light: rgba(0, 0, 0, 0.08);
        --shadow-medium: rgba(0, 0, 0, 0.12);
        --card-radius: 12px;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f8f9fa;
        padding-bottom: 20px;
    }

    .page-title {
        text-align: center;
        font-size: clamp(1.8rem, 4vw, 2.2rem);
        font-weight: 600;
        color: var(--text-dark);
        margin: 15px 0 25px;
        padding: 0 15px;
    }

    .monitoring-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 25px;
        padding: 0 20px 20px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .employer-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: var(--card-radius);
        box-shadow: 0 5px 15px var(--shadow-light);
        padding: 25px;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        border: 1px solid rgba(255, 255, 255, 0.8);
        position: relative;
        overflow: hidden;
    }

    .employer-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-blue), var(--primary-green));
    }

    .employer-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 25px var(--shadow-medium);
    }

    .employer-header {
        display: flex;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--border-color);
    }

    .employer-avatar {
        position: relative;
        margin-right: 18px;
    }

    .employer-avatar img {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #e9ecef;
        transition: border-color 0.3s ease;
    }

    .employer-card:hover .employer-avatar img {
        border-color: var(--primary-blue);
    }

    .employer-status {
        position: absolute;
        bottom: 5px;
        right: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid white;
    }

    .status-active {
        background-color: var(--primary-green);
    }

    .status-inactive {
        background-color: var(--text-light);
    }

    .employer-details {
        flex-grow: 1;
    }

    .employer-details h3 {
        margin: 0 0 5px 0;
        font-size: 1.3rem;
        font-weight: 600;
        color: var(--text-medium);
    }

    .employer-details .location {
        display: flex;
        align-items: center;
        font-size: 0.9rem;
        color: var(--text-light);
        margin: 0;
    }

    .location i {
        margin-right: 6px;
        font-size: 0.8rem;
    }

    .stats-container {
        width: 100%;
    }

    .stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 0;
        border-bottom: 1px solid var(--border-color);
        transition: background-color 0.2s ease;
        border-radius: 6px;
        padding-left: 15px;
        padding-right: 10px;
        margin-bottom: 5px;
    }

    .stat-item:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }

    .stat-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .stat-label {
        display: flex;
        align-items: center;
        font-size: 0.9rem;
        color: var(--text-medium);
        font-weight: 500;
    }

    .stat-label i {
        margin-right: 8px;
        width: 16px;
        text-align: center;
        font-size: 0.85rem;
    }

    .stat-value {
        font-size: 1.1rem;
        font-weight: 600;
    }

    /* Color coding for different stats */
    .total-customers .stat-value { color: var(--primary-blue); }
    .paid-customers .stat-value { color: var(--primary-green); }
    .unpaid-customers .stat-value { color: var(--primary-red); }
    .monthly-paid .stat-value { color: var(--dark-green); }
    .monthly-unpaid .stat-value { color: var(--dark-red); }
    .total-balance .stat-value { color: var(--dark-red); }


    /* Colorful left borders */
    .stat-item {
        border-left: 4px solid transparent;
    }

    .total-customers { border-left-color: var(--primary-blue); }
    .paid-customers { border-left-color: var(--primary-green); }
    .unpaid-customers { border-left-color: var(--primary-red); }
    .monthly-paid { border-left-color: var(--dark-green); }
    .monthly-unpaid { border-left-color: var(--dark-red); }
    .total-balance { border-left-color: var(--dark-red); }

    .no-data {
        text-align: center;
        font-size: 1.2rem;
        color: var(--text-light);
        margin: 50px auto;
        padding: 40px 20px;
        background-color: white;
        border-radius: var(--card-radius);
        box-shadow: 0 4px 12px var(--shadow-light);
        max-width: 500px;
        grid-column: 1 / -1;
    }

    .no-data i {
        font-size: 3rem;
        color: #bdc3c7;
        margin-bottom: 15px;
        display: block;
    }

    /* Responsive adjustments */
    @media (max-width: 1200px) {
        .monitoring-container {
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        }
    }

    @media (max-width: 768px) {
        .monitoring-container {
            grid-template-columns: 1fr;
            gap: 20px;
            padding: 0 15px 20px;
        }
        
        .employer-card {
            padding: 20px;
        }
        
        .employer-avatar img {
            width: 60px;
            height: 60px;
        }
        
        .employer-details h3 {
            font-size: 1.2rem;
        }
        
        .stat-item {
            padding: 12px 0;
            padding-left: 12px;
            padding-right: 8px;
        }
    }

    @media (max-width: 480px) {
        .monitoring-container {
            padding: 0 10px 15px;
        }
        
        .page-title {
            margin: 10px 0 20px;
        }
        
        .employer-header {
            flex-direction: column;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .employer-avatar {
            margin-right: 0;
            margin-bottom: 15px;
        }
        
        .employer-avatar img {
            width: 80px;
            height: 80px;
        }
        
        .stat-label, .stat-value {
            font-size: 0.95rem;
        }
    }

    /* Animation for card entrance */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .employer-card {
        animation: fadeInUp 0.5s ease forwards;
    }

    /* Stagger animation for multiple cards */
    .monitoring-container .employer-card:nth-child(1) { animation-delay: 0.1s; }
    .monitoring-container .employer-card:nth-child(2) { animation-delay: 0.2s; }
    .monitoring-container .employer-card:nth-child(3) { animation-delay: 0.3s; }
    .monitoring-container .employer-card:nth-child(4) { animation-delay: 0.4s; }
</style>

<h1 class="page-title">Employee Monitoring</h1>

<div class="monitoring-container">
    <?php if (!empty($monitoring_data)): ?>
        <?php foreach ($monitoring_data as $data): ?>
            <div class="employer-card">
                <div class="employer-header">
                    <div class="employer-avatar">
                        <img src="<?php echo !empty($data->info->profile_pic) ? htmlspecialchars($data->info->profile_pic) : '1112.jpg'; ?>" alt="Employer Avatar">
                        <div class="employer-status status-active"></div>
                    </div>
                    <div class="employer-details">
                        <h3><?php echo htmlspecialchars($data->info->full_name); ?></h3>
                        <p class="location">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo htmlspecialchars($data->info->location); ?>
                        </p>
                    </div>
                </div>

                <div class="stats-container">
                    <div class="stat-item total-customers">
                        <span class="stat-label">
                            <i class="fas fa-users"></i>
                            Total Customers
                        </span>
                        <span class="stat-value"><?php echo $data->stats['total_customers']; ?></span>
                    </div>
                    <div class="stat-item paid-customers">
                        <span class="stat-label">
                            <i class="fas fa-check-circle"></i>
                            Paid Customers
                        </span>
                        <span class="stat-value"><?php echo $data->stats['paid_customers']; ?></span>
                    </div>
                    <div class="stat-item unpaid-customers">
                        <span class="stat-label">
                            <i class="fas fa-exclamation-circle"></i>
                            Unpaid Customers
                        </span>
                        <span class="stat-value"><?php echo $data->stats['unpaid_customers']; ?></span>
                    </div>
                    <div class="stat-item monthly-paid">
                        <span class="stat-label">
                            <i class="fas fa-money-bill-wave"></i>
                            Month Paid Collection
                        </span>
                        <span class="stat-value">₱<?php echo number_format($data->stats['monthly_paid_collection'], 2); ?></span>
                    </div>
                    <div class="stat-item monthly-unpaid">
                        <span class="stat-label">
                            <i class="fas fa-money-bill"></i>
                            Month Unpaid Amount
                        </span>
                        <span class="stat-value">₱<?php echo number_format($data->stats['monthly_unpaid_collection'], 2); ?></span>
                    </div>
                    <div class="stat-item total-balance">
                        <span class="stat-label">
                            <i class="fas fa-balance-scale"></i>
                            Total Balance
                        </span>
                        <span class="stat-value">₱<?php echo number_format($data->stats['total_balance'], 2); ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-data">
            <i class="fas fa-user-slash"></i>
            <p>No employee data to display.</p>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>