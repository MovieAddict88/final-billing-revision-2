<?php
require_once 'includes/customer_header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-sm-12">
            <div class="card mt-5">
                <div class="card-header">
                    <h3 class="text-center">Customer Login</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid_code'): ?>
                        <div class="alert alert-danger" role="alert">
                            Invalid login code. Please try again.
                        </div>
                    <?php endif; ?>
                    <form action="customer_dashboard.php" method="post">
                        <div class="form-group">
                            <label for="login_code">Enter Your Login Code</label>
                            <input type="text" name="login_code" id="login_code" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/customer_footer.php';
?>