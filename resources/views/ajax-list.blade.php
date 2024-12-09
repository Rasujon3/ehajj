<?php
date_default_timezone_set("Asia/Riyadh");
$curtime = strtotime(date('Y-m-d'));
?>
<input type="hidden" id="version" value="4">
<input type="hidden" id="cur_day" value="{{$curDay}}">
<input type="hidden" id="cur_month" value="{{$curMonth}}">
<input type="hidden" id="cur_notice" value="{{$notice}}">
<input type="hidden" id="cur_total_data" value="{{count($flights)}}">
@if($flights)
        <?php
        $sl = 1;
        ?>
    @foreach($flights as $flight)
        <div class="list-box" id="{{$flight->id}}">
            <div class="TimeC">
                    <?php
                    $departure_time = strtotime(date('Y-m-d', strtotime($flight->departure_time)));
                    $plusminus = '';
                    if ($curtime > $departure_time) {
                        $plusminus = '-1';
                    } else if ($departure_time > $curtime) {
                        $plusminus = '+1';
                    }
                    ?>
                {{date('H:i',strtotime($flight->departure_time))}}{{$plusminus}}
            </div>
            <div class="FlightC">
                {{$flight->Flight}}
            </div>
            <div class="AirlinesC">
                <img src="/airlines/{{$flight->airlines}}.png" alt="/airlines/{{$flight->airlines}}.png" alt="2"
                     style="width:170px;height:45px; "/>
            </div>
            <div class="DestinationC">
                {{$flight->arrival_to}}
            </div>

                <?php
                $status = strtolower($flight->Status);
                $bg_color = 'green';
                $showRemarks = false;

                if ($status == 'delayed') {
                    $showRemarks = true;
                    $bg_color = '#00733e';
                    $color = '#fff';
                } elseif ($status == 'boarding') {
                    $bg_color = 'blue';
                    $color = '#fff';
                } elseif ($status == 'cancelled') {
                    $showRemarks = true;
                    $bg_color = 'red';
                    $color = '#fff';
                } elseif ($status == 'scheduled') {
                    $bg_color = '#6037E9';
                    $color = '#fff';
                } elseif ($status == 'departed') {
                    $bg_color = 'navy';
                    $color = '#000';
                } elseif ($status == 'checking') {
                    $bg_color = '#6734FF';
                    $color = '#fff';
                    $flight->Status = "Check-In";
                }
                ?>
            <div class="RemarksC" style="background:{{$bg_color}}">
                    <?php
                    if ($flight->description != '' && $showRemarks) {
                        echo '<marquee>' . ucfirst($flight->Status) . ' <span style="color:#FFFF5F;font-size:36px;">' . $flight->description . '</span></marquee>';
                    } else {
                        echo ucfirst($flight->Status);
                    }
                    ?>
            </div>
            <div class="ExpC">
                @if(!empty($flight->departure_actual_time))
                        <?php
                        $schedule_time = strtotime(date('Y-m-d', strtotime($flight->departure_actual_time)));
                        $plus_minus = '';
                        if ($curtime > $schedule_time) {
                            $plus_minus = '-1';
                        } else if ($schedule_time > $curtime) {
                            $plus_minus = '+1';
                        }
                        ?>
                    {!! date('H:i' , strtotime($flight->departure_actual_time)) !!}{{$plus_minus}}
                @elseif(!empty($flight->departure_revise_schedule))
                        <?php
                        $schedule_time = strtotime(date('Y-m-d', strtotime($flight->departure_revise_schedule)));
                        $plus_minus = '';
                        if ($curtime > $schedule_time) {
                            $plus_minus = '-1';
                        } else if ($schedule_time > $curtime) {
                            $plus_minus = '+1';
                        }
                        ?>
                    {!! date('H:i' , strtotime($flight->departure_revise_schedule)) !!}{{$plus_minus}}
                @elseif(!empty($flight->departure_time))

                        <?php
                        $schedule_time = strtotime(date('Y-m-d', strtotime($flight->departure_time)));
                        $plus_minus = '';
                        if ($curtime > $schedule_time) {
                            $plus_minus = '-1';
                        } else if ($schedule_time > $curtime) {
                            $plus_minus = '+1';
                        }
                        ?>
                    {!! date('H:i' , strtotime($flight->departure_time)) !!}{{$plus_minus}}
                @endif
            </div>
            <div class="GateC">
                <div class="gatebox">{{$flight->gate_no}}</div>
            </div>
            <div class="clear"></div>
        </div>
            <?php
            ++$sl;
            if($sl>8){
                break;
            }
            ?>
    @endforeach
@endif
