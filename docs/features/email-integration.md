# Email Integration

Pivor supports two-way email integration with Gmail and Outlook/Microsoft 365. Connect your email account to automatically sync communications with your CRM contacts.

## Features

- **Gmail & Outlook Support** — Connect either or both email providers
- **Two-Way Sync** — Import emails and send directly from Pivor
- **Contact Matching** — Only syncs emails from/to your CRM contacts
- **Automatic Sync** — Emails sync every 5 minutes in the background
- **Per-User Config** — Each user connects their own email account

## Setup

### 1. Configure OAuth Credentials

Add the following to your `.env` file:

```env
# Gmail (Google Cloud Console)
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret

# Outlook (Azure Portal)
MICROSOFT_CLIENT_ID=your-client-id
MICROSOFT_CLIENT_SECRET=your-client-secret
MICROSOFT_TENANT_ID=common
```

---

## Getting Gmail OAuth Credentials

Follow these steps to get your Google Client ID and Secret:

### Step 1: Create a Google Cloud Project

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Click the project dropdown at the top of the page
3. Click **New Project**
4. Enter a project name (e.g., "Pivor CRM")
5. Click **Create**
6. Wait for the project to be created, then select it

### Step 2: Enable the Gmail API

1. In the left sidebar, go to **APIs & Services** > **Library**
2. Search for "Gmail API"
3. Click on **Gmail API**
4. Click **Enable**

### Step 3: Configure OAuth Consent Screen

1. Go to **APIs & Services** > **OAuth consent screen**
2. Select **External** (or Internal if using Google Workspace)
3. Click **Create**
4. Fill in the required fields:
   - **App name**: Pivor CRM
   - **User support email**: Your email
   - **Developer contact email**: Your email
5. Click **Save and Continue**
6. On the **Scopes** page, click **Add or Remove Scopes**
7. Add these scopes:
   - `https://www.googleapis.com/auth/gmail.readonly`
   - `https://www.googleapis.com/auth/gmail.send`
   - `https://www.googleapis.com/auth/userinfo.email`
8. Click **Update**, then **Save and Continue**
9. On the **Test users** page, add your email for testing
10. Click **Save and Continue**, then **Back to Dashboard**

### Step 4: Create OAuth Credentials

1. Go to **APIs & Services** > **Credentials**
2. Click **Create Credentials** > **OAuth client ID**
3. Select **Web application**
4. Enter a name (e.g., "Pivor Web Client")
5. Under **Authorized redirect URIs**, add:
   ```
   https://your-domain.com/email/oauth/google/callback
   ```
   For local development:
   ```
   http://localhost:8080/email/oauth/google/callback
   ```
6. Click **Create**
7. Copy the **Client ID** and **Client Secret**
8. Add them to your `.env` file:
   ```env
   GOOGLE_CLIENT_ID=your-client-id.apps.googleusercontent.com
   GOOGLE_CLIENT_SECRET=GOCSPX-your-client-secret
   ```

### Step 5: Publish the App (Production)

For production use with users outside your organization:

1. Go to **OAuth consent screen**
2. Click **Publish App**
3. Submit for Google verification (required for sensitive scopes)

> **Note**: During development, you can use the app in "Testing" mode with up to 100 test users without verification.

---

## Getting Outlook/Microsoft OAuth Credentials

Follow these steps to get your Microsoft Client ID and Secret:

### Step 1: Register an Application in Azure

1. Go to [Azure Portal](https://portal.azure.com/)
2. Sign in with your Microsoft account
3. Search for **App registrations** in the search bar
4. Click **App registrations**
5. Click **New registration**
6. Fill in the details:
   - **Name**: Pivor CRM
   - **Supported account types**: Select one of:
     - "Accounts in any organizational directory and personal Microsoft accounts" (recommended for most users)
     - "Accounts in this organizational directory only" (for single-tenant apps)
   - **Redirect URI**: Select "Web" and enter:
     ```
     https://your-domain.com/email/oauth/microsoft/callback
     ```
     For local development:
     ```
     http://localhost:8080/email/oauth/microsoft/callback
     ```
7. Click **Register**

### Step 2: Copy the Application (Client) ID

1. On the app overview page, copy the **Application (client) ID**
2. Add it to your `.env` file:
   ```env
   MICROSOFT_CLIENT_ID=xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
   ```

### Step 3: Create a Client Secret

1. In the left sidebar, click **Certificates & secrets**
2. Click **New client secret**
3. Enter a description (e.g., "Pivor Production")
4. Select an expiration period (24 months recommended)
5. Click **Add**
6. **Immediately copy the Value** (it won't be shown again!)
7. Add it to your `.env` file:
   ```env
   MICROSOFT_CLIENT_SECRET=your-client-secret-value
   ```

### Step 4: Configure API Permissions

1. In the left sidebar, click **API permissions**
2. Click **Add a permission**
3. Select **Microsoft Graph**
4. Select **Delegated permissions**
5. Add these permissions:
   - `User.Read` — Sign in and read user profile
   - `Mail.Read` — Read user mail
   - `Mail.Send` — Send mail as a user
   - `offline_access` — Maintain access to data (for refresh tokens)
6. Click **Add permissions**
7. If you see "Admin consent required", click **Grant admin consent for [Your Organization]**

### Step 5: Configure Tenant ID (Optional)

The tenant ID determines which accounts can sign in:

- **`common`** — Any Microsoft account (personal + work/school) — Default
- **`organizations`** — Only work/school accounts
- **`consumers`** — Only personal Microsoft accounts
- **Your Tenant ID** — Only accounts from your specific organization

Add to your `.env`:
```env
MICROSOFT_TENANT_ID=common
```

### Step 6: Add Additional Redirect URIs (Optional)

If you have multiple environments:

1. Go to **Authentication** in the left sidebar
2. Under **Web** > **Redirect URIs**, click **Add URI**
3. Add URIs for each environment:
   ```
   http://localhost:8080/email/oauth/microsoft/callback
   https://staging.your-domain.com/email/oauth/microsoft/callback
   https://your-domain.com/email/oauth/microsoft/callback
   ```
4. Click **Save**

---

## Usage

### Connecting Your Email

1. Navigate to **Settings** (bottom of sidebar)
2. Click **Connect Gmail** or **Connect Outlook**
3. Authorize access in the popup
4. Your account will show as connected

### Automatic Sync

Once connected, Pivor automatically:
- Syncs new emails every 5 minutes
- Only imports emails from/to contacts in your CRM
- Creates Communication records for each synced email
- Preserves email metadata (from, to, cc, subject, body)

### Sending Emails

1. Open a Contact page
2. Click the **Send Email** button (if contact has email)
3. Compose your message
4. Click **Send** — email sends via your connected account

### Manual Sync

Click **Sync Now** on the Settings page to trigger an immediate sync.

## How It Works

```
┌──────────────┐     ┌───────────────┐     ┌──────────────┐
│   Gmail /    │────▶│  Pivor Sync   │────▶│ Communications│
│   Outlook    │     │   Service     │     │    Module     │
└──────────────┘     └───────────────┘     └──────────────┘
                            │
                            ▼
                     ┌──────────────┐
                     │   Contact    │
                     │   Matching   │
                     └──────────────┘
```

1. **Email Fetch** — Background job fetches emails from connected accounts
2. **Contact Matching** — Emails are matched against contact email addresses
3. **Communication Creation** — Matched emails become Communication records
4. **Two-Way Link** — Communications link back to original email data

## Security

- OAuth tokens are encrypted at rest using Laravel's encryption
- Tokens auto-refresh before expiration
- Users can only access their own email accounts
- Minimal OAuth scopes are requested (read, send, offline access)

## Troubleshooting

### Emails Not Syncing

1. Check Settings page for sync errors
2. Verify contact has correct email address
3. Try clicking "Sync Now" manually
4. Check Laravel logs for API errors

### OAuth Connection Failed

1. Verify credentials in `.env`
2. Check redirect URIs match exactly
3. Ensure required API permissions are granted
4. Try disconnecting and reconnecting

### Queue Not Processing

Email sync runs via Laravel's queue. Ensure you have a queue worker running:

```bash
php artisan queue:work
```

Or for development:

```bash
php artisan queue:listen
```

---

[Back to Features](../README.md#features)
