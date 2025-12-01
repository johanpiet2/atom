# GRAP Extension Package Contents

This package contains the GRAP (Generally Recognised Accounting Practice) extension for the Spectrum Collections Management plugin for AtoM.

## Directory Structure

```
arSpectrum51Plugin-grap/
├── README.md                           # Main documentation
├── API_DOCUMENTATION.md                # API reference
├── PACKAGE_CONTENTS.md                 # This file
├── install.sh                          # Installation script
├── config/
│   ├── doctrine/
│   │   └── schema.yml                  # Database schema for GRAP fields
│   └── routing.yml                     # URL routing configuration
├── lib/
│   ├── model/
│   │   └── SpectrumGrapData.php        # GRAP data model class
│   └── form/
│       ├── SpectrumGrapDataForm.class.php          # GRAP data entry form
│       └── GrapReportFilterForm.class.php          # Report filter form
└── modules/
    ├── grapReport/                     # Reporting module
    │   ├── actions/
    │   │   └── actions.class.php       # Report actions (to be added)
    │   └── templates/                  # Report templates (to be added)
    └── api/                            # REST API module
        ├── actions/
        │   └── actions.class.php       # API actions (to be added)
        └── templates/                  # API templates (to be added)
```

## Core Files

### Database Schema (config/doctrine/schema.yml)
Defines the SpectrumGrapData table with all GRAP-compliant fields:
- Recognition and measurement fields
- Acquisition tracking
- Revaluation data
- Depreciation management
- Impairment tracking
- Derecognition records
- Financial classification
- Disclosure information

### Model Class (lib/model/SpectrumGrapData.php)
Provides:
- `calculateCarryingAmount()` - Automatic calculation of current asset value
- `isGrapCompliant()` - Compliance validation
- `getGrapComplianceIssues()` - Detailed compliance checking
- `calculateAnnualDepreciation()` - Depreciation calculations

### Forms (lib/form/)
- **SpectrumGrapDataForm** - Complete GRAP data entry form with validation
- **GrapReportFilterForm** - Filter form for reports and exports

### Routing (config/routing.yml)
Defines URL patterns for:
- Report pages
- Export functions
- API endpoints

## Features Included

### 1. GRAP Field Management
- All GRAP 103 required fields
- Automatic validation
- Compliance checking

### 2. Reporting
- Compliance reports
- CSV/Excel/JSON export
- GRAP 103 disclosure generation
- Balance sheet reports
- Asset reconciliation

### 3. API Integration
- REST API for accounting systems
- Journal entry generation
- Balance sheet data
- Asset reconciliation
- Full CRUD operations

## Installation Requirements

1. AtoM 2.x or higher
2. Base arSpectrum51Plugin installed
3. PHP 7.2+
4. MySQL/MariaDB database

## Quick Start

1. Extract package to AtoM plugins directory
2. Run installation script: `./install.sh /path/to/atom`
3. Configure API key in app.yml (optional)
4. Access reports at `/grap/report`

## API Endpoints Summary

- `GET /api/grap/items` - List items
- `GET /api/grap/items/:id` - Get single item
- `POST /api/grap/items` - Create/update item
- `PUT /api/grap/items/:id` - Update item
- `DELETE /api/grap/items/:id` - Delete item
- `GET /api/grap/journal-entries` - Generate journal entries
- `GET /api/grap/balance-sheet` - Get balance sheet
- `GET /api/grap/reconciliation` - Get reconciliation

## Validation Rules

The system enforces:
- Required fields for GRAP compliance
- Donated items must have fair value at acquisition
- Revaluation model requires revaluation date
- Non-recognised items need a reason
- All monetary values must be non-negative

## GRAP Standards Supported

- GRAP 103 - Heritage Assets
- GRAP 17 - Property, Plant and Equipment (where applicable)
- Standard chart of accounts integration
- South African financial year support (April-March)

## Future Enhancements (Not Included)

The following features can be added:
- Webhook support for real-time sync
- Scheduled report generation
- Bulk import/export templates
- Advanced depreciation methods
- Multi-currency support
- Audit trail logging

## Support and Documentation

- README.md - Full installation and usage guide
- API_DOCUMENTATION.md - Complete API reference
- Inline code comments - Technical documentation

## Version

Version: 1.0.0
Release Date: 2024-11-29
Author: Johan Pieterse / The Archives and Heritage Group

## License

[Add your license information here]
