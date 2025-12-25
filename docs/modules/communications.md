# Communications Module

The Communications module logs all interactions with clients and contacts.

## Features

- Log emails, calls, meetings, notes, and tasks
- Track direction (inbound, outbound, internal)
- Set priorities and due dates
- Mark tasks as complete
- Link to clients and contacts
- Assign follow-ups to team members

## Communication Types

| Type | Description |
|------|-------------|
| Email | Email correspondence |
| Phone | Phone calls |
| Meeting | In-person or virtual meetings |
| Note | Internal notes and comments |
| Task | Follow-up actions and to-dos |

## Communication Fields

| Field | Description |
|-------|-------------|
| Type | email, phone, meeting, note, task |
| Direction | inbound, outbound, internal |
| Subject | Brief summary (required) |
| Content | Full details |
| Client | Associated client |
| Contact | Specific contact (optional) |
| Due At | Due date for tasks |
| Completed At | When task was completed |
| Priority | low, normal, high, urgent |
| Status | pending, in_progress, completed, cancelled |
| Created By | User who created it |
| Assigned To | User responsible |

## Tasks

Communications with type "task" can have due dates and be marked as complete:

```php
$communication->markAsCompleted();
```

Check if a task is overdue:

```php
if ($communication->is_overdue) {
    // Handle overdue task
}
```

## Scopes

```php
// Get tasks only
Communication::tasks()->get();

// Get pending items
Communication::pending()->get();

// Get overdue tasks
Communication::overdue()->get();

// Filter by client
Communication::forClient($clientId)->get();

// Filter by contact
Communication::forContact($contactId)->get();

// Filter by type
Communication::byType('email')->get();
```

---

[Back to Modules](../README.md) | [Contacts](contacts.md)
