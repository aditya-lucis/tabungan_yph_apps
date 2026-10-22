<div class="az-header">
      <div class="container d-flex align-items-center justify-content-between">
        <div class="az-header-left d-flex align-items-center">
          <a href="" id="azMenuShow" class="az-header-menu-icon d-lg-none mr-2"><span></span></a>
          <a href="{{ auth()->check() && auth()->user()->role === 'krw' ? route('employee.show', Crypt::encryptString(auth()->user()->id_employee)) : url('/') }}" class="d-flex align-items-center text-decoration-none" title="Yayasan Persada Hati">
            <img src="{{ asset('assets/auth/img/thumb1.png') }}" alt="Yayasan Persada Hati Logo" style="height: 38px; width: auto; max-width: 75px; object-fit: contain; display: block;">
          </a>
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
            <a href="" class="az-img-user"><img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=1e40af&color=fff" alt=""></a>
            <div class="dropdown-menu">
              <div class="az-dropdown-header d-sm-none">
                <a href="" class="az-header-arrow"><i class="icon ion-md-arrow-back"></i></a>
              </div>
              <div class="az-header-profile">
                <div class="az-img-user">
                  <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=1e40af&color=fff" alt="">
                </div><!-- az-img-user -->
                <h6 style="text-align: center;">{{ auth()->user()->name }}</h6>
                <span style="text-align: center;">
                  @switch(auth()->user()->role)
                    @case('adm')
                      Admin
                      @break
                    @case('krw')
                      {{ auth()->user()->karyawan->company->name ?? 'Karyawan' }}
                      @break
                  @endswitch
                </span>
              </div><!-- az-header-profile -->
              <a href="{{route('users.show', Crypt::encryptString(auth()->user()->id) )}}" class="dropdown-item"><i class="typcn typcn-edit"></i> Edit Profile</a>
               <form action="{{route('logout')}}" method="POST">
                @csrf
                <button class="dropdown-item text-danger" type="submit"><i class="typcn typcn-power-outline"></i> Log Out</button>
               </form>
            </div><!-- dropdown-menu -->
          </div>
        </div><!-- az-header-right -->
      </div><!-- container -->
    </div><!-- az-header -->

    <!-- Mobile Bottom Navigation (Visible on screen < 992px) -->
    @auth
    <div class="mobile-bottom-nav d-lg-none">
        @if(auth()->user()->role === 'krw' && auth()->user()->id_employee)
            <a href="{{ route('employee.show', Crypt::encryptString(auth()->user()->id_employee)) }}" class="{{ Request::is('employee*') ? 'active' : '' }}">
                <i class="typcn typcn-home"></i>
                <span>Tabungan</span>
            </a>
            <a href="{{ route('tabungan.inbox') }}" class="{{ Request::is('tabungan/inbox*') ? 'active' : '' }}">
                <i class="typcn typcn-clipboard"></i>
                <span>Pengajuan</span>
            </a>
            <a href="javascript:void(0)" onclick="$('#notifBell').dropdown('toggle');">
                <i class="typcn typcn-bell"></i>
                <span>Notifikasi</span>
            </a>
            <a href="{{ route('users.show', Crypt::encryptString(auth()->user()->id)) }}" class="{{ Request::is('users*') ? 'active' : '' }}">
                <i class="typcn typcn-user"></i>
                <span>Akun</span>
            </a>
        @else
            <a href="{{ route('homeindex') }}" class="{{ Request::is('/') ? 'active' : '' }}">
                <i class="typcn typcn-chart-pie"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('tabungan.inbox') }}" class="{{ Request::is('tabungan/inbox*') ? 'active' : '' }}">
                <i class="typcn typcn-clipboard"></i>
                <span>Approval</span>
            </a>
            <a href="{{ route('pengajuan.index') }}" class="{{ Request::is('pengajuan*') ? 'active' : '' }}">
                <i class="typcn typcn-group"></i>
                <span>Peserta</span>
            </a>
            <a href="{{ route('users.show', Crypt::encryptString(auth()->user()->id)) }}" class="{{ Request::is('users*') ? 'active' : '' }}">
                <i class="typcn typcn-user"></i>
                <span>Akun</span>
            </a>
        @endif
    </div>
    @endauth
