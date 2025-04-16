<br/>
<ul class="messages">
    @foreach ($reuniones as $reunion)
        <x-reunion-item :reunion="$reunion" />
    @endforeach
</ul>
