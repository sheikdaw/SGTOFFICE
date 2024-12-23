<table>
    <thead>
        <tr>
            <th>Assessment</th>
            <th>Old Assessment</th>
            <th>Owner Name</th>
            <th>Mobile No</th>
            <th>Usage</th>
            <th>Old Door No</th>
            <th>New Door No</th>
            <th>Road Name</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $row)
            <tr>
                <td>{{ $row->assessment }}</td>
                <td>{{ $row->old_assessment }}</td>
                <td>{{ $row->owner_name }}</td>
                <td>{{ $row->phone }}</td>
                <td>{{ $row->building_usage }}</td>
                <td>{{ $row->old_door_no }}</td>
                <td>{{ $row->new_door_no }}</td>
                <td>{{ $row->road_name }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
