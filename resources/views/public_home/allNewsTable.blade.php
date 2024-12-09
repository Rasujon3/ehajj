@if(count($responseData) > 0)
    @foreach($responseData as $Data)
        <div class="notice-item">
            @php
                $newDateYear = date('Y',strtotime($Data['News_Date']));
                $custom_date = ($newDateYear == date('Y')) ? date('F d',strtotime($Data['News_Date'])) : $Data['News_Date'];
            @endphp

            <p>{{$Data['post_title']}} <span class="notice-date">{{$custom_date}}</span></p>
            <a href="{{$Data['url']}}" target="_blank" class="notice-action" aria-label="Notice Action">
                <span class="icon-img40 icon-pdf-red"></span>
                <span class="icon-img40 icon-download-green"></span>
            </a>
        </div>
    @endforeach
@else
    <p>No available data.</p>
@endif
