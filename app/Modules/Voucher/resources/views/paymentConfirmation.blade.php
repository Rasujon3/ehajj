@extends('public_home.front')
@section('header-resources')
    <style>
        .notify-content{
            position: relative;
            width: 100%;
        }
        .notify-box{
            position: relative;
            display: flex;
            flex-direction: column;
            word-wrap: break-word;
            background-color: #fff;
            border: 1px solid #cccccc;
            border-radius: 6px;
            margin-bottom: 20px;
            box-shadow: 0 2px 15px -3px rgba(0,0,0,0.07), 0 10px 20px -2px rgba(0,0,0,0.04);
            padding: 20px;
            text-align: center;
        }
        .notify-box h3,
        .notify-box h4,
        .notify-box p{
            margin: 0;
            padding: 10px 0;
        }
        .notify-box h3{
            font-size: 24px;
            color: #0F6849;
        }
        .notify-box h4{
            font-size: 20px;
        }
        .notify-box p{
            font-size: 16px;
        }
        .notify-box .notify-icon{
            height: 50px;
            width: 50px;
            margin: 0 auto 10px;
        }
        .notify-box .notify-icon svg{
            width: 50px;
        }
        .notify-box .notify-icon svg path{
            fill: #0F6849;
        }
        .massege-card{
            width: 100%;
            max-width: 380px;
            border-radius: 5px;
            position: relative;
            background: #0F6849;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            text-align: center;
            row-gap: 5px;
            margin: 10px auto;
            padding: 5px 15px;
        }
        .faild-notify .massege-card{
            background: #cc2936;
        }
        .faild-notify h3{
            color: #cc2936;
        }
        .faild-notify .notify-icon svg path{
            fill: #cc2936;
        }
        .back_btn{
            /*background-color: #009444;*/
        }
        @media only screen and (max-width: 600px) {
            .site-header, .ehajj-portal-footer, .back_btn{
                display: none;
            }
            .notify-box{
                height: 100vh;
            }
        }
    </style>
@endsection
@section('body')
    <section class="home-intro-section">
        <div class="container">
            <div class="notify-content">
                @if(isset($exception_id) && $exception_id == 1)
                <div class="notify-box">
                    <span class="notify-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-111 111-47-47c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l64 64c9.4 9.4 24.6 9.4 33.9 0L369 209z"/>
                        </svg>
                    </span>
                    <h3>Thank you!</h3>
                    <p>Payment is Successful</p>
                    <div class="massege-card">
                        <p>Your Voucher Tracking No: <strong>@if(!empty($tracking_no)){{$tracking_no}}@endif</strong></p>
                    </div>
                    <br>
                    <!--<span><a class="btn btn-primary back_btn" href="{{URL::to('/dashboard')}}">Return Home</a></span>-->
                </div>
                @elseif(isset($exception_id) && $exception_id == 2)
                    <div class="notify-box">
                    <span class="notify-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-111 111-47-47c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l64 64c9.4 9.4 24.6 9.4 33.9 0L369 209z"/>
                        </svg>
                    </span>
                        <h3>Thank you!</h3>
                        <p>Counter Payment is Successful</p>
                        <div class="massege-card">
                            <p>Your Voucher Tracking No: <strong>@if(!empty($tracking_no)){{$tracking_no}}@endif</strong></p>
                        </div>
                        <br>
                        <!--<span><a class="btn btn-primary back_btn" href="{{URL::to('/dashboard')}}">Return Home</a></span>-->
                    </div>
                @else
                 <div class="notify-box faild-notify">
                    <span class="notify-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zm0-384c13.3 0 24 10.7 24 24l0 112c0 13.3-10.7 24-24 24s-24-10.7-24-24l0-112c0-13.3 10.7-24 24-24zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"/>
                        </svg>
                    </span>
                    <h3>Sorry!</h3>
                    <p>We Couldn't Process Your Payment!</p>
                    <div class="massege-card">
                        <p>Voucher Tracking No: <strong>@if(!empty($tracking_no)){{$tracking_no}}@endif</strong></p>
                    </div>
                    @if(!empty($source) && $source == 1)
                    <br>
                     <!--<span><a class="btn btn-primary back_btn" href="{{URL::to('/dashboard')}}">Return Home</a></span> -->
                    @endif

                 </div>
                @endif
            </div>
        </div>
    </section>
@endsection

@section('footer-script')
    @if(!empty($source) && $source == 1)
    <script>
         let url = "{{URL::to('pilgrim/voucher/index#/voucher-detail-view/'.$group_payment_id)}}";
            setTimeout(function() {
                window.location.href = url;
            }, 2000);
    </script>
    @endif
@endsection
