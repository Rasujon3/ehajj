@extends('public_home.front')
@section('header-resources')

@endsection
    <style>
        body {
            background-color: #ffffff;
            background-repeat: no-repeat;
            background-position: top left;
            background-attachment: fixed;
        }

        .privacy-policy h1 {
            font-family: Helvetica, sans-serif;
            color: #000000;
            background-color: #ffffff;
        }

        .privacy-policy p {
            font-family: Georgia, serif;
            font-size: 18px;
            font-style: normal;
            font-weight: normal;
            color: #000000;
            background-color: #ffffff;
            text-align: justify;
        }
    </style>

<style>
    .privacy-policy h1 {
        text-align: center;
    }

    .privacy-policy p {
        font-size: medium
    }
    .personal_data { padding-left: 24px; }
    .personal_data li { list-style-type: lower-alpha; }
</style>

@section('body')
    <section class="prp-main-sec">
        <div class="container">
            <div class="white-box p-5 mb-3 privacy-policy" style="padding-bottom: 10px !important;">
                    <h1>Privacy Policy</h1>

                    <b>1. General </b>
                    <br>
                    <p>
                        e-Hajj is completely used for haj related services provided by the Ministry Of Religious Affairs, Government of
                        Bangladesh.
                    </p>
                    <p>
                        We will process your personal data in accordance with the General Data Protection Regulation by google and
                        national
                        laws which relate to the processing of personal data (Data Protection Legislation).
                        This policy explains how we, e-Hajj, a brand of <a href="https://ehaj.hajj.gov.bd/" target="_blank" style="text-decoration: none">https://ehaj.hajj.gov.bd/</a> , use your personal information
                        which you provide to us when using our service, including but not limited to our website and mobile applications
                        (apps).
                    </p>
                    <b> 2. Personal data</b>
                    <br>

                    <p> Personally identifiable data.</p>
                        <ul class="personal_data">
                            <li>May be shared the necessary data with other Government agencies to serve your request in the most efficient and
                                effective way.</li>
                            <li>Will NOT share Personal Data with non-Government entities, except who are authorised to carry out the specific
                                Government services.</li>
                            <li>For your convenience, we may display your available data with us or other Government Agencies to accelerate the
                                submission of service requests. For your convenience in future, please update the data, if required.</li>
                        </ul>
                    <br>
                    <b> 3. How we collect your data </b>
                    <br>
                    <p> Appropriate security technologies have been used to secure the electronic storage and transmission of Personal
                        Data. </p>
                    <b> 4. How we use your personal Information </b>
                    <br>
                    <p> You have a right to request a copy of the information we hold on you at any time. Please email us if you would like to receive a copy of this information of mitul@batworld.com.</p>
                    <b> 5. If you fail to provide personal data </b>
                    <br>
                    <p> Where we need to collect personal data by law or under the terms of a contract we have with you and you fail to provide the data when requested. We may not be able to identify your activities.</p>
                    <b>6. Data security </b>
                    <p>Information you provide to us is stored on our secure servers. We have implemented appropriate physical, technical and organisational measures designed to secure your information against accidental loss and unauthorised access, use, alteration or disclosure.</p>
                    <b> 7. Right to withdraw consent </b>
                    <br>

                    <p> Where you have provided your consent to the collection, processing and transfer of your personal data, you may withdraw that consent at any time. This will not affect the lawfulness of data processing based on consent before it is withdrawn.</p>
                    <b> 8. Changes to our privacy policy </b>
                    <p> Any changes we may make to this privacy policy in the future will be posted on this page. We encourage you to check this privacy policy from time to time for any updates or changes to the privacy policy. If we would like to use your previously collected personal data for different purposes than those we notified you about at the time of collection, we will provide you with notice and, where required by law, seek your consent before using your personal data for a new or unrelated purpose. We may process your personal data without your knowledge or consent where required by applicable law or regulation.</p>
                </div>
            </div>
    </section>
@endsection

