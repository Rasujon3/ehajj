<style>
    .loader {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        position: relative;
        animation: rotate 1s linear infinite;
    }
    .loader::before {
        content: "";
        box-sizing: border-box;
        position: absolute;
        inset: 0px;
        border-radius: 50%;
        border: 5px solid #1f3eff;
        animation: prixClipFix 2s linear infinite ;
    }

    @keyframes rotate {
        100%   {transform: rotate(360deg)}
    }

    @keyframes prixClipFix {
        0%   {clip-path:polygon(50% 50%,0 0,0 0,0 0,0 0,0 0)}
        25%  {clip-path:polygon(50% 50%,0 0,100% 0,100% 0,100% 0,100% 0)}
        50%  {clip-path:polygon(50% 50%,0 0,100% 0,100% 100%,100% 100%,100% 100%)}
        75%  {clip-path:polygon(50% 50%,0 0,100% 0,100% 100%,0 100%,0 100%)}
        100% {clip-path:polygon(50% 50%,0 0,100% 0,100% 100%,0 100%,0 0)}
    }
</style>
<div id="scan-prescription-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="margin-top: 0; top: 0;">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #0F6849 !important">
                <h5 class="modal-title" id="gridSystemModalLabel" style="color: white !important">প্রেস্ক্রিপশনের ছবিটি স্ক্যান করুন</h5>
                <button type="button" id="" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true" style="font-size: 22px; color: white">&times;</span></button>
            </div>
            {!! Form::open(array('url' => 'medicine-receive/upload-prescription','method' => 'post', 'id'=> 'scanForm')) !!}
            {!! Form::hidden('image', '', ['id' => 'image']) !!}
            <div class="modal-body text-center">
                <img src="" id="capturedImage" class="img-fluid align-center" style="width: 100%; height: auto;"></img>
                <div id="webcamContainer" style="max-width: 100%; overflow: hidden;">
                    <video id="webcamVideo" autoplay style="width: 100%; height: auto;"></video>
                </div>
                <canvas id="snapCanvas" style="display: none;"></canvas>
            </div>
            <div class="flex-center" style="margin-bottom: 10px !important;">
                <br>
                <button class="btn btn-primary" type="button" id="captureButton">Capture</button>
                <button class="btn btn-primary mr-1" type="submit" id="upButton" style="display: none">Upload file</button>
                <button class="btn btn-info ml-5 mr-1" type="button" id="draftButton" style="display: none">Upload Draft</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<script>
    let stream;
    $('#scanForm').on('submit', function() {
        var imageData = $('#capturedImage').attr('src');
        $('#upButton').after('<span class="loader"></span>');
        if(imageData) {
            $('#image').val(imageData);
            return true;
        } else {
            return false;
        }
        $('#upButton').next().hide();
    })

    function scanPrescription() {
        $('#scan-prescription-modal').modal('show');
        $('#webcamVideo').show();
        // $('#draftButton').next().hide();
        // $('#upButton').next().hide();
        // Check for browser support
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            // Access webcam
            navigator.mediaDevices.getUserMedia({video: {width: { ideal: 1920  }, height: { ideal: 1080  }}
            }).then(function(str) {
                    // Store the stream globally
                    stream = str;
                    // Display webcam stream on <video> element
                    $('#webcamVideo').get(0).srcObject = stream;
                })
                .catch(function(error) {
                    console.log("Error accessing webcam: " + error.message);
                });
        } else {
            console.log("getUserMedia not supported on your browser");
        }

        // Capture button click event
        $('#captureButton').click(function() {
            captureSnapshot();
        });
        // Draft button click event
        $('#draftButton').click(function() {
            captureSnapshot();
        });

        // Bind event handler for modal hide
        $('#scan-prescription-modal').on('hide.bs.modal', function () {
            // Clear captured image
            $('#capturedImage').attr('src', '');
            // Stop the stream and detach it from the video element
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                $('#webcamVideo').get(0).srcObject = null;
            }
            // Show capture button again
            $('#captureButton').show();
            // Show draft button again
            $('#draftButton').hide();
            // Hide upload button
            $('#upButton').hide();
        });
    }

    function captureSnapshot() {
        // Get video element and canvas
        var video = $('#webcamVideo').get(0);
        var canvas = $('#snapCanvas').get(0);
        var context = canvas.getContext('2d');

        // Set canvas dimensions to match video
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        // Draw current frame from video onto canvas
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        // Convert canvas content to base64 image data
        var imageDataURL = canvas.toDataURL('image/png');

        // Display the captured image
        $('#capturedImage').attr('src', imageDataURL);

        // Hide capture button
        $('#captureButton').hide();
        // show upload button
        $('#upButton').show();
        // show draft button
        $('#draftButton').show();

        $('#webcamVideo').hide();

    }

    $('#draftButton').click(function() {
        $('#upButton, #draftButton').prop('disabled', true);
        let imageData = $('#capturedImage').attr('src');
        $('#draftButton').after('<span class="loader"></span>');
        $.ajax({
            url: "/medicine-receive/drafted-image-upload",
            type: 'ajax',
            method: 'POST',
            data:
                {
                    _token: $('input[name="_token"]').val(),
                    'image': imageData,
                    'flag': 'draft',
                },
            success: function(response) {
                $('#upButton, #draftButton').prop('disabled', false);
                $('#scan-prescription-modal').modal('hide');
                if (response.responseCode == -1) {
                    toastr.error(response.message);
                } else {
                    toastr.success(response.message);
                }
                $('#draftButton').next().hide();
            },
            error: function(data) {
                $('#draftButton').next().hide();
                // console.log((data.responseJSON.errors));
                $('#upButton, #draftButton').prop('disabled', false);
            }
        })
    })
</script>
