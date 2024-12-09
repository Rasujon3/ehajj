<style>
    .card-header {
        padding: 0.75rem 1.25rem !important;
    }

    .input-disabled {
        pointer-events: none;
    }

    .modal.fade .modal-dialog {
        margin-top: 5px;
        margin-left: 19%;
    }

    .modal-content {
        width: 190%;
    }

</style>
<div class="dash-content-main">
    <div class="dash-content-main">
        <div class="ehajj-text-form">
            <div class="form-group row">
                <div class="col-md-2"><strong>Title</strong></div>
                <div class="col-md-10">: {{ $data->title }}</div>
            </div>
            <div class="form-group row">
                <div class="col-md-2"><strong>Publish Date</strong></div>
                <div class="col-md-10">: {{ date('M d, Y', strtotime($data->publish_date)) }}</div>
            </div>
            <div class="form-group row">
                <div class="col-md-2"><strong>Description</strong></div>
                <div class="col-md-10" style="display: inherit;">:&nbsp;{!! $data->description !!}</div>
            </div>

            <div class="form-group row">
                <div class="col-md-2"><strong>Attached File</strong></div>
                <div class="col-md-10">: <a href="{{ url($data->file_path) }}" target="_blank">Click to View</a></div>
{{--                <div class="col-md-10">: <a href="{{ url("/news/".\App\Libraries\Encryption::encodeId($data->id)) }}" target="_blank">Click to View</a></div>--}}
            </div>
        </div>
    </div>
</div>
