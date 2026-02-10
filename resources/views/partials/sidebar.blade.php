{{-- Sidebar --}}
<aside class="sidebar" id="sidebar">
    {{-- Brand --}}
    <div class="sidebar-brand">
        <i class="fas fa-graduation-cap me-2" style="color:#4e73df;font-size:1.1rem;"></i>
        <h5>e-Rapor</h5>
    </div>

    <ul class="sidebar-nav">

        @if(auth()->user()->role && auth()->user()->role->name === 'admin')
            {{-- ═══════════════════════════ ADMIN MENU ═══════════════════════════ --}}

            <li class="sidebar-item">
                <a href="{{ route('admin.dashboard') }}"
                   class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>

            {{-- Dropdown: Master Data --}}
            @php $masterOpen = request()->routeIs('admin.users.*','admin.guru.*','admin.siswa.*','admin.kelas.*','admin.jurusans.*','admin.mata-pelajaran.*','admin.tahun-ajaran.*','admin.kelas-mapel.*'); @endphp
            <li class="sidebar-item">
                <a href="#masterDataMenu" class="sidebar-link sidebar-dropdown-toggle {{ $masterOpen ? 'active' : '' }}"
                   data-bs-toggle="collapse" aria-expanded="{{ $masterOpen ? 'true' : 'false' }}">
                    <i class="fas fa-database"></i> Master Data
                    <i class="fas fa-chevron-down sidebar-arrow"></i>
                </a>
                <ul class="sidebar-submenu collapse {{ $masterOpen ? 'show' : '' }}" id="masterDataMenu">
                    <li><a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><i class="fas fa-users"></i> User</a></li>
                    <li><a href="{{ route('admin.guru.index') }}" class="sidebar-link {{ request()->routeIs('admin.guru.*') ? 'active' : '' }}"><i class="fas fa-chalkboard-teacher"></i> Guru</a></li>
                    <li><a href="{{ route('admin.siswa.index') }}" class="sidebar-link {{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}"><i class="fas fa-user-graduate"></i> Siswa</a></li>
                    <li><a href="{{ route('admin.kelas.index') }}" class="sidebar-link {{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}"><i class="fas fa-door-open"></i> Kelas</a></li>
                    <li><a href="{{ route('admin.jurusans.index') }}" class="sidebar-link {{ request()->routeIs('admin.jurusans.*') ? 'active' : '' }}"><i class="fas fa-layer-group"></i> Jurusan</a></li>
                    <li><a href="{{ route('admin.mata-pelajaran.index') }}" class="sidebar-link {{ request()->routeIs('admin.mata-pelajaran.*') ? 'active' : '' }}"><i class="fas fa-book"></i> Mata Pelajaran</a></li>
                    <li><a href="{{ route('admin.tahun-ajaran.index') }}" class="sidebar-link {{ request()->routeIs('admin.tahun-ajaran.*') ? 'active' : '' }}"><i class="fas fa-calendar-alt"></i> Tahun Ajaran</a></li>
                    <li><a href="{{ route('admin.kelas-mapel.index') }}" class="sidebar-link {{ request()->routeIs('admin.kelas-mapel.*') ? 'active' : '' }}"><i class="fas fa-th-list"></i> Mapel per Kelas</a></li>
                </ul>
            </li>

            {{-- Dropdown: Akademik --}}
            @php $akademikOpen = request()->routeIs('admin.nilai.*','admin.import.*'); @endphp
            <li class="sidebar-item">
                <a href="#akademikMenu" class="sidebar-link sidebar-dropdown-toggle {{ $akademikOpen ? 'active' : '' }}"
                   data-bs-toggle="collapse" aria-expanded="{{ $akademikOpen ? 'true' : 'false' }}">
                    <i class="fas fa-graduation-cap"></i> Akademik
                    <i class="fas fa-chevron-down sidebar-arrow"></i>
                </a>
                <ul class="sidebar-submenu collapse {{ $akademikOpen ? 'show' : '' }}" id="akademikMenu">
                    <li><a href="{{ route('admin.nilai.create') }}" class="sidebar-link {{ request()->routeIs('admin.nilai.*') ? 'active' : '' }}"><i class="fas fa-pen-alt"></i> Input Nilai</a></li>
                    <li><a href="{{ route('admin.import.form') }}" class="sidebar-link {{ request()->routeIs('admin.import.*') ? 'active' : '' }}"><i class="fas fa-file-import"></i> Import Data</a></li>
                </ul>
            </li>

        @elseif(auth()->user()->role && auth()->user()->role->name === 'wali_kelas')
            {{-- ═══════════════════════ WALI KELAS MENU ════════════════════════ --}}

            <li class="sidebar-item">
                <a href="{{ route('wali_kelas.dashboard') }}"
                   class="sidebar-link {{ request()->routeIs('wali_kelas.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>

            {{-- Dropdown: Akademik --}}
            @php $wkAkademikOpen = request()->routeIs('wali_kelas.nilai.*','wali_kelas.rapor.*','wali_kelas.statistik'); @endphp
            <li class="sidebar-item">
                <a href="#wkAkademikMenu" class="sidebar-link sidebar-dropdown-toggle {{ $wkAkademikOpen ? 'active' : '' }}"
                   data-bs-toggle="collapse" aria-expanded="{{ $wkAkademikOpen ? 'true' : 'false' }}">
                    <i class="fas fa-graduation-cap"></i> Akademik
                    <i class="fas fa-chevron-down sidebar-arrow"></i>
                </a>
                <ul class="sidebar-submenu collapse {{ $wkAkademikOpen ? 'show' : '' }}" id="wkAkademikMenu">
                    <li><a href="{{ route('wali_kelas.nilai.index') }}" class="sidebar-link {{ request()->routeIs('wali_kelas.nilai.*') ? 'active' : '' }}"><i class="fas fa-pen-alt"></i> Input Nilai</a></li>
                    <li><a href="{{ route('wali_kelas.rapor.list') }}" class="sidebar-link {{ request()->routeIs('wali_kelas.rapor.*') ? 'active' : '' }}"><i class="fas fa-file-alt"></i> Rekap & Cetak Rapor</a></li>
                    <li><a href="{{ route('wali_kelas.statistik') }}" class="sidebar-link {{ request()->routeIs('wali_kelas.statistik') ? 'active' : '' }}"><i class="fas fa-chart-bar"></i> Statistik Kelas</a></li>
                </ul>
            </li>

        @endif

        {{-- Logout (semua role) --}}
        <li class="sidebar-item mt-3">
            <a href="{{ route('logout') }}" class="sidebar-link"
               onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</aside>
