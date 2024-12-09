
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

</head>
<body>

   <p>তারিখ: {!! $current_time !!}</p>
   <div style="line-height: 5px">
       <p>বরাবর,</p>
       <p>মাননীয় পরিচালক,</p>
       <p>হজ অফিস, আশকোনা, ঢাকা।</p>
   </div>

   <p>বিষয়ঃ ফ্লাইট নির্ধারণের আবেদন।</p>
   <p> জনাব,</p>
  <p>বিনীত নিবেদন এই যে, আমি {!! $hajjSession->caption !!} সালের হজ গাইড নির্বাচিত হয়েছি। আমার সঙ্গে যে সকল হজযাত্রী হজে যেতে ইচ্ছুক, তাঁদের
    তালিকা নিম্নে প্রদান করা হল। আমাকে {!! $possibleFlightDate !!} এর মধ্যে হজ ফ্লাইট দেওয়ার অনুরোধ করা হলো।
  </p>


   <h2 align="center" style="margin: 15px 0px; font-size: 20px; font-weight: bold;">হজযাত্রীর তালিকা</h2>

   @if(count($flightReqInfo) > 0 && count($flightReqInfo) <= 9)
   <table style="width: 100%; border: 1px solid #F5F6F7">
    <thead>
        <tr style="background-color: #DFE2E6">
            <th style="font-size: 11px; text-align: center; padding: 15px 0px;">SL</th>
            <th style="font-size: 11px; text-align: center; padding: 15px 0px;">পিআইডি</th>
            <th style="font-size: 11px; text-align: center; padding: 15px 0px;">নাম</th>
            <th style="font-size: 11px; text-align: center; padding: 15px 0px;">ট্র্যাকিং নম্বর</th>
            <th style="font-size: 11px; text-align: center; padding: 15px 0px;">মোবাইল নং</th>

        </tr>
    </thead>
    <tbody>
        @for($i=0; $i<count($flightReqInfo) && $i<10; $i++)
            <tr>
                <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$i + 1}}</td>
                <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->pid}}</td>
                <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->pilgrim_name}}</td>
                <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->tracking_no}}</td>
                <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->pilgrim_mobile}}</td>
            </tr>
        @endfor
        </tbody>
    </table>

       <table align="right" style="display: block;">
           <tbody style="display: block; text-align: right;">
           <tr>
               <td colspan="2" style="font-size: 15px; text-align: center; padding-bottom: 35px;"><b>নিবেদক</b></td>
               <td></td>
           </tr>
           <tr>
               <td style="padding-right: 5px; font-size: 15px;">গাইড নাম</td>
               <td style="font-size: 15px;">: {!! $guideInfo->user_first_name !!} </td>
           </tr>
           <tr>
               <td style="padding-right: 5px; font-size: 15px;">গাইড ট্রাকিং নং</td>
               <td style="font-size: 15px;">: {!! $guideEmail[0] !!}</td>
           </tr>
           <tr>
               <td style="padding-right: 5px; font-size: 15px;">মোবাইল নম্বর</td>
               <td style="font-size:15px;">: {!! $guideInfo->user_mobile !!} </td>
           </tr>
           <tr>
               <td style="padding-right: 5px; font-size: 15px;">আবেদনের ট্রাকিং নং</td>
               <td style="font-size:15px;">: {!! $application_tracking_no !!} </td>
           </tr>
           </tbody>
       </table>
    @endif
   @if(count($flightReqInfo) > 9 && count($flightReqInfo) <= 31)
       <table style="width: 100%; border: 1px solid #F5F6F7">
           <thead>
           <tr style="background-color: #DFE2E6">
               <th style="font-size: 11px; text-align: center; padding: 15px 0px;">SL</th>
               <th style="font-size: 11px; text-align: center; padding: 15px 0px;">পিআইডি</th>
               <th style="font-size: 11px; text-align: center; padding: 15px 0px;">নাম</th>
               <th style="font-size: 11px; text-align: center; padding: 15px 0px;">ট্র্যাকিং নম্বর</th>
               <th style="font-size: 11px; text-align: center; padding: 15px 0px;">মোবাইল নং</th>
           </tr>
           </thead>
           <tbody>
           @if(count($flightReqInfo) > 9 && count($flightReqInfo) < 16)
               @php($iii=9)
           @else
               @php($iii=15)
           @endif
           @for($i=0; $i<count($flightReqInfo) && $i<$iii; $i++)
               <tr>
                   <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$i + 1}}</td>
                   <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->pid}}</td>
                   <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->pilgrim_name}}</td>
                   <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->tracking_no}}</td>
                   <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->pilgrim_mobile}}</td>
               </tr>
           @endfor
           </tbody>
       </table>

       <table align="right" style="display: block;">
           <tbody style="display: block; text-align: right;">
           <tr>
               <td colspan="2" style="font-size: 15px; text-align: center; padding-bottom: 35px;"><b>নিবেদক</b></td>
               <td></td>
           </tr>
           <tr>
               <td style="padding-right: 5px; font-size: 15px;">গাইড নাম</td>
               <td style="font-size: 15px;">: {!! $guideInfo->user_first_name !!} </td>
           </tr>
           <tr>
               <td style="padding-right: 5px; font-size: 15px;">গাইড ট্রাকিং নং</td>
               <td style="font-size: 15px;">: {!! $guideEmail[0] !!}</td>
           </tr>
           <tr>
               <td style="padding-right: 5px; font-size: 15px;">মোবাইল নম্বর</td>
               <td style="font-size:15px;">: {!! $guideInfo->user_mobile !!} </td>
           </tr>
           <tr>
               <td style="padding-right: 5px; font-size: 15px;">আবেদনের ট্রাকিং নং</td>
               <td style="font-size:15px;">: {!! $application_tracking_no !!} </td>
           </tr>
           </tbody>
       </table>
       @if(count($flightReqInfo) > 9)
       <pagebreak></pagebreak>
        {{--       Next rows --}}
       <h2 align="center" style="margin: 15px 0px; font-size: 20px; font-weight: bold;">হজযাত্রীর তালিকা</h2>
       <table style="width: 100%; border: 1px solid #F5F6F7">
           <thead>
           <tr style="background-color: #DFE2E6">
               <th style="font-size: 11px; text-align: center; padding: 15px 0px;">SL</th>
               <th style="font-size: 11px; text-align: center; padding: 15px 0px;">পিআইডি</th>
               <th style="font-size: 11px; text-align: center; padding: 15px 0px;">নাম</th>
               <th style="font-size: 11px; text-align: center; padding: 15px 0px;">ট্র্যাকিং নম্বর</th>
               <th style="font-size: 11px; text-align: center; padding: 15px 0px;">মোবাইল নং</th>
           </tr>
           </thead>
           <tbody>

           @for($i = $iii; $i<count($flightReqInfo) && $i<31; $i++)
               <tr>
                   <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$i + 1}}</td>
                   <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->pid}}</td>
                   <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->pilgrim_name}}</td>
                   <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->tracking_no}}</td>
                   <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->pilgrim_mobile}}</td>
               </tr>
           @endfor
           </tbody>
       </table>

       <table align="right" style="display: block;">
           <tbody style="display: block; text-align: right;">
           <tr>
               <td colspan="2" style="font-size: 15px; text-align: center; padding-bottom: 35px;"><b>নিবেদক</b></td>
               <td></td>
           </tr>
           <tr>
               <td style="padding-right: 5px; font-size: 15px;">গাইড নাম</td>
               <td style="font-size: 15px;">: {!! $guideInfo->user_first_name !!} </td>
           </tr>
           <tr>
               <td style="padding-right: 5px; font-size: 15px;">গাইড ট্রাকিং নং</td>
               <td style="font-size: 15px;">: {!! $guideEmail[0] !!}</td>
           </tr>
           <tr>
               <td style="padding-right: 5px; font-size: 15px;">মোবাইল নম্বর</td>
               <td style="font-size:15px;">: {!! $guideInfo->user_mobile !!} </td>
           </tr>
           <tr>
               <td style="padding-right: 5px; font-size: 15px;">আবেদনের ট্রাকিং নং</td>
               <td style="font-size:15px;">: {!! $application_tracking_no !!} </td>
           </tr>
           </tbody>
       </table>
       @endif
   @endif
   @if(count($flightReqInfo) > 31 && count($flightReqInfo) <= 38)
       {{--       1st page--}}
       <table style="width: 100%; border: 1px solid #F5F6F7">
           <thead>
           <tr style="background-color: #DFE2E6">
               <th style="font-size: 11px; text-align: center; padding: 15px 0px;">SL</th>
               <th style="font-size: 11px; text-align: center; padding: 15px 0px;">পিআইডি</th>
               <th style="font-size: 11px; text-align: center; padding: 15px 0px;">নাম</th>
               <th style="font-size: 11px; text-align: center; padding: 15px 0px;">ট্র্যাকিং নম্বর</th>
               <th style="font-size: 11px; text-align: center; padding: 15px 0px;">মোবাইল নং</th>
           </tr>
           </thead>
           <tbody>
           @for($i=0; $i<count($flightReqInfo) && $i<16; $i++)
               <tr>
                   <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$i + 1}}</td>
                   <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->pid}}</td>
                   <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->pilgrim_name}}</td>
                   <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->tracking_no}}</td>
                   <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->pilgrim_mobile}}</td>
               </tr>
           @endfor
           </tbody>
       </table>
       <table align="right" style="display: block;margin-bottom: 0 !important;padding-bottom: 0 !important;">
           <tbody style="display: block; text-align: right;">
           <tr>
               <td colspan="2" style="font-size: 15px; text-align: center; padding-bottom: 35px;"><b>নিবেদক</b></td>
               <td></td>
           </tr>
           <tr>
               <td style="padding-right: 5px; font-size: 15px;">গাইড নাম</td>
               <td style="font-size: 15px;">: {!! $guideInfo->user_first_name !!} </td>
           </tr>
           <tr>
               <td style="padding-right: 5px; font-size: 15px;">গাইড ট্রাকিং নং</td>
               <td style="font-size: 15px;">: {!! $guideEmail[0] !!}</td>
           </tr>
           <tr>
               <td style="padding-right: 5px; font-size: 15px;">মোবাইল নম্বর</td>
               <td style="font-size:15px;">: {!! $guideInfo->user_mobile !!} </td>
           </tr>
           <tr>
               <td style="padding-right: 5px; font-size: 15px;">আবেদনের ট্রাকিং নং</td>
               <td style="font-size:15px;">: {!! $application_tracking_no !!} </td>
           </tr>
           </tbody>
       </table>
       <pagebreak></pagebreak>
       {{--       2nd page --}}
       <h2 align="center" style="margin: 15px 0px; font-size: 20px; font-weight: bold;">হজযাত্রীর তালিকা</h2>
       <table style="width: 100%; border: 1px solid #F5F6F7">
           <thead>
           <tr style="background-color: #DFE2E6">
               <th style="font-size: 11px; text-align: center; padding: 15px 0px;">SL</th>
               <th style="font-size: 11px; text-align: center; padding: 15px 0px;">পিআইডি</th>
               <th style="font-size: 11px; text-align: center; padding: 15px 0px;">নাম</th>
               <th style="font-size: 11px; text-align: center; padding: 15px 0px;">ট্র্যাকিং নম্বর</th>
               <th style="font-size: 11px; text-align: center; padding: 15px 0px;">মোবাইল নং</th>
           </tr>
           </thead>
           <tbody>
           @if(count($flightReqInfo) == 37 || count($flightReqInfo) == 36)
               @php($ii=35)
           @else
               @php($ii=37)
           @endif
           @for($i=16; $i<count($flightReqInfo) && $i<$ii; $i++)
               <tr>
                   <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$i + 1}}</td>
                   <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->pid}}</td>
                   <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->pilgrim_name}}</td>
                   <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->tracking_no}}</td>
                   <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->pilgrim_mobile}}</td>
               </tr>
           @endfor
           </tbody>
       </table>

       <table align="right" style="display: block;">
           <tbody style="display: block; text-align: right;">
           <tr>
               <td colspan="2" style="font-size: 15px; text-align: center; padding-bottom: 35px;"><b>নিবেদক</b></td>
               <td></td>
           </tr>
           <tr>
               <td style="padding-right: 5px; font-size: 15px;">গাইড নাম</td>
               <td style="font-size: 15px;">: {!! $guideInfo->user_first_name !!} </td>
           </tr>
           <tr>
               <td style="padding-right: 5px; font-size: 15px;">গাইড ট্রাকিং নং</td>
               <td style="font-size: 15px;">: {!! $guideEmail[0] !!}</td>
           </tr>
           <tr>
               <td style="padding-right: 5px; font-size: 15px;">মোবাইল নম্বর</td>
               <td style="font-size:15px;">: {!! $guideInfo->user_mobile !!} </td>
           </tr>
           <tr>
               <td style="padding-right: 5px; font-size: 15px;">আবেদনের ট্রাকিং নং</td>
               <td style="font-size:15px;">: {!! $application_tracking_no !!} </td>
           </tr>
           </tbody>
       </table>
       @if(count($flightReqInfo) > 35)
           <pagebreak></pagebreak>
           {{--       last page --}}
           <h2 align="center" style="margin: 15px 0px; font-size: 20px; font-weight: bold;">হজযাত্রীর তালিকা</h2>
           <table style="width: 100%; border: 1px solid #F5F6F7">
               <thead>
               <tr style="background-color: #DFE2E6">
                   <th style="font-size: 11px; text-align: center; padding: 15px 0px;">SL</th>
                   <th style="font-size: 11px; text-align: center; padding: 15px 0px;">পিআইডি</th>
                   <th style="font-size: 11px; text-align: center; padding: 15px 0px;">নাম</th>
                   <th style="font-size: 11px; text-align: center; padding: 15px 0px;">ট্র্যাকিং নম্বর</th>
                   <th style="font-size: 11px; text-align: center; padding: 15px 0px;">মোবাইল নং</th>
               </tr>
               </thead>
               <tbody>
               @for($i = $ii; $i<count($flightReqInfo); $i++)
                   <tr>
                       <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$i + 1}}</td>
                       <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->pid}}</td>
                       <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->pilgrim_name}}</td>
                       <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->tracking_no}}</td>
                       <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->pilgrim_mobile}}</td>
                   </tr>
               @endfor
               </tbody>
           </table>
           <table align="right" style="display: block;">
               <tbody style="display: block; text-align: right;">
               <tr>
                   <td colspan="2" style="font-size: 15px; text-align: center; padding-bottom: 35px;"><b>নিবেদক</b></td>
                   <td></td>
               </tr>
               <tr>
                   <td style="padding-right: 5px; font-size: 15px;">গাইড নাম</td>
                   <td style="font-size: 15px;">: {!! $guideInfo->user_first_name !!} </td>
               </tr>
               <tr>
                   <td style="padding-right: 5px; font-size: 15px;">গাইড ট্রাকিং নং</td>
                   <td style="font-size: 15px;">: {!! $guideEmail[0] !!}</td>
               </tr>
               <tr>
                   <td style="padding-right: 5px; font-size: 15px;">মোবাইল নম্বর</td>
                   <td style="font-size:15px;">: {!! $guideInfo->user_mobile !!} </td>
               </tr>
               <tr>
                   <td style="padding-right: 5px; font-size: 15px;">আবেদনের ট্রাকিং নং</td>
                   <td style="font-size:15px;">: {!! $application_tracking_no !!} </td>
               </tr>
               </tbody>
           </table>
       @endif
   @endif
   @if(count($flightReqInfo) > 38)
        {{--       1st page--}}
       <table style="width: 100%; border: 1px solid #F5F6F7">
           <thead>
           <tr style="background-color: #DFE2E6">
               <th style="font-size: 11px; text-align: center; padding: 15px 0px;">SL</th>
               <th style="font-size: 11px; text-align: center; padding: 15px 0px;">পিআইডি</th>
               <th style="font-size: 11px; text-align: center; padding: 15px 0px;">নাম</th>
               <th style="font-size: 11px; text-align: center; padding: 15px 0px;">ট্র্যাকিং নম্বর</th>
               <th style="font-size: 11px; text-align: center; padding: 15px 0px;">মোবাইল নং</th>
           </tr>
           </thead>
           <tbody>
           @for($i=0; $i<count($flightReqInfo) && $i<16; $i++)
               <tr>
                   <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$i + 1}}</td>
                   <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->pid}}</td>
                   <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->pilgrim_name}}</td>
                   <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->tracking_no}}</td>
                   <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->pilgrim_mobile}}</td>
               </tr>
           @endfor
           </tbody>
       </table>
       <table align="right" style="display: block;margin-bottom: 0 !important;padding-bottom: 0 !important;">
           <tbody style="display: block; text-align: right;">
           <tr>
               <td colspan="2" style="font-size: 15px; text-align: center; padding-bottom: 35px;"><b>নিবেদক</b></td>
               <td></td>
           </tr>
           <tr>
               <td style="padding-right: 5px; font-size: 15px;">গাইড নাম</td>
               <td style="font-size: 15px;">: {!! $guideInfo->user_first_name !!} </td>
           </tr>
           <tr>
               <td style="padding-right: 5px; font-size: 15px;">গাইড ট্রাকিং নং</td>
               <td style="font-size: 15px;">: {!! $guideEmail[0] !!}</td>
           </tr>
           <tr>
               <td style="padding-right: 5px; font-size: 15px;">মোবাইল নম্বর</td>
               <td style="font-size:15px;">: {!! $guideInfo->user_mobile !!} </td>
           </tr>
           <tr>
               <td style="padding-right: 5px; font-size: 15px;">আবেদনের ট্রাকিং নং</td>
               <td style="font-size:15px;">: {!! $application_tracking_no !!} </td>
           </tr>
           </tbody>
       </table>
       <pagebreak></pagebreak>
       {{--       2nd page --}}
       <h2 align="center" style="margin: 15px 0px; font-size: 20px; font-weight: bold;">হজযাত্রীর তালিকা</h2>
       <table style="width: 100%; border: 1px solid #F5F6F7">
           <thead>
           <tr style="background-color: #DFE2E6">
               <th style="font-size: 11px; text-align: center; padding: 15px 0px;">SL</th>
               <th style="font-size: 11px; text-align: center; padding: 15px 0px;">পিআইডি</th>
               <th style="font-size: 11px; text-align: center; padding: 15px 0px;">নাম</th>
               <th style="font-size: 11px; text-align: center; padding: 15px 0px;">ট্র্যাকিং নম্বর</th>
               <th style="font-size: 11px; text-align: center; padding: 15px 0px;">মোবাইল নং</th>
           </tr>
           </thead>
           <tbody>
           @for($i=16; $i<count($flightReqInfo) && $i<37; $i++)
               <tr>
                   <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$i + 1}}</td>
                   <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->pid}}</td>
                   <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->pilgrim_name}}</td>
                   <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->tracking_no}}</td>
                   <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->pilgrim_mobile}}</td>
               </tr>
           @endfor
           </tbody>
       </table>

       <table align="right" style="display: block;">
           <tbody style="display: block; text-align: right;">
           <tr>
               <td colspan="2" style="font-size: 15px; text-align: center; padding-bottom: 35px;"><b>নিবেদক</b></td>
               <td></td>
           </tr>
           <tr>
               <td style="padding-right: 5px; font-size: 15px;">গাইড নাম</td>
               <td style="font-size: 15px;">: {!! $guideInfo->user_first_name !!} </td>
           </tr>
           <tr>
               <td style="padding-right: 5px; font-size: 15px;">গাইড ট্রাকিং নং</td>
               <td style="font-size: 15px;">: {!! $guideEmail[0] !!}</td>
           </tr>
           <tr>
               <td style="padding-right: 5px; font-size: 15px;">মোবাইল নম্বর</td>
               <td style="font-size:15px;">: {!! $guideInfo->user_mobile !!} </td>
           </tr>
           <tr>
               <td style="padding-right: 5px; font-size: 15px;">আবেদনের ট্রাকিং নং</td>
               <td style="font-size:15px;">: {!! $application_tracking_no !!} </td>
           </tr>
           </tbody>
       </table>
        @if(count($flightReqInfo) > 37)
            <pagebreak></pagebreak>
            {{--       last page --}}
            <h2 align="center" style="margin: 15px 0px; font-size: 20px; font-weight: bold;">হজযাত্রীর তালিকা</h2>
            <table style="width: 100%; border: 1px solid #F5F6F7">
                <thead>
                <tr style="background-color: #DFE2E6">
                    <th style="font-size: 11px; text-align: center; padding: 15px 0px;">SL</th>
                    <th style="font-size: 11px; text-align: center; padding: 15px 0px;">পিআইডি</th>
                    <th style="font-size: 11px; text-align: center; padding: 15px 0px;">নাম</th>
                    <th style="font-size: 11px; text-align: center; padding: 15px 0px;">ট্র্যাকিং নম্বর</th>
                    <th style="font-size: 11px; text-align: center; padding: 15px 0px;">মোবাইল নং</th>
                </tr>
                </thead>
                <tbody>
                @for($i=37; $i<count($flightReqInfo); $i++)
                    <tr>
                        <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$i + 1}}</td>
                        <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->pid}}</td>
                        <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->pilgrim_name}}</td>
                        <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->tracking_no}}</td>
                        <td style="font-size: 15px; text-align: center; padding: 5px 0px; border-bottom: 1px solid #DFE2E6">{{$flightReqInfo[$i]->pilgrim_mobile}}</td>
                    </tr>
                @endfor
                </tbody>
            </table>
            <table align="right" style="display: block;">
                <tbody style="display: block; text-align: right;">
                <tr>
                    <td colspan="2" style="font-size: 15px; text-align: center; padding-bottom: 35px;"><b>নিবেদক</b></td>
                    <td></td>
                </tr>
                <tr>
                    <td style="padding-right: 5px; font-size: 15px;">গাইড নাম</td>
                    <td style="font-size: 15px;">: {!! $guideInfo->user_first_name !!} </td>
                </tr>
                <tr>
                    <td style="padding-right: 5px; font-size: 15px;">গাইড ট্রাকিং নং</td>
                    <td style="font-size: 15px;">: {!! $guideEmail[0] !!}</td>
                </tr>
                <tr>
                    <td style="padding-right: 5px; font-size: 15px;">মোবাইল নম্বর</td>
                    <td style="font-size:15px;">: {!! $guideInfo->user_mobile !!} </td>
                </tr>
                <tr>
                    <td style="padding-right: 5px; font-size: 15px;">আবেদনের ট্রাকিং নং</td>
                    <td style="font-size:15px;">: {!! $application_tracking_no !!} </td>
                </tr>
                </tbody>
            </table>
        @endif
   @endif
<br>
<div style="line-height: 5px;text-align: left">
     <p style="text-align: left;">হজ অফিস কর্তৃক পূরণীয়ঃ</p>
     <hr style="margin: 0 !important;padding: 0 !important;">
    <table style="margin-top: 18px">
        <tbody>
            <tr>
                <td style="padding-right: 5px; font-size: 15px;">গাইড নং</td>
                <td style="font-size: 15px;">: </td>
            </tr>
            <tr>
                <td style="padding-right: 5px; font-size: 15px;">ফ্লাইটের তারিখ</td>
                <td style="font-size: 15px;">: </td>
            </tr>
            <tr>
                <td style="padding-right: 5px; font-size: 15px;">ফ্লাইট কোড</td>
                <td style="font-size:15px;">: </td>
            </tr>
        </tbody>
    </table>
</div>
   <table align="right" style="display: block;">
       <tbody style="display: block; text-align: right;">
       <tr>
           <td colspan="2" style="font-size: 15px; text-align: center;"><b>পরিচালক</b></td>
           <td></td>
       </tr>
       <tr>
           <td colspan="2" style="padding-right: 5px; font-size: 15px; text-align: center">হজ অফিস,ঢাকা</td>
       </tr>
       </tbody>
   </table>
</body>
</html>
