<?php
// Start from getting the header which contains some settings we need
require_once 'includes/header.php';

// Redirect visitor to the login page if he is trying to access
// this page without being logged in
if (!isset($_SESSION['admin_session'])) {
    $commons->redirectTo(SITE_PATH . 'login.php');
}

require_once 'config/dbconnection.php';
$dbh = new Dbconnect();
require_once "includes/classes/admin-class.php";
$admins = new Admins($dbh);
?>

<div class="dashboard">		
    <div class="col-md-12 col-sm-12" id="employee_table">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>Customers</h4>
            </div>
            <div class="panel-body">
                <div class="col-md-6">
                    <button type="button" name="add" id="add" class="btn btn-info" data-toggle="modal" data-target="#add_data_Modal">Add New Customer</button>
                    <button onclick="packages()" class="btn btn-info">Packages</button>
                    <a href="disconnected_clients.php" class="btn btn-warning">View Disconnected Clients</a>
                </div>
                <div class="col-md-6">
                    <form class="form-inline pull-right">
                        <div class="form-group">
                            <label class="sr-only" for="search">Search for</label>
                            <div class="input-group">
                                <div class="input-group-addon"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></div>
                                <input type="text" class="form-control" id="search" placeholder="Type a name" list="global-suggestions" autocomplete="off">
                                <div class="input-group-addon"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <?php if (isset($_SESSION['errors'])) { ?>
                    <div class="alert alert-danger">
                        <?php foreach ($_SESSION['errors'] as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach ?>
                    </div>
                <?php 
                    unset($_SESSION['errors']);
                } ?>
                <?php if (isset($_SESSION['success'])) { ?>
                    <div class="alert alert-success">
                        <?= $_SESSION['success'] ?>
                    </div>
                <?php 
                    unset($_SESSION['success']);
                } ?>
            </div>
            <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table class="table table-striped table-bordered" id="grid-basic" style="min-width: 1200px; width: 100%;">
                    <thead class="thead-inverse">
                        <tr class="info">
                            <th style="min-width: 50px;">ID</th>
                            <th style="min-width: 100px;">Action</th>
                            <th style="min-width: 150px;">Name</th>
                            <th style="min-width: 150px;">Employer</th>
                            <th style="min-width: 120px;">NID</th>
                            <th style="min-width: 200px;">ADDRESS</th>
                            <th style="min-width: 120px;">Package</th>
                            <th style="min-width: 120px;">IP</th>
                            <th style="min-width: 200px;">Email</th>
                            <th style="min-width: 120px;">Contact</th>
                            <th style="min-width: 100px;">Type</th>
                            <th style="min-width: 100px;">Status</th>
                            <th style="min-width: 120px;">Amount Paid</th>
                            <th style="min-width: 120px;">Balance</th>
                            <th style="min-width: 150px;">Login Code</th>
                            <th style="min-width: 120px;">Due Date</th>
                            <th style="min-width: 200px;">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded here via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Customer Modal -->
<div id="add_data_Modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4>Insert Data</h4>
            </div>
            <form action="" method="POST" id="insert_form">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Full Name" required>
                    </div>
                    <div class="form-group">
                        <label for="nid">NID</label>
                        <input type="text" class="form-control" id="nid" name="nid" placeholder="NID" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="textarea" class="form-control" id="address" name="address" placeholder="Address" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" class="form-control" id="email" name="email" placeholder="Email Address" required>
                    </div>
                    <div class="form-group">
                        <label for="conn_location">Connection Location</label>
                        <input type="textarea" class="form-control" id="conn_location" name="conn_location" placeholder="Connection location">
                    </div>
                    <div class="form-group">
                        <label for="package">Select Package</label>
                        <select class="form-control form-control-sm" name="package" id="package" required>
                            <?php 
                            $packages = $admins->getPackages();
                            if (isset($packages) && sizeof($packages) > 0){ 
                                foreach ($packages as $package) { ?>
                                <option value='<?=$package->id?>'><?=$package->name?></option>
                            <?php }} ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="package">Select Employer</label>
                        <select class="form-control form-control-sm" name="employer" id="employer" required>
                            <option value=''>Select an employer</option>
                            <?php
                            $employers = $admins->getEmployers();
                            if (isset($employers) && sizeof($employers) > 0){
                                foreach ($employers as $employer) { ?>
                                <option value='<?=$employer->user_id?>'><?=$employer->full_name?></option>
                            <?php }} ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="ip_address">IP Address</label>
                        <input type="text" class="form-control" id="ip_address" name="ip_address" placeholder="IP Address">
                    </div>
                    <div class="form-group">
                        <label for="conn_type">Connection Type</label>
                        <input type="text" class="form-control" id="conn_type" name="conn_type" placeholder="Connection Type">
                    </div>
                    <div class="form-group">
                        <label for="contact">Contact</label>
                        <input type="tel" class="form-control" id="contact" name="contact" placeholder="Contact" required>
                    </div>
                    <div class="form-group">
                        <label for="due_date">Due Date</label>
                        <input type="date" class="form-control" id="due_date" name="due_date" placeholder="Due Date" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="#" class="btn btn-warning" data-dismiss="modal">Cancel</a>
                </div>
            </form>
        </div>
    </div>		
</div>

<!-- Remark Modal -->
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

<?php include 'includes/footer.php'; ?>

<script type="text/javascript">
    // Form submission for adding new customer
    $('#insert_form').on('submit',function(event){
        event.preventDefault();
        $.ajax({
            url: "customers_approve.php?p=add",
            method:"POST",
            data:$('#insert_form').serialize(),
            success: function (data) {
                $('#insert_form')[0].reset();
                $('#add_data_Modal').modal('hide');
                viewData();
            }
        });
    });

    // Load customer data
    function viewData(page, q, limit) {
        page = typeof page !== 'undefined' ? page : 1;
        q = typeof q !== 'undefined' ? q : '';
        limit = typeof limit !== 'undefined' ? limit : 10;
        $.ajax({
            method: "GET",
            url:"customers_approve.php",
            data: { page: page, q: q, limit: limit },
            success: function(data){
                $('tbody').html(data);
            }
        });
    }

    // Delete customer
    function delData(del_id){
        if(confirm('Are you sure you want to delete this customer? This action cannot be undone.')) {
            $.ajax({
                method:"POST",
                url: "customers_approve.php?p=del",
                data: "id="+del_id,
                success: function (data){
                    viewData();
                }
            });
        }
    }

    // Update customer data
    function updateData(str){
        var id = str;
        var full_name = $('#fnm-'+str).val();
        var nid = $('#nid-'+str).val();
        var address = $('#ad-'+str).val();
        var package = $('#pk-'+str).val();
        var conn_location = $('#conn_loc-'+str).val();
        var email = $('#em-'+str).val();
        var ip_address = $('#ip-'+str).val();
        var conn_type = $('#ct-'+str).val();
        var contact = $('#con-'+str).val();
        var employer = $('#emp-'+str).val();
        var due_date = $('#due_date-'+str).val();
        
        $.ajax({
            method:"POST",
            url: "customers_approve.php?p=edit",
            data: "full_name="+full_name+"&nid="+nid+"&address="+address+"&conn_location="+conn_location+"&email="+email+"&package="+package+"&ip_address="+ip_address+"&conn_type="+conn_type+"&contact="+contact+"&employer="+employer+"&id="+id+"&due_date="+due_date,
            success: function (data){
                viewData();
            }
        });
    }

    // Load data when page loads
    window.onload = function(){ viewData(1, '', 10); };

    // Search functionality
    $(function() {
        var typingTimer;
        var lastQ = '';

        $('#search').on('input', function(){
            var q = $(this).val();
            clearTimeout(typingTimer);
            typingTimer = setTimeout(function(){ 
                lastQ = q; 
                viewData(1, q); 
            }, 250);
            
            if (q && q.length >= 1) {
                $.get('search_suggestions.php', { type: 'customers', q: q }, function(items){
                    var dl = $('#global-suggestions'); 
                    dl.empty();
                    (items || []).forEach(function(it){ 
                        dl.append('<option value="'+ (it.value || '') +'">'); 
                    });
                }, 'json');
            }
        });

        // Pagination controls
        $('#grid-basic').on('click', '.page-prev, .page-next', function(){
            var row = $('.pagination-row');
            var page = parseInt(row.data('page')) || 1;
            var total = parseInt(row.data('total')) || 0;
            var limit = parseInt(row.data('limit')) || 10;
            var q = row.data('query') || '';
            if ($(this).hasClass('page-prev') && page > 1) page--; 
            else if ($(this).hasClass('page-next')) page++;
            viewData(page, q, limit);
        });

        $('#grid-basic').on('click', '.page-num', function(){
            var page = parseInt($(this).text());
            var row = $('.pagination-row');
            var limit = parseInt(row.data('limit')) || 10;
            var q = row.data('query') || '';
            viewData(page, q, limit);
        });
    });

    function packages() {
        let left = (screen.width/2)-(600/2);
        let top = (screen.height/2)-(800/2);
        let params = `scrollbars=no,resizable=no,status=no,location=no,toolbar=no,menubar=no,width=600,height=800,left=${left},top=${top}`;
        open('packages.php', 'Packages', params);
    }

    function openRemarkModal(customer_id, remarks) {
        $('#customer_id_remark').val(customer_id);
        $('#remark').val(remarks);
        $('#add_remark_modal').modal('show');
    }
</script>