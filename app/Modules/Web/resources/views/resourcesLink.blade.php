@if(!empty($resourcesLink) && count($resourcesLink) > 0)
<div class="sec-title">
    <h2>অন্যান্য</h2>
</div>
<ul>
    @foreach($resourcesLink as $item)
        <li class="package-list-item">
            <span class="list-text">{{$item->caption}}</span>
            <a href="{{$item->link}}" target="_blank" class="lists-action" aria-label="List Action">
                <span class="icon-img40 icon-pdf-red"></span>
                <span class="icon-img40 icon-download-white"></span>
            </a>
        </li>
    @endforeach
</ul>
@endif
