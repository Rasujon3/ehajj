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
<div class="row hjp-row-gap" id="newsListSection">
    <input type="hidden" name="csrf_token" value="{{ csrf_token() }}">
    <div class="col-lg-12">
        <button class="btn btn-outline-button btn-sm yearBtn mb-2"
                year="2023" {{ (isset($selectedYear) && $selectedYear == '2023') ? 'disabled' : '' }}>২০২৩
        </button>
        <button class="btn btn-outline-button btn-sm yearBtn mb-2"
                year="2022" {{ (isset($selectedYear) && $selectedYear == '2022') ? 'disabled' : '' }}>২০২২
        </button>
        <button class="btn btn-outline-button btn-sm yearBtn mb-2"
                year="2021" {{ (isset($selectedYear) && $selectedYear == '2021') ? 'disabled' : '' }}>২০২১
        </button>
        <button class="btn btn-outline-button btn-sm yearBtn mb-2"
                year="2020" {{ (isset($selectedYear) && $selectedYear == '2020') ? 'disabled' : '' }}>২০২০
        </button>
        <button class="btn btn-outline-button btn-sm yearBtn mb-2"
                year="2019" {{ (isset($selectedYear) && $selectedYear == '2019') ? 'disabled' : '' }}>২০১৯
        </button>
        <button class="btn btn-outline-button btn-sm yearBtn mb-2"
                year="2018" {{ (isset($selectedYear) && $selectedYear == '2018') ? 'disabled' : '' }}>২০১৮
        </button>
    </div>
    <div class="col-lg-12 table-responsive">
        @if(count($newsList) > 0)
        <table id="newsTable" class="display">
            <thead>
            <tr>
                <th>{{\App\Libraries\CommonFunction::convert2Bangla($selectedYear)}} সালের বিজ্ঞপ্তি সমূহের তালিকা</th>
                <th>ডাউনলোড</th>
            </tr>
            </thead>
            <tbody>
                @foreach($newsList as $index=>$news)
                    <tr>
                        <td><p class="news-list-title"><span class="news-icon"><img
                                        src="{{asset('assets/images/icon-list-arrow-right.png')}}"
                                        alt=""></span> {{ $news['title'] }} <span
                                    class="news-list-date">{{ $news['News_Date'] }}</span></p></td>
                        <td><a class="icon-pdf-download" target="_blank" href="{{ $news['url'] }}"></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @else
            <p>সংবাদ ও বিজ্ঞপ্তি এর তথ্য পাওয়া যায়নি!!!</p>
        @endif
    </div>
</div>
