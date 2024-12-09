@if(!empty($importantLink) && count($importantLink) > 0)
<div class="sec-title">
    <h2>গুরুত্বপূর্ণ লিংক</h2>
</div>
<ul>
    @foreach($importantLink as $item)
        <li><a class="hajj-imp-links-item" target="_blank" href="{{$item->link}}">{{$item->caption}}</a></li>
    @endforeach
</ul>
@else
    <ul>
        <li><span class="list-text"> গুরুত্বপূর্ণ লিঙ্ক এর তথ্য পাওয়া যায়নি </span></li>
    </ul>
@endif
