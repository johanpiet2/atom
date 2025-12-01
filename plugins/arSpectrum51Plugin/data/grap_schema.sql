-- ============================================================================
-- GRAP (Generally Recognised Accounting Practice) Database Schema
-- Implements GRAP 103 Heritage Assets for South African public sector
-- ============================================================================

-- ----------------------------------------------------------------------------
-- GRAP DATA - Financial accounting data for heritage assets
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS spectrum_grap_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    information_object_id INT NOT NULL,
    
    -- Recognition & Measurement (GRAP 103.14-27)
    recognition_status VARCHAR(50),              -- 'recognised', 'not_recognised'
    recognition_status_reason VARCHAR(255),      -- Required if not_recognised
    measurement_basis VARCHAR(50),               -- 'cost_model', 'revaluation_model'
    initial_recognition_date DATE,
    initial_recognition_value DECIMAL(15,2),
    carrying_amount DECIMAL(15,2),               -- Calculated field
    
    -- Acquisition GRAP fields (GRAP 103.28-35)
    acquisition_method_grap VARCHAR(50),         -- 'purchase', 'donation', 'transfer', 'exchange', 'other'
    cost_of_acquisition DECIMAL(15,2),
    fair_value_at_acquisition DECIMAL(15,2),     -- Required for donated items
    donor_restrictions TEXT,
    
    -- Revaluation (GRAP 103.42-49)
    last_revaluation_date DATE,
    revaluation_amount DECIMAL(15,2),
    valuer_credentials VARCHAR(255),
    valuation_method VARCHAR(50),                -- 'market_approach', 'cost_approach', 'income_approach'
    revaluation_frequency VARCHAR(50),
    
    -- Depreciation (GRAP 103.50-58)
    depreciation_policy VARCHAR(50),             -- 'not_depreciated', 'depreciated'
    useful_life_years INT,
    residual_value DECIMAL(15,2),
    depreciation_method VARCHAR(50),             -- 'straight_line', 'reducing_balance'
    accumulated_depreciation DECIMAL(15,2),
    
    -- Impairment (GRAP 103.59-63)
    last_impairment_assessment_date DATE,
    impairment_indicators BOOLEAN DEFAULT FALSE,
    impairment_indicators_details TEXT,
    impairment_loss_amount DECIMAL(15,2),
    
    -- Derecognition (GRAP 103.64-69)
    derecognition_date DATE,
    derecognition_reason VARCHAR(50),
    derecognition_value DECIMAL(15,2),
    gain_loss_on_derecognition DECIMAL(15,2),
    
    -- Classification (GRAP 103.10-13)
    asset_class VARCHAR(50),                     -- 'heritage_asset', 'operational_asset', 'investment'
    gl_account_code VARCHAR(50),
    cost_center VARCHAR(50),
    fund_source VARCHAR(100),
    
    -- Disclosure requirements (GRAP 103.70-79)
    restrictions_use_disposal TEXT,
    heritage_significance_rating VARCHAR(50),
    conservation_commitments TEXT,
    insurance_coverage_required DECIMAL(15,2),
    insurance_coverage_actual DECIMAL(15,2),
    
    -- Timestamps
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign key
    FOREIGN KEY (information_object_id) REFERENCES information_object(id) ON DELETE CASCADE,
    
    -- Indexes for common queries
    INDEX idx_grap_io (information_object_id),
    INDEX idx_grap_asset_class (asset_class),
    INDEX idx_grap_recognition (recognition_status),
    INDEX idx_grap_gl_account (gl_account_code),
    INDEX idx_grap_cost_center (cost_center),
    INDEX idx_grap_recognition_date (initial_recognition_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- GRAP JOURNAL ENTRIES - Track financial transactions
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS spectrum_grap_journal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    grap_data_id INT NOT NULL,
    journal_date DATE NOT NULL,
    journal_type VARCHAR(50) NOT NULL,           -- 'recognition', 'revaluation', 'depreciation', 'impairment', 'derecognition'
    debit_account VARCHAR(50),
    credit_account VARCHAR(50),
    amount DECIMAL(15,2) NOT NULL,
    description TEXT,
    reference_number VARCHAR(100),
    posted_by INT,
    posted_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (grap_data_id) REFERENCES spectrum_grap_data(id) ON DELETE CASCADE,
    INDEX idx_journal_date (journal_date),
    INDEX idx_journal_type (journal_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- GRAP REVALUATION HISTORY - Track revaluation changes over time
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS spectrum_grap_revaluation_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    grap_data_id INT NOT NULL,
    revaluation_date DATE NOT NULL,
    previous_value DECIMAL(15,2),
    new_value DECIMAL(15,2),
    revaluation_surplus DECIMAL(15,2),
    valuer_name VARCHAR(255),
    valuer_credentials VARCHAR(255),
    valuation_method VARCHAR(50),
    valuation_report_reference VARCHAR(255),
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    
    FOREIGN KEY (grap_data_id) REFERENCES spectrum_grap_data(id) ON DELETE CASCADE,
    INDEX idx_reval_date (revaluation_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- GRAP DEPRECIATION SCHEDULE - Track depreciation over time
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS spectrum_grap_depreciation_schedule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    grap_data_id INT NOT NULL,
    fiscal_year INT NOT NULL,
    fiscal_period VARCHAR(20),                   -- 'Q1', 'Q2', 'Q3', 'Q4', 'annual'
    opening_value DECIMAL(15,2),
    depreciation_amount DECIMAL(15,2),
    closing_value DECIMAL(15,2),
    calculated_at DATETIME,
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (grap_data_id) REFERENCES spectrum_grap_data(id) ON DELETE CASCADE,
    UNIQUE KEY idx_deprec_period (grap_data_id, fiscal_year, fiscal_period),
    INDEX idx_fiscal_year (fiscal_year)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------------------
-- VIEWS FOR REPORTING
-- ----------------------------------------------------------------------------

-- Balance Sheet View
CREATE OR REPLACE VIEW v_grap_balance_sheet AS
SELECT 
    g.asset_class,
    g.gl_account_code,
    COUNT(*) as item_count,
    SUM(g.initial_recognition_value) as total_cost,
    SUM(COALESCE(g.accumulated_depreciation, 0)) as total_depreciation,
    SUM(COALESCE(g.revaluation_amount, g.initial_recognition_value) 
        - COALESCE(g.accumulated_depreciation, 0)) as carrying_amount
FROM spectrum_grap_data g
WHERE g.recognition_status = 'recognised'
GROUP BY g.asset_class, g.gl_account_code;

-- Compliance Summary View
CREATE OR REPLACE VIEW v_grap_compliance_summary AS
SELECT 
    COUNT(*) as total_items,
    SUM(CASE WHEN recognition_status IS NOT NULL 
             AND measurement_basis IS NOT NULL 
             AND initial_recognition_date IS NOT NULL 
             AND initial_recognition_value IS NOT NULL 
             AND acquisition_method_grap IS NOT NULL 
             THEN 1 ELSE 0 END) as compliant_items,
    SUM(CASE WHEN recognition_status IS NULL 
             OR measurement_basis IS NULL 
             OR initial_recognition_date IS NULL 
             OR initial_recognition_value IS NULL 
             OR acquisition_method_grap IS NULL 
             THEN 1 ELSE 0 END) as non_compliant_items
FROM spectrum_grap_data;

-- Heritage Assets Summary View (GRAP 103)
CREATE OR REPLACE VIEW v_grap_103_summary AS
SELECT 
    measurement_basis,
    COUNT(*) as item_count,
    SUM(initial_recognition_value) as total_initial_value,
    SUM(COALESCE(revaluation_amount, initial_recognition_value) 
        - COALESCE(accumulated_depreciation, 0)) as total_carrying_amount
FROM spectrum_grap_data
WHERE asset_class = 'heritage_asset'
  AND recognition_status = 'recognised'
GROUP BY measurement_basis;
