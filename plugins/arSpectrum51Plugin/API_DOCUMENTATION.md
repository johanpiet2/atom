# GRAP API Documentation

## Authentication

All API requests require an API key passed in the `X-API-Key` header or as an `api_key` parameter.

Example:
```bash
curl -H "X-API-Key: your-api-key" http://yoursite/api/grap/items
```

## Endpoints

### List Items

**GET** `/api/grap/items`

List all GRAP items with optional filters and pagination.

**Parameters:**
- `asset_class` (optional) - Filter by asset class (heritage_asset, operational_asset, investment)
- `recognition_status` (optional) - Filter by recognition status (recognised, not_recognised)
- `measurement_basis` (optional) - Filter by measurement basis (cost_model, revaluation_model)
- `cost_center` (optional) - Filter by cost center
- `date_from` (optional) - Filter by initial recognition date (from) in YYYY-MM-DD format
- `date_to` (optional) - Filter by initial recognition date (to) in YYYY-MM-DD format
- `page` (optional) - Page number (default: 1)
- `limit` (optional) - Items per page (default: 50, max: 100)

**Example:**
```bash
curl -H "X-API-Key: your-api-key" \
  "http://yoursite/api/grap/items?asset_class=heritage_asset&page=1&limit=20"
```

**Response:**
```json
{
  "page": 1,
  "limit": 20,
  "total": 150,
  "items": [
    {
      "id": 123,
      "title": "Historic Painting",
      "recognition_status": "recognised",
      "measurement_basis": "cost_model",
      "initial_recognition_value": 50000.00,
      "carrying_amount": 50000.00,
      ...
    }
  ]
}
```

### Get Single Item

**GET** `/api/grap/items/:id`

Get a single GRAP item by information object ID.

**Example:**
```bash
curl -H "X-API-Key: your-api-key" http://yoursite/api/grap/items/123
```

### Create or Update Item

**POST** `/api/grap/items`

Create or update GRAP data for an item.

**Request Body:**
```json
{
  "information_object_id": 123,
  "recognition_status": "recognised",
  "measurement_basis": "cost_model",
  "initial_recognition_date": "2024-01-01",
  "initial_recognition_value": 50000.00,
  "acquisition_method_grap": "purchase",
  "cost_of_acquisition": 50000.00,
  "asset_class": "heritage_asset",
  "gl_account_code": "1500",
  "cost_center": "MUSEUM"
}
```

**Example:**
```bash
curl -X POST \
  -H "X-API-Key: your-api-key" \
  -H "Content-Type: application/json" \
  -d @data.json \
  http://yoursite/api/grap/items
```

### Update Item

**PUT** `/api/grap/items/:id`

Update GRAP data for an existing item.

**Request Body:** Same as POST

### Delete Item

**DELETE** `/api/grap/items/:id`

Delete GRAP data for an item.

**Example:**
```bash
curl -X DELETE \
  -H "X-API-Key: your-api-key" \
  http://yoursite/api/grap/items/123
```

### Get Journal Entries

**GET** `/api/grap/journal-entries`

Generate journal entries for accounting system import.

**Parameters:**
- `date_from` (required) - Start date in YYYY-MM-DD format
- `date_to` (required) - End date in YYYY-MM-DD format

**Example:**
```bash
curl -H "X-API-Key: your-api-key" \
  "http://yoursite/api/grap/journal-entries?date_from=2024-01-01&date_to=2024-12-31"
```

**Response:**
```json
{
  "period": {
    "from": "2024-01-01",
    "to": "2024-12-31"
  },
  "entries": [
    {
      "date": "2024-01-15",
      "reference": "ACQ-123",
      "description": "Acquisition: Historic Painting",
      "lines": [
        {
          "account": "1500",
          "debit": 50000.00,
          "credit": 0,
          "cost_center": "MUSEUM"
        },
        {
          "account": "2100",
          "debit": 0,
          "credit": 50000.00,
          "cost_center": "MUSEUM"
        }
      ]
    }
  ],
  "count": 1
}
```

### Get Balance Sheet

**GET** `/api/grap/balance-sheet`

Get balance sheet data for heritage assets.

**Parameters:**
- `as_of_date` (optional) - Balance sheet date in YYYY-MM-DD format (default: today)

**Example:**
```bash
curl -H "X-API-Key: your-api-key" \
  "http://yoursite/api/grap/balance-sheet?as_of_date=2024-12-31"
```

**Response:**
```json
{
  "as_of_date": "2024-12-31",
  "heritage_assets": {
    "cost_model": {
      "cost": 500000.00,
      "accumulated_depreciation": 0,
      "carrying_amount": 500000.00
    },
    "revaluation_model": {
      "revalued_amount": 750000.00,
      "accumulated_depreciation": 0,
      "carrying_amount": 750000.00
    },
    "total_carrying_amount": 1250000.00
  },
  "by_category": {
    "heritage_asset": 1250000.00
  }
}
```

### Get Reconciliation

**GET** `/api/grap/reconciliation`

Get asset reconciliation for a financial year.

**Parameters:**
- `financial_year` (optional) - Financial year (default: current year)

**Example:**
```bash
curl -H "X-API-Key: your-api-key" \
  "http://yoursite/api/grap/reconciliation?financial_year=2024"
```

**Response:**
```json
{
  "financial_year": 2024,
  "period": {
    "start": "2024-04-01",
    "end": "2025-03-31"
  },
  "opening_balance": 1000000.00,
  "additions": 250000.00,
  "disposals": 0,
  "depreciation": 0,
  "impairments": 0,
  "gain_loss_on_disposal": 0,
  "closing_balance": 1250000.00
}
```

## Error Responses

All endpoints return standard HTTP status codes:

- `200 OK` - Request successful
- `201 Created` - Item created successfully
- `400 Bad Request` - Invalid request data
- `401 Unauthorized` - Invalid or missing API key
- `404 Not Found` - Item not found
- `405 Method Not Allowed` - Invalid HTTP method
- `500 Internal Server Error` - Server error

Error response format:
```json
{
  "error": "Error message here",
  "message": "Additional details"
}
```

## Rate Limiting

No rate limiting is currently implemented. Consider implementing rate limiting in production environments.

## Data Fields

### Required Fields (for compliance)
- `recognition_status`
- `measurement_basis`
- `initial_recognition_date`
- `initial_recognition_value`
- `acquisition_method_grap`
- `asset_class`
- `gl_account_code`

### Optional Fields
All other fields are optional but recommended for complete GRAP compliance.

## Integration Examples

### Python Example
```python
import requests

api_key = 'your-api-key'
base_url = 'http://yoursite/api/grap'

headers = {
    'X-API-Key': api_key,
    'Content-Type': 'application/json'
}

# Get all items
response = requests.get(f'{base_url}/items', headers=headers)
items = response.json()

# Create new item
data = {
    'information_object_id': 123,
    'recognition_status': 'recognised',
    'measurement_basis': 'cost_model',
    'initial_recognition_date': '2024-01-01',
    'initial_recognition_value': 50000.00,
    'acquisition_method_grap': 'purchase',
    'asset_class': 'heritage_asset',
    'gl_account_code': '1500'
}

response = requests.post(f'{base_url}/items', json=data, headers=headers)
result = response.json()
```

### PHP Example
```php
<?php

$apiKey = 'your-api-key';
$baseUrl = 'http://yoursite/api/grap';

$headers = [
    'X-API-Key: ' . $apiKey,
    'Content-Type: application/json'
];

// Get all items
$ch = curl_init($baseUrl . '/items');
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$items = json_decode($response, true);
curl_close($ch);

// Create new item
$data = [
    'information_object_id' => 123,
    'recognition_status' => 'recognised',
    'measurement_basis' => 'cost_model',
    'initial_recognition_date' => '2024-01-01',
    'initial_recognition_value' => 50000.00,
    'acquisition_method_grap' => 'purchase',
    'asset_class' => 'heritage_asset',
    'gl_account_code' => '1500'
];

$ch = curl_init($baseUrl . '/items');
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$result = json_decode($response, true);
curl_close($ch);
```
