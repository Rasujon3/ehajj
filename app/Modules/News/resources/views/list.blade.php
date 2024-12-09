
@extends('layouts.admin')

@section('header-resources')
    @include('partials.datatable-css')
@endsection
@section('content')
    @include('partials.messages')

    <div class="row">
        <div class="col-lg-12">
            <div class="card card-magenta border border-magenta">
                <div class="card-header">
                    <h3 class="card-title pt-2 pb-2"><i class="fa fa-list"></i> News List</h3>
                    <!-- /.card-tools -->
                </div>

                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered dt-responsive " cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Title</th>
                                    <th>Publish Date</th>
                                    <th>Post Type</th>
                                    <th>Post Status</th>
                                    <th>Attachment</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div><!-- /.table-responsive -->
                </div><!-- /.panel-body -->
            </div><!-- /.panel -->
        </div><!-- /.col-lg-12 -->
    </div>
@endsection
<!--content section-->

@section('footer-script')
    @include('partials.datatable-js')
<script>
    $(function() {
        $('#list').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ url('newslist/get-news-list') }}',
                method: 'get',
                data: function(d) {
                    d._token = $('input[name="_token"]').val();
                }
            },
            columns: [
                {
                    data: null,
                    // render: function (data, type, row, meta) {
                    //     console.log(row);
                    //     return meta.row + 1;
                    // },
                    name: 'serial_number',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'title',
                    name: 'title'
                },

                {
                    data: 'publish_date',
                    name: 'publish_date'
                },
                {
                    data: 'post_type',
                    name: 'post_type'
                },
                {
                    data: 'post_status',
                    name: 'post_status'
                },
                {
                    data: 'file_path',
                    name: 'file_path',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            "rowCallback": function(row, data, index) {
                var api = this.api();
                var startIndex = api.page.info().start;
                var counter = startIndex + index + 1;
                $('td:eq(0)', row).html(counter);
            },
            "aaSorting": []
        });
    });
</script>
@endsection
<!--- footer-script--->
