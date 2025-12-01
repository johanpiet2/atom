<?php decorate_with('layout_1col.php'); ?>

<?php slot('title'); ?>
    <h1><?php echo __('Collections Management Dashboard'); ?></h1>
<?php end_slot(); ?>

<?php slot('content'); ?>

<div class="spectrum-dashboard">

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h2><?php echo $stats['active_loans_in']; ?></h2>
                    <p class="mb-0"><?php echo __('Active Loans In'); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h2><?php echo $stats['active_loans_out']; ?></h2>
                    <p class="mb-0"><?php echo __('Active Loans Out'); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body text-center">
                    <h2><?php echo $stats['pending_condition_checks']; ?></h2>
                    <p class="mb-0"><?php echo __('Pending Condition Checks'); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h2>R<?php echo number_format($stats['total_insurance_value'], 0); ?></h2>
                    <p class="mb-0"><?php echo __('Total Insurance Value'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-danger"><?php echo $stats['overdue_loans_in']; ?></h3>
                    <p class="mb-0"><?php echo __('Overdue Loans In'); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-danger"><?php echo $stats['overdue_loans_out']; ?></h3>
                    <p class="mb-0"><?php echo __('Overdue Loans Out'); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3><?php echo $stats['recent_movements']; ?></h3>
                    <p class="mb-0"><?php echo __('Movements (30 days)'); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3><?php echo $stats['recent_acquisitions']; ?></h3>
                    <p class="mb-0"><?php echo __('Acquisitions (30 days)'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Overdue Loans In -->
    <?php if ($overdueLoansIn->count() > 0) { ?>
    <div class="card mb-4 border-danger">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> <?php echo __('Overdue Loans In'); ?></h5>
        </div>
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th><?php echo __('Object'); ?></th>
                        <th><?php echo __('Loan Number'); ?></th>
                        <th><?php echo __('Lender'); ?></th>
                        <th><?php echo __('Due Date'); ?></th>
                        <th><?php echo __('Days Overdue'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($overdueLoansIn as $loan) { ?>
                        <tr>
                            <td>
                                <a href="<?php echo url_for(['module' => 'informationobject', 'slug' => $loan->object_id]); ?>">
                                    <?php echo $loan->object_title; ?>
                                </a>
                            </td>
                            <td><?php echo $loan->loan_in_number; ?></td>
                            <td><?php echo $loan->lender_name; ?></td>
                            <td><?php echo $loan->loan_return_date; ?></td>
                            <td class="text-danger font-weight-bold">
                                <?php echo (int) ((time() - strtotime($loan->loan_return_date)) / 86400); ?> <?php echo __('days'); ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php } ?>

    <!-- Overdue Loans Out -->
    <?php if ($overdueLoansOut->count() > 0) { ?>
    <div class="card mb-4 border-danger">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> <?php echo __('Overdue Loans Out'); ?></h5>
        </div>
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th><?php echo __('Object'); ?></th>
                        <th><?php echo __('Loan Number'); ?></th>
                        <th><?php echo __('Borrower'); ?></th>
                        <th><?php echo __('Due Date'); ?></th>
                        <th><?php echo __('Days Overdue'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($overdueLoansOut as $loan) { ?>
                        <tr>
                            <td>
                                <a href="<?php echo url_for(['module' => 'informationobject', 'slug' => $loan->object_id]); ?>">
                                    <?php echo $loan->object_title; ?>
                                </a>
                            </td>
                            <td><?php echo $loan->loan_out_number; ?></td>
                            <td><?php echo $loan->borrower_name; ?></td>
                            <td><?php echo $loan->loan_return_date; ?></td>
                            <td class="text-danger font-weight-bold">
                                <?php echo (int) ((time() - strtotime($loan->loan_return_date)) / 86400); ?> <?php echo __('days'); ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php } ?>

    <!-- Pending Condition Checks -->
    <?php if ($pendingConditionChecks->count() > 0) { ?>
    <div class="card mb-4 border-warning">
        <div class="card-header text-white" style="background-color: #6488EA;">
            <h5 class="card-title text-pure-white"> <?php echo __('Pending Condition Checks'); ?></h5>
        </div>
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th><?php echo __('Object'); ?></th>
                        <th><?php echo __('Last Check'); ?></th>
                        <th><?php echo __('Scheduled'); ?></th>
                        <th><?php echo __('Current Condition'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingConditionChecks as $check) { ?>
                        <tr>
                            <td>
                                <a href="<?php echo url_for(['module' => 'informationobject', 'slug' => $check->object_id]); ?>">
                                    <?php echo $check->object_title; ?>
                                </a>
                            </td>
                            <td><?php echo $check->check_date; ?></td>
                            <td><?php echo $check->next_check_date; ?></td>
                            <td><?php echo ucfirst($check->overall_condition); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php } ?>

    <!-- Active Loans Summary -->
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title text-pure-white"><?php echo __('Active Loans In'); ?></h5>
                </div>
                <div class="card-body">
                    <?php if ($activeLoansIn->count() > 0) { ?>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th><?php echo __('Object'); ?></th>
                                    <th><?php echo __('Lender'); ?></th>
                                    <th><?php echo __('Return Due'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($activeLoansIn as $loan) { ?>
                                    <tr>
                                        <td><?php echo $loan->object_title; ?></td>
                                        <td><?php echo $loan->lender_name; ?></td>
                                        <td><?php echo $loan->loan_return_date; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } else { ?>
                        <p class="text-muted"><?php echo __('No active incoming loans.'); ?></p>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title text-pure-white"><?php echo __('Active Loans Out'); ?></h5>
                </div>
                <div class="card-body">
                    <?php if ($activeLoansOut->count() > 0) { ?>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th><?php echo __('Object'); ?></th>
                                    <th><?php echo __('Borrower'); ?></th>
                                    <th><?php echo __('Return Due'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($activeLoansOut as $loan) { ?>
                                    <tr>
                                        <td><?php echo $loan->object_title; ?></td>
                                        <td><?php echo $loan->borrower_name; ?></td>
                                        <td><?php echo $loan->loan_return_date; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } else { ?>
                        <p class="text-muted"><?php echo __('No active outgoing loans.'); ?></p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

</div>

<?php end_slot(); ?>
