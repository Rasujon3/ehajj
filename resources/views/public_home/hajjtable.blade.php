@php
$count = 0;
@endphp

@foreach($responseData->data as $Data)
<div class="notice-item <?= ($count > 6) ? 'd-none' : ''?>" >
    @php
        $count++;
        $newDateYear = date('Y',strtotime($Data->News_Date));
        $custom_date = ($newDateYear == date('Y')) ? date('F d',strtotime($Data->News_Date)) : $Data->News_Date;
    @endphp

    <p>{{$Data->post_title}} <span class="notice-date">{{$custom_date}}</span></p>
    <a href="{{$Data->url}}" class="notice-action" aria-label="Notice Action">
        <span class="icon-img40 icon-pdf-red"></span>
        <span class="icon-img40 icon-download-green"></span>
    </a>
</div>
@endforeach

<button class="btn btn-success btn-sm" id="showMoreBtn">আরও দেখুন</button>
{{--<a href="https://hajj.gov.bd/bn/news-and-info/" target="_blank" class="btn btn-success btn-sm d-none" id="allNews">সকল নিউজ</a>--}}
<a href="{{ url('all-news') }}" target="_blank" class="btn btn-success btn-sm d-none" id="allNews">সকল নিউজ</a>
