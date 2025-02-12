<div class="card">
    <div class="card-header bg-primary">
        Uploaded Materials
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <caption>List of materials</caption>
                        <thead>
                        <tr>
{{--                            @foreach($materials[0] as $keys => $title)--}}
{{--                            <th>--}}
{{--                                {{$title}}--}}
{{--                            </th>--}}
{{--                            @endforeach--}}
                                <th>S/N</th>
                                <th>Type</th>
                                <th>Generic Name</th>
                                <th>Trade Name</th>
                                <th>SKU</th>
                                <th>Existing</th>
                                <th>New</th>
                                <th>Batch Name</th>
                                <th>Expiry Date</th>
                                <th>Last Updated</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($materials as $key => $item)
                        @if($key > 0)
                        <tr>
                            @foreach($item as $k => $val)
                            <td>
                                @if($k != 8)
                                    {!! $val !!}
                                @else
                                    {{ $val=='-'?'N/A':\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val)->format('d-M-Y')}}
                                @endif
                            </td>

                            @endforeach
                        </tr>
                        @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
            <a class="btn btn-default" href="{{asset($filePath)}}" style="float: right"><i class="fa fa-download"></i> Download</a>
    </div>
</div>
