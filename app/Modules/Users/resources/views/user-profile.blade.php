<?php

use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;

$accessMode = ACL::getAccsessRight('user');
$isFromGuideView = Request::has('from') && Request::get('from') == 'guide-view';

if (!ACL::isAllowed($accessMode, 'V')) {
    die('no access right!');
}; ?>




@extends('layouts.admin')

@section('header-resources')
@endsection

@section('content')
    @include('partials.messages')
    {!! Form::open(array('url' => 'users/reject/'.Request::segment(3),'method' => 'post', 'class' => 'form-horizontal', 'id' => 'rejectUser')) !!}
    <!-- Modal -->
    <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">{{trans('Users::messages.reject_user')}}</h4>
                </div>
                <div class="modal-body">
                    <label class="required-star">{{trans('Users::messages.reject_reason')}} : </label>
                    <textarea name="reject_reason" class="form-control" required></textarea>
                </div>
                <div class="modal-footer">
                    @if(ACL::getAccsessRight('user','E'))
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->

    {!! Form::close() !!}

    <div class="row"> <!-- Horizontal Form -->
        <div class="col-sm-12">
            {!! Form::open(array('url' => '/users/approve/'.Encryption::encodeId($user->id),'method' => 'post', 'class' => 'form-horizontal',   'id'=> 'user_edit_form')) !!}
            <div class="card card-magenta border border-magenta">
                <div class="card-header">
                    <h5 class="card-title"><strong>{{trans('Users::messages.user_profile')}}
                            : {!!$user->user_first_name!!}&nbsp;</strong></h5>
                </div> <!-- /.panel-heading -->

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="col-md-10 col-xs-12">
                                <label class="col-md-4 col-xs-5">{{trans('Users::messages.user_name')}}</label>
                                <span class="col-md-1 col-xs-1">:</span>
                                <span
                                    class="col-md-5 col-xs-6">{!! $user->user_first_name . $user->user_middle_name . $user->user_last_name !!}</span>
                            </div>
                            @if($user->passport_no == '')
                                <div class="col-md-10 col-xs-12">
                                    <label class="col-md-4 col-xs-5">{{trans('Users::messages.user_nid')}}</label>
                                    <span class="col-md-1 col-xs-1">:</span>
                                    <span class="col-md-5 col-xs-6">{!!$user->user_nid!!}</span>
                                </div>
                            @endif
                            <div class="col-md-10 col-xs-12">
                                <label class="col-md-4 col-xs-5">{{trans('Users::messages.user_dob')}}</label>
                                <span class="col-md-1 col-xs-1">:</span>

                                <span class="col-md-5 col-xs-6">{{ $user->user_DOB != null ? CommonFunction::changeDateFormat($user->user_DOB) : $user->user_DOB }}</span>
{{--                                    class="col-md-5 col-xs-6">{{ CommonFunction::changeDateFormat($user->user_DOB) }}</span>--}}
                            </div>

                            <div class="col-md-10 col-xs-12">
                                <label class="col-md-4 col-xs-5">{{trans('Users::messages.user_gender')}}</label>
                                <span class="col-md-1 col-xs-1">:</span>
                                <span
                                    class="col-md-5 col-xs-6">{{ $user->user_gender == 'Male' ? 'Male' : ($user->user_gender == 'Female' ? 'Female' : '') }}</span>
                            </div>
                            <div class="col-md-10 col-xs-12">
                                <label class="col-md-4 col-xs-5">{{trans('Users::messages.user_designation')}}</label>
                                <span class="col-md-1 col-xs-1">:</span>
                                <span class="col-md-5 col-xs-6">{{ $user->designation }}</span>
                            </div>
                            <div class="col-md-10 col-xs-12">
                                <label class="col-md-4 col-xs-5">{{trans('Users::messages.user_mobile')}}</label>
                                <span class="col-md-1 col-xs-1">:</span>
                                <span class="col-md-5 col-xs-6">{{ $user->user_mobile }}</span>
                            </div>
                            <div class="col-md-10 col-xs-12">
                                <label class="col-md-4 col-xs-5">{{trans('Users::messages.user_type')}}</label>
                                <span class="col-md-1 col-xs-1">:</span>
                                <span class="col-md-5 col-xs-6">{{ $user->type_name }}</span>
                            </div>
                            <div class="col-md-10 col-xs-12">
                                <label class="col-md-4 col-xs-5">{{trans('Users::messages.user_email')}}</label>
                                <span class="col-md-1 col-xs-1">:</span>
                                <span class="col-md-5 col-xs-6">{{ $user->user_email }}</span>
                            </div>
                            <div class="col-md-10 col-xs-12">
                                <label class="col-md-4 col-xs-5">{{trans('Users::messages.working_office')}}</label>
                                <span class="col-md-1 col-xs-1">:</span>
                                <span class="col-md-5 col-xs-6">{{ $working_office }}</span>
                            </div>
                            <div class="col-md-10 col-xs-12">
                                <label class="col-md-4 col-xs-5">{{trans('Users::messages.user_status')}}</label>
                                <span class="col-md-1 col-xs-1">:</span>
                                <span class="col-md-5 col-xs-6">{{ $user->user_status }}</span>
                            </div>
                            @if($user->working_user_type == 'Guide')
                                <div class="col-md-10 col-xs-12">
                                    <label class="col-md-4 col-xs-5">{{trans('Users::messages.flight_code')}}</label>
                                    <span class="col-md-1 col-xs-1">:</span>
                                    <span class="col-md-5 col-xs-6">{{ $user->flight_code }}</span>
                                </div>
                                <div class="col-md-10 col-xs-12">
                                    <label class="col-md-4 col-xs-5">{{trans('Users::messages.flight_date')}}</label>
                                    <span class="col-md-1 col-xs-1">:</span>
                                    <span class="col-md-5 col-xs-6">{{ \Carbon\Carbon::parse($user->departure_time)->format('d-m-Y h:i A' ) }}</span>
                                </div>
                            @endif
                            <div class="col-md-10 col-xs-12">
                                <label
                                    class="col-md-4 col-xs-5">{{trans('Users::messages.verification_status')}}</label>
                                <span class="col-md-1 col-xs-1">:</span>
                                <span class="col-md-5 col-xs-6">{{ $user->user_verification }}</span>
                                @if ($user->user_verification == 'no')
                                    <a href="{{ url('users/resend-email-verification-from-admin/' . Encryption::encodeId($user->id)) }}">
                                        <button type="button" class="btn btn-info">Resend mail</button>
                                    </a>
                                @endif
                            </div>
                            <div class="col-md-10 col-xs-12">
                                @if ($user->is_approved != 1)
                                    <label class="col-md-4 text-left">{{trans('Users::messages.verification_expire')}}
                                        : </label>
                                    <span class="col-md-1 col-xs-1">:</span>
                                    <span class="col-md-8 text-left">{!! $user->user_hash_expire_time !!}&nbsp;</span>
                                @endif
                            </div>
                            <br>


                            @if(($user->type_id=='4x404') && isset($delegateInfo) && $delegateInfo != '')

                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border">Delegated To</legend>
                                    <div class="control-group">
                                        <div style="text-align: left;">
                                            <b>Name : </b> {{ $delegateInfo->user_full_name }}<br/>
                                            <b>Designation : </b>{{ $delegateInfo->desk_name }}<br/>
                                            <b>Email : </b>{{ $delegateInfo->user_email }}<br/>
                                            <b>Mobile : </b>{{ $delegateInfo->user_mobile }}<br/><br/>
                                        </div>
                                    </div>
                                </fieldset>
                            @endif
                            <div class="col-md-12">
                            <div class="col-md-6">
                            <?php
                            $approval = '';
                            $type = explode('x', Auth::user()->user_type);

                            if (substr($type[1], 2, 2) == 1  && ( $user->is_approved!=1)) { // if this is admin user
                                echo Form::select('user_type', $user_types, null, $attributes = array('class' => 'form-control required', 'required' => 'required',
                                    'placeholder' => 'Select A User Type', 'id' => "user_type"));
                            }

                            if ($user->user_status == 'inactive' && $user->user_verification == 'yes') {
                                $approval = '<button type="submit" class="btn btn-sm btn-success"> <i class="fa  fa-check"></i> Approve</button></form>';
                                $approval .= ' <a data-toggle="modal" data-target="#myModal2" class="btn btn-sm btn-danger addProjectModa2"><i class="fa fa-times"></i>&nbsp;Reject User</a> ';
                            }
                            ?>
                            </div>
                            </div>
                        </div>

                        <div class="col-md-3 text-center">
                            <br/>
                            <img src="{{ CommonComponent()->dynamicImageUrl($user->user_pic, 'userProfile', 'users/profile-pic/') }}" alt="Profile picture" class="profile-user-img img-responsive img-circle"/>
                        </div>
                    </div>
                </div><!-- /.box -->

                <div class="card-footer">
                    <div class="float-left">
                        <a href="{{ $isFromGuideView ? url('guide-users/lists') : url('users/lists') }}" class="btn btn-sm btn-default"><i
                                class="fa fa-times"></i> Close</a>
                    </div>
                    <div class="float-right">
                        <?php
                        $delegations = '';
                        if ($user->type_id == '4x404') {
                            $delegations = '<a href="' . url('users/delegations/' . Encryption::encodeId($user->id)) . '" class="btn btn-sm btn-primary"><i class="fa fa-paper-plane"></i> Delegation</a>';
                        }
                        $edit = '';
                        $access_users = \App\Modules\Settings\Models\Configuration::where('caption','Guide_List_Side_Bar')->first();
                        $access_users_array = json_decode($access_users->value2);
                        if ($user->user_type != '1x101') {
                            $edit = '<a href="'. url('users/edit/' . Encryption::encodeId($user->id) . ($isFromGuideView ? '?from=guide-view' : '')) .'" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> Edit</a>';
                        }
                        $reset_password = '<a href="' . url('users/reset-password/' . Encryption::encodeId($user->id)) . '" class="btn btn-sm btn-warning"'
                            . 'onclick="return confirm(\'Are you sure?\')">'
                            . '<i class="fa fa-refresh"></i> Reset password</a>';

                        $logged_in_user_type = Auth::user()->user_type;
                        $activate = '';
                        if ($logged_in_user_type == '1x101') {
                            if ($user->user_status == 'inactive') {
                                $activate = '<a href="' . url('users/activate/' . Encryption::encodeId($user->id)) . '" class="btn btn-sm btn-success"><i class="fa fa-unlock"></i>  Activate</a>';
                            } else {
                                $activate = '<a href="' . url('users/activate/' . Encryption::encodeId($user->id)) . '" class="btn btn-sm btn-danger"'
                                    . 'onclick="return confirm(\'Are you sure?\')">'
                                    . '<i class="fa fa-unlock-alt"></i> Deactivate</a>';
                            }
                        }
                        if ($user->is_approved == true) {
                            if (in_array(Auth::user()->user_type, ['1x101', '8x808']) || in_array(\Illuminate\Support\Facades\Auth::user()->user_email,$access_users_array)) {
                                if (ACL::getAccsessRight('user', 'E')|| in_array(\Illuminate\Support\Facades\Auth::user()->user_email,$access_users_array)) {
                                    echo $delegations . '&nbsp;' . $edit;
                                }

                                if (ACL::getAccsessRight('user', 'E')) {
                                    echo '&nbsp;' . $activate;
                                }
                            }
                        } else {
                            echo $approval;
                        }
                        ?>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>

            {!! Form::close() !!}
        </div>
    </div>

@endsection <!--content section-->
