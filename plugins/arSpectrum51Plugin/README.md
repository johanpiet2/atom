# Spectrum GRAP Extension for AtoM

This plugin extends the arSpectrum51Plugin to add GRAP (Generally Recognised Accounting Practice) compliance fields for South African museums and archives.

## Features

- GRAP-compliant data fields for heritage assets
- Recognition and measurement tracking
- Depreciation and impairment management
- Revaluation support
- Compliance checking and reporting
- Export capabilities (CSV, Excel, JSON)
- REST API for accounting system integration
- GRAP 103 disclosure generation

## Requirements

- AtoM 2.x or higher
- arSpectrum51Plugin (base Spectrum plugin)
- PHP 7.2+
- MySQL/MariaDB

## Installation

1. Extract this archive to your AtoM plugins directory:
   ```bash
   cd /path/to/atom/plugins
   tar -xzf arSpectrum51Plugin-grap.tar.gz
   ```

2. Build the database schema:
   ```bash
   cd /path/to/atom
   php symfony doctrine:build --all-classes
   php symfony propel:build-model
   ```

3. Run the database migration:
   ```bash
   php symfony doctrine:insert-sql
   ```

4. Clear the cache:
   ```bash
   php symfony cc
   ```

5. Configure API access (optional):
   Edit `apps/qubit/config/app.yml` and add:
   ```yaml
   all:
     grap_api_key: "your-secure-api-key-here"
   ```

## Usage

### Adding GRAP Data to Items

1. Navigate to an information object in AtoM
2. Edit the object
3. Look for the "GRAP Compliance Data" section
4. Fill in the required fields:
   - Recognition Status
   - Measurement Basis
   - Initial Recognition Date and Value
   - Acquisition Method
   - Asset Class
   - GL Account Code

### Generating Reports

Access the GRAP reports at: `/grap/report`

#### Export Options:
- CSV: `/grap/report/export?format=csv`
- Excel: `/grap/report/export?format=excel`
- JSON: `/grap/report/export?format=json`

#### Compliance Check:
View overall compliance status at: `/grap/compliance`

#### GRAP 103 Disclosure:
Generate disclosure notes at: `/grap/disclosure/103`

### API Endpoints

All API requests require authentication via API key in header:
```
X-API-Key: your-api-key
```

#### GET /api/grap/items
List all GRAP items with optional filters

Parameters:
- `asset_class` - Filter by asset class
- `recognition_status` - Filter by recognition status
- `measurement_basis` - Filter by measurement basis
- `date_from` - From date
- `date_to` - To date
- `page` - Page number (default: 1)
- `limit` - Items per page (max: 100)

#### GET /api/grap/items/:id
Get a single item by ID

#### POST /api/grap/items
Create or update GRAP data

Request body (JSON):
```json
{
  "information_object_id": 123,
  "recognition_status": "recognised",
  "measurement_basis": "cost_model",
  "initial_recognition_date": "2024-01-01",
  "initial_recognition_value": 50000.00,
  "acquisition_method_grap": "purchase",
  "asset_class": "heritage_asset",
  "gl_account_code": "1500"
}
```

#### GET /api/grap/journal-entries
Generate journal entries for accounting import

Parameters:
- `date_from` (required)
- `date_to` (required)

#### GET /api/grap/balance-sheet
Get balance sheet data

Parameters:
- `as_of_date` (default: today)

#### GET /api/grap/reconciliation
Get asset reconciliation

Parameters:
- `financial_year` (default: current year)

## GRAP Compliance Fields

### Recognition & Measurement
- Recognition Status (recognised/not recognised)
- Recognition Status Reason
- Measurement Basis (cost model/revaluation model)
- Initial Recognition Date
- Initial Recognition Value
- Carrying Amount (auto-calculated)

### Acquisition
- Acquisition Method (purchase/donation/transfer/exchange/other)
- Cost of Acquisition
- Fair Value at Acquisition
- Donor Restrictions

### Revaluation
- Last Revaluation Date
- Revaluation Amount
- Valuer Credentials
- Valuation Method
- Revaluation Frequency

### Depreciation
- Depreciation Policy
- Useful Life (years)
- Residual Value
- Depreciation Method
- Accumulated Depreciation

### Impairment
- Last Impairment Assessment Date
- Impairment Indicators
- Impairment Indicators Details
- Impairment Loss Amount

### Derecognition
- Derecognition Date
- Derecognition Reason
- Derecognition Value
- Gain/Loss on Derecognition

### Classification
- Asset Class
- GL Account Code
- Cost Center
- Fund Source

### Disclosure
- Restrictions on Use/Disposal
- Heritage Significance Rating
- Conservation Commitments
- Insurance Coverage (Required/Actual)

## Compliance Rules

The system validates GRAP compliance based on:

1. **Required Fields**: Recognition status, measurement basis, initial recognition date/value, acquisition method, asset class, GL account code

2. **Donated Items**: Must have fair value at acquisition

3. **Revaluation Model**: Requires revaluation date

4. **Non-Recognised Items**: Require a reason

## Integration with Accounting Systems

The API provides journal entries in a standard format that can be imported into:
- Pastel
- Sage
- SAP
- Oracle Financials
- Custom accounting systems

Export journal entries and import into your accounting system's journal import function.

## Support

For issues or questions:
- GitHub: [Add your repository URL]
- Email: [Add your support email]

## License

[Add your license information]

## Credits

Developed for The Archives and Heritage Group (The AHG)
Based on the Spectrum Collections Management Standard
GRAP standards from the South African Accounting Standards Board

## Version History

### 1.0.0 (2024-11-29)
- Initial release
- GRAP field support
- Reporting and export
- REST API
- Compliance checking
