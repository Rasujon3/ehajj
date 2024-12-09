@extends('layouts.admin')

@section('content')
    <div class="dash-content-main">
        <div class="border-card-block">
            <div class="bd-card-head">
                <div class="bd-card-title">
                    <span class="title-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 26 26" fill="none">
                            <path d="M20.9041 15.0465L17.4667 11.3003L16.6981 10.473C16.5849 10.3345 16.5431 10.0856 16.6169 9.92139L17.8699 7.12973C18.2631 6.25391 18.0822 4.92313 17.4778 4.16954C17.2655 3.90984 16.9371 3.76242 16.6019 3.77642C15.6463 3.82975 14.5276 4.58816 14.1345 5.46397L12.8814 8.25563C12.8077 8.41985 12.594 8.5541 12.4153 8.56157L6.21182 8.46254C5.53059 8.44175 4.74181 8.95363 4.46334 9.574L3.92279 10.7782C3.57471 11.5537 3.9866 12.199 4.84655 12.2123L10.3017 12.2933C10.7272 12.2979 10.9331 12.6206 10.757 13.0129L10.3025 14.0255L9.56536 15.6677C9.47117 15.8775 9.21743 16.1253 9.00594 16.2058L5.93524 17.3814C5.61801 17.5021 5.32519 17.8858 5.28631 18.241L5.13992 19.6661C5.06253 20.278 5.55911 20.8078 6.17554 20.7776L9.16585 19.9276C9.62122 19.7922 10.1595 20.0338 10.361 20.4641L11.7132 23.2633C12.1045 23.7349 12.8263 23.7629 13.2503 23.3066L14.2178 22.2501C14.4533 21.9941 14.5495 21.5111 14.4288 21.1939L13.2664 18.1182C13.1769 17.9026 13.1934 17.5483 13.2876 17.3385L14.4792 14.6836C14.6553 14.2913 15.02 14.2358 15.3194 14.5456L19.0056 18.5678C19.5871 19.2015 20.3429 19.0804 20.691 18.305L21.2316 17.1007C21.51 16.4803 21.3683 15.5508 20.9041 15.0465Z" fill="white"/>
                        </svg>
                    </span>
                    <h3>Flight List for {{ $hajjSession['caption'] }}</h3>
                </div>
            </div>
            <div class="bd-card-content">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="flight-info-lists">
                            <ul>
                                <li>
                                    <span class="flight-info-list-title">Ballottee</span>
                                    @if ($flightDetails[0]['is_ballottee'] == 1)
                                        <span class="flight-info-list-desc">Government</span>
                                    @elseif($flightDetails[0]['is_ballottee'] == 0)
                                        <span class="flight-info-list-desc">Private</span>
                                    @else
                                        <span class="flight-info-list-desc">Mixed</span>
                                    @endif
                                    
                                </li>
                                <li>
                                    <span class="flight-info-list-title">Flight Type</span>
                                    <span class="flight-info-list-desc">{{ $flightDetails[0]['type'] }}</span>
                                </li>
                                <li>
                                    <span class="flight-info-list-title">Flight Code</span>
                                    <span class="flight-info-list-desc">{{ $flightDetails[0]['flight_code'] }}</span>
                                </li>
                                <li>
                                    <span class="flight-info-list-title">Departure Time</span>
                                    <span class="flight-info-list-desc">{{ $flightDetails[0]['departure_time_details'] }}</span>
                                </li>
                                <li>
                                    <span class="flight-info-list-title">Departure City</span>
                                    <span class="flight-info-list-desc">{{ $flightDetails[0]['departure_city'] }}</span>
                                </li>
                                <li>
                                    <span class="flight-info-list-title">Route To Makkah</span>
                                    <span class="flight-info-list-desc">{{ $flightDetails[0]['rout_to_makkah'] }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="flight-info-lists">
                            <ul>
                                <li>
                                    <span class="flight-info-list-title">Aircraft</span>
                                    <span class="flight-info-list-desc">{{ $flightDetails[0]['aircraft'] }}</span>
                                </li>
                                <li>
                                    <span class="flight-info-list-title">Airlines</span>
                                    <span class="flight-info-list-desc">{{ $flightDetails[0]['airlines_name'] }}</span>
                                </li>
                                <li>
                                    <span class="flight-info-list-title">Flight Duration</span>
                                    <span class="flight-info-list-desc">{{ $flightDetails[0]['flight_duration'] }} hours</span>

                                </li>
                                <li>
                                    <span class="flight-info-list-title">Arrival time</span>
                                    <span class="flight-info-list-desc">{{ $flightDetails[0]['arrival_time_details'] }}</span>
                                </li>
                                <li>
                                    <span class="flight-info-list-title">Arrival City</span>
                                    <span class="flight-info-list-desc">{{ $flightDetails[0]['arrival_city'] }}</span>
                                </li>
                                <li>
                                    <span class="flight-info-list-title">Flight Capacity</span>
                                    <span class="flight-info-list-desc">{{ $flightDetails[0]['flight_capacity'] }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bd-card-footer">
                <div class="flex-space-btw info-btn-group">
                    <a href="{{ url('/flight') }}" class="btn btn-default"><span>Close</span></a>
                    <a href="{{ url('/flight/edit-flight/'.Encryption::encodeId($flightDetails[0]['id'])) }}" class="btn btn-green">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="14" viewBox="0 0 13 14" fill="none">
                                <g clip-path="url(#clip0_2139_2329)">
                                    <path d="M10.1055 1.00387L3.50151 7.60787C3.24928 7.85874 3.04931 8.15715 2.91319 8.48583C2.77707 8.8145 2.7075 9.16692 2.70851 9.52266V10.2501C2.70851 10.3938 2.76558 10.5316 2.86716 10.6331C2.96874 10.7347 3.10651 10.7918 3.25017 10.7918H3.97763C4.33338 10.7928 4.68579 10.7232 5.01447 10.5871C5.34314 10.451 5.64155 10.251 5.89242 9.99879L12.4964 3.39479C12.813 3.07746 12.9907 2.64754 12.9907 2.19933C12.9907 1.75111 12.813 1.32119 12.4964 1.00387C12.1745 0.696146 11.7463 0.524414 11.301 0.524414C10.8556 0.524414 10.4274 0.696146 10.1055 1.00387ZM11.7305 2.62887L5.12651 9.23287C4.82107 9.53644 4.40826 9.70733 3.97763 9.70846H3.79184V9.52266C3.79297 9.09204 3.96386 8.67922 4.26742 8.37379L10.8714 1.76979C10.9871 1.65926 11.141 1.59759 11.301 1.59759C11.461 1.59759 11.6148 1.65926 11.7305 1.76979C11.8442 1.88382 11.9081 2.03829 11.9081 2.19933C11.9081 2.36037 11.8442 2.51484 11.7305 2.62887Z" fill="white"/>
                                    <path d="M12.4583 5.36363C12.3147 5.36363 12.1769 5.42069 12.0753 5.52228C11.9737 5.62386 11.9167 5.76163 11.9167 5.90529V8.625H9.75C9.31903 8.625 8.9057 8.79621 8.60095 9.10095C8.29621 9.4057 8.125 9.81903 8.125 10.25V12.4167H2.70833C2.27736 12.4167 1.86403 12.2455 1.55929 11.9407C1.25454 11.636 1.08333 11.2226 1.08333 10.7917V3.20833C1.08333 2.77736 1.25454 2.36403 1.55929 2.05929C1.86403 1.75454 2.27736 1.58333 2.70833 1.58333H7.60609C7.74974 1.58333 7.88752 1.52627 7.9891 1.42468C8.09068 1.3231 8.14775 1.18533 8.14775 1.04167C8.14775 0.898008 8.09068 0.760233 7.9891 0.658651C7.88752 0.557068 7.74974 0.5 7.60609 0.5H2.70833C1.9903 0.50086 1.30193 0.786478 0.794203 1.2942C0.286478 1.80193 0.00086009 2.4903 0 3.20833L0 10.7917C0.00086009 11.5097 0.286478 12.1981 0.794203 12.7058C1.30193 13.2135 1.9903 13.4991 2.70833 13.5H8.85246C9.20829 13.501 9.56079 13.4315 9.88955 13.2953C10.2183 13.1592 10.5168 12.9593 10.7678 12.707L12.2065 11.2673C12.4587 11.0164 12.6588 10.718 12.795 10.3893C12.9312 10.0607 13.0009 9.70824 13 9.35246V5.90529C13 5.76163 12.9429 5.62386 12.8414 5.52228C12.7398 5.42069 12.602 5.36363 12.4583 5.36363ZM10.0019 11.9411C9.78414 12.1583 9.50878 12.3087 9.20834 12.3744V10.25C9.20834 10.1063 9.2654 9.96857 9.36699 9.86699C9.46857 9.7654 9.60634 9.70834 9.75 9.70834H11.876C11.809 10.0082 11.6588 10.283 11.4427 10.5013L10.0019 11.9411Z" fill="white"/>
                                </g>
                                <defs>
                                    <clipPath id="clip0_2139_2329">
                                        <rect width="13" height="13" fill="white" transform="translate(0 0.5)"/>
                                    </clipPath>
                                </defs>
                            </svg>
                        </span>
                        <span>Edit</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection