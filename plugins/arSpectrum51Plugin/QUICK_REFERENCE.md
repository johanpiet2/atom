# GRAP Extension Quick Reference

## Installation (5 minutes)

```bash
# 1. Extract to AtoM plugins directory
cd /usr/share/nginx/atom/plugins
tar -xzf arSpectrum51Plugin-grap.tar.gz

# 2. Run installer
cd arSpectrum51Plugin-grap
./install.sh /usr/share/nginx/atom

# 3. Configure API (optional)
# Edit: /usr/share/nginx/atom/apps/qubit/config/app.yml
# Add:
all:
  grap_api_key: "your-secure-key-here"
```

## Required GRAP Fields

When adding GRAP data to an item, you MUST complete:

1. **Recognition Status** - recognised or not_recognised
2. **Measurement Basis** - cost_model or revaluation_model  
3. **Initial Recognition Date** - When first recognised
4. **Initial Recognition Value** - Amount in Rands
5. **Acquisition Method** - purchase/donation/transfer/exchange/other
6. **Asset Class** - heritage_asset/operational_asset/investment
7. **GL Account Code** - Your chart of accounts code

### Special Rules

- **Donated items** → Must have "Fair Value at Acquisition"
- **Revaluation model** → Must have "Last Revaluation Date"
- **Not recognised** → Must have "Recognition Status Reason"

## Quick Access URLs

```
Reports:          /grap/report
Compliance:       /grap/compliance
GRAP 103:         /grap/disclosure/103

API Docs:         See API_DOCUMENTATION.md
```

## Common Exports

### CSV Export
```
/grap/report/export?format=csv&asset_class=heritage_asset
```

### Excel Export
```
/grap/report/export?format=excel&recognition_status=recognised
```

### JSON Export
```
/grap/report/export?format=json&date_from=2024-01-01&date_to=2024-12-31
```

## API Quick Examples

### Get All Heritage Assets
```bash
curl -H "X-API-Key: your-key" \
  "http://yoursite/api/grap/items?asset_class=heritage_asset"
```

### Create GRAP Record
```bash
curl -X POST \
  -H "X-API-Key: your-key" \
  -H "Content-Type: application/json" \
  -d '{
    "information_object_id": 123,
    "recognition_status": "recognised",
    "measurement_basis": "cost_model",
    "initial_recognition_date": "2024-01-01",
    "initial_recognition_value": 50000.00,
    "acquisition_method_grap": "purchase",
    "asset_class": "heritage_asset",
    "gl_account_code": "1500"
  }' \
  http://yoursite/api/grap/items
```

### Get Journal Entries
```bash
curl -H "X-API-Key: your-key" \
  "http://yoursite/api/grap/journal-entries?date_from=2024-01-01&date_to=2024-12-31"
```

## Field Mapping for Accounting Systems

| GRAP Field | Accounting Field | Notes |
|------------|------------------|-------|
| gl_account_code | Account Code | Asset account |
| cost_center | Cost Center | Department/division |
| initial_recognition_value | Debit Amount | For purchases |
| fair_value_at_acquisition | Debit Amount | For donations |
| carrying_amount | Balance Sheet Value | Auto-calculated |
| accumulated_depreciation | Contra-asset | If depreciated |

## Compliance Checklist

- [ ] All heritage assets have recognition status
- [ ] All recognised assets have measurement basis
- [ ] All items have initial recognition date/value
- [ ] Donated items have fair value
- [ ] Revalued items have revaluation date
- [ ] All items have asset class
- [ ] All items have GL account code
- [ ] Non-recognised items have reason

## Common Issues

### Issue: "Donated items require fair value at acquisition"
**Solution:** Fill in "Fair Value at Acquisition" field for donated items

### Issue: "Revaluation model requires revaluation date"
**Solution:** Add "Last Revaluation Date" or change to "Cost Model"

### Issue: API returns 401 Unauthorized
**Solution:** Check X-API-Key header matches app.yml configuration

### Issue: Carrying amount is wrong
**Solution:** System auto-calculates. Check:
- Initial recognition value is correct
- Accumulated depreciation is correct
- For revaluation model: revaluation amount is set

## Field Descriptions

| Field | Purpose | Example |
|-------|---------|---------|
| Recognition Status | Is it on balance sheet? | recognised |
| Measurement Basis | Cost or revaluation? | cost_model |
| Initial Recognition Value | Original amount | R 50,000.00 |
| Carrying Amount | Current book value | R 45,000.00 |
| GL Account Code | Chart of accounts | 1500 |
| Cost Center | Department | MUSEUM |
| Depreciation Policy | Depreciate it? | not_depreciated |
| Useful Life | Years of use | 20 |

## Chart of Accounts (Example)

Common GL codes for heritage assets:
- 1500 - Heritage Assets (Cost Model)
- 1510 - Heritage Assets (Revaluation Model)
- 1520 - Operational Assets
- 1590 - Accumulated Depreciation

## Financial Year

Default: April to March (South African standard)
- Opening: April 1
- Closing: March 31

Can be customized in reconciliation API call.

## Support

For issues or questions:
1. Check README.md
2. Check API_DOCUMENTATION.md
3. Review code comments
4. Contact: [Add your support contact]

## Version Info

Package: arSpectrum51Plugin-grap v1.0.0
Created: 2024-11-29
For: AtoM 2.x + arSpectrum51Plugin
