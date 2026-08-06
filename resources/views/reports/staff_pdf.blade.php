<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Staff Activity & Performance Report</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h2 {
            margin: 0;
            color: #1E1B4B;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header p {
            margin: 4px 0 0 0;
            color: #64748B;
            font-size: 10px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 15px;
            font-size: 10px;
        }
        .meta-table td {
            padding: 4px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #CBD5E1;
            padding: 6px 8px;
            text-align: left;
        }
        table.data-table th {
            background-color: #F1F5F9;
            color: #1E293B;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
        table.data-table tr:nth-child(even) {
            background-color: #F8FAFC;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-primary { background-color: #E0E7FF; color: #3730A3; }
        .badge-success { background-color: #DCFCE7; color: #166534; }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #94A3B8;
            border-top: 1px solid #E2E8F0;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>College CRM - Staff Activity & Performance Report</h2>
        <p>Generated on {{ now()->format('d M Y, h:i A') }}</p>
    </div>

    <table class="meta-table">
        <tr>
            <td><strong>Filter Staff:</strong> {{ $selectedStaffName ?? 'All Staff Members' }}</td>
            <td><strong>Date Range:</strong> {{ $dateRangeText ?? 'All Time' }}</td>
            <td><strong>Total Staff Evaluated:</strong> {{ count($staffData) }}</td>
        </tr>
    </table>

    <h4 style="color: #1E293B; margin-bottom: 8px;">1. Staff Activity Summary</h4>
    <table class="data-table">
        <thead>
            <tr>
                <th>Staff Member</th>
                <th>Email</th>
                <th style="text-align: center;">Colleges Added</th>
                <th style="text-align: center;">Contacts Added</th>
                <th style="text-align: center;">Corporate Clients</th>
                <th style="text-align: center;">Academic Logs</th>
                <th style="text-align: center;">Corporate Logs</th>
                <th style="text-align: center;">Total Logs</th>
                <th style="text-align: center;">Pending Follow-ups</th>
            </tr>
        </thead>
        <tbody>
            @forelse($staffData as $row)
                <tr>
                    <td><strong>{{ $row->staff_name }}</strong></td>
                    <td>{{ $row->staff_email }}</td>
                    <td style="text-align: center;">{{ $row->colleges_added }}</td>
                    <td style="text-align: center;">{{ $row->contacts_added }}</td>
                    <td style="text-align: center;">{{ $row->clients_added }}</td>
                    <td style="text-align: center;">{{ $row->academic_interactions }}</td>
                    <td style="text-align: center;">{{ $row->non_academic_interactions }}</td>
                    <td style="text-align: center;"><strong>{{ $row->total_interactions }}</strong></td>
                    <td style="text-align: center;">{{ $row->pending_followups }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center;">No activity records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if(isset($detailedLogs) && count($detailedLogs) > 0)
    <h4 style="color: #1E293B; margin-bottom: 8px;">2. Detailed Interaction Audit Logs</h4>
    <table class="data-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Staff</th>
                <th>Type</th>
                <th>Institution / Client Name</th>
                <th>Contact Mode</th>
                <th>Status</th>
                <th>Remarks / Response</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detailedLogs as $log)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($log->contact_date)->format('d M Y H:i') }}</td>
                    <td>{{ $log->staff_name }}</td>
                    <td><span class="badge badge-primary">{{ $log->type }}</span></td>
                    <td><strong>{{ $log->target_name }}</strong></td>
                    <td>{{ $log->contact_mode }}</td>
                    <td><span class="badge badge-success">{{ $log->status }}</span></td>
                    <td>{{ \Illuminate\Support\Str::limit($log->remarks ?? 'N/A', 60) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        Confidential - College CRM & Institution Management System (CIMS) - Page 1
    </div>

</body>
</html>
