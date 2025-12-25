# Clients Module

The Clients module manages companies and organisations in your CRM.

## Features

- Create and edit client profiles
- Track company details (name, trading name, registration number, VAT)
- Store contact information (email, phone, website)
- Full address management with UK-friendly fields
- Status tracking (active, inactive, prospect, archived)
- Assign clients to team members
- Notes and internal comments

## Client Fields

| Field | Description |
|-------|-------------|
| Name | Legal company name (required) |
| Trading Name | Name the company trades under |
| Registration Number | Companies House number |
| VAT Number | UK VAT registration |
| Type | company, individual, organisation |
| Status | active, inactive, prospect, archived |
| Email | Primary contact email |
| Phone | Main phone number |
| Website | Company website |
| Address | Full UK address fields |
| Industry | Business sector |
| Employee Count | Number of employees |
| Annual Revenue | Yearly turnover |
| Assigned To | Team member responsible |
| Notes | Internal notes |

## Relationships

- **Contacts** - A client can have multiple contacts
- **Communications** - All interactions are linked to a client

## Scopes

Useful query scopes for developers:

```php
// Get active clients only
Client::active()->get();

// Get prospects
Client::prospects()->get();

// Get clients assigned to a user
Client::assignedTo($userId)->get();
```

---

[Back to Modules](../README.md) | [Contacts](contacts.md)
