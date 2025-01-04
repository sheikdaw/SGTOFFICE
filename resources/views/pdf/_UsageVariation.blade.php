<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        .header {
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h1 class="header">Usage Variation Report for {{ $misRoadName }}</h1>

    <table class="table">
        <thead>
            <tr>
                <th>Road Name</th>
                <th>Usage</th>
                <th>Area</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($filteredUsage as $item)
                <tr>
                    <td>{{ $item->road_name }}</td>
                    <td>{{ $item->usage }}</td>
                    <td>{{ $item->area }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
