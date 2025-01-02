<table>
    <thead>
        <tr>
            <th>Surveyor Name</th>
            <th>Surveyed Count</th>
            <th>Not Connected Count</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($surveyors as $surveyor)
            <tr>
                <td>{{ $surveyor['surveyor'] }}</td>
                <td>{{ $surveyor['surveyed_count'] }}</td>
                <td>{{ $surveyor['not_connected_count'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
