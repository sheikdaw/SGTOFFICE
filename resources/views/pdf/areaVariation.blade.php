<!DOCTYPE html>
<html>

<head>
    <title>Area Variation - 1</title>
</head>

<body>
    <h1>Area Variation for Road: {{ $roadName }}</h1>
    <table border="1">
        <thead>
            <tr>
                <th>S.NO</th>
                <th>GIS ID</th>
                <th>Road Name</th>
                <th>Assessment</th>
                <th>Old Assessment</th>
                <th>Owner Name</th>
                <th>Phone Number</th>
                <th>New Door No</th>
                <th>Building Usage</th>
                <th>Bill Usage</th>
                <th>Plot Area</th>
                <th>Basement</th>
                <th>Floor</th>
                <th>Percentage</th>
                <th>Total Drone Area</th>
                <th>Area Variation</th>
                <th>Half-Year Tax</th>
                <th>Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($areaVariation as $point)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $point['point_gisid'] ?? 'N/A' }}</td>
                    <td>{{ $point['road_name'] ?? 'N/A' }}</td>
                    <td>{{ $point['assessment'] ?? 'N/A' }}</td>
                    <td>{{ $point['old_assessment'] ?? 'N/A' }}</td>
                    <td>{{ $point['owner_name'] ?? 'N/A' }}</td>
                    <td>{{ $point['phone_number'] ?? 'N/A' }}</td>
                    <td>{{ $point['new_door_no'] ?? 'N/A' }}</td>
                    <td>{{ $point['building_usage'] ?? 'N/A' }}</td>
                    <td>{{ $point['bill_usage'] ?? 'N/A' }}</td>
                    <td>{{ $point['plot_area'] ?? 'N/A' }}</td>
                    <td>{{ $point['basement'] ?? 'N/A' }}</td>
                    <td>{{ $point['number_floor'] ?? 'N/A' }}</td>
                    <td>{{ $point['percentage'] ?? 'N/A' }}</td>
                    <td>{{ $point['totaldronearea'] ?? 'N/A' }}</td>
                    <td>{{ $point['areavariation'] ?? 'N/A' }}</td>
                    <td>{{ $point['halfyeartax'] ?? 'N/A' }}</td>
                    <td>{{ $point['balance'] ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
