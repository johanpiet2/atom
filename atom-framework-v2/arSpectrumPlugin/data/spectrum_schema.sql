-- ============================================================================
-- Spectrum 5.0 Collections Management Database Schema
-- Implements primary procedures for museum collections management
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 1. OBJECT ENTRY - Tracking items arriving at museum
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS spectrum_object_entry (
    id INT AUTO_INCREMENT PRIMARY KEY,
    object_id INT NOT NULL,
    entry_number VARCHAR(50) NOT NULL,
    entry_date DATE NOT NULL,
    entry_method VARCHAR(50),  -- 'deposit', 'loan_in', 'purchase', 'donation', 'found'
    entry_reason TEXT,
    depositor_name VARCHAR(255),
    depositor_contact TEXT,
    depositor_address TEXT,
    current_owner VARCHAR(255),
    owner_contact TEXT,
    return_date DATE,
    entry_note TEXT,
    received_by VARCHAR(255),
    packing_note TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    updated_by INT,
    FOREIGN KEY (object_id) REFERENCES information_object(id) ON DELETE CASCADE,
    INDEX idx_entry_number (entry_number),
    INDEX idx_entry_date (entry_date),
    INDEX idx_depositor (depositor_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 2. ACQUISITION - Recording how objects are acquired
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS spectrum_acquisition (
    id INT AUTO_INCREMENT PRIMARY KEY,
    object_id INT NOT NULL,
    acquisition_number VARCHAR(50) NOT NULL,
    acquisition_date DATE,
    acquisition_method VARCHAR(50),  -- 'purchase', 'gift', 'bequest', 'exchange', 'field_collection', 'transfer'
    acquisition_source VARCHAR(255),
    source_contact TEXT,
    acquisition_reason TEXT,
    acquisition_authorization VARCHAR(255),
    authorization_date DATE,
    funding_source VARCHAR(255),
    purchase_price DECIMAL(15,2),
    price_currency VARCHAR(10),
    group_purchase_price DECIMAL(15,2),
    accession_date DATE,
    accession_number VARCHAR(50),
    title_transfer_date DATE,
    ownership_history TEXT,
    acquisition_note TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    updated_by INT,
    FOREIGN KEY (object_id) REFERENCES information_object(id) ON DELETE CASCADE,
    INDEX idx_acquisition_number (acquisition_number),
    INDEX idx_accession_number (accession_number),
    INDEX idx_acquisition_date (acquisition_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 3. LOCATION & MOVEMENT CONTROL - Where objects are/were
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS spectrum_location (
    id INT AUTO_INCREMENT PRIMARY KEY,
    object_id INT NOT NULL,
    location_type VARCHAR(50),  -- 'current', 'normal', 'temporary'
    location_name VARCHAR(255) NOT NULL,
    location_building VARCHAR(255),
    location_floor VARCHAR(50),
    location_room VARCHAR(100),
    location_unit VARCHAR(100),
    location_shelf VARCHAR(100),
    location_box VARCHAR(100),
    location_note TEXT,
    fitness_for_purpose TEXT,
    security_note TEXT,
    environment_note TEXT,
    is_current BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (object_id) REFERENCES information_object(id) ON DELETE CASCADE,
    INDEX idx_location_current (object_id, is_current),
    INDEX idx_location_name (location_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS spectrum_movement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    object_id INT NOT NULL,
    movement_reference VARCHAR(50),
    movement_date DATETIME NOT NULL,
    movement_reason VARCHAR(100),  -- 'exhibition', 'loan', 'conservation', 'storage_reorganization', 'photography', 'research'
    location_from INT,
    location_to INT,
    movement_method VARCHAR(100),  -- 'hand_carry', 'trolley', 'crate', 'courier'
    movement_contact VARCHAR(255),
    handler_name VARCHAR(255),
    condition_before TEXT,
    condition_after TEXT,
    planned_return_date DATE,
    actual_return_date DATE,
    movement_note TEXT,
    removal_authorization VARCHAR(255),
    authorization_date DATE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (object_id) REFERENCES information_object(id) ON DELETE CASCADE,
    FOREIGN KEY (location_from) REFERENCES spectrum_location(id),
    FOREIGN KEY (location_to) REFERENCES spectrum_location(id),
    INDEX idx_movement_date (movement_date),
    INDEX idx_movement_reference (movement_reference)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 4. LOANS IN - Borrowing from others
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS spectrum_loan_in (
    id INT AUTO_INCREMENT PRIMARY KEY,
    object_id INT NOT NULL,
    loan_in_number VARCHAR(50) NOT NULL,
    lender_name VARCHAR(255) NOT NULL,
    lender_contact TEXT,
    lender_address TEXT,
    loan_in_date DATE NOT NULL,
    loan_return_date DATE,
    actual_return_date DATE,
    loan_purpose VARCHAR(100),  -- 'exhibition', 'research', 'photography', 'education'
    loan_conditions TEXT,
    insurance_value DECIMAL(15,2),
    insurance_currency VARCHAR(10),
    insurance_reference VARCHAR(100),
    insurance_note TEXT,
    loan_agreement_date DATE,
    loan_agreement_reference VARCHAR(100),
    special_requirements TEXT,
    loan_status VARCHAR(50) DEFAULT 'active',  -- 'requested', 'approved', 'active', 'returned', 'cancelled'
    loan_note TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    updated_by INT,
    FOREIGN KEY (object_id) REFERENCES information_object(id) ON DELETE CASCADE,
    INDEX idx_loan_in_number (loan_in_number),
    INDEX idx_loan_in_status (loan_status),
    INDEX idx_loan_return_date (loan_return_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 5. LOANS OUT - Lending to others
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS spectrum_loan_out (
    id INT AUTO_INCREMENT PRIMARY KEY,
    object_id INT NOT NULL,
    loan_out_number VARCHAR(50) NOT NULL,
    borrower_name VARCHAR(255) NOT NULL,
    borrower_contact TEXT,
    borrower_address TEXT,
    venue_name VARCHAR(255),
    venue_address TEXT,
    loan_out_date DATE NOT NULL,
    loan_return_date DATE,
    actual_return_date DATE,
    loan_purpose VARCHAR(100),
    loan_conditions TEXT,
    insurance_value DECIMAL(15,2),
    insurance_currency VARCHAR(10),
    insurance_reference VARCHAR(100),
    indemnity_reference VARCHAR(100),
    loan_agreement_date DATE,
    loan_agreement_reference VARCHAR(100),
    exhibition_title VARCHAR(255),
    exhibition_dates TEXT,
    special_requirements TEXT,
    courier_required BOOLEAN DEFAULT FALSE,
    courier_name VARCHAR(255),
    loan_status VARCHAR(50) DEFAULT 'active',
    loan_note TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    updated_by INT,
    FOREIGN KEY (object_id) REFERENCES information_object(id) ON DELETE CASCADE,
    INDEX idx_loan_out_number (loan_out_number),
    INDEX idx_loan_out_status (loan_status),
    INDEX idx_borrower (borrower_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 6. CONDITION CHECKING - Condition assessments over time
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS spectrum_condition_check (
    id INT AUTO_INCREMENT PRIMARY KEY,
    object_id INT NOT NULL,
    condition_reference VARCHAR(50),
    check_date DATETIME NOT NULL,
    check_reason VARCHAR(100),  -- 'acquisition', 'loan_in', 'loan_out', 'return', 'routine', 'conservation', 'damage_report'
    checked_by VARCHAR(255) NOT NULL,
    overall_condition VARCHAR(50),  -- 'excellent', 'good', 'fair', 'poor', 'unacceptable'
    condition_note TEXT,
    completeness_note TEXT,
    hazard_note TEXT,
    technical_assessment TEXT,
    recommended_treatment TEXT,
    treatment_priority VARCHAR(50),  -- 'urgent', 'high', 'medium', 'low', 'none'
    next_check_date DATE,
    environment_recommendation TEXT,
    handling_recommendation TEXT,
    display_recommendation TEXT,
    storage_recommendation TEXT,
    packing_recommendation TEXT,
    image_reference TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (object_id) REFERENCES information_object(id) ON DELETE CASCADE,
    INDEX idx_condition_date (check_date),
    INDEX idx_condition_reference (condition_reference),
    INDEX idx_overall_condition (overall_condition)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 7. CONSERVATION & TREATMENT - Conservation work done
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS spectrum_conservation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    object_id INT NOT NULL,
    conservation_reference VARCHAR(50),
    treatment_date DATE NOT NULL,
    treatment_end_date DATE,
    conservator_name VARCHAR(255) NOT NULL,
    conservator_organization VARCHAR(255),
    condition_before TEXT,
    treatment_proposal TEXT,
    treatment_performed TEXT,
    materials_used TEXT,
    condition_after TEXT,
    treatment_cost DECIMAL(15,2),
    cost_currency VARCHAR(10),
    next_treatment_date DATE,
    treatment_note TEXT,
    report_reference VARCHAR(100),
    image_before TEXT,
    image_after TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    updated_by INT,
    FOREIGN KEY (object_id) REFERENCES information_object(id) ON DELETE CASCADE,
    INDEX idx_conservation_reference (conservation_reference),
    INDEX idx_treatment_date (treatment_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 8. OBJECT EXIT - Items leaving the museum
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS spectrum_object_exit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    object_id INT NOT NULL,
    exit_number VARCHAR(50) NOT NULL,
    exit_date DATE NOT NULL,
    exit_reason VARCHAR(50),  -- 'loan_out', 'return_to_owner', 'deaccession', 'disposal', 'transfer', 'destruction'
    exit_destination VARCHAR(255),
    recipient_name VARCHAR(255),
    recipient_contact TEXT,
    recipient_address TEXT,
    authorization_name VARCHAR(255),
    authorization_date DATE,
    packing_note TEXT,
    dispatch_note TEXT,
    courier_name VARCHAR(255),
    expected_return_date DATE,
    exit_note TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (object_id) REFERENCES information_object(id) ON DELETE CASCADE,
    INDEX idx_exit_number (exit_number),
    INDEX idx_exit_date (exit_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 9. DEACCESSION & DISPOSAL - Removing from collection
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS spectrum_deaccession (
    id INT AUTO_INCREMENT PRIMARY KEY,
    object_id INT NOT NULL,
    deaccession_number VARCHAR(50) NOT NULL,
    deaccession_date DATE NOT NULL,
    proposal_date DATE,
    authorization_date DATE,
    authorized_by VARCHAR(255),
    deaccession_reason TEXT,  -- 'duplicate', 'outside_scope', 'poor_condition', 'repatriation', 'legal_requirement'
    disposal_method VARCHAR(50),  -- 'transfer', 'sale', 'exchange', 'destruction', 'return', 'loss'
    disposal_date DATE,
    disposal_recipient VARCHAR(255),
    disposal_price DECIMAL(15,2),
    disposal_currency VARCHAR(10),
    new_owner VARCHAR(255),
    new_owner_contact TEXT,
    legal_requirements_met BOOLEAN DEFAULT FALSE,
    documentation_complete BOOLEAN DEFAULT FALSE,
    deaccession_note TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    updated_by INT,
    FOREIGN KEY (object_id) REFERENCES information_object(id) ON DELETE CASCADE,
    INDEX idx_deaccession_number (deaccession_number),
    INDEX idx_deaccession_date (deaccession_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 10. VALUATION - Insurance and other valuations
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS spectrum_valuation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    object_id INT NOT NULL,
    valuation_reference VARCHAR(50),
    valuation_date DATE NOT NULL,
    valuation_type VARCHAR(50),  -- 'insurance', 'indemnity', 'auction', 'probate', 'donation', 'internal'
    valuation_amount DECIMAL(15,2) NOT NULL,
    valuation_currency VARCHAR(10) DEFAULT 'ZAR',
    valuer_name VARCHAR(255),
    valuer_organization VARCHAR(255),
    valuation_note TEXT,
    renewal_date DATE,
    is_current BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (object_id) REFERENCES information_object(id) ON DELETE CASCADE,
    INDEX idx_valuation_date (valuation_date),
    INDEX idx_valuation_current (object_id, is_current)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- 11. AUDIT TRAIL - Track all procedure actions
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS spectrum_audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    object_id INT,
    procedure_type VARCHAR(50) NOT NULL,  -- 'entry', 'acquisition', 'location', 'movement', 'loan_in', 'loan_out', 'condition', 'conservation', 'exit', 'deaccession', 'valuation'
    procedure_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,  -- 'create', 'update', 'delete', 'view'
    action_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    user_id INT,
    user_name VARCHAR(255),
    ip_address VARCHAR(45),
    old_values TEXT,
    new_values TEXT,
    note TEXT,
    INDEX idx_audit_object (object_id),
    INDEX idx_audit_procedure (procedure_type, procedure_id),
    INDEX idx_audit_date (action_date),
    INDEX idx_audit_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
