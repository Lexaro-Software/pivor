# Roles & Permissions

Pivor includes a flexible role-based access control system that allows administrators to manage what users can see and do.

## Default Roles

Pivor comes with three system roles:

### Admin
- Full access to all features
- Can manage users and roles
- Can see all records regardless of assignment
- Cannot be deleted

### Manager
- Can view all records
- Can create, edit, and delete clients, contacts, and communications
- Cannot manage users or roles

### User
- Can only see records assigned to them
- Can create new records (assigned to themselves)
- Can edit their own records

## Permissions

Permissions are organized by module:

### Clients
- `clients.view` - View client records
- `clients.create` - Create new clients
- `clients.edit` - Edit existing clients
- `clients.delete` - Delete clients

### Contacts
- `contacts.view` - View contact records
- `contacts.create` - Create new contacts
- `contacts.edit` - Edit existing contacts
- `contacts.delete` - Delete contacts

### Communications
- `communications.view` - View communications
- `communications.create` - Create new communications
- `communications.edit` - Edit existing communications
- `communications.delete` - Delete communications

### Administration
- `users.manage` - Manage user accounts
- `roles.manage` - Manage roles and permissions
- `records.view_all` - View all records (not just assigned)

## Managing Roles

Administrators can manage roles from the Admin menu:

1. Navigate to **Admin** > **Roles**
2. Click **Create Role** or edit an existing role
3. Enter a name and description
4. Select permissions for the role
5. Save the role

### System Roles

System roles (Admin, Manager, User) cannot be deleted but can be modified. The Admin role always has all permissions.

## Assigning Users to Roles

1. Navigate to **Admin** > **Users**
2. Click **Edit** on a user
3. Select a role from the dropdown
4. Save the user

## Record Visibility

Record visibility is controlled by the `records.view_all` permission and role:

| Role | Can See |
|------|---------|
| Admin | All records |
| Manager | All records |
| User | Only assigned records |

Records are assigned via the `assigned_to` field on clients, contacts, and communications.

---

[Back to Documentation](../README.md)
