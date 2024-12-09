{!! Form::open([
                     'url' => url('go-passport/delete-members/'. $id.'/' .($encode_ref_id ).'/'.($encode_process_type_id)),
                     'method' => 'post',
                     'class' => 'form-horizontal',
                     'id' => 'go_passport_delete_member',
                     'enctype' => 'multipart/form-data',
                     'files' => 'true'
               ])!!}
<div>
    <p>Are you sure you want to delete?</p>
    <input type="text" name="archived_reason" id="reasonInput" class="form-control mb-2" placeholder="Enter reason">
    <button type="submit" id="confirmDeleteButton" class="btn btn-danger btn-block">Delete</button>
    <button id="cancelDeleteButton" class="btn btn-secondary btn-block" data-dismiss="modal">Cancel</button>
</div>

{!! Form::close() !!}
