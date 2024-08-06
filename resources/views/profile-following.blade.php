<x-profile :sharedData="$sharedData">
  <div class="list-group">
    @foreach ($following as $follow)
      <a href="/profile/{{$follow->userGetsFollowed->username}}" class="list-group-item list-group-item-action">
        <img class="avatar-tiny" src="{{$follow->userGetsFollowed->avatar}}" />
        {{$follow->userGetsFollowed->username}}
      </a>
        
    @endforeach
  </div>
</x-profile>
