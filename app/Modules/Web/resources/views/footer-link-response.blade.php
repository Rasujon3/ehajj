@if(!empty($important_link_list))
    @foreach($important_link_list as $linkIndex => $link)
        <li><a href="{{$link['link']}}" target="_blank">{{ $link['caption'] }}</a></li>
    @endforeach
@else
    <li><span class="list-text">কোন প্যাকেজ এর তথ্য পাওয়া যাইনি</span></li>
@endif
