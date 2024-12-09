<div class="table-responsive">
    <table class="table dash-table">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">যাত্রার তারিখ ও সময়</th>
            <th scope="col">ফ্লাইট কোড</th>
            <th scope="col">গমন পথ</th>
            <th scope="col">পৌঁছানোর সময়</th>
            <th scope="col">স্ট্যাটাস</th>
        </tr>
        </thead>
        <tbody>
        @if(count($flight_data) > 0 )
            @foreach($flight_data as $item)
                <tr class="table-row-space"><td colspan="6">&nbsp;</td></tr>
                <tr>
                    <td scope="row">{{ $loop->iteration }}</td>
                    <td>{{ $item['Departure'] }}</td>
                    <td>{!! $item['Flight'] !!}</td>
                    <td>{{ $item['Route'] }}</td>
                    <td>{{ $item['Arrival'] }}</td>
                    <td>{{ $item['Status'] }}</td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>
</div>


