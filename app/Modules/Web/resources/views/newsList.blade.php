<style>
    .btn-outline-button {
        color: #00684D;
        background-color: transparent;
        background-image: none;
        border-color: #00684D;
    }
    .btn-outline-button:hover {
        color: #ffffff !important;
        background-color: #00684D;
        border-color: #097E35;
    }
    .btn-outline-button:focus,
    .btn-outline-button.focus {
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.5);
    }
    .btn-outline-button.disabled,
    .btn-outline-button:disabled {
        color: #fff;
        background-color: #00684D;
        border-color: #097E35;
    }
    .btn-outline-button:not(:disabled):not(.disabled):active,
    .btn-outline-button:not(:disabled):not(.disabled).active,
    .show > .btn-outline-button.dropdown-toggle {
        color: #fff;
        background-color: #28a745;
        border-color: #097E35;
    }
    .btn-outline-button:not(:disabled):not(.disabled):active:focus,
    .btn-outline-button:not(:disabled):not(.disabled).active:focus,
    .show > .btn-outline-button.dropdown-toggle:focus {
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.5);
    }
    .btn:disabled{
        opacity: 1 !important;
    }
</style>
@if(count($newsList) > 0)
    <table id="newsTable" class="display">
        <thead>
        <tr>
            <th>
                <button class="btn btn-outline-button btn-sm yearBtn mb-2" year="recent" {{ (isset($selectedYear) && $selectedYear == 'recent') ? 'disabled' : '' }}>সাম্প্রতিক নোটিশ ও বিজ্ঞপ্তি</button>
                @php
                    $currentYear = date('Y');
                @endphp
                @for ($i = $currentYear; $i >= 2018; $i--)
                    <button class="btn btn-outline-button btn-sm yearBtn mb-2" year="{{$i}}" {{ (isset($selectedYear) && $selectedYear == $i) ? 'disabled' : '' }}>{{\App\Libraries\CommonFunction::convert2Bangla($i)}}</button>
                @endfor

                @if(isset($selectedYear) && $selectedYear == 'recent')
                    <p>সাম্প্রতিক নোটিশ ও বিজ্ঞপ্তিসমূহ তালিকা</p>
                @else
                    <p>{{\App\Libraries\CommonFunction::convert2Bangla($selectedYear)}} সালের বিজ্ঞপ্তিসমূহ তালিকা</p>
                @endif
            </th>
            <th>ডাউনলোড</th>
        </tr>
        </thead>
        <tbody>
            @foreach($newsList as $index=>$news)
                <tr>
                    <td><p class="news-list-title"><span class="news-icon"><img src="{{ asset('assets/images/icon-list-arrow-right.png') }}" alt=""></span> {{ $news['title'] }} <span class="news-list-date">{{ $news['News_Date'] }}</span></p></td>
                    <td><a class="icon-pdf-download" target="_blank" href="{{ $news['url'] }}"></a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p>
        <button class="btn btn-outline-button btn-sm yearBtn mb-2" year="recent" {{ (isset($selectedYear) && $selectedYear == 'recent') ? 'disabled' : '' }}>সাম্প্রতিক নোটিশ ও বিজ্ঞপ্তি</button>
        @php
            $currentYear = date('Y');
        @endphp
        @for ($i = $currentYear; $i >= 2018; $i--)
            <button class="btn btn-outline-button btn-sm yearBtn mb-2" year="{{$i}}" {{ (isset($selectedYear) && $selectedYear == $i) ? 'disabled' : '' }}>{{\App\Libraries\CommonFunction::convert2Bangla($i)}}</button>
        @endfor

        @if(isset($selectedYear) && $selectedYear == 'recent')
            <p>সাম্প্রতিক নোটিশ ও বিজ্ঞপ্তিসমূহ তালিকা</p>
        @else
            <p>{{\App\Libraries\CommonFunction::convert2Bangla($selectedYear)}} সালের বিজ্ঞপ্তিসমূহ তালিকা</p>
        @endif
    </p>
    <p>সংবাদ ও বিজ্ঞপ্তি এর তথ্য পাওয়া যায়নি!!!</p>
@endif
{{-- <div class="text-right py-3">
    <span class="news-seemore-text" id="prev_news">পূর্ববর্তী সংবাদ ও বিজ্ঞপ্তিসমূহ...</span>
</div> --}}
