<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Task Reminder</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #0284c7;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .task-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .task-title {
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
            margin: 0 0 10px 0;
        }
        .task-meta {
            color: #64748b;
            font-size: 14px;
        }
        .task-meta strong {
            color: #475569;
        }
        .priority-high, .priority-urgent {
            color: #dc2626;
        }
        .priority-medium {
            color: #f59e0b;
        }
        .priority-low {
            color: #22c55e;
        }
        .btn {
            display: inline-block;
            background: #0284c7;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 15px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #94a3b8;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0; color: #0284c7; font-size: 24px;">{{ config('app.name') }}</h1>
    </div>

    <p>Hi {{ $task->assignedUser?->name ?? 'there' }},</p>

    <p>You have a task that is <strong>due {{ $timing }}</strong>:</p>

    <div class="task-card">
        <h2 class="task-title">{{ $task->subject }}</h2>
        <div class="task-meta">
            <p><strong>Due:</strong> {{ $task->due_at->format('l, F j, Y \a\t g:i A') }}</p>
            <p><strong>Priority:</strong> <span class="priority-{{ $task->priority }}">{{ ucfirst($task->priority) }}</span></p>
            @if($task->client)
                <p><strong>Client:</strong> {{ $task->client->display_name }}</p>
            @endif
            @if($task->contact)
                <p><strong>Contact:</strong> {{ $task->contact->full_name }}</p>
            @endif
            @if($task->content)
                <p><strong>Notes:</strong> {{ Str::limit($task->content, 200) }}</p>
            @endif
        </div>
    </div>

    <a href="{{ url('/communications') }}" class="btn">View in {{ config('app.name') }}</a>

    <div class="footer">
        <p>You received this email because you have task reminders enabled in your notification settings.</p>
        <p>{{ config('app.name') }}</p>
    </div>
</body>
</html>
