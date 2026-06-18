<!DOCTYPE html>
<html>
<head>
    <title>Colleges Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Colleges Report</h2>
    <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>College Name</th>
                <th>Code</th>
                <th>University</th>
                <th>Type</th>
                <th>State</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($colleges as $index => $college)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $college->name }}</td>
                <td>{{ $college->code ?? '-' }}</td>
                <td>{{ $college->university ? $college->university->name : 'N/A' }}</td>
                <td>{{ $college->type }}</td>
                <td>{{ $college->state ?? '-' }}</td>
                <td>{{ $college->university ? ucfirst($college->university->status) : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
