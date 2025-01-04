<!DOCTYPE html>
<html>

<head>
    <title>Usage Variation - 1</title>
</head>

<body>
    <h1>Usage Variation for Road: {{ $misRoadName }}</h1>
    <table border="1">
        <thead>
            <tr>
                <th>S.NO</th>
                <th>GIS ID</th>
                <th>Road Name</th>
                <th>Assessment</th>
                <th>Old Assessment</th>
                <th>Building Usage</th>
                <th>Bill Usage</th>
                <th>Owner Name</th>
                <th>Floor</th>
                <th>Phone Number</th>
                <th>Plot Area</th>
                <th>Half-Year Tax</th>
                <th>Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($filteredUsage as $point)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $point['point_gisid'] ?? 'N/A' }}</td>
                    <td>{{ $point['road_name'] ?? 'N/A' }}</td>
                    <td>{{ $point['assessment'] ?? 'N/A' }}</td>
                    <td>{{ $point['old_assessment'] ?? 'N/A' }}</td>
                    <td>{{ $point['building_usage'] ?? 'N/A' }}</td>
                    <td>{{ $point['bill_usage'] ?? 'N/A' }}</td>
                    <td>{{ $point['owner_name'] ?? 'N/A' }}</td>
                    <td>{{ $point['floor'] ?? 'N/A' }}</td>
                    <td>{{ $point['phone_number'] ?? 'N/A' }}</td>
                    <td>{{ $point['plot_area'] ?? 'N/A' }}</td>
                    <td>{{ $point['halfyeartax'] ?? 'N/A' }}</td>
                    <td>{{ $point['balance'] ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
