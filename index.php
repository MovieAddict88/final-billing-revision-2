<?php
include 'includes/header.php';
require_once "includes/classes/admin-class.php";

$admins = new Admins($dbh);

// Check user role from session
$user_role = $_SESSION['user_role'] ?? 'admin';

if ($user_role == 'employer') {
    // Employer Dashboard
    $employer_id = $_SESSION['user_id'];
    
    // Pagination setup
    $customers_per_page = 10;
    $current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $offset = ($current_page - 1) * $customers_per_page;
    
    // Fetch paginated customers
    $customers = $admins->fetchCustomersByEmployerPage($employer_id, $offset, $customers_per_page);
    $total_customers = $admins->countCustomersByEmployer($employer_id);
    $total_pages = ceil($total_customers / $customers_per_page);
    
    $products = $admins->fetchProductsByEmployer($employer_id);
?>
<h3>Employer Dashboard</h3>
<style>
    .table-custom thead {
        background-color: #008080;
        color: white;
    }
    .table-custom .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .action-btn {
        width: 80px;
        margin-bottom: 5px;
    }

    .progress-container {
        width: 100%;
        padding: 10px;
    }
    .progress-item {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }
    .progress-label {
        width: 80px;
        font-size: 16px;
    }
    .progress-bar-background {
        flex-grow: 1;
        background-color: #f0f0f0;
        height: 20px;
        border-radius: 5px;
        margin: 0 10px;
    }
    .progress-bar-fill {
        height: 100%;
        border-radius: 5px;
        text-align: right;
        color: white;
        font-weight: bold;
        padding-right: 5px;
    }
    .progress-value {
        width: 40px;
        font-size: 16px;
        text-align: right;
    }

    /* Enhanced table styling for professional look */
    .table-container {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    
    .professional-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        min-width: 1200px; /* Force horizontal scroll on smaller screens */
    }
    
    .professional-table thead th {
        background: #2c3e50;
        color: white;
        font-weight: 600;
        padding: 12px 15px;
        text-align: left;
        border: none;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    .professional-table tbody tr {
        border-bottom: 1px solid #e8e8e8;
        transition: background-color 0.2s ease;
    }
    
    .professional-table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .professional-table tbody tr:nth-child(even) {
        background-color: #fafafa;
    }
    
    .professional-table tbody tr:nth-child(even):hover {
        background-color: #f1f3f4;
    }
    
    .professional-table td {
        padding: 12px 15px;
        vertical-align: middle;
        border: none;
        word-wrap: break-word;
        max-width: 200px;
    }
    
    .professional-table .text-nowrap {
        white-space: nowrap;
    }
    
    .professional-table .login-code {
        font-family: 'Courier New', monospace;
        font-size: 12px;
        word-break: break-all;
        max-width: 150px;
    }
    
    .professional-table .amount {
        text-align: right;
        font-family: 'Courier New', monospace;
        font-weight: 600;
    }
    
    .professional-table .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .status-paid { background: #d4edda; color: #155724; }
    .status-unpaid { background: #f8d7da; color: #721c24; }
    .status-balance { background: #fff3cd; color: #856404; }
    .status-pending { background: #cce5ff; color: #004085; }
    
    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 5px;
        min-width: 120px;
    }
    
    .scrollable-table-wrapper {
        overflow-x: auto;
        overflow-y: visible;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        margin-bottom: 20px;
        -webkit-overflow-scrolling: touch;
    }
    
    .scrollable-table-wrapper::-webkit-scrollbar {
        height: 8px;
    }
    
    .scrollable-table-wrapper::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    .scrollable-table-wrapper::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }
    
    .scrollable-table-wrapper::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
    
    /* Responsive design */
    @media (max-width: 768px) {
        .professional-table {
            font-size: 12px;
            min-width: 1000px;
        }
        
        .professional-table th,
        .professional-table td {
            padding: 8px 10px;
        }
        
        .action-btn {
            width: 70px;
            font-size: 11px;
            padding: 4px 6px;
        }
        
        .professional-table .login-code {
            font-size: 10px;
            max-width: 120px;
        }
    }
    
    @media (min-width: 1200px) {
        .professional-table {
            min-width: auto;
            width: 100%;
        }
        
        .scrollable-table-wrapper {
            overflow-x: visible;
        }
    }
    
    /* Pagination Styling */
    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .pagination-container nav {
        flex: 1;
        display: flex;
        justify-content: center;
    }
    
    .pagination-info {
        min-width: 200px;
        text-align: right;
    }
    
    .pagination {
        margin: 0;
    }
    
    .pagination .page-link {
        color: #2c3e50;
        border-color: #dee2e6;
        padding: 6px 12px;
        transition: all 0.2s;
    }
    
    .pagination .page-link:hover {
        background-color: #e9ecef;
        border-color: #dee2e6;
        color: #2c3e50;
    }
    
    .pagination .page-item.active .page-link {
        background-color: #2c3e50;
        border-color: #2c3e50;
        color: white;
        font-weight: 600;
    }
    
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: #fff;
        border-color: #dee2e6;
    }
    
    @media (max-width: 768px) {
        .pagination-container {
            flex-direction: column;
            text-align: center;
        }
        
        .pagination-info {
            text-align: center;
            width: 100%;
        }
        
        .pagination .page-link {
            padding: 4px 8px;
            font-size: 12px;
        }
    }
</style>

<div class="row employer-stack-laptop">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">Your Assigned Customers</h4>
            </div>
            <div class="panel-body">
                <div class="scrollable-table-wrapper">
                    <table class="professional-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Contact</th>
                                <th>Login Code</th>
                                <th>Due Date</th>
                                <th class="text-nowrap">Paid (₱)</th>
                                <th class="text-nowrap">Balance (₱)</th>
                                <th>Status</th>
                                <th>Actions</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($customers): ?>
                                <?php foreach ($customers as $customer): ?>
                                    <tr>
                                        <td class="text-nowrap"><?php echo htmlspecialchars($customer->full_name); ?></td>
                                        <td><?php echo htmlspecialchars($customer->address); ?></td>
                                        <td class="text-nowrap"><?php echo htmlspecialchars($customer->contact); ?></td>
                                        <td class="login-code"><?php echo htmlspecialchars($customer->login_code); ?></td>
                                        <td class="text-nowrap"
                                            <?php
                                            $dueDate = $customer->due_date;
                                            $status = $customer->status;
                                            $style = '';
                                            if ($status == 'Paid') {
                                                $style = 'style="background-color: #D4EFDF;"'; // Light Green
                                            } else if ($dueDate) {
                                                $today = new DateTime();
                                                $dueDateObj = new DateTime($dueDate);
                                                $today->setTime(0,0,0);
                                                $dueDateObj->setTime(0,0,0);

                                                if ($dueDateObj < $today) {
                                                    $style = 'style="background-color: #8B0000; color: white;"'; // Dark Red for overdue
                                                } else {
                                                    $interval = $today->diff($dueDateObj);
                                                    $days = $interval->days;

                                                    if ($days <= 3) {
                                                        $style = 'style="background-color: #FADBD8;"'; // Light Red
                                                    } elseif ($days <= 7) {
                                                        $style = 'style="background-color: #FDEBD0;"'; // Light Orange
                                                    } else {
                                                        $style = 'style="background-color: #D6EAF8;"'; // Light Blue
                                                    }
                                                }
                                            }
                                            echo $style;
                                            ?>
                                        ><?php echo htmlspecialchars($customer->due_date); ?></td>
                                        <td class="amount"><?php echo htmlspecialchars(number_format($customer->total_paid, 2)); ?></td>
                                        <td class="amount"><?php echo htmlspecialchars(number_format($customer->total_balance, 2)); ?></td>
                                        <td>
                                            <?php
                                            $status_class = 'status-' . strtolower($customer->status);
                                            echo '<span class="status-badge ' . $status_class . '">' . htmlspecialchars($customer->status) . '</span>';
                                            ?>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="pay.php?customer=<?php echo $customer->id; ?>&action=bill" class="btn btn-primary btn-sm action-btn">Invoice</a>
                                                <a href="pay.php?customer=<?php echo $customer->id; ?>" class="btn btn-info btn-sm action-btn">Bill</a>
                                                <?php if ($customer->total_balance > 0): ?>
                                                    <a href="manual_payment.php?customer=<?php echo $customer->id; ?>" class="btn btn-warning btn-sm action-btn">
                                                        <?php echo ($customer->total_paid > 0) ? 'Pay Balance' : 'Pay'; ?>
                                                    </a>
                                                <?php elseif ($customer->status != 'Paid' && $customer->status != 'Partial'): ?>
                                                    <a href="manual_payment.php?customer=<?php echo $customer->id; ?>" class="btn btn-success btn-sm action-btn">Pay</a>
                                                <?php endif; ?>
												<button type="button" class="btn btn-primary btn-sm action-btn" onclick="openRemarkModal(<?php echo $customer->id; ?>, '<?php echo htmlspecialchars($customer->remarks); ?>')">Remark</button>
												<?php
													$dueDate = new DateTime($customer->due_date);
													$today = new DateTime();
													if ($dueDate < $today) {
														echo '<a href="disconnect_customer.php?customer_id=' . $customer->id . '" class="btn btn-danger btn-sm action-btn">DISCONNECT</a>';
													}
												?>
                                            </div>
                                        </td>
										<td><?php echo htmlspecialchars($customer->remarks); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fa fa-users fa-2x mb-2"></i><br>
                                            No customers found for this location.
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if ($total_pages > 1): ?>
                <div class="pagination-container">
                    <nav aria-label="Customer pagination">
                        <ul class="pagination pagination-sm">
                            <?php if ($current_page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=1" aria-label="First">
                                        <span aria-hidden="true">&laquo;&laquo;</span>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $current_page - 1; ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php
                            // Show page numbers with ellipsis for large page counts
                            $page_range = 2; // Show 2 pages on each side of current page
                            $start_page = max(1, $current_page - $page_range);
                            $end_page = min($total_pages, $current_page + $page_range);
                            
                            // Show first page and ellipsis if needed
                            if ($start_page > 1) {
                                echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
                                if ($start_page > 2) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                            }
                            
                            // Show page numbers
                            for ($i = $start_page; $i <= $end_page; $i++):
                            ?>
                                <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php
                            endfor;
                            
                            // Show last page and ellipsis if needed
                            if ($end_page < $total_pages) {
                                if ($end_page < $total_pages - 1) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                                echo '<li class="page-item"><a class="page-link" href="?page=' . $total_pages . '">' . $total_pages . '</a></li>';
                            }
                            ?>
                            
                            <?php if ($current_page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $current_page + 1; ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $total_pages; ?>" aria-label="Last">
                                        <span aria-hidden="true">&raquo;&raquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <div class="pagination-info">
                        <span class="text-muted">
                            Showing <?php echo (($current_page - 1) * $customers_per_page + 1); ?> 
                            to <?php echo min($current_page * $customers_per_page, $total_customers); ?> 
                            of <?php echo $total_customers; ?> customers
                        </span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row employer-stack-laptop">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">Products Availed by Your Customers</h4>
            </div>
            <div class="panel-body">
                <div class="scrollable-table-wrapper">
                    <table class="professional-table">
                        <thead>
                            <tr>
                                <th>Package Name</th>
                                <th class="text-nowrap">Fee (₱)</th>
                                <th>Number of Customers</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($products): ?>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($product->name); ?></td>
                                        <td class="amount"><?php echo htmlspecialchars(number_format($product->fee, 2)); ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-primary" style="font-size: 14px; padding: 6px 12px;">
                                                <?php echo htmlspecialchars($product->customer_count); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fa fa-box fa-2x mb-2"></i><br>
                                            No products found for this location.
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">Overview - Subscribers Count</h4>
            </div>
            <div class="panel-body" id="customer-status-progress-bars">
                <!-- Progress bars will be generated here by JavaScript -->
            </div>
        </div>
    </div>
</div>
<?php
} else {
    // Admin Dashboard (unchanged)
?>
<!-- Admin dashboard code remains the same -->
<style>
    .chart-legend {
        width: 100%;
    }
    
    .legend-items-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 8px;
        width: 100%;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        padding: 6px 0;
    }
    
    .legend-color {
        width: 20px;
        height: 10px;
        margin-right: 8px;
        border-radius: 3px;
        flex-shrink: 0;
    }
    
    .legend-text {
        flex: 1;
        min-width: 0;
        word-wrap: break-word;
    }

    /* === Tablet / Small Laptop (768px - 1024px) === */
    @media (min-width: 768px) {
        .legend-items-container {
            grid-template-columns: repeat(2, 1fr);
            max-height: 400px;
            overflow-y: auto;
        }
    }

    /* === Standard Laptop / Desktop (1024px and above) === */
    @media (min-width: 1024px) {
        .legend-items-container {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    /* === Large Desktop / Smart TV (1400px and above) === */
    @media (min-width: 1400px) {
        .legend-items-container {
            grid-template-columns: repeat(4, 1fr);
        }
        .chart-legend {
            font-size: 1.1em;
        }
    }
</style>

<div class="col-md-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            Customer Connection Locations
        </div>
        <div class="panel-body">
            <canvas id="locationChart"></canvas>
        </div>
    </div>
</div>
<div class="col-md-6">
    <div class="panel panel-default">
        <div class="panel-heading">
        Balance &amp; Cash Collection Chart:
        </div>
        <div class="panel-body">
            <canvas id="myChart">
					
				</canvas>
        </div>
    </div>
</div>
<div class="col-md-6">
    <div class="panel panel-default">
        <div class="panel-heading">
        Monthly Bill Collection : 2016
        </div>
        <div class="panel-body">
            <canvas id="chart2">
					
				</canvas>
        </div>
    </div>
</div>
<?php
} // End of role check
include 'includes/footer.php';
?>

<script type="text/javascript">
    $(document).ready(function() {
        <?php if ($user_role == 'employer'): ?>
        $.ajax({
            url: "customer_status_chart_data.php",
            method: "GET",
            dataType: 'JSON',
            success: function(data) {
                var container = $('#customer-status-progress-bars');
                if (!container.length) return;

                var statusConfig = {
                    'Paid': { label: 'Paid', color: '#28a745', order: 1 },
                    'Balance': { label: 'Balance', color: '#fd7e14', order: 2 },
                    'Unpaid': { label: 'Unpaid', color: '#8B0000', order: 3 },
                    'Rejected': { label: 'Reject', color: '#dc3545', order: 4 },
                    'Prospects': { label: 'Prospect', color: '#6c757d', order: 5 },
                    'Pending': { label: 'Pending', color: '#ffc107', order: 6 }
                };

                var total = data.reduce((acc, item) => acc + parseInt(item.count), 0);

                var displayData = data.map(item => {
                    var config = statusConfig[item.status] || {
                        label: item.status,
                        color: '#333',
                        order: 99
                    };
                    return {
                        ...item,
                        ...config,
                        count: parseInt(item.count)
                    };
                });

                displayData.sort((a, b) => a.order - b.order);

                var progressHtml = '<div class="progress-container">';
                displayData.forEach(function(item) {
                    var percentage = (total > 0) ? (item.count / total) * 100 : 0;
                    progressHtml += `
                        <div class="progress-item">
                            <div class="progress-label">${item.label}</div>
                            <div class="progress-bar-background">
                                <div class="progress-bar-fill" style="width: ${percentage}%; background-color: ${item.color};"></div>
                            </div>
                            <div class="progress-value">${item.count}</div>
                        </div>
                    `;
                });
                progressHtml += '</div>';

                container.html(progressHtml);
            },
            error: function(data) {
                console.log(data);
            }
        });
        <?php else: ?>
        // Admin dashboard JavaScript remains the same
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Sat', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
                datasets: [{
                    label: 'Cash Collection',
                    data: [12, 19, 3, 17, 6, 3, 20],
                    backgroundColor: "rgba(153,255,51,0.6)"
                }, {
                    label: 'Balance',
                    data: [2, 29, 5, 5, 2, 3, 10],
                    backgroundColor: "rgba(245,0,0,0.6)"
                }]
            }
        });
        var ctx = document.getElementById('chart2').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Monthly Bill Collection',
                    data: [50000, 60000, 30000, 45000, 48000, 38000, 80000, 50000, 0],
                    backgroundColor: "rgba(0,255,51,0.6)"
                }]
            }
        });

        $.ajax({
            url: "location_chart_data.php",
            method: "GET",
            dataType: 'JSON',
            success: function(data) {
                if (!data || data.length === 0) {
                    $('#locationChart').hide();
                    return;
                }

                var locations = data.map(item => item.conn_location);
                var counts = data.map(item => item.count);

                var professionalColors = [
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(153, 102, 255, 0.6)',
                    'rgba(255, 159, 64, 0.6)',
                    'rgba(199, 199, 199, 0.6)',
                    'rgba(83, 102, 255, 0.6)',
                    'rgba(100, 255, 100, 0.6)',
                    'rgba(255, 100, 100, 0.6)'
                ];

                var backgroundColors = data.map((_, index) => professionalColors[index % professionalColors.length]);

                var chartdata = {
                    labels: locations,
                    datasets: [{
                        label: 'Customer Count',
                        backgroundColor: backgroundColors,
                        borderColor: professionalColors.map(color => color.replace('0.6', '1')),
                        borderWidth: 1,
                        data: counts
                    }]
                };

                var ctx = $('#locationChart');
                if (!ctx.length) return;

                var barChart = new Chart(ctx, {
                    type: 'horizontalBar',
                    data: chartdata,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Customer Distribution by Location',
                            fontSize: 16,
                            fontColor: '#333'
                        },
                        scales: {
                            xAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    precision: 0,
                                    fontColor: '#333'
                                },
                                gridLines: {
                                    color: "rgba(0, 0, 0, 0.05)",
                                }
                            }],
                            yAxes: [{
                                ticks: {
                                    fontColor: '#333'
                                },
                                gridLines: {
                                    display: false
                                }
                            }]
                        },
                        tooltips: {
                            enabled: true,
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(0,0,0,0.7)',
                            titleFontColor: '#fff',
                            bodyFontColor: '#fff',
                            borderColor: 'rgba(0,0,0,0.8)',
                            borderWidth: 1,
                            callbacks: {
                                label: function(tooltipItem, data) {
                                    var label = data.datasets[tooltipItem.datasetIndex].label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += tooltipItem.xLabel;
                                    return label;
                                }
                            }
                        },
                        animation: {
                            duration: 1000,
                            easing: 'easeInOutQuart',
                            onComplete: function() {
                                var chartInstance = this.chart;
                                var ctx = chartInstance.ctx;
                                ctx.textAlign = 'left';
                                ctx.font = "bold 12px Arial";
                                ctx.fillStyle = "#333";

                                this.data.datasets.forEach(function(dataset, i) {
                                    var meta = chartInstance.controller.getDatasetMeta(i);
                                    meta.data.forEach(function(bar, index) {
                                        var data = dataset.data[index];
                                        if (data > 0) {
                                            ctx.fillText(data, bar._model.x + 5, bar._model.y + 4);
                                        }
                                    });
                                });
                            }
                        }
                    }
                });
                var chartHeight = 50 + data.length * 30;
                ctx.closest('.panel-body').css('height', chartHeight + 'px');

            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX Error:", textStatus, errorThrown);
                $('#locationChart').parent().html('<p class="text-center text-danger">Could not load chart data.</p>');
            }
        });

        $.ajax({
            url: "chart.php",
            method: "GET",
            dataType: 'JSON',
            success: function(data) {
                console.log(data);
                var raw = [];
                var qty = [];

                for (var i in data) {
                    raw.push(data[i].name);
                    qty.push(data[i].quantity);
                }
                console.log(raw);
                console.log(qty);
                var chartdata = {
                    labels: raw,
                    datasets: [{
                        label: 'Product Stock',
                        backgroundColor: 'rgba(0,200,225,.5)',
                        borderColor: 'rgba(200, 200, 200, 0.75)',
                        hoverBackgroundColor: 'rgba(200, 200, 200, 1)',
                        hoverBorderColor: 'rgba(200, 200, 200, 1)',
                        data: qty
                    }]
                };

                var ctx = $('#myChart2');

                var barGraph = new Chart(ctx, {
                    type: 'bar',
                    data: chartdata
                });
            },
            error: function(data) {
                console.log(data);
            }
        });
        <?php endif; ?>
    });
	function openRemarkModal(customer_id, remarks) {
		$('#customer_id_remark').val(customer_id);
		$('#remark').val(remarks);
		$('#add_remark_modal').modal('show');
	}
</script>
<div id="add_remark_modal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Add/Edit Remark</h4>
			</div>
			<div class="modal-body">
				<form method="post" id="remark_form" action="remarks.php">
					<label>Remark</label>
					<textarea name="remark" id="remark" class="form-control"></textarea>
					<input type="hidden" name="customer_id" id="customer_id_remark">
					<br>
					<input type="submit" name="add_remark" id="add_remark_btn" class="btn btn-primary" value="Add Remark">
				</form>
			</div>
		</div>
	</div>
</div>