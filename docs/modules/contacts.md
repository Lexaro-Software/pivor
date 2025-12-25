# Contacts Module

The Contacts module manages people and their relationships to clients.

## Features

- Create and edit contact profiles
- Link contacts to clients
- Designate primary contacts per client
- Track job titles and departments
- Multiple phone numbers (landline, mobile)
- LinkedIn profile linking
- Status management

## Contact Fields

| Field | Description |
|-------|-------------|
| First Name | Contact's first name (required) |
| Last Name | Contact's last name (required) |
| Email | Email address |
| Phone | Landline number |
| Mobile | Mobile number |
| Job Title | Role at the company |
| Department | Department (Sales, Marketing, etc.) |
| Client | Associated client |
| Is Primary | Primary contact for the client |
| Address | Contact-specific address |
| LinkedIn URL | LinkedIn profile |
| Status | active, inactive |
| Assigned To | Team member responsible |
| Notes | Internal notes |

## Primary Contacts

Each client can have one primary contact. When you mark a contact as primary, any existing primary contact for that client is automatically demoted.

## Relationships

- **Client** - Each contact belongs to one client
- **Communications** - Interactions can be linked to specific contacts

## Scopes

```php
// Get active contacts
Contact::active()->get();

// Get primary contacts only
Contact::primary()->get();

// Get contacts for a specific client
Contact::forClient($clientId)->get();
```

---

[Back to Modules](../README.md) | [Clients](clients.md) | [Communications](communications.md)
