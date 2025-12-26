# Import/Export

Pivor allows you to import and export your data as CSV files. This is useful for:
- Migrating data from other CRMs
- Backing up your data
- Bulk updates
- Data analysis in spreadsheet applications

## Exporting Data

Each list view (Clients, Contacts, Communications) has an **Import / Export** dropdown button.

To export data:
1. Navigate to the list view (e.g., Clients)
2. Click the **Import / Export** button
3. Select **Export CSV**
4. The file will download automatically

Exported files include:
- UTF-8 encoding with BOM for Excel compatibility
- All fields for the data type
- Only records visible to your user role

## Importing Data

### Step 1: Download a Template

Before importing, download a template CSV:
1. Navigate to the list view
2. Click **Import / Export** > **Download Template**
3. Open the template in your spreadsheet application
4. Fill in your data following the example row

### Step 2: Import Your Data

1. Click **Import / Export** > **Import CSV**
2. Upload your CSV file
3. Map your CSV columns to Pivor fields (auto-detected when possible)
4. Preview the data to be imported
5. Confirm the import

### Field Mapping

The import wizard will attempt to automatically map your CSV headers to Pivor fields. You can manually adjust mappings if needed.

### Validation

During import, Pivor validates:
- Required fields are present
- Email formats are valid
- Dates are in the correct format
- Referenced records (clients, contacts) exist

Errors are reported with row numbers so you can fix and re-import.

## CSV Format

### Clients Template

| Column | Required | Description |
|--------|----------|-------------|
| name | Yes | Company/organisation name |
| trading_name | No | Trading/display name |
| type | No | company, individual, or organisation |
| status | No | active, prospect, inactive, archived |
| email | No | Primary email address |
| phone | No | Phone number |
| website | No | Website URL |
| address_line_1 | No | Street address |
| city | No | City |
| country | No | Country (2-letter code) |

### Contacts Template

| Column | Required | Description |
|--------|----------|-------------|
| first_name | Yes | First name |
| last_name | Yes | Last name |
| email | No | Email address |
| phone | No | Phone number |
| job_title | No | Job title |
| client_name | No | Associated client name |
| is_primary | No | yes/no - primary contact for client |

### Communications Template

| Column | Required | Description |
|--------|----------|-------------|
| type | Yes | email, call, meeting, note, or task |
| subject | Yes | Subject/title |
| client_name | No | Associated client name |
| contact_name | No | Associated contact name |
| status | No | pending, completed, cancelled |
| due_at | No | Due date (YYYY-MM-DD HH:MM) |

---

[Back to Documentation](../README.md)
