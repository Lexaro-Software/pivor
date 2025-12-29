# REST API

Pivor provides a RESTful API for programmatic access to clients, contacts, and communications.

## Authentication

The API uses token-based authentication via Laravel Sanctum.

### Creating a Token

```bash
curl -X POST https://your-domain.com/api/tokens/create \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com", "password": "yourpassword"}'
```

**Response:**
```json
{
  "token": "1|abc123...",
  "user": {
    "id": 1,
    "name": "User Name",
    "email": "user@example.com",
    "role": "admin"
  }
}
```

### Using the Token

Include the token in the `Authorization` header:

```bash
curl https://your-domain.com/api/clients \
  -H "Authorization: Bearer 1|abc123..." \
  -H "Accept: application/json"
```

### Revoking a Token

```bash
curl -X DELETE https://your-domain.com/api/tokens/revoke \
  -H "Authorization: Bearer 1|abc123..."
```

## Endpoints

### Current User

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/user` | Get authenticated user info |

### Clients

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/clients` | List all clients |
| POST | `/api/clients` | Create a new client |
| GET | `/api/clients/{id}` | Get a specific client |
| PUT | `/api/clients/{id}` | Update a client |
| DELETE | `/api/clients/{id}` | Delete a client |

**Create/Update fields:**
- `name` (required) - Company name
- `trading_name` - Trading name
- `registration_number` - Company registration number
- `vat_number` - VAT number
- `type` - `company` or `individual`
- `status` - `active`, `inactive`, `prospect`, or `archived`
- `email` - Primary email
- `phone` - Phone number
- `website` - Website URL
- `address_line_1`, `address_line_2`, `city`, `county`, `postcode`, `country` - Address fields
- `industry` - Industry sector
- `employee_count` - Number of employees
- `annual_revenue` - Annual revenue
- `notes` - Additional notes
- `assigned_to` - User ID to assign

### Contacts

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/contacts` | List all contacts |
| POST | `/api/contacts` | Create a new contact |
| GET | `/api/contacts/{id}` | Get a specific contact |
| PUT | `/api/contacts/{id}` | Update a contact |
| DELETE | `/api/contacts/{id}` | Delete a contact |

**Create/Update fields:**
- `first_name` (required) - First name
- `last_name` (required) - Last name
- `email` - Email address
- `phone` - Phone number
- `mobile` - Mobile number
- `job_title` - Job title
- `department` - Department
- `client_id` - Associated client ID
- `is_primary_contact` - Boolean, primary contact for client
- `address_line_1`, `address_line_2`, `city`, `county`, `postcode`, `country` - Address fields
- `linkedin_url` - LinkedIn profile URL
- `status` - `active` or `inactive`
- `notes` - Additional notes
- `assigned_to` - User ID to assign

### Communications

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/communications` | List all communications |
| POST | `/api/communications` | Create a new communication |
| GET | `/api/communications/{id}` | Get a specific communication |
| PUT | `/api/communications/{id}` | Update a communication |
| DELETE | `/api/communications/{id}` | Delete a communication |

**Create/Update fields:**
- `type` (required) - `email`, `phone`, `meeting`, `note`, or `task`
- `direction` - `inbound`, `outbound`, or `internal`
- `subject` (required) - Subject/title
- `content` - Body/notes
- `client_id` - Associated client ID
- `contact_id` - Associated contact ID
- `due_at` - Due date (for tasks)
- `completed_at` - Completion date
- `priority` - `low`, `medium`, `high`, or `urgent`
- `status` - `pending`, `in_progress`, `completed`, or `cancelled`
- `assigned_to` - User ID to assign

## Query Parameters

### Pagination

```
GET /api/clients?per_page=25
```

- `per_page` - Items per page (default: 15, max: 100)

### Sorting

```
GET /api/clients?sort=-created_at
```

- `sort` - Field to sort by (prefix with `-` for descending)
- Allowed fields vary by resource

### Filtering

```
GET /api/clients?filter[status]=active&filter[search]=acme
GET /api/communications?filter[type]=task&filter[priority]=high
```

**Client filters:** `search`, `status`, `type`

**Contact filters:** `search`, `status`, `client_id`

**Communication filters:** `search`, `type`, `status`, `priority`, `direction`, `client_id`, `contact_id`

## Response Format

All responses follow a consistent format:

**Single resource:**
```json
{
  "data": {
    "id": 1,
    "uuid": "...",
    "name": "...",
    ...
  }
}
```

**Collection:**
```json
{
  "data": [...],
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": "..."
  },
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 72
  }
}
```

**Error:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": ["The name field is required."]
  }
}
```

## Access Control

API access respects user roles and permissions:

- **Admin** - Full access to all records
- **Manager** - Access to all records
- **User** - Access only to assigned records

## Examples

### Create a Client

```bash
curl -X POST https://your-domain.com/api/clients \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Acme Corporation",
    "type": "company",
    "status": "active",
    "email": "contact@acme.com",
    "phone": "+1 555-0100"
  }'
```

### List Communications with Filters

```bash
curl "https://your-domain.com/api/communications?filter[type]=task&filter[status]=pending&sort=-priority" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Update a Contact

```bash
curl -X PUT https://your-domain.com/api/contacts/5 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "job_title": "Senior Developer",
    "department": "Engineering"
  }'
```
