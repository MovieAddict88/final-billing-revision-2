<?php
$conn = new mysqli("localhost", "cornerst_121", "cornerst_121", "cornerst_121");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get monthly balances (grouped by Year + Month)
$sql = "SELECT DATE_FORMAT(created_at, '%M %Y') AS month, 
               SUM(amount) AS balance 
        FROM bills 
        GROUP BY YEAR(created_at), MONTH(created_at)
        ORDER BY created_at DESC";

$result = $conn->query($sql);
?>

<table border="1" cellpadding="8">
    <tr>
        <th>Month</th>
        <th>Balance</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?=htmlspecialchars($row['month'])?></td>
        <td>₱<?=number_format($row['balance'], 2)?></td>
    </tr>
    <?php endwhile; ?>
</table>
