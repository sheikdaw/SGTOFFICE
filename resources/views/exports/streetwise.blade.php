<table>
    <thead>
        <tr>
            <th colspan="3" style="text-align: center; font-weight: bold;">Road: {{ $roadName }}</th>
        </tr>
        <tr>
            <th>Old Door No</th>
            <th>New Door No</th>
            <!-- Add other columns as necessary -->
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $row)
            <tr>
                <td>{{ $row->old_door_no }}</td>
                <td>{{ $row->new_door_no }}</td>
                <!-- Add other fields as necessary -->
            </tr>
        @endforeach
    </tbody>
</table>
