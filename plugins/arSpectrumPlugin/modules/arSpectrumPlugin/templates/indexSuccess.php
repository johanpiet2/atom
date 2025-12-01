<?php decorate_with('layout_2col.php'); ?>

<?php slot('sidebar'); ?>
    <?php include_component('informationobject', 'contextMenu'); ?>
<?php end_slot(); ?>

<?php slot('title'); ?>
    <h1><?php echo __('Spectrum Procedures'); ?></h1>
    <h2><?php echo render_title($resource); ?></h2>
<?php end_slot(); ?>

<?php slot('content'); ?>

<div class="spectrum-procedures">

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title"><?php echo __('Current Location'); ?></h5>
                    <p class="card-text">
                        <?php echo $currentLocation ? $currentLocation->location_name : __('Not set'); ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title"><?php echo __('Condition'); ?></h5>
                    <p class="card-text">
                        <?php echo $summary['latest_condition'] ? ucfirst($summary['latest_condition']->overall_condition) : __('Not checked'); ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title"><?php echo __('Insurance Value'); ?></h5>
                    <p class="card-text">
                        <?php if ($currentValuation) { ?>
                            <?php echo $currentValuation->valuation_currency.' '.number_format($currentValuation->valuation_amount, 2); ?>
                        <?php } else { ?>
                            <?php echo __('Not valued'); ?>
                        <?php } ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title"><?php echo __('Movements'); ?></h5>
                    <p class="card-text"><?php echo $summary['movement_count']; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Object Entry -->
    <fieldset class="collapsible collapsed" id="objectEntryArea">
        <legend><?php echo __('Object Entry'); ?></legend>
        
        <div class="mb-3">
			<a href="<?php echo url_for('spectrum_edit', ['slug' => $resource->slug, 'procedure' => 'entry']); ?>" class="btn btn-primary btn-sm">
				<i class="fas fa-plus"></i> <?php echo __('Add Entry'); ?>
			</a>
        </div>

        <?php if ($objectEntry->count() > 0) { ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><?php echo __('Entry Number'); ?></th>
                        <th><?php echo __('Date'); ?></th>
                        <th><?php echo __('Method'); ?></th>
                        <th><?php echo __('Depositor'); ?></th>
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($objectEntry as $entry) { ?>
                        <tr>
                            <td><?php echo $entry->entry_number; ?></td>
                            <td><?php echo $entry->entry_date; ?></td>
                            <td><?php echo ucfirst(str_replace('_', ' ', $entry->entry_method)); ?></td>
                            <td><?php echo $entry->depositor_name; ?></td>
                            <td>
								<a href="<?php echo url_for('spectrum_edit', ['slug' => $resource->slug, 'procedure' => 'entry', 'procedure_id' => $entry->id]); ?>" class="btn btn-sm btn-primary" title="<?php echo __('Edit'); ?>">
									<i class="fas fa-edit"></i>
								</a>
								<a href="<?php echo url_for('spectrum_delete', ['slug' => $resource->slug, 'procedure' => 'entry', 'id' => $entry->id]); ?>" class="btn btn-sm btn-danger" onclick="return confirm('<?php echo __('Are you sure?'); ?>');" title="<?php echo __('Delete'); ?>">
									<i class="fas fa-trash"></i>
								</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p class="text-muted"><?php echo __('No entry records.'); ?></p>
        <?php } ?>
    </fieldset>

    <!-- Acquisition -->
    <fieldset class="collapsible collapsed" id="acquisitionArea">
        <legend><?php echo __('Acquisition'); ?></legend>
        
        <div class="mb-3">
			<a href="<?php echo url_for('spectrum_edit', ['slug' => $resource->slug, 'procedure' => 'acquisition']); ?>" class="btn btn-primary btn-sm">
				<i class="fas fa-plus"></i> <?php echo __('Add Acquisition'); ?>
			</a>
        </div>

        <?php if ($acquisitions->count() > 0) { ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><?php echo __('Acquisition Number'); ?></th>
                        <th><?php echo __('Date'); ?></th>
                        <th><?php echo __('Method'); ?></th>
                        <th><?php echo __('Source'); ?></th>
                        <th><?php echo __('Accession Number'); ?></th>
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($acquisitions as $acq) { ?>
                        <tr>
                            <td><?php echo $acq->acquisition_number; ?></td>
                            <td><?php echo $acq->acquisition_date; ?></td>
                            <td><?php echo ucfirst($acq->acquisition_method); ?></td>
                            <td><?php echo $acq->acquisition_source; ?></td>
                            <td><?php echo $acq->accession_number; ?></td>
                            <td>
								<a href="<?php echo url_for('spectrum_edit', ['slug' => $resource->slug, 'procedure' => 'acquisition', 'procedure_id' => $acq->id]); ?>" class="btn btn-sm btn-primary" title="<?php echo __('Edit'); ?>">
									<i class="fas fa-edit"></i>
								</a>
								<a href="<?php echo url_for('spectrum_delete', ['slug' => $resource->slug, 'procedure' => 'acquisition', 'id' => $acq->id]); ?>" class="btn btn-sm btn-danger" onclick="return confirm('<?php echo __('Are you sure?'); ?>');" title="<?php echo __('Delete'); ?>">
									<i class="fas fa-trash"></i>
								</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p class="text-muted"><?php echo __('No acquisition records.'); ?></p>
        <?php } ?>
    </fieldset>

    <!-- Location & Movement -->
    <fieldset class="collapsible" id="locationArea">
        <legend><?php echo __('Location & Movement'); ?></legend>
        
        <div class="mb-3">
			<a href="<?php echo url_for('spectrum_edit', ['slug' => $resource->slug, 'procedure' => 'location']); ?>" class="btn btn-primary btn-sm">
				<i class="fas fa-plus"></i> <?php echo __('Add Location'); ?>
			</a>			

			<a href="<?php echo url_for('spectrum_edit', ['slug' => $resource->slug, 'procedure' => 'movement']); ?>" class="btn btn-primary btn-sm">
				<i class="fas fa-plus"></i> <?php echo __('Record Movement'); ?>
			</a>	
        </div>

		<div class="card mb-3">
			<div class="card-body">
				<h5 class="card-title"><?php echo __('Current Location'); ?></h5>
				<?php if ($currentLocation) { ?>
					<p class="mb-1"><strong><?php echo $currentLocation->location_name; ?></strong></p>
					<?php if ($currentLocation->location_building) { ?>
						<p class="text-muted mb-0">
							<?php echo $currentLocation->location_building; ?>
							<?php if ($currentLocation->location_floor) { ?> - <?php echo $currentLocation->location_floor; ?><?php } ?>
							<?php if ($currentLocation->location_room) { ?> - <?php echo $currentLocation->location_room; ?><?php } ?>
						</p>
					<?php } ?>
					<div class="mt-2">
						<a href="<?php echo url_for('spectrum_edit', ['slug' => $resource->slug, 'procedure' => 'location', 'procedure_id' => $currentLocation->id]); ?>" class="btn btn-sm btn-primary" title="<?php echo __('Edit'); ?>">
							<i class="fas fa-edit"></i>
						</a>
						<a href="<?php echo url_for('spectrum_delete', ['slug' => $resource->slug, 'procedure' => 'location', 'id' => $currentLocation->id]); ?>" class="btn btn-sm btn-danger" onclick="return confirm('<?php echo __('Are you sure?'); ?>');" title="<?php echo __('Delete'); ?>">
							<i class="fas fa-trash"></i>
						</a>
					</div>
				<?php } else { ?>
					<p class="text-muted"><?php echo __('No current location set.'); ?></p>
				<?php } ?>
			</div>
		</div>

		<h5><?php echo __('Movement History'); ?></h5>
		<?php if ($movementHistory->count() > 0) { ?>
			<table class="table table-striped">
				<thead>
					<tr>
						<th><?php echo __('Date'); ?></th>
						<th><?php echo __('From'); ?></th>
						<th><?php echo __('To'); ?></th>
						<th><?php echo __('Reason'); ?></th>
						<th><?php echo __('Handler'); ?></th>
						<th><?php echo __('Actions'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($movementHistory as $move) { ?>
						<tr>
							<td><?php echo $move->movement_date; ?></td>
							<td><?php echo $move->from_location_name; ?></td>
							<td><?php echo $move->to_location_name; ?></td>
							<td><?php echo ucfirst(str_replace('_', ' ', $move->movement_reason)); ?></td>
							<td><?php echo $move->handler_name; ?></td>
							<td>
								<a href="<?php echo url_for('spectrum_edit', ['slug' => $resource->slug, 'procedure' => 'movement', 'procedure_id' => $move->id]); ?>" class="btn btn-sm btn-primary" title="<?php echo __('Edit'); ?>">
									<i class="fas fa-edit"></i>
								</a>
								<a href="<?php echo url_for('spectrum_delete', ['slug' => $resource->slug, 'procedure' => 'movement', 'id' => $move->id]); ?>" class="btn btn-sm btn-danger" onclick="return confirm('<?php echo __('Are you sure?'); ?>');" title="<?php echo __('Delete'); ?>">
									<i class="fas fa-trash"></i>
								</a>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		<?php } else { ?>
			<p class="text-muted"><?php echo __('No movement records.'); ?></p>
		<?php } ?>
		</fieldset>

    <!-- Loans In -->
    <fieldset class="collapsible collapsed" id="loansInArea">
        <legend><?php echo __('Loans In'); ?></legend>
        
        <div class="mb-3">
			<a href="<?php echo url_for('spectrum_edit', ['slug' => $resource->slug, 'procedure' => 'loan_in']); ?>" class="btn btn-primary btn-sm">
			<i class="fas fa-plus"></i> <?php echo __('Add Loan In'); ?>
			</a>		
        </div>

        <?php if ($loansIn->count() > 0) { ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><?php echo __('Loan Number'); ?></th>
                        <th><?php echo __('Lender'); ?></th>
                        <th><?php echo __('Date In'); ?></th>
                        <th><?php echo __('Return Due'); ?></th>
                        <th><?php echo __('Status'); ?></th>
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($loansIn as $loan) { ?>
                        <tr class="<?php echo ('active' === $loan->loan_status && $loan->loan_return_date < date('Y-m-d')) ? 'table-danger' : ''; ?>">
                            <td><?php echo $loan->loan_in_number; ?></td>
                            <td><?php echo $loan->lender_name; ?></td>
                            <td><?php echo $loan->loan_in_date; ?></td>
                            <td><?php echo $loan->loan_return_date; ?></td>
                            <td><?php echo ucfirst($loan->loan_status); ?></td>
                            <td>
								<a href="<?php echo url_for('spectrum_edit', ['slug' => $resource->slug, 'procedure' => 'loan_in', 'procedure_id' => $loan->id]); ?>" class="btn btn-sm btn-primary" title="<?php echo __('Edit'); ?>">
									<i class="fas fa-edit"></i>
								</a>
								<a href="<?php echo url_for('spectrum_delete', ['slug' => $resource->slug, 'procedure' => 'loan_in', 'id' => $loan->id]); ?>" class="btn btn-sm btn-danger" onclick="return confirm('<?php echo __('Are you sure?'); ?>');" title="<?php echo __('Delete'); ?>">
									<i class="fas fa-trash"></i>
								</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p class="text-muted"><?php echo __('No incoming loans.'); ?></p>
        <?php } ?>
    </fieldset>

    <!-- Loans Out -->
    <fieldset class="collapsible collapsed" id="loansOutArea">
        <legend><?php echo __('Loans Out'); ?></legend>
        
        <div class="mb-3">
			<a href="<?php echo url_for('spectrum_edit', ['slug' => $resource->slug, 'procedure' => 'loan_out']); ?>" class="btn btn-primary btn-sm">
				<i class="fas fa-plus"></i> <?php echo __('Add Loan Out'); ?>
			</a>	
        </div>

        <?php if ($loansOut->count() > 0) { ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><?php echo __('Loan Number'); ?></th>
                        <th><?php echo __('Borrower'); ?></th>
                        <th><?php echo __('Date Out'); ?></th>
                        <th><?php echo __('Return Due'); ?></th>
                        <th><?php echo __('Status'); ?></th>
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($loansOut as $loan) { ?>
                        <tr class="<?php echo ('active' === $loan->loan_status && $loan->loan_return_date < date('Y-m-d')) ? 'table-danger' : ''; ?>">
                            <td><?php echo $loan->loan_out_number; ?></td>
                            <td><?php echo $loan->borrower_name; ?></td>
                            <td><?php echo $loan->loan_out_date; ?></td>
                            <td><?php echo $loan->loan_return_date; ?></td>
                            <td><?php echo ucfirst($loan->loan_status); ?></td>
                            <td>
								<a href="<?php echo url_for('spectrum_edit', ['slug' => $resource->slug, 'procedure' => 'loan_out', 'procedure_id' => $loan->id]); ?>" class="btn btn-sm btn-primary" title="<?php echo __('Edit'); ?>">
									<i class="fas fa-edit"></i>
								</a>
								<a href="<?php echo url_for('spectrum_delete', ['slug' => $resource->slug, 'procedure' => 'loan_out', 'id' => $loan->id]); ?>" class="btn btn-sm btn-danger" onclick="return confirm('<?php echo __('Are you sure?'); ?>');" title="<?php echo __('Delete'); ?>">
									<i class="fas fa-trash"></i>
								</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p class="text-muted"><?php echo __('No outgoing loans.'); ?></p>
        <?php } ?>
    </fieldset>

    <!-- Condition Checking -->
    <fieldset class="collapsible collapsed" id="conditionArea">
        <legend><?php echo __('Condition Checking'); ?></legend>
        
        <div class="mb-3">
			<a href="<?php echo url_for('spectrum_edit', ['slug' => $resource->slug, 'procedure' => 'condition']); ?>" class="btn btn-primary btn-sm">
				<i class="fas fa-plus"></i> <?php echo __('Add Condition Check'); ?>
			</a>			
        </div>

        <?php if ($conditionHistory->count() > 0) { ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><?php echo __('Date'); ?></th>
                        <th><?php echo __('Reason'); ?></th>
                        <th><?php echo __('Checked By'); ?></th>
                        <th><?php echo __('Condition'); ?></th>
                        <th><?php echo __('Priority'); ?></th>
                        <th><?php echo __('Next Check'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($conditionHistory as $check) { ?>
                        <tr>
                            <td><?php echo $check->check_date; ?></td>
                            <td><?php echo ucfirst(str_replace('_', ' ', $check->check_reason)); ?></td>
                            <td><?php echo $check->checked_by; ?></td>
                            <td><?php echo ucfirst($check->overall_condition); ?></td>
                            <td><?php echo ucfirst($check->treatment_priority); ?></td>
                            <td><?php echo $check->next_check_date; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p class="text-muted"><?php echo __('No condition checks.'); ?></p>
        <?php } ?>
    </fieldset>

    <!-- Conservation -->
    <fieldset class="collapsible collapsed" id="conservationArea">
        <legend><?php echo __('Conservation & Treatment'); ?></legend>
        
        <div class="mb-3">
			<a href="<?php echo url_for('spectrum_edit', ['slug' => $resource->slug, 'procedure' => 'conservation']); ?>" class="btn btn-primary btn-sm">
				<i class="fas fa-plus"></i> <?php echo __('Add Conservation Record'); ?>
			</a>	
        </div>

        <?php if ($conservationHistory->count() > 0) { ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><?php echo __('Reference'); ?></th>
                        <th><?php echo __('Date'); ?></th>
                        <th><?php echo __('Conservator'); ?></th>
                        <th><?php echo __('Treatment'); ?></th>
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($conservationHistory as $cons) { ?>
                        <tr>
                            <td><?php echo $cons->conservation_reference; ?></td>
                            <td><?php echo $cons->treatment_date; ?></td>
                            <td><?php echo $cons->conservator_name; ?></td>
                            <td><?php echo mb_substr($cons->treatment_performed, 0, 100).'...'; ?></td>
                            <td>
								<a href="<?php echo url_for('spectrum_edit', ['slug' => $resource->slug, 'procedure' => 'conservation', 'procedure_id' => $cons->id]); ?>" class="btn btn-sm btn-primary" title="<?php echo __('Edit'); ?>">
									<i class="fas fa-edit"></i>
								</a>
								<a href="<?php echo url_for('spectrum_delete', ['slug' => $resource->slug, 'procedure' => 'conservation', 'id' => $cons->id]); ?>" class="btn btn-sm btn-danger" onclick="return confirm('<?php echo __('Are you sure?'); ?>');" title="<?php echo __('Delete'); ?>">
									<i class="fas fa-trash"></i>
								</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p class="text-muted"><?php echo __('No conservation records.'); ?></p>
        <?php } ?>
    </fieldset>

    <!-- Valuation -->
    <fieldset class="collapsible collapsed" id="valuationArea">
        <legend><?php echo __('Valuation'); ?></legend>
        
        <div class="mb-3">
			<a href="<?php echo url_for('spectrum_edit', ['slug' => $resource->slug, 'procedure' => 'valuation']); ?>" class="btn btn-primary btn-sm">
				<i class="fas fa-plus"></i> <?php echo __('Add Valuation'); ?>
			</a>				
        </div>

        <?php if ($valuationHistory->count() > 0) { ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><?php echo __('Date'); ?></th>
                        <th><?php echo __('Type'); ?></th>
                        <th><?php echo __('Value'); ?></th>
                        <th><?php echo __('Valuer'); ?></th>
                        <th><?php echo __('Current'); ?></th>
                        <th><?php echo __('Renewal'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($valuationHistory as $val) { ?>
                        <tr>
                            <td><?php echo $val->valuation_date; ?></td>
                            <td><?php echo ucfirst($val->valuation_type); ?></td>
                            <td><?php echo $val->valuation_currency.' '.number_format($val->valuation_amount, 2); ?></td>
                            <td><?php echo $val->valuer_name; ?></td>
                            <td><?php echo $val->is_current ? '<i class="fas fa-check text-success"></i>' : ''; ?></td>
                            <td><?php echo $val->renewal_date; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p class="text-muted"><?php echo __('No valuations.'); ?></p>
        <?php } ?>
    </fieldset>

    <!-- Deaccession -->
    <fieldset class="collapsible collapsed" id="deaccessionArea">
        <legend><?php echo __('Deaccession & Disposal'); ?></legend>
        
        <div class="mb-3">
			<a href="<?php echo url_for('spectrum_edit', ['slug' => $resource->slug, 'procedure' => 'deaccession']); ?>" class="btn btn-primary btn-sm">
				<i class="fas fa-plus"></i> <?php echo __('Add Deaccession'); ?>
			</a>			
        </div>

        <?php if ($deaccessions->count() > 0) { ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><?php echo __('Number'); ?></th>
                        <th><?php echo __('Date'); ?></th>
                        <th><?php echo __('Method'); ?></th>
                        <th><?php echo __('Recipient'); ?></th>
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($deaccessions as $deacc) { ?>
                        <tr>
                            <td><?php echo $deacc->deaccession_number; ?></td>
                            <td><?php echo $deacc->deaccession_date; ?></td>
                            <td><?php echo ucfirst($deacc->disposal_method); ?></td>
                            <td><?php echo $deacc->disposal_recipient; ?></td>
                            <td>
								<a href="<?php echo url_for('spectrum_edit', ['slug' => $resource->slug, 'procedure' => 'deaccession', 'procedure_id' => $deacc->id]); ?>" class="btn btn-sm btn-primary" title="<?php echo __('Edit'); ?>">
									<i class="fas fa-edit"></i>
								</a>
								<a href="<?php echo url_for('spectrum_delete', ['slug' => $resource->slug, 'procedure' => 'deaccession', 'id' => $deacc->id]); ?>" class="btn btn-sm btn-danger" onclick="return confirm('<?php echo __('Are you sure?'); ?>');" title="<?php echo __('Delete'); ?>">
									<i class="fas fa-trash"></i>
								</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p class="text-muted"><?php echo __('No deaccession records.'); ?></p>
        <?php } ?>
    </fieldset>

    <!-- Audit Log -->
    <fieldset class="collapsible collapsed" id="auditArea">
        <legend><?php echo __('Audit Log'); ?></legend>
        
        <?php if ($auditLog->count() > 0) { ?>
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th><?php echo __('Date'); ?></th>
                        <th><?php echo __('Procedure'); ?></th>
                        <th><?php echo __('Action'); ?></th>
                        <th><?php echo __('User'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($auditLog as $log) { ?>
                        <tr>
                            <td><?php echo $log->action_date; ?></td>
                            <td><?php echo ucfirst(str_replace('_', ' ', $log->procedure_type)); ?></td>
                            <td><?php echo ucfirst($log->action); ?></td>
                            <td><?php echo $log->user_name; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p class="text-muted"><?php echo __('No audit records.'); ?></p>
        <?php } ?>
    </fieldset>

</div>

<?php end_slot(); ?>
