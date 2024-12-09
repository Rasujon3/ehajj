<html>
<head>

    <link rel="stylesheet" type="text/css" href="{{ asset("assets/stylesheets/css3clock.css") }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset("assets/stylesheets/animate.css") }}"/>

    <link rel="stylesheet" href="{{ asset("assets/stylesheets/amchart.css") }}" type="text/css" media="all"/>
    <script src="{{ asset("assets/scripts/amchart/amchart.js") }}" type="text/javascript"></script>
    <script src="{{ asset("assets/scripts/amchart/gauge.js") }}" type="text/javascript"></script>
    {{--<script src="{{ asset("assets/scripts/amchart/light.js") }}" type="text/javascript"></script>--}}
    <script type="text/javascript">
        AmCharts.themes.none = {};
    </script>

    <style>

        body {
            padding: 0px;
            margin: 0px;
            /*min-width: 1310px;*/
            width: 100%;
        }

        /*
        ** For Amchart
        */

        #chartdiv {
            width: 240px;
            height: 162px;
        }

        .amcharts-main-div {
            position: relative;
            right: 35px;
            bottom: 10px;
        }

        @font-face {
            font-family: SolaimanLipi;
            src: {{ asset("assets/fonts/SolaimanLipi.ttf") }};
        }


        .container-box {
            width: 100%;
            height: 720px;
            margin: 0 auto;
            position: relative;
        }

        .top-section {
            width: 100%;
            height: 205px;
            margin-bottom: 3px;
        }

        .bottom-section {
            width: 100%;
            min-height: 429px;
            background: #000;
            padding-bottom: 10px;
        }

        .logo {
            width: 120px;
            height: 120px;
            float: left;
            padding-top: 9px;
            padding-left: 10px;
        }

        .top-title {
            width: 450px;
            float: left;
            padding-left: 10px;
            font-family: SolaimanLipi;
            font-size: 68px;
            color: #333333;
            margin-top: 24px;
        }

        .clear {
            clear: both;
        }

        .calender-area {
            width: 160px;
            float: left;
            height: 160px;
        }

        .timeZone {
            width: 480px;
            float: right;
            height: 160px;
        }

        .automation_logo img{
            height: 65px;
        }

        /*********Calender Starts**************/
        .calendar {
            /*margin-left: 47px !important;*/
            margin: 15px 5px 5px 0;
            padding-top: 0px;
            float: left;
            width: 125px;
            background: #ededef;
            background: -webkit-gradient(linear, left top, left bottom, from(#ededef), to(#ccc));
            background: -moz-linear-gradient(top, #ededef, #ccc);
            font: bold 71px/80px Arial Black, Arial, Helvetica, sans-serif;
            text-align: center;
            color: #848181;
            text-shadow: #fff 0 1px 0;
            -moz-border-radius: 3px;
            -webkit-border-radius: 3px;
            border-radius: 3px;
            position: relative;
            -moz-box-shadow: 0 2px 2px #888;
            -webkit-box-shadow: 0 2px 2px #888;
            box-shadow: 0 2px 2px #888;
        }

        .calendar em {
            display: block;
            font: normal bold 30px/45px Arial, Helvetica, sans-serif;
            color: #fff;
            text-shadow: #00365a 0 -1px 0;
            background: #04599a;
            background: -webkit-gradient(linear, left top, left bottom, from(#778692), to(#8599a7));
            background: -moz-linear-gradient(top, #04599a, #00365a);
            -moz-border-radius-bottomright: 3px;
            -webkit-border-bottom-right-radius: 3px;
            border-bottom-right-radius: 3px;
            -moz-border-radius-bottomleft: 3px;
            -webkit-border-bottom-left-radius: 3px;
            border-bottom-left-radius: 3px;
            border-top: 1px solid #00365a;
            font-size: 20px !important;
        }

        /*********Calender Starts**************/

        .clock-box {
            width: 160px;
            float: right;
            height: 160px;
        }

        .gradient-back {
            background: #1e5799; /* Old browsers */
            background: -moz-linear-gradient(top, #1e5799 0%, #207cca 39%, #2989d8 57%, #2989d8 57%, #2989d8 59%, #7db9e8 100%); /* FF3.6-15 */
            background: -webkit-linear-gradient(top, #1e5799 0%, #207cca 39%, #2989d8 57%, #2989d8 57%, #2989d8 59%, #7db9e8 100%); /* Chrome10-25,Safari5.1-6 */
            background: linear-gradient(to bottom, #1e5799 0%, #207cca 39%, #2989d8 57%, #2989d8 57%, #2989d8 59%, #7db9e8 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#1e5799', endColorstr='#7db9e8', GradientType=0); /* IE6-9 */
        }

        .outer_face {
            width: 130px;
            height: 130px;
            margin-top: 10px;
        }

        .schedule-title {
            width: 100%;
            height: 45px;
            font-family: SolaimanLipi;
        }

        .Time {
            width: 150px;
            float: left;
            color: #33322F;
            margin-left: 30px;
            font-size: 40px;
        }

        .Flight {
            width: 185px;
            float: left;
            color: #33322F;
            margin-left: 10px;
            font-size: 40px;
        }

        .Airlines {
            width: 180px;
            float: left;
            color: #33322F;
            margin-left: 10px;
            margin-right: 10px;
            font-size: 40px;
        }

        .Destination {
            width: 160px;
            float: left;
            color: #33322F;
            margin-left: 10px;
            font-size: 40px;
        }

        .Gate {
            width: 100px;
            float: right;
            color: #33322F;
            margin-right: 20px;
            font-size: 40px;
        }

        .Exp {
            width: 143px;
            float: right;
            color: #33322F;
            margin-right: 20px;
            font-size: 40px;
        }

        .Remarks {
            width: 180px;
            float: right;
            color: #33322F;
            margin-right: 10px;
            font-size: 40px;
        }

        .TimeC {
            width: 150px;
            float: left;
            margin-left: 30px;
            color: #FFFF5F;
            font-size: 40px;
            padding-top: 5px;
        }

        .FlightC {
            width: 185px;
            float: left;
            color: #000;
            margin-left: 10px;
            color: #fff;
            font-size: 40px;
            padding-top: 5px;
        }

        .AirlinesC {
            width: 180px;
            float: left;
            color: #000;
            margin-left: 10px;
            margin-top: 5px;
        }

        .DestinationC {
            width: 160px;
            float: left;
            color: #000;
            margin-left: 20px;
            color: #fff;
            font-size: 40px;
            padding-top: 5px;
        }

        .GateC {
            width: 89px;
            float: right;
            color: #000;
            margin-right: 20px;
            margin-top: 2px;
        }

        .ExpC {
            width: 135px;
            float: right;
            margin-right: 20px;
            color: #FFFF5F;
            font-size: 40px;
            padding-top: 5px;
        }

        .RemarksC {
            width: 180px;
            float: right;
            color: #fff;
            margin-right: 20px;
            background: #6037E7;
            font-size: 40px;
            margin-top: 5px;
            padding-left: 10px;
        }

        .list-box {
            width: 100%;
            margin: 0 auto;
            clear: both;
            margin-bottom: 1px;
        }

        .gatebox {
            background: #F0DB6C;
            min-width: 50px;
            height: 45px;
            font-size: 28px;
            color: #000;
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        .footer-box {
            font-size: 30px;
            color: #000;
            width: 1140px;
            min-height: 50px;
            float: left;
            /*position: relative;*/
        }

        .inner_face::after {
            content: "JEDDAH";
            position: absolute;
            width: 100%;
            font: normal 0.8em Arial;
            color: black;
            text-align: center;
            top: 70%
        }

        .inner_face_dhaka::after {
            content: "DHAKA";
            position: absolute;
            width: 100%;
            font: normal 0.8em Arial;
            color: black;
            text-align: center;
            top: 70%
        }

        .etimemsg1 {
            font-size: 16px;
            color: #fff;
        }

        .etimemsg2 {
            font-size: 16px;
            color: #000;
        }

        .etimemsg3 {
            font-size: 16px;
            color: #000;
        }

        .errorMsg {
            display: none;
            position: absolute;
            top: 35%;
            left: 30%;
            width: 600px;
            height: 120px;
            background: blue;
            z-index: 1;
            font-size: 30px;
            text-align: center;
            padding: 20px;
            color: red;
            opacity: .80;
        }
        .logo img{width: 100px;height: 135px;}
    </style>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <style>
        @media all and (max-width: 1250px) and (min-width: 1150px){
            .logo {
                width: 100px;
                height: 100px;
                margin-top: 15px;
            }
            .logo img{width: 80px;height: 80px;}


            .top-title {
                width: 350px;
                margin-top: 10px;
            }
        }

        @media all and (max-width: 1149px) and (min-width: 1050px){
            .logo {
                width: 90px;
                height: 90px;
                margin-top: 15px;
            }
            .logo img{width: 80px;height: 80px;}


            .top-title {
                width: 300px;
                margin-top: 10px;
            }
        }

        @media all and (max-width: 1049px) and (min-width: 950px){
            .logo {
                width: 80px;
                height: 80px;
                margin-top: 10px;
            }
            .logo img{width: 80px;height: 80px;}


            .top-title {
                width: 280px;
                margin-top: 10px;
            }
        }


        @media all and (max-width: 949px) and (min-width: 900px){
            .logo {
                width: 70px;
                height: 70px;
                margin-top: 10px;
            }
            .logo img{width: 70px;height: 70px;}


            .top-title {
                width: 250px;
                margin-top: 10px;
            }
        }



        @media all and (max-width: 899px) and (min-width: 850px){
            .logo {
                width: 60px;
                height: 60px;
                margin-top: 15px;
            }
            .logo img{width: 60px;height: 60px;}


            .top-title {
                width: 200px;
                margin-top: 15px;
            }


            .clock-box {
                width: 145px;
                height: 145px;
            }

            .calender-area {
                width: 145px;
                height: 145px;
            }
            .timeZone {
                width: 435px;
                height: 145px;
            }

        }


        @media all and (max-width: 849px) and (min-width: 825px){
            .logo {
                width: 55px;
                height: 55px;
                margin-top: 15px;
            }
            .logo img{width: 55px;height: 55px;}


            .top-title {
                width: 190px;
                margin-top: 15px;
            }


            .clock-box {
                width: 140px;
                height: 140px;
            }

            .calender-area {
                width: 140px;
                height: 140px;
            }
            .timeZone {
                width: 425px;
                height: 140px;
            }
        }

        @media all and (max-width: 824px) and (min-width: 560px){
            .logo {
                width: 45px;
                height: 45px;
                margin-top: 15px;
            }
            .logo img{width: 45px;height: 45px;}

            .top-title {
                width: 170px;
                margin-top: 15px;
            }
            .top-title img{width: 170px;}


            .clock-box {
                width: 100px;
                height: 100px;
            }
            .calender-area {
                width: 100px;
                height: 100px;
            }
            .timeZone {
                width: 301px;
                height: 100px;
            }
            .outer_face {
                width: 85px;
                height: 85px;
            }
            .calendar{
                width: 90px;
                font: bold 25px/40px Arial Black, Arial, Helvetica, sans-serif;
                margin: 6px 5px 5px 0;
                width: 85px;
                height: 85px;
            }
        }




        @media all and (max-width: 559px) and (min-width: 510px){
            .logo {
                width: 40px;
                height: 40px;
                margin-top: 15px;
            }
            .logo img{width: 40px;height: 40px;}

            .top-title {
                width: 160px;
                margin-top: 15px;
            }
            .top-title img{width: 160px;}


            .clock-box {
                width: 90px;
                height: 90px;
            }
            .calender-area {
                width: 90px;
                height: 90px;
            }
            .timeZone {
                width: 270px;
                height: 100px;
            }
            .outer_face {
                width: 80px;
                height: 80px;
            }
            .calendar{
                width: 80px;
                font: bold 20px/35px Arial Black, Arial, Helvetica, sans-serif;
                margin: 9px 4px 4px 0;
                height: 80px;
            }
        }




        @media all and (max-width: 509px) and (min-width: 450px){
            .logo {
                width: 35px;
                height: 35px;
                margin-top: 15px;
            }
            .logo img{width: 35px;height: 35px;}

            .top-title {
                width: 140px;
                margin-top: 15px;
                margin-top: 15px;
            }
            .top-title img{width: 140px;}


            .clock-box {
                width: 75px;
                height: 75px;
            }
            .calender-area {
                width: 75px;
                height: 75px;
            }
            .timeZone {
                width: 225px;
                height: 100px;
            }
            .outer_face {
                width: 70px;
                height: 70px;
            }
            .calendar{
                width: 70px;
                font: bold 17px/25px Arial Black, Arial, Helvetica, sans-serif;
                margin: 9px 4px 4px 0;
                height: 70px;
            }
            .inner_face_dhaka::after {
                font: normal 0.6em Arial !important;
            }

            .inner_face::after {
                font: normal 0.6em Arial !important;
            }

        }


        @media all and (max-width: 449px) and (min-width: 410px){
            .logo {
                width: 35px;
                height: 35px;
                margin-top: 10px;
            }
            .logo img{width: 35px;height: 35px;}

            .top-title {
                width: 140px;
                margin-top: 0px;
                margin-top: 10px;
            }
            .top-title img{width: 140px;}


            .clock-box {
                width: 50px;
                height: 50px;
                margin-right:5px;
            }
            .calender-area {
                width: 50px;
                height: 50px;
            }
            .timeZone {
                width: 165px;
                height: 100px;
            }
            .outer_face {
                width: 50px;
                height: 50px;
            }
            .calendar{
                width: 50px;
                font: bold 13px/20px Arial Black, Arial, Helvetica, sans-serif;
                margin: 9px 4px 4px 0;
                height: 50px;
            }
            .calendar em{ font-size: 12px !important;    font: normal bold 20px/25px Arial, Helvetica, sans-serif;}
            .inner_face_dhaka::after {
                font: normal 0.6em Arial !important;
            }
            .inner_face::after {
                font: normal 0.6em Arial !important;
            }
        }



        @media all and (max-width: 409px) and (min-width: 360px){
            .logo {
                width: 40px;
                height: 40px;
                margin-top: 10px;
            }
            .logo img{width: 40px;height: 40px;}

            .top-title {
                width: 140px;
                margin-top: 0px;
                margin-top: 10px;
            }
            .top-title img{width: 140px;}


            .clock-box {
                width: 48px;
                height: 48px;
                margin-right:5px;
            }
            .calender-area {
                width: 48px;
                height: 48px;
            }
            .timeZone {
                width: 160px;
                height: 90px;
            }
            .outer_face {
                width: 48px;
                height: 48px;
            }
            .calendar{
                width: 48px;
                font: bold 11px/16px Arial Black, Arial, Helvetica, sans-serif;
                margin: 9px 4px 4px 0;
                height: 48px;
            }
            .calendar em{ font-size: 12px !important;    font: normal bold 16px/20px Arial, Helvetica, sans-serif;}
            .inner_face_dhaka::after {
                font: normal 0.6em Arial !important;
            }
            .inner_face::after {
                font: normal 0.6em Arial !important;
            }
        }

    </style>


    <style>
        /*
                @media all and (max-width: 1250px) and (min-width: 1000px){
                    .footer-box {
                        width: 850px !important;
                    }
                }


                @media all and (max-width: 999px) and (min-width: 900px){
                    .footer-box {
                        width: 750px !important;
                    }
                }


                @media all and (max-width: 1250px) and (min-width: 1060px) {


                    .top-title {
                        width: 305px;
                        float: left;
                        padding-left: 10px;
                        font-family: SolaimanLipi;
                        font-size: 68px;
                        color: #333333;
                        margin-top: 24px;
                    }
                    .Time {
                        width: 120px;
                        float: left;
                        color: #33322F;
                        margin-left: 30px;
                        font-size: 35px;
                    }

                    .Flight {
                        width: 125px;
                        float: left;
                        color: #33322F;
                        margin-left: 10px;
                        font-size: 35px;
                    }

                    .Airlines {
                        width: 180px;
                        float: left;
                        color: #33322F;
                        margin-left: 10px;
                        margin-right: 10px;
                        font-size: 35px;
                    }

                    .Destination {
                        width: 120px;
                        float: left;
                        color: #33322F;
                        margin-left: 20px;
                        font-size: 35px;
                    }

                    .Gate {
                        width: 80px;
                        float: right;
                        color: #33322F;
                        margin-right: 15px;
                        font-size: 35px;
                    }

                    .Exp {
                        width: 125px;
                        float: right;
                        color: #33322F;
                        margin-right: 20px;
                        font-size: 35px;
                    }

                    .Remarks {
                        width: 160px;
                        float: right;
                        color: #33322F;
                        margin-right: 20px;
                        font-size: 35px;
                        text-align: right;
                    }


                    .TimeC {
                        width: 120px;
                        float: left;
                        margin-left: 30px;
                        color: #FFFF5F;
                        font-size: 35px;
                        padding-top: 5px;
                    }

                    .FlightC {
                        width: 125px;
                        float: left;
                        color: #000;
                        margin-left: 10px;
                        color: #fff;
                        font-size: 35px;
                        padding-top: 5px;
                    }

                    .AirlinesC {
                        width: 180px;
                        float: left;
                        color: #000;
                        margin-left: 10px;
                        margin-top: 5px;
                    }

                    .DestinationC {
                        width: 120px;
                        float: left;
                        color: #000;
                        margin-left: 20px;
                        color: #fff;
                        font-size: 35px;
                        padding-top: 5px;
                    }

                    .GateC {
                        width: 80px;
                        float: right;
                        color: #000;
                        margin-right: 15px;
                        margin-top: 2px;
                    }

                    .ExpC {
                        width: 125px;
                        float: right;
                        margin-right: 5px;
                        color: #FFFF5F;
                        font-size: 35px;
                        padding-top: 5px;
                    }

                    .RemarksC {
                        width: 160px;
                        float: right;
                        color: #fff;
                        margin-right: 20px;
                        background: #6037E7;
                        font-size: 35px;
                        margin-top: 5px;
                        padding-left: 10px;
                    }


                    .gatebox {
                        background: #F0DB6C;
                        min-width: 50px;
                        height: 45px;
                        font-size: 25px;
                        color: #000;
                        display: table-cell;
                        vertical-align: middle;
                        text-align: center;
                    }




                }


                @media all and (max-width: 1059px) and (min-width: 760px) {


                    .top-title {
                        width: 250px;
                        float: left;
                        padding-left: 10px;
                        font-family: SolaimanLipi;
                        font-size: 45px;
                        color: #333333;
                        margin-top: 24px;
                    }
                    .top-title img{width: 240px;}


                    .Time {
                        width: 110px;
                        float: left;
                        color: #33322F;
                        margin-left: 30px;
                        font-size: 25px;
                    }

                    .Flight {
                        width: 115px;
                        float: left;
                        color: #33322F;
                        margin-left: 10px;
                        font-size: 25px;
                    }

                    .Airlines {
                        width: 175px;
                        float: left;
                        color: #33322F;
                        margin-left: 10px;
                        margin-right: 10px;
                        font-size: 25px;
                    }

                    .Destination {
                        width: 110px;
                        float: left;
                        color: #33322F;
                        margin-left: 10px;
                        font-size: 25px;
                    }

                    .Gate {
                        width: 75px;
                        float: right;
                        color: #33322F;
                        margin-right: 20px;
                        font-size: 25px;
                    }

                    .Exp {
                        width: 115px;
                        float: right;
                        color: #33322F;
                        margin-right: 20px;
                        font-size: 25px;
                    }

                    .Remarks {
                        width: 125px;
                        float: right;
                        color: #33322F;
                        margin-right: 20px;
                        font-size: 25px;
                        text-align: right;
                    }


                    .TimeC {
                        width: 110px;
                        float: left;
                        margin-left: 30px;
                        color: #FFFF5F;
                        font-size: 25px;
                        padding-top: 5px;
                    }

                    .FlightC {
                        width: 115px;
                        float: left;
                        color: #000;
                        margin-left: 10px;
                        color: #fff;
                        font-size: 25px;
                        padding-top: 5px;
                    }

                    .AirlinesC {
                        width: 170px;
                        float: left;
                        color: #000;
                        font-size: 25px;
                        margin-left: 10px;
                        margin-top: 5px;
                    }

                    .DestinationC {
                        width: 110px;
                        float: left;
                        color: #fff;
                        margin-left: 20px;
                        color: #fff;
                        font-size: 25px;
                        padding-top: 5px;
                    }

                    .GateC {
                        width: 75px;
                        float: right;
                        font-size: 25px;
                        color: #000;
                        margin-right: 20px;
                        margin-top: 2px;
                    }

                    .ExpC {
                        width: 95px;
                        float: right;
                        margin-right: 5px;
                        color: #FFFF5F;
                        font-size: 25px;
                        padding-top: 5px;
                    }

                    .RemarksC {
                        width: 135px;
                        float: right;
                        color: #fff;
                        margin-right: 20px;
                        background: #6037E7;
                        font-size: 25px;
                        margin-top: 5px;
                        padding-left: 10px;
                        text-align: center;
                    }


                    .gatebox {
                        background: #F0DB6C;
                        min-width: 50px;
                        height: 45px;
                        font-size: 25px;
                        color: #000;
                        display: table-cell;
                        vertical-align: middle;
                        text-align: center;
                    }

                }

                */


    </style>


    <style>
        @media all and (max-width: 1350px) and (min-width: 1150px){
            .Time,.TimeC {
                width: 120px;
            }
            .Flight,.FlightC {
                width: 120px;
            }
            .Airlines,.AirlinesC {
                width: 120px;
            }.Exp,.ExpC {
                 width: 120px;
             }.Gate .GateC {
                  width: 120px;
              }.Destination,.DestinationC{
                   width: 120px;
                   margin-left: 100px;
               }
            .Remarks.RemarksC{
                width: 120px;
            }
            .schedule-title img{
                width: 77px;
            }
            .Gate img{
                width: 60px;
            }
            .Airlines  img{
                width: 140px;
            }

        }

        @media all and (max-width: 1149px) and (min-width: 960px){
            .Time,.TimeC {
                width: 80px;
                font-size: 25px;
            }
            .Flight,.FlightC {
                width: 100px;
                font-size: 25px;
            }
            .Airlines,.AirlinesC {
                width: 80px !important;
            }.Exp,.ExpC {
                 width: 80px;
                 font-size: 22px;
             }.Gate .GateC {
                  width: 80px;
                  font-size: 22px;

              }.Destination,.DestinationC{
                   width: 80px;
                   margin-left: 100px;
                   font-size: 22px;
               }
            .Remarks.RemarksC{
                width: 80px;
                font-size: 22px;
            }
            .schedule-title img{
                width: 67px;
            }
            .Gate img{
                width: 50px;
            }
            .Airlines  img{
                width: 110px;
            }

        }

        @media all and (min-width: 600px) and (max-width: 959px){
            .GateC {
                display: none;

            }
            .schedule-title .Gate{
                display: none;
            }
            .Time,.TimeC {
                width: 60px;
                font-size: 22px;
            }
            .Flight,.FlightC {
                width: 60px;
                font-size: 22px;
            }
            .Airlines,.AirlinesC {
                width: 170px;
                font-size: 22px;
            }
            .AirlinesC img{
                width: 60px;
            }
            .Exp,.ExpC {
                width: 60px;
                font-size: 22px;
            }.Destination,.DestinationC{
                 width: 60px;
                 /*margin-left: 100px;*/
                 font-size: 22px;
             }
            .Remarks.RemarksC{
                width: 40px;
                font-size: 22px;
            }
            .schedule-title img{
                width: 57px;
            }
            .Gate img{
                width: 45px;
            }
            .Airlines  img{
                width: 70px;
            }

            .schedule-title>div, .list-box>div{
                width:calc(100% / 7) !important;
            }
            .AirlinesC{
                margin-left: 0px;
            }
            .list-box>div img{
                max-width: 100%;
                max-height: 30px;
            }
            .RemarksC{
                font-size: 22px;
            }
            .Remarks{
                margin-right: 5px;
            }
            .schedule-title img{
                max-width: 100%;
            }
            .top-section{
                height: auto;
            }
            .RemarksC{
                float:right;
            }
            .Exp{
                margin-right: 0;
            }
            .ExpC{
                float: right;
                margin-right: 0;
            }
            .RemarksC{
                margin-right: 10px;
                font-size: 18px;
            }
            .Time{
                margin-left: 15px;
            }
        }

        @media all and (max-width: 599px) and (min-width: 450px){
            .GateC {
                display: none;

            }
            .schedule-title .Gate{
                display: none;
            }
            .Time,.TimeC {
                width: 50px;
                font-size: 18px;
            }
            .Flight,.FlightC {
                width: 50px;
                font-size: 18px;
            }
            .Airlines,.AirlinesC {
                width: 100px;
                font-size: 18px;
            }
            .AirlinesC img{
                width: 50px;
            }
            .Exp,.ExpC {
                width: 50px;
                font-size: 18px;
            }.Destination,.DestinationC{
                 width: 40px;
                 /*margin-left: 100px;*/
                 font-size: 18px;
             }
            .Remarks.RemarksC{
                width: 40px;
                font-size: 18px;
            }
            .schedule-title img{
                width: 57px;
            }
            .Gate img{
                width: 45px;
            }
            .Airlines  img{
                width: 70px;
            }

            .schedule-title>div, .list-box>div{
                width:calc(100% / 7) !important;
            }
            .AirlinesC{
                margin-left: 0px;
            }
            .list-box>div img{
                max-width: 88%;
                max-height: 28px;
            }
            .RemarksC{
                font-size: 18px;
            }
            .Remarks{
                margin-right: 5px;
            }
            .schedule-title img{
                max-width: 100%;
            }
            .top-section{
                height: auto;
            }
            .RemarksC{
                float:right;
            }
            .DestinationC{
                margin-left: 0px;
                margin-right: 10px;
            }
            .Exp{
                margin-right: 0;
            }
            .ExpC{
                float: left;
                margin-right: 0;
            }
            .RemarksC{
                margin-right: 10px;
                font-size: 18px;
            }
            .Time{
                margin-left: 15px;
            }
            .TimeC{
                margin-left: 13px;
            }
        }
        @media all and (max-width: 449px) and (min-width: 360px){
            .GateC {
                display: none;

            }
            .schedule-title .Gate{
                display: none;
            }

            .Time,.TimeC {
                width: 50px;
                font-size: 18px;
            }
            .Flight,.FlightC {
                width: 50px;
                font-size: 14px;
            }
            .Airlines,.AirlinesC {
                width: 100px;
                font-size: 18px;
            }
            .AirlinesC img{
                width: 50px;
            }
            .Exp,.ExpC {
                width: 50px;
                font-size: 18px;
            }.Destination,.DestinationC{
                 width: 40px;
                 /*margin-left: 100px;*/
                 font-size: 18px;
             }
            .Remarks.RemarksC{
                width: 40px;
                font-size: 18px;
            }
            .schedule-title img{
                width: 47px;
            }

            .Airlines  img{
                width: 50px;
            }

            .schedule-title>div, .list-box>div{
                width:calc(100% / 7) !important;
            }
            .AirlinesC{
                margin-left: 0px;
            }
            .list-box>div img{
                max-width: 78%;
                max-height: 24px;
            }
            .RemarksC{
                font-size: 18px;
            }
            .Remarks{
                margin-right: 5px;
            }
            .schedule-title img{
                max-width: 100%;
            }
            .top-section{
                height: auto;
            }
            .RemarksC{
                float:right;
            }
            .DestinationC{
                margin-left: 0px;
                margin-right: 10px;
            }
            .Exp{
                margin-right: 0;
            }
            .ExpC{
                float: left;
                margin-right: 0;
            }
            .RemarksC{
                margin-right: 10px;
                font-size: 12px;
            }
            .Time{
                margin-left: 2px;

            }
            .TimeC{
                margin-left: 10px;
            }

        }

        @media all and (max-width: 1350px) and (min-width: 1100px){
            .footer-box{
                width: 900px;
            }
            .scrolling_text{
                font-size: 28px !important;
            }
            .automation_logo img{
                height: 60px;
            }
        }

        @media all and (max-width: 1099px) and (min-width: 800px){
            .footer-box{
                width: 625px;
            }
            .scrolling_text{
                font-size: 28px !important;
            }
            .automation_logo img{
                height: 50px;
            }
        }

        @media all and (max-width: 799px) and (min-width: 500px){
            .footer-box{
                width: 350px;
            }
            .scrolling_text{
                font-size: 24px !important;
            }
            .automation_logo img{
                height: 50px;
            }
        }

        /*@media all and (max-width: 499px) and (min-width: 450px){*/
        /*.scrolling_text{*/
        /*font-size: 18px !important;*/
        /*}*/
        /*.automation_logo img{*/
        /*height: 40px;*/
        /*}*/
        /*}*/

        @media all and (max-width: 449px) and (min-width: 300px){
            .footer-box{
                width: 200px;
            }
            .scrolling_text{
                font-size: 16px !important;
            }
            .automation_logo img{
                height: 40px;
                width: 70px;
            }
        }
    </style>

</head>
<body>
<div class="container-box">
    <div class="errorMsg">
        Network Error! Please wait...
        <div style="clear:both;"></div>
        <span class="etimemsg1"></span>
        <div style="clear:both;"></div>
        <span class="etimemsg2"></span>
        <div style="clear:both;"></div>
        <span class="etimemsg3"></span>
    </div>
    <div class="top-section">
        <div class="logo">
            <img src="{{ asset("assets/images/plane.svg") }}"  alt="Airplane" title="Airplane"/>
        </div>
        <div class="top-title">

            @if(isset($list_title)) {!! $list_title !!} @endif


            {{--<div style="font-size: 18px;margin-top:-10px;font-style: italic;">--}}
            {{--<span style="font-size:12px;">প্রস্থান স্থলের স্থানীয় সময় অনুযায়ী (তথ্য সূত্র: www.hajj.gov.bd)</span>--}}
            {{--</div>--}}
        </div>

        {{--<div class="armchart">--}}
        {{--<div class="" style="float: left;">--}}
        {{--<div id="chartdiv"></div>--}}
        {{--<div class="clear"></div>--}}
        {{--</div>--}}

        {{--</div>--}}

        <div class="timeZone">

            <div class="clock-box" style="float: left;">
                <div id="liveclock_dhaka" class="outer_face">
                    <div class="marker oneseven"></div>
                    <div class="marker twoeight"></div>
                    <div class="marker fourten"></div>
                    <div class="marker fiveeleven"></div>
                    <div class="inner_face inner_face_dhaka">
                        <div class="hand hour"></div>
                        <div class="hand minute"></div>
                        <div class="hand second"></div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>


            <div class="calender-area">
                <p class="calendar">
                    <span id="curdaytxt"><?php echo date('d'); ?></span>
                    <em id="curmonthtxt"><?php echo date('F'); ?></em>
                </p>
                <div class="clear"></div>
            </div>

            <div class="clock-box">
                <div id="liveclock_jeddah" class="outer_face">
                    <div class="marker oneseven"></div>
                    <div class="marker twoeight"></div>
                    <div class="marker fourten"></div>
                    <div class="marker fiveeleven"></div>
                    <div class="inner_face">
                        <div class="hand hour"></div>
                        <div class="hand minute"></div>
                        <div class="hand second"></div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <div class="clear"></div>


        </div>
        <div class="clear"></div>
        <div class="schedule-title">
            <div class="Time">
                <img class="img-responsive" src="{{asset('assets/images/flight_time.png')}}">
            </div>
            <div class="Flight">
                <img class="img-responsive" src="{{asset('assets/images/flight_flight.png')}}">
            </div>
            <div class="Airlines subairlines">
                <img class="img-responsive" src="{{asset('assets/images/flight_airlines.png')}}">
            </div>
            <div class="Destination">
                <img class="img-responsive" src="{{asset('assets/images/flight_destination.png')}}">
            </div>
            <div class="Remarks">
                <img class="img-responsive" src="{{asset('assets/images/flight_status.png')}}">
            </div>
            <div class="Exp">
                <img class="img-responsive" src="{{asset('assets/images/flight_etd.png')}}">
            </div>
            <div class="Gate subgate">
                <img class="img-responsive" src="{{asset('assets/images/flight_gate.png')}}">
            </div>
        </div>
    </div>
    <div class="bottom-section">

        {{--@include('flight::ajax-list')--}}

    </div>
    <div class="clear"></div>
    <div class="footer-box">
        <p class="scrolling_text" style="text-align:center;padding:10px 0px 0px 30px;margin:0px;font-size: 34px;">
            <marquee id="marq_id">
                {{$notice}}
            </marquee>
        </p>

    </div>
    @if(!isset($_GET['no_logo']))
        <div class="automation_logo" style="">
            <img src="{{url('assets/images/business_automation.png')}}"
                 style="margin-top: 2px;margin-left:23px;
    right: 0;
    top: 0;">
        </div>
    @endif

</div>
<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
<script src="{{ asset("assets/scripts/jquery.min.js") }}" type="text/javascript"></script>

<script type="text/javascript">

    var chart = AmCharts.makeChart("chartdiv",
        {
            "type": "gauge",
            "marginBottom": 0,
            "marginTop": 0,
            "startDuration": 0,
            "fontSize": 13,
            "theme": "default",
            "hideCredits": true,
            "arrows": [
                {
                    "id": "GaugeArrow-1",
                    "value": 10
                }
            ],
            "axes": [
                {
                    "bottomText": "আগত হজযাত্রী",
                    "bottomTextYOffset": 10,
                    "endValue": 100,
                    "id": "GaugeAxis-1",
                    "valueInterval": 25,
                    "radius": "75%",
                    "topTextColor": "#ffffff",
                    "unit" : "%",
                    "bands": [
                        {
                            "alpha": 0.7,
                            "color": "#00CC00",
                            "endValue": 100,
                            "id": "GaugeBand-1",
                            "startValue": 0
                        }
                    ]
                }
            ],
            "allLabels": [],
            "balloon": {},
            "titles": []
        });

    function setGaugeMeterValue(value){
        chart.arrows[0].setValue(value);
        chart.axes[0].setTopText(value + " %");
    }
    //setTimeout(function() {setGaugeMeterValue('{{$percentage}}');}, 2000);
    // For Chart
    function getPercentage() {
        $.ajax({
            url: '<?php echo env('APP_URL').'/f/l/get-percentage'?>',
            type: 'GET',
            success: function (response) {
                setGaugeMeterValue(response.percentage);
            }
        });
    }
    //setInterval(getPercentage, 300000);

    try {

        // LIVE clock jeddah
        var $hands = $('#liveclock_jeddah div.hand');
        window.requestAnimationFrame = window.requestAnimationFrame
            || window.mozRequestAnimationFrame
            || window.webkitRequestAnimationFrame
            || window.msRequestAnimationFrame
            || function (f) {
                setTimeout(f, 60)
            }

        function updateclock() {

            var d = new Date();
            var local = d.getTime();

            var offset = (d.getTimezoneOffset() * (60 * 1000));
            var utc = new Date(local + offset);
            var curdate = new Date(utc.getTime() + (3 * 60 * 60 * 1000));

            var hour_as_degree = (curdate.getHours() + curdate.getMinutes() / 60) / 12 * 360
            var minute_as_degree = curdate.getMinutes() / 60 * 360
            var second_as_degree = (curdate.getSeconds() + curdate.getMilliseconds() / 1000) / 60 * 360;
            $hands.filter('.hour').css({transform: 'rotate(' + hour_as_degree + 'deg)'});
            $hands.filter('.minute').css({transform: 'rotate(' + minute_as_degree + 'deg)'});
            $hands.filter('.second').css({transform: 'rotate(' + second_as_degree + 'deg)'});
            requestAnimationFrame(updateclock);
        }

        requestAnimationFrame(updateclock);

        //=================================
        // LIVE clock dhaka
        var $hands_dhaka = $('#liveclock_dhaka div.hand');
        window.requestAnimationFrame = window.requestAnimationFrame
            || window.mozRequestAnimationFrame
            || window.webkitRequestAnimationFrame
            || window.msRequestAnimationFrame
            || function (f) {
                setTimeout(f, 60)
            };

        function updateclock_dhaka() {

            var d = new Date();
            var localTime = d.getTime();

            var offset = (d.getTimezoneOffset() * (60 * 1000));
            var utc = new Date(localTime + offset);
            var local = new Date(utc.getTime() + (6 * 60 * 60 * 1000));

            var hour_as_degree = (local.getHours() + local.getMinutes() / 60) / 12 * 360;
            var minute_as_degree = local.getMinutes() / 60 * 360;
            var second_as_degree = (local.getSeconds() + local.getMilliseconds() / 1000) / 60 * 360;
            $hands_dhaka.filter('.hour').css({transform: 'rotate(' + hour_as_degree + 'deg)'});
            $hands_dhaka.filter('.minute').css({transform: 'rotate(' + minute_as_degree + 'deg)'});
            $hands_dhaka.filter('.second').css({transform: 'rotate(' + second_as_degree + 'deg)'});
            requestAnimationFrame(updateclock_dhaka);
        }

        requestAnimationFrame(updateclock_dhaka);
        //===================================

        var incrementer = 0;
        autoloopajaxload(incrementer);

        var errorStart = 0;
        var sttime = '';
        //variables
        var lastTryTime = new Date();
        var InfoUpdatedOn = new Date();
        var errorCount = 0;
        var version = null;
        var nextTry = 12000;

        function showNetworkError(flag, msg) {
            if (flag) {
                $('.etimemsg1').html('Updated On ' + InfoUpdatedOn);
                $('.etimemsg2').html('Last Try: ' + lastTryTime);
                $('.etimemsg3').html('Server Response: ' + msg.statusText + " (" + msg.status + ")");
                $('.errorMsg').css('display', 'block');
            } else {
                $('.errorMsg').css('display', 'none');
            }
        }

        var cur_total_data = 0;

        function autoloopajaxload() {
            lastTryTime = new Date();
            $.ajax({
                url: '<?php echo env('APP_URL').'/ajax-flight-list'?>',
                type: 'post',
                data: {
                    _token: $('input[name="_token"]').val(),
                    incrementer: incrementer
                },
                success: function (response) {
                    $('.bottom-section').html(response);
                    cur_total_data = document.getElementById('cur_total_data').value;
                    if ($('#curdaytxt').innerText != $('#cur_day').val()) {
                        $('#curdaytxt').html($('#cur_day').val());
                        $('#curmonthtxt').html($('#cur_month').val());
                        $('#marq_id').html($('#cur_notice').val());
                        if (version == null) {
                            version = $('#version').val();
                        } else if (version != $('#version').val()) {
                            location.reload();
                        }
                    }
                    InfoUpdatedOn = new Date();
                    showNetworkError(false, '');
                    errorCount = 0;
                    if (cur_total_data <= 8) {
                        incrementer = 0;
                    } else {
                        incrementer++;
                    }
                    // myVar = setTimeout(autoloopajaxload, nextTry);
                },
                error: function (request, status, error) {
                    incrementer = 10;
                    errorCount++;
                    if (errorCount > 200) {
                        location.reload();
                    } else {
                        showNetworkError(true, request);
                        // myVar = setTimeout(autoloopajaxload, 12000);
                    }
                }
            });
        }
    } catch (err) {
        window.location.reload();
    }
</script>

</body>
</html>
