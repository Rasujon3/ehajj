<link href="{{ asset("assets/plugins/jquery-ui/jquery-ui.css") }}" rel="Stylesheet" type="text/css"/>
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

    .file-upload-box {
        width: 100%;
        text-align: center;
        border: 1px dashed #D0D5DD;
        padding: 15px;
    }

    .file-upload-box .upload-box-container {
        display: inline-block;
        width: 100%;
        max-width: 320px;
    }

    .file-upload-box .upload-box-container p {
        font-size: 0.90rem;
        margin: 10px 0;
        color: #8d9194;
    }

    .file-upload-box .upload-box-container p strong {
        font-size: 1rem;
        color: #000000;
    }

    .file-upload-box .upload-icon {
        display: flex;
        height: 50px;
        width: 50px;
        margin: 0 auto;
        align-items: center;
        justify-content: center;
    }

    .file-upload-box .upload-icon img {
        display: block;
        max-width: 30px;
    }

    .file-upload-box .btn-filebrowse {
        border: 1px solid #009444;
        border-radius: 4px;
        background-color: #009444;
        height: 44px;
        padding: 10px 20px;
        color: #ffffff;
        margin: 10px 0;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        width: 140px;
    }

    .file-upload-box .btn-filebrowse:hover {
        color: #ffffff;
    }

    .file-upload-box .btn-filebrowse i {
        margin-left: 10px;
    }

    .btn-filebrowse input.uploadInput {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        right: 0;
        margin: 0;
        padding: 0;
        font-size: 20px;
        cursor: pointer;
        opacity: 0;
        filter: alpha(opacity=0);
    }

    .ck-editor__editable_inline {
        min-height: 150px;
    }
</style>

<div class="card card-magenta border border-magenta">
    <div class="card-header">
        <h3 class="card-title pt-2 pb-2"><i class="fa fa-list"></i> সংবাদ ও বিজ্ঞপ্তি সংযোজন ফরম</h3>
    </div>

    <!-- /.card-header -->
    <div class="card-body">
        <form action="{{ url('process/action/store/'.\App\Libraries\Encryption::encodeId($process_type_id)) }}" method="POST" class="form-horizontal" id="news_form" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="process_type_id" id="process_type_id"
                   value="{{ \App\Libraries\Encryption::encodeId($process_type_id) }}">
            <div class="dash-content-main">
                <div class="dash-content-main">
                    <div class="ehajj-text-form">
                        <div class="form-group">
                            <label>Title <strong class="text-danger">*</strong></label>
                            <input class="form-control required" type="text" name="title">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" id="description" name="description" cols="40"
                                      rows="10"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Publish Date <strong class="text-danger">*</strong></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control required" id="publish_date" name="publish_date"
                                               autocomplete="off">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Post Type <strong class="text-danger">*</strong></label>
                                    <select class="form-control required valid" id="post_type" name="post_type">
                                        <option value="">Select Below</option>
                                        @foreach($post_types as $post_index => $post_type)
                                            <option value="{{ $post_type }}">{{ ucfirst($post_type) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label>File Attaced Title</label>
                            <div class="file-upload-box">
                                <div class="upload-box-container">
                                    <span class="upload-icon"><img
                                            src="{{ asset('assets/images/icon-download-lightblue.svg') }}"
                                            alt="Icon"></span>
                                    <p class="text-eng"><strong>File</strong><br> File Format: *.jpg/*.png, / *.pdf,
                                        File Size <span class="clr-red">4 MB</span></p>
                                    <div class="fileUpload btn btn-filebrowse">
                                        <span>Browse <i class="fas fa-plus"></i></span>
                                        <input id="uploadBtn" type="file" name="file" class="uploadInput"/>
                                    </div>
                                </div>
                            </div>
                            <div id="uploaded_file" style="display:none;">
                                <span><button type="button" id="displayFile"
                                              class="btn btn-sm">Click to View</button></span>
                                <span><button type="button" id="resetFile" class="btn btn-sm">Reset</button></span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ url('/process/list') }}" class="btn btn-default"><i class="fas fa-times"></i> <span>Close</span></a>
                            <div class="">
                                <button type="submit" class="btn btn-primary" id="director_create_btn" name="actionBtn" value="draft"> Save as Draft </button>
                                <input type="submit" name="actionBtn" class="btn" style="background: #009444;color:white;" value="Submit"/>
                            </div>
                        </div>

                    </div>
                </div>


                <div class="modal fade" id="fileModal" style="width:100%;" tabindex="-1" role="dialog"
                     aria-labelledby="fileModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" style="max-width: 33%;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="fileModalLabel">File Display</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <iframe id="fileIframe" width="100%" height="450" frameborder="0"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{--@push('custom_script')--}}
<script src="{{ asset("assets/plugins/jquery-ui/jquery-ui.js") }}" type="text/javascript"></script>
{{--    <script src="https://cdn.ckeditor.com/4.20.2/standard/ckeditor.js"></script>--}}
<script>
    $(document).ready(function () {
        CKEDITOR.replace('description', {
            // htmlSupport: {
            //     allow: [
            //         {
            //             name: /.*!/,
            //             attributes: true,
            //             classes: true,
            //             styles: true
            //         }
            //     ]
            // }
        });

        $('#publish_date').datepicker({
            format: 'dd-M-yyyy',
        });
        $('#uploaded_file').hide();

        $(document).on('change', '#uploadBtn', function () {
            var file = event.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    var dataURL = e.target.result;
                    $("#displayFile").attr("data-url", dataURL);

                    $('.file-upload-box').hide();
                    $('#uploaded_file').show();
                };
                reader.readAsDataURL(file);
            }
        });
        $("#displayFile").on("click", function (event) {
            var dataURL = $(this).attr("data-url");
            if (dataURL) {
                $("#fileIframe").attr("src", dataURL);
            }
            $('#fileModal').modal();
        });

        $('#resetFile').click(function () {
            $('.file-upload-box').show();
            $('#uploaded_file').hide();
            $("#fileIframe").attr("src", "");
        });
        $("#news_form").validate({
            errorPlacement: function() {
                return false;
            }
        });
    });
</script>
{{--@endpush--}}
