<style>
    .dash-list-table {
        overflow-x: hidden;
    }
</style>

<div class="dash-content-main">
    <div class="card">
        <div class="card-header bg-primary">
            <h5 style="float: left">
                Pharmacy wise Inventory
            </h5>
            @if(count($pharmacyInventory) > 0)
            <button type="button" style="float: right" onclick="table2excel('medicineInventory')" class="btn btn-default btn-sm"><i class="fa fa-download"></i> Download
            </button>
            @endif
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="medicineInventory">
                            <thead>
                            <tr>
                                <th>Type</th>
                                <th>Trade Name</th>
                                <th>SKU</th>
                                <th>Store Qty</th>
                                <th>In Requisition Qty</th>
                                <th>Out Requisition Qty</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($pharmacyInventory) > 0)
                                @foreach($pharmacyInventory as $key => $item)
                                    <tr>
                                        <td>{{$item['med_type']}}</td>
                                        <td>{{$item['trade_name']}}</td>
                                        <td>{{$item['sku']}}</td>
                                        <td>{{$item['quantity']}}</td>
                                        <td>{{$item['inRequisition'] ?? 0}}</td>
                                        <td>{{$item['outRequisition'] ?? 0}}</td>
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

