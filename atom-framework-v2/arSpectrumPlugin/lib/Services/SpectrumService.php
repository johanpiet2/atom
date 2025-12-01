<?php

use AtomExtensions\Database\DatabaseBootstrap;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Spectrum Service.
 *
 * Provides access to Spectrum 5.0 procedures using Laravel Query Builder.
 * Implements primary procedures for museum collections management.
 *
 * @author Johan Pieterse <johan@theahg.co.za>
 */
class SpectrumService
{
    protected $culture;
    protected $userId;

    public function __construct($culture = 'en', $userId = null)
    {
        $this->culture = $culture;
        $this->userId = $userId;

        // Initialize database if not already done
        if (null === DatabaseBootstrap::getCapsule()) {
            DatabaseBootstrap::initializeFromAtom();
        }
    }

    // ========================================================================
    // OBJECT ENTRY
    // ========================================================================

    public function createObjectEntry($objectId, $data)
    {
        $data['object_id'] = $objectId;
        $data['created_by'] = $this->userId;
        $data['created_at'] = date('Y-m-d H:i:s');

        $id = Capsule::table('spectrum_object_entry')->insertGetId($data);
        $this->logAudit($objectId, 'entry', $id, 'create', $data);

        return $id;
    }

    public function getObjectEntry($id)
    {
        return Capsule::table('spectrum_object_entry')->where('id', $id)->first();
    }

    public function getObjectEntries($objectId)
    {
        return Capsule::table('spectrum_object_entry')
            ->where('object_id', $objectId)
            ->orderBy('entry_date', 'desc')
            ->get();
    }

    public function updateObjectEntry($id, $data)
    {
        $old = $this->getObjectEntry($id);
        $data['updated_by'] = $this->userId;
        $data['updated_at'] = date('Y-m-d H:i:s');

        $result = Capsule::table('spectrum_object_entry')->where('id', $id)->update($data);
        $this->logAudit($old->object_id, 'entry', $id, 'update', $data, (array) $old);

        return $result > 0;
    }

    // ========================================================================
    // ACQUISITION
    // ========================================================================

    public function createAcquisition($objectId, $data)
    {
        $data['object_id'] = $objectId;
        $data['created_by'] = $this->userId;
        $data['created_at'] = date('Y-m-d H:i:s');

        $id = Capsule::table('spectrum_acquisition')->insertGetId($data);
        $this->logAudit($objectId, 'acquisition', $id, 'create', $data);

        return $id;
    }

    public function getAcquisition($id)
    {
        return Capsule::table('spectrum_acquisition')->where('id', $id)->first();
    }

    public function getAcquisitions($objectId)
    {
        return Capsule::table('spectrum_acquisition')
            ->where('object_id', $objectId)
            ->orderBy('acquisition_date', 'desc')
            ->get();
    }

    public function updateAcquisition($id, $data)
    {
        $old = $this->getAcquisition($id);
        $data['updated_by'] = $this->userId;
        $data['updated_at'] = date('Y-m-d H:i:s');

        $result = Capsule::table('spectrum_acquisition')->where('id', $id)->update($data);
        $this->logAudit($old->object_id, 'acquisition', $id, 'update', $data, (array) $old);

        return $result > 0;
    }

    // ========================================================================
    // LOCATION & MOVEMENT
    // ========================================================================

    public function createLocation($objectId, $data)
    {
        // Set previous current location to not current
        if (!empty($data['is_current']) && $data['is_current']) {
            Capsule::table('spectrum_location')
                ->where('object_id', $objectId)
                ->where('is_current', true)
                ->update(['is_current' => false]);
        }

        $data['object_id'] = $objectId;
        $data['created_by'] = $this->userId;
        $data['created_at'] = date('Y-m-d H:i:s');

        $id = Capsule::table('spectrum_location')->insertGetId($data);
        $this->logAudit($objectId, 'location', $id, 'create', $data);

        return $id;
    }

    public function getCurrentLocation($objectId)
    {
        return Capsule::table('spectrum_location')
            ->where('object_id', $objectId)
            ->where(function ($query) {
                $query->where('is_current', 1)
                    ->orWhere('location_type', 'current');
            })
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function getLocationHistory($objectId)
    {
        return Capsule::table('spectrum_location')
            ->where('object_id', $objectId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function createMovement($objectId, $data)
    {
        $data['object_id'] = $objectId;
        $data['created_by'] = $this->userId;
        $data['created_at'] = date('Y-m-d H:i:s');

        $id = Capsule::table('spectrum_movement')->insertGetId($data);
        $this->logAudit($objectId, 'movement', $id, 'create', $data);

        // Update current location if location_to is provided
        if (!empty($data['location_to'])) {
            Capsule::table('spectrum_location')
                ->where('object_id', $objectId)
                ->update(['is_current' => false]);

            Capsule::table('spectrum_location')
                ->where('id', $data['location_to'])
                ->update(['is_current' => true]);
        }

        return $id;
    }

    // ========================================================================
    // LOANS IN
    // ========================================================================

    public function createLoanIn($objectId, $data)
    {
        $data['object_id'] = $objectId;
        $data['created_by'] = $this->userId;
        $data['created_at'] = date('Y-m-d H:i:s');

        $id = Capsule::table('spectrum_loan_in')->insertGetId($data);
        $this->logAudit($objectId, 'loan_in', $id, 'create', $data);

        return $id;
    }

    public function getLoanIn($id)
    {
        return Capsule::table('spectrum_loan_in')->where('id', $id)->first();
    }

    public function getLoansIn($objectId)
    {
        return Capsule::table('spectrum_loan_in')
            ->where('object_id', $objectId)
            ->orderBy('loan_in_date', 'desc')
            ->get();
    }

    public function getActiveLoansIn()
    {
        return Capsule::table('spectrum_loan_in as l')
            ->join('information_object as io', 'l.object_id', '=', 'io.id')
            ->leftJoin('information_object_i18n as ioi', function ($join) {
                $join->on('io.id', '=', 'ioi.id')
                    ->where('ioi.culture', '=', $this->culture);
            })
            ->where('l.loan_status', 'active')
            ->select('l.*', 'ioi.title as object_title')
            ->orderBy('l.loan_return_date', 'asc')
            ->get();
    }

    public function getOverdueLoansIn()
    {
        return Capsule::table('spectrum_loan_in as l')
            ->join('information_object as io', 'l.object_id', '=', 'io.id')
            ->leftJoin('information_object_i18n as ioi', function ($join) {
                $join->on('io.id', '=', 'ioi.id')
                    ->where('ioi.culture', '=', $this->culture);
            })
            ->where('l.loan_status', 'active')
            ->where('l.loan_return_date', '<', date('Y-m-d H:i:s'))
            ->whereNull('l.actual_return_date')
            ->select('l.*', 'ioi.title as object_title')
            ->orderBy('l.loan_return_date', 'asc')
            ->get();
    }

    public function returnLoanIn($id, string $returnDate, ?string $note = null)
    {
        $old = $this->getLoanIn($id);
        $data = [
            'actual_return_date' => $returnDate,
            'loan_status' => 'returned',
            'updated_by' => $this->userId,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($note) {
            $data['loan_note'] = $old->loan_note ? $old->loan_note."\n".$note : $note;
        }

        $result = Capsule::table('spectrum_loan_in')->where('id', $id)->update($data);
        $this->logAudit($old->object_id, 'loan_in', $id, 'update', $data, (array) $old);

        return $result > 0;
    }

    // ========================================================================
    // LOANS OUT
    // ========================================================================

    public function createLoanOut($objectId, $data)
    {
        $data['object_id'] = $objectId;
        $data['created_by'] = $this->userId;
        $data['created_at'] = date('Y-m-d H:i:s');

        $id = Capsule::table('spectrum_loan_out')->insertGetId($data);
        $this->logAudit($objectId, 'loan_out', $id, 'create', $data);

        return $id;
    }

    public function getLoanOut($id)
    {
        return Capsule::table('spectrum_loan_out')->where('id', $id)->first();
    }

    public function getLoansOut($objectId)
    {
        return Capsule::table('spectrum_loan_out')
            ->where('object_id', $objectId)
            ->orderBy('loan_out_date', 'desc')
            ->get();
    }

    public function getActiveLoansOut()
    {
        return Capsule::table('spectrum_loan_out as l')
            ->join('information_object as io', 'l.object_id', '=', 'io.id')
            ->leftJoin('information_object_i18n as ioi', function ($join) {
                $join->on('io.id', '=', 'ioi.id')
                    ->where('ioi.culture', '=', $this->culture);
            })
            ->where('l.loan_status', 'active')
            ->select('l.*', 'ioi.title as object_title')
            ->orderBy('l.loan_return_date', 'asc')
            ->get();
    }

    public function getOverdueLoansOut()
    {
        return Capsule::table('spectrum_loan_out as l')
            ->join('information_object as io', 'l.object_id', '=', 'io.id')
            ->leftJoin('information_object_i18n as ioi', function ($join) {
                $join->on('io.id', '=', 'ioi.id')
                    ->where('ioi.culture', '=', $this->culture);
            })
            ->where('l.loan_status', 'active')
            ->where('l.loan_return_date', '<', date('Y-m-d H:i:s'))
            ->whereNull('l.actual_return_date')
            ->select('l.*', 'ioi.title as object_title')
            ->orderBy('l.loan_return_date', 'asc')
            ->get();
    }

    public function returnLoanOut($id, string $returnDate, ?string $note = null)
    {
        $old = $this->getLoanOut($id);
        $data = [
            'actual_return_date' => $returnDate,
            'loan_status' => 'returned',
            'updated_by' => $this->userId,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($note) {
            $data['loan_note'] = $old->loan_note ? $old->loan_note."\n".$note : $note;
        }

        $result = Capsule::table('spectrum_loan_out')->where('id', $id)->update($data);
        $this->logAudit($old->object_id, 'loan_out', $id, 'update', $data, (array) $old);

        return $result > 0;
    }

    // ========================================================================
    // CONDITION CHECKING
    // ========================================================================

    public function createConditionCheck($objectId, $data)
    {
        $data['object_id'] = $objectId;
        $data['created_by'] = $this->userId;
        $data['created_at'] = date('Y-m-d H:i:s');

        $id = Capsule::table('spectrum_condition_check')->insertGetId($data);
        $this->logAudit($objectId, 'condition', $id, 'create', $data);

        return $id;
    }

    public function getLatestConditionCheck($objectId)
    {
        return Capsule::table('spectrum_condition_check')
            ->where('object_id', $objectId)
            ->orderBy('check_date', 'desc')
            ->first();
    }

    public function getConditionHistory($objectId)
    {
        return Capsule::table('spectrum_condition_check')
            ->where('object_id', $objectId)
            ->orderBy('check_date', 'desc')
            ->get();
    }

    public function getObjectsNeedingConditionCheck()
    {
        return Capsule::table('spectrum_condition_check as cc')
            ->join('information_object as io', 'cc.object_id', '=', 'io.id')
            ->leftJoin('information_object_i18n as ioi', function ($join) {
                $join->on('io.id', '=', 'ioi.id')
                    ->where('ioi.culture', '=', $this->culture);
            })
            ->whereNotNull('cc.next_check_date')
            ->where('cc.next_check_date', '<=', date('Y-m-d H:i:s'))
            ->select('cc.*', 'ioi.title as object_title')
            ->orderBy('cc.next_check_date', 'asc')
            ->get();
    }

    // ========================================================================
    // CONSERVATION
    // ========================================================================

    public function createConservation($objectId, $data)
    {
        $data['object_id'] = $objectId;
        $data['created_by'] = $this->userId;
        $data['created_at'] = date('Y-m-d H:i:s');

        $id = Capsule::table('spectrum_conservation')->insertGetId($data);
        $this->logAudit($objectId, 'conservation', $id, 'create', $data);

        return $id;
    }

    public function getConservation($id)
    {
        return Capsule::table('spectrum_conservation')->where('id', $id)->first();
    }

    public function getConservationHistory($objectId)
    {
        return Capsule::table('spectrum_conservation')
            ->where('object_id', $objectId)
            ->orderBy('treatment_date', 'desc')
            ->get();
    }

    public function updateConservation($id, $data)
    {
        $old = $this->getConservation($id);
        $data['updated_by'] = $this->userId;
        $data['updated_at'] = date('Y-m-d H:i:s');

        $result = Capsule::table('spectrum_conservation')->where('id', $id)->update($data);
        $this->logAudit($old->object_id, 'conservation', $id, 'update', $data, (array) $old);

        return $result > 0;
    }

    // ========================================================================
    // OBJECT EXIT
    // ========================================================================

    public function createObjectExit($objectId, $data)
    {
        $data['object_id'] = $objectId;
        $data['created_by'] = $this->userId;
        $data['created_at'] = date('Y-m-d H:i:s');

        $id = Capsule::table('spectrum_object_exit')->insertGetId($data);
        $this->logAudit($objectId, 'exit', $id, 'create', $data);

        return $id;
    }

    public function getObjectExit($id)
    {
        return Capsule::table('spectrum_object_exit')->where('id', $id)->first();
    }

    public function getObjectExits($objectId)
    {
        return Capsule::table('spectrum_object_exit')
            ->where('object_id', $objectId)
            ->orderBy('exit_date', 'desc')
            ->get();
    }

    // ========================================================================
    // DEACCESSION
    // ========================================================================

    public function createDeaccession($objectId, $data)
    {
        $data['object_id'] = $objectId;
        $data['created_by'] = $this->userId;
        $data['created_at'] = date('Y-m-d H:i:s');

        $id = Capsule::table('spectrum_deaccession')->insertGetId($data);
        $this->logAudit($objectId, 'deaccession', $id, 'create', $data);

        return $id;
    }

    public function getDeaccession($id)
    {
        return Capsule::table('spectrum_deaccession')->where('id', $id)->first();
    }

    public function getDeaccessions($objectId)
    {
        return Capsule::table('spectrum_deaccession')
            ->where('object_id', $objectId)
            ->orderBy('deaccession_date', 'desc')
            ->get();
    }

    public function updateDeaccession($id, $data)
    {
        $old = $this->getDeaccession($id);
        $data['updated_by'] = $this->userId;
        $data['updated_at'] = date('Y-m-d H:i:s');

        $result = Capsule::table('spectrum_deaccession')->where('id', $id)->update($data);
        $this->logAudit($old->object_id, 'deaccession', $id, 'update', $data, (array) $old);

        return $result > 0;
    }

    // ========================================================================
    // VALUATION
    // ========================================================================

    public function createValuation($objectId, $data)
    {
        // Set previous current valuation to not current for same type
        if (!empty($data['is_current']) && $data['is_current']) {
            Capsule::table('spectrum_valuation')
                ->where('object_id', $objectId)
                ->where('valuation_type', $data['valuation_type'] ?? null)
                ->where('is_current', true)
                ->update(['is_current' => false]);
        }

        $data['object_id'] = $objectId;
        $data['created_by'] = $this->userId;
        $data['created_at'] = date('Y-m-d H:i:s');

        $id = Capsule::table('spectrum_valuation')->insertGetId($data);
        $this->logAudit($objectId, 'valuation', $id, 'create', $data);

        return $id;
    }

    public function getValuationHistory($objectId)
    {
        return Capsule::table('spectrum_valuation')
            ->where('object_id', $objectId)
            ->orderBy('valuation_date', 'desc')
            ->get();
    }

    public function getTotalInsuranceValue(?int $repositoryId = null): float
    {
        $query = Capsule::table('spectrum_valuation as v')
            ->join('information_object as io', 'v.object_id', '=', 'io.id')
            ->where('v.is_current', true)
            ->where('v.valuation_type', 'insurance');

        if ($repositoryId) {
            $query->where('io.repository_id', $repositoryId);
        }

        return (float) $query->sum('v.valuation_amount');
    }

    public function getAuditLog($objectId)
    {
        return Capsule::table('spectrum_audit_log')
            ->where('object_id', $objectId)
            ->orderBy('action_date', 'desc')
            ->get();
    }

    public function getAuditLogByProcedure($procedureType, $procedureId)
    {
        return Capsule::table('spectrum_audit_log')
            ->where('procedure_type', $procedureType)
            ->where('procedure_id', $procedureId)
            ->orderBy('action_date', 'desc')
            ->get();
    }

    // ========================================================================
    // REPORTS
    // ========================================================================

    public function getObjectSummary($objectId)
    {
        return [
            'current_location' => $this->getCurrentLocation($objectId),
            'latest_condition' => $this->getLatestConditionCheck($objectId),
            'current_valuation' => $this->getCurrentValuation($objectId),
            'active_loans_in' => Capsule::table('spectrum_loan_in')
                ->where('object_id', $objectId)
                ->where('loan_status', 'active')
                ->count(),
            'active_loans_out' => Capsule::table('spectrum_loan_out')
                ->where('object_id', $objectId)
                ->where('loan_status', 'active')
                ->count(),
            'movement_count' => Capsule::table('spectrum_movement')
                ->where('object_id', $objectId)
                ->count(),
            'conservation_count' => Capsule::table('spectrum_conservation')
                ->where('object_id', $objectId)
                ->count(),
        ];
    }

    public function getDashboardStats()
    {
        return [
            'active_loans_in' => Capsule::table('spectrum_loan_in')
                ->where('loan_status', 'active')
                ->count(),
            'active_loans_out' => Capsule::table('spectrum_loan_out')
                ->where('loan_status', 'active')
                ->count(),
            'overdue_loans_in' => $this->getOverdueLoansIn()->count(),
            'overdue_loans_out' => $this->getOverdueLoansOut()->count(),
            'pending_condition_checks' => $this->getObjectsNeedingConditionCheck()->count(),
            'total_insurance_value' => $this->getTotalInsuranceValue(),
            'recent_movements' => Capsule::table('spectrum_condition_check')
                ->where('check_date', '>=', date('Y-m-d H:i:s', strtotime('-30 days')))
                ->count(),
            'recent_acquisitions' => Capsule::table('spectrum_condition_check')
                ->where('check_date', '>=', date('Y-m-d H:i:s', strtotime('-30 days')))
                ->count(),
        ];
    }

    public function getLocations($objectId)
    {
        return Capsule::table('spectrum_location')
            ->where('object_id', $objectId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function deleteObjectEntry($id)
    {
        return Capsule::table('spectrum_object_entry')->where('id', $id)->delete();
    }

    public function deleteAcquisition($id)
    {
        return Capsule::table('spectrum_acquisition')->where('id', $id)->delete();
    }

    public function deleteLocation($id)
    {
        return Capsule::table('spectrum_location')->where('id', $id)->delete();
    }

    public function deleteMovement($id)
    {
        return Capsule::table('spectrum_movement')->where('id', $id)->delete();
    }

    public function deleteLoanIn($id)
    {
        return Capsule::table('spectrum_loan_in')->where('id', $id)->delete();
    }

    public function deleteLoanOut($id)
    {
        return Capsule::table('spectrum_loan_out')->where('id', $id)->delete();
    }

    public function deleteConditionCheck($id)
    {
        return Capsule::table('spectrum_condition_check')->where('id', $id)->delete();
    }

    public function deleteConservation($id)
    {
        return Capsule::table('spectrum_conservation')->where('id', $id)->delete();
    }

    public function deleteObjectExit($id)
    {
        return Capsule::table('spectrum_object_exit')->where('id', $id)->delete();
    }

    public function deleteDeaccession($id)
    {
        return Capsule::table('spectrum_deaccession')->where('id', $id)->delete();
    }

    public function deleteValuation($id)
    {
        return Capsule::table('spectrum_valuation')->where('id', $id)->delete();
    }

    public function updateLocation($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        Capsule::table('spectrum_location')
            ->where('id', $id)
            ->update($data);

        return $id;
    }

    public function updateMovement($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        Capsule::table('spectrum_movement')
            ->where('id', $id)
            ->update($data);

        return $id;
    }

    public function updateLoanIn($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        Capsule::table('spectrum_loan_in')
            ->where('id', $id)
            ->update($data);

        return $id;
    }

    public function updateLoanOut($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        Capsule::table('spectrum_loan_out')
            ->where('id', $id)
            ->update($data);

        return $id;
    }

    public function updateConditionCheck($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        Capsule::table('spectrum_condition_check')
            ->where('id', $id)
            ->update($data);

        return $id;
    }

    public function updateObjectExit($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        Capsule::table('spectrum_object_exit')
            ->where('id', $id)
            ->update($data);

        return $id;
    }

    public function updateValuation($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        Capsule::table('spectrum_valuation')
            ->where('id', $id)
            ->update($data);

        return $id;
    }

    public function getMovementHistory($objectId)
    {
        $result = Capsule::table('spectrum_movement')->where('id', $id)->first();

        return Capsule::table('spectrum_movement')
            ->where('object_id', $objectId)
            ->orderBy('movement_date', 'desc')
            ->get();
    }

    public function getMovement($id)
    {
        return Capsule::table('spectrum_movement')->where('id', $id)->first();
    }

    public function getLocation($id)
    {
        return Capsule::table('spectrum_location')->where('id', $id)->first();
    }

    public function getCurrentValuation($objectId)
    {
        return Capsule::table('spectrum_valuation')
            ->where('object_id', $objectId)
            ->where('is_current', 1)
            ->orderBy('valuation_date', 'desc')
            ->first();
    }

    // ========================================================================
    // AUDIT LOG
    // ========================================================================

    protected function logAudit(
        $objectId,
        $procedureType,
        $procedureId,
        $action,
        $newValues = [],
        $oldValues = []
    ) {
        Capsule::table('spectrum_audit_log')->insert([
            'object_id' => $objectId,
            'procedure_type' => $procedureType,
            'procedure_id' => $procedureId,
            'action' => $action,
            'action_date' => date('Y-m-d H:i:s'),
            'user_id' => $this->userId,
            'user_name' => $this->getUserName(),
            'ip_address' => $this->getIpAddress(),
            'old_values' => !empty($oldValues) ? json_encode($oldValues) : null,
            'new_values' => !empty($newValues) ? json_encode($newValues) : null,
        ]);
    }

    // ========================================================================
    // HELPERS
    // ========================================================================

    protected function getUserName(): ?string
    {
        if (!$this->userId) {
            return null;
        }

        $user = Capsule::table('user')
            ->where('id', $this->userId)
            ->first();

        return $user->username ?? null;
    }

    protected function getIpAddress(): ?string
    {
        return $_SERVER['REMOTE_ADDR'] ?? null;
    }
}
