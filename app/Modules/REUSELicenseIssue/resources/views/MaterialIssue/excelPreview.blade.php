<div class="card">
    <div class="card-header bg-primary">
        Issued Materials
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
{{--                                <th>--}}
{{--                                    {{$title}}--}}
{{--                                </th>--}}
{{--                            @endforeach--}}
                            <th>S/N</th>
                            <th>Type</th>
                            <th>Trade Name</th>
                            <th>SKU</th>
                            <th>Stock</th>
                            <th>Requisition</th>
                            <th>Last Updated</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($materials as $key => $item)
                            @if($key > 0)
                                <tr>
                                    @foreach($item as $k => $val)
                                        <td>
                                            @if($k != 7)
                                                {!! $val !!}
                                            @else
                                                {{\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val)->format('d-M-Y')}}
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
        @if($negativeInventory > 0)
            <p class="text-danger">বর্তমান ইনভেন্টরিতে কিছু প্রোডাক্ট কম / না থাকায় আবেদনটি গ্রহণযোগ্য নয়। অনুগ্রহ করে ইনভেন্টরি চেক করে আবার ফাইল আপলোড করুন।</p>
        @endif
        <a class="btn btn-default" href="{{asset($filePath)}}" style="float: right"><i class="fa fa-download"></i> Download</a>
    </div>
</div>
