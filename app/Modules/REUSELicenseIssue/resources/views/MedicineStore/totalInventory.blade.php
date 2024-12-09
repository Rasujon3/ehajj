<style>
    .dash-list-table {
        overflow-x: hidden;
    }
</style>

<div class="dash-content-main">
    <div class="card">
        <div class="card-header bg-primary">
            <h5 style="float: left">
                Total Inventory
            </h5>
            @if(count($mainInventory) > 0)
            <button type="button" style="float: right" onclick="table2excel('totalInventory')" class="btn btn-default btn-sm"><i class="fa fa-download"></i> Download
            </button>
            @endif
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="totalInventory">
                            <thead>
                            <tr>
                                @if(!empty($title))
                                    @foreach($title as $keys => $item)
                                        <th>
                                            {{$item}}
                                        </th>
                                    @endforeach
                                    <th>
                                        Total Qty
                                    </th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($mainInventory) > 0)
                                @foreach($mainInventory as $key => $item)
                                    <tr>
                                        <td>
                                            {{$item['med_type']}}
                                        </td>
                                        <td>
                                            {{$item['generic_name']}}
                                        </td>
                                        <td>
                                            {{$item['trade_name'] }}
                                        </td>
                                        <td>
                                            {{$item['sku'] }}
                                        </td>
                                        <td>
                                            {{$item['quantity'] }}
                                        </td>

                                        @foreach ($pharmacyCount as $pharmacyId)
                                            @php
                                                $qty = 0;
                                                if(isset($item[$pharmacyId->id]))
                                                    $qty = $item[$pharmacyId->id];
                                            @endphp
                                            <td>
                                                {{ $qty }}
                                            </td>
                                        @endforeach
                                        <td>
                                            {{ $item['total'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

