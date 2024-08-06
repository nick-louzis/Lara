<x-profile :sharedData="$sharedData" doctitle="{{$sharedData['username']}}'s Followers">
  <div class="list-group">
    @foreach ($followers as $follow)
      <a href="/profile/{{$follow->userFollows->username}}" class="list-group-item list-group-item-action">
        <img class="avatar-tiny" src="{{$follow->userFollows->avatar}}" />
        {{$follow->userFollows->username}}
      </a>
        
    @endforeach
  </div>
</x-profile>
