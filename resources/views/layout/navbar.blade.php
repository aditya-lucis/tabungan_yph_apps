<div class="az-header">
      <div class="container">
        <div class="az-header-left">
          <a href="" id="azMenuShow" class="az-header-menu-icon d-lg-none"><span></span></a>
        </div><!-- az-header-left -->
        <div class="az-header-menu">
        <ul class="nav">
          @php
            $currentRole = auth()->check() ? auth()->user()->role : null;
          @endphp

          @foreach ($navbarMenu as $key => $item )
              @php
                  // Jika route dalam bentuk array, gunakan route() dengan parameter
                  $routeUrl = is_array($item['route']) 
                              ? route($item['route']['name'], $item['route']['params']) 
                              : route($item['route']);
              @endphp

              @if (!isset($item['role']) || in_array($currentRole, $item['role']))
                @if (!isset($item['subitems']))
                    <li class="nav-item">
                        <a href="{{ $routeUrl }}" 
                          class="nav-link {{ Request::is($item['pattern']) ? 'active' : '' }}">
                            <i class="{{ $item['icon'] }}"></i> {{ $item['name'] }}
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="#" class="nav-link with-sub {{ Request::is($item['pattern']) ? 'active' : '' }}">
                            <i class="{{ $item['icon'] }}"></i> {{ $item['name'] }}
                        </a>
                        <nav class="az-menu-sub">
                            @foreach ($item['subitems'] as $subitem)
                                @if (!isset($subitem['role']) || in_array($currentRole, $subitem['role']))
                                <a href="{{ route($subitem['route']) }}" class="nav-link {{ Request::is($subitem['pattern']) ? 'active' : '' }}">
                                    {{ $subitem['name'] }}
                                </a>
                                @endif
                            @endforeach
                        </nav>
                    </li>
                @endif
              @endif
          @endforeach
      </ul>
        </div><!-- az-header-menu -->
        <div class="az-header-right">
          <div class="dropdown az-header-notification">
            <a href="#" class="az-bell" id="notifBell"><i class="typcn typcn-bell"></i></a>
            <div class="dropdown-menu">
              <div class="az-dropdown-header mg-b-20 d-sm-none">
                <a href="#" class="az-header-arrow"><i class="icon ion-md-arrow-back"></i></a>
              </div>
              <h6 class="az-notification-title">Notifications</h6>
              <p class="az-notification-text" id="notifText">Loading notifications...</p>
              <div class="az-notification-list" id="notif-list" style="max-height: 300px; overflow-y: auto;"></div>
              <div class="dropdown-footer d-none" id="notifToggleWrapper">
                <a href="javascript:void(0)" id="toggleNotif">View All Notifications</a>
              </div>
            </div>
          </div><!-- az-header-notification -->
          <div class="dropdown az-profile-menu">
            <a href="" class="az-img-user"><img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}" alt=""></a>
            <div class="dropdown-menu">
              <div class="az-dropdown-header d-sm-none">
                <a href="" class="az-header-arrow"><i class="icon ion-md-arrow-back"></i></a>
              </div>
              <div class="az-header-profile">
                <div class="az-img-user">
                  <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}" alt="">
                </div><!-- az-img-user -->
                <h6 style="text-align: center;">{{ auth()->user()->name }}</h6>
                <span style="text-align: center;">
                  @switch(auth()->user()->role)
                    @case('adm')
                      Admin
                      @break
                    @case('krw')
                      {{ auth()->user()->karyawan->company->name }}
                      @break
                  @endswitch
                </span>
              </div><!-- az-header-profile -->
              <a href="{{route('users.show', auth()->user()->id)}}" class="dropdown-item"><i class="typcn typcn-edit"></i> Edit Profile</a>
              <!-- <a href="{{route('logout')}}" class="dropdown-item"><i class="typcn typcn-power-outline"></i> Log Out</a> -->
               <form action="{{route('logout')}}" method="POST">
                @csrf
                <button class="dropdown-item" type="submit"><i class="typcn typcn-power-outline"></i> Log Out</button>
               </form>
            </div><!-- dropdown-menu -->
          </div>
        </div><!-- az-header-right -->
      </div><!-- container -->
    </div><!-- az-header -->
