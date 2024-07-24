<x-profile :sharedData="$sharedData">
  <div class="list-group">
    @foreach ($followers as $follow)
      <a href="/post/{{$post->id}}" class="list-group-item list-group-item-action">
        <img class="avatar-tiny" src="{{$follow->userFollows->avatar}}" />
        {{$follow->userFollows->username}}
      </a>
        
    @endforeach
  </div>
</x-profile>
