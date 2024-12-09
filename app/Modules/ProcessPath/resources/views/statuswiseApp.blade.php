<style>
    .statusBox {
        float: left;
        width: 110px;
        margin: 5px 3px;
        height: 80px;
    }

    .statusBox-inner {
        padding: 3px !important;
        font-weight: bold !important;
        height: 100%;
    }
</style>
@if ($status_wise_apps) {{-- Desk Officers --}}
<?php
    $process_type_name = array_column($status_wise_apps, 'process_type_name');
?>
<p><strong>Application list <span id="status_wise_label">{{ $process_type_name[0] }}</span> (Status Wise Total Summary)</strong></p>
    @foreach ($status_wise_apps as $row)
        <div class="statusBox">
            <div class="card statusBox-inner"
                style="display:block; {{ $row['color'] }}; border: 1px solid {{ $row['color'] }} !important;">
                <a href="#" class="statusWiseList" data-id="{{ $row['process_type_id'] . ',' . $row['id'] }}"
                    style="background: {{ $row['color'] }}">
                    <div class="card-header"
                        style="background: {{ $row['color'] }};color: white; padding: 10px 5px !important; alignment-adjust: central;height: 100%"
                        title="{{ !empty($row['status_name']) ? $row['status_name'] : 'N/A' }}">

                        <div class="row">
                            <div class="col-12 text-center">
                                <div class="h3" style="margin-top:0;margin-bottom:0;font-size:20px;font-weight: bold"
                                    id="{{ !empty($row['status_name']) ? $row['status_name'] : 'N/A' }}">
                                    {{ !empty($row['totalApplication']) ? $row['totalApplication'] : '0' }}
                                </div>
                            </div>
                        </div>

                        <div class="row" style=" text-decoration: none !important">
                            <div class="col-12 text-center">
                                <div class="h3"
                                    style="margin-top:0;margin-bottom:0;font-size:13px; font-weight: bold">
                                    {{ !empty($row['status_name']) ? $row['status_name'] : 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    @endforeach
@endif {{-- checking not empty $appsInDesk --}}
