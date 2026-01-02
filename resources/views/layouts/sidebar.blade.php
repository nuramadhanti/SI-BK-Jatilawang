<div class="sidebar-menu shadow-sm" style="min-height: 100vh;">
    <ul class="menu list-unstyled mt-3">

        {{-- =========================
            DASHBOARD
        ========================== --}}
        <li class="sidebar-item {{ request()->is('home') ? 'active' : '' }}">
            <a href="/home" class="sidebar-link d-flex align-items-center gap-2">
                <i class="bi bi-grid-fill"></i>
                <span>Dashboard</span>
            </a>
        </li>


        {{-- =========================
            GURU BK
        ========================== --}}
        @if (auth()->user()->role === 'guru' && auth()->user()->guru && auth()->user()->guru->role_guru === 'bk')

            {{-- DATA --}}
            <li
                class="sidebar-item has-sub 
                {{ request()->is('tahun-akademik', 'kelas', 'guru', 'siswa', 'kategori-konseling') ? 'active' : '' }}">
                <a href="#" class="sidebar-link d-flex align-items-center gap-2">
                    <i class="bi bi-folder-fill"></i>
                    <span>Data</span>
                </a>

                <ul class="submenu list-unstyled ps-3">
                    <li class="submenu-item {{ request()->is('tahun-akademik') ? 'active' : '' }}">
                        <a href="/tahun-akademik">Tahun Akademik</a>
                    </li>
                    <li class="submenu-item {{ request()->is('kelas') ? 'active' : '' }}">
                        <a href="/kelas">Kelas</a>
                    </li>
                    <li class="submenu-item {{ request()->is('guru') ? 'active' : '' }}">
                        <a href="/guru">Guru</a>
                    </li>
                    <li class="submenu-item {{ request()->is('siswa') ? 'active' : '' }}">
                        <a href="/siswa">Siswa</a>
                    </li>
                    {{-- <li class="submenu-item {{ request()->is('kategori-konseling') ? 'active' : '' }}">
                        <a href="/kategori-konseling">Kategori Konseling</a>
                    </li> --}}
                </ul>
            </li>

            {{-- KRITERIA --}}
            <li class="sidebar-item {{ request()->is('kriteria') ? 'active' : '' }}">
                <a href="/kriteria" class="sidebar-link d-flex align-items-center gap-2">
                    <i class="bi bi-filter-circle-fill"></i>
                    <span>Kriteria</span>
                </a>
            </li>

            {{-- PERMOHONAN KONSELING --}}
            <li class="sidebar-item {{ request()->is('permohonan-konseling') ? 'active' : '' }}">
                <a href="/permohonan-konseling" class="sidebar-link d-flex align-items-center gap-2">
                    <i class="bi bi-envelope-fill"></i>
                    <span>Permohonan Konseling</span>

                    @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
                    @if ($unread > 0)
                        <span class="badge bg-danger ms-2">{{ $unread }}</span>
                    @endif
                </a>
            </li>

            {{-- JADWAL --}}
            <li class="sidebar-item {{ request()->is('jadwal-konseling') ? 'active' : '' }}">
                <a href="/jadwal-konseling" class="sidebar-link d-flex align-items-center gap-2">
                    <i class="bi bi-calendar-fill"></i>
                    <span>Jadwal Konseling</span>
                </a>
            </li>

            {{-- RIWAYAT (sebelum laporan) --}}
            <li class="sidebar-item {{ request()->is('riwayat-konseling') ? 'active' : '' }}">
                <a href="/riwayat-konseling" class="sidebar-link d-flex align-items-center gap-2">
                    <i class="bi bi-clock-fill"></i>
                    <span>Riwayat Konseling</span>
                </a>
            </li>

            {{-- LAPORAN --}}
            <li class="sidebar-item {{ request()->is('laporan') ? 'active' : '' }}">
                <a href="/laporan" class="sidebar-link d-flex align-items-center gap-2">
                    <i class="bi bi-bar-chart-fill"></i>
                    <span>Laporan</span>
                </a>
            </li>

            {{-- MANAJEMEN USER --}}
            <li class="sidebar-item {{ request()->is('manajemen-user*') ? 'active' : '' }}">
                <a href="/manajemen-user" class="sidebar-link d-flex align-items-center gap-2">
                    <i class="bi bi-people-fill"></i>
                    <span>Manajemen User</span>
                </a>
            </li>

        @endif


        {{-- =========================
            GURU WALI KELAS
        ========================== --}}
        @if (auth()->user()->role === 'guru' && auth()->user()->guru && auth()->user()->guru->role_guru === 'walikelas')
            {{-- PENGAJUAN --}}
            <li class="sidebar-item {{ request()->is('permohonan-konseling') ? 'active' : '' }}">
                <a href="/permohonan-konseling" class="sidebar-link d-flex align-items-center gap-2">
                    <i class="bi bi-envelope-fill"></i>
                    <span>Pengajuan Konseling</span>
                </a>
            </li>

            {{-- JADWAL --}}
            <li class="sidebar-item {{ request()->is('jadwal-konseling') ? 'active' : '' }}">
                <a href="/jadwal-konseling" class="sidebar-link d-flex align-items-center gap-2">
                    <i class="bi bi-calendar-fill"></i>
                    <span>Jadwal Konseling</span>
                </a>
            </li>

            {{-- RIWAYAT --}}
            <li class="sidebar-item {{ request()->is('riwayat-konseling') ? 'active' : '' }}">
                <a href="/riwayat-konseling" class="sidebar-link d-flex align-items-center gap-2">
                    <i class="bi bi-clock-fill"></i>
                    <span>Riwayat Konseling</span>
                </a>
            </li>
        @endif


        {{-- =========================
            SISWA
        ========================== --}}
        @if (auth()->user()->role === 'siswa')
            {{-- PENGAJUAN --}}
            <li class="sidebar-item {{ request()->is('permohonan-konseling') ? 'active' : '' }}">
                <a href="/permohonan-konseling" class="sidebar-link d-flex align-items-center gap-2">
                    <i class="bi bi-envelope-fill"></i>
                    <span>Pengajuan Konseling</span>
                </a>
            </li>

            {{-- JADWAL --}}
            <li class="sidebar-item {{ request()->is('jadwal-konseling') ? 'active' : '' }}">
                <a href="/jadwal-konseling" class="sidebar-link d-flex align-items-center gap-2">
                    <i class="bi bi-calendar-fill"></i>
                    <span>Jadwal Konseling</span>
                </a>
            </li>

            {{-- RIWAYAT --}}
            <li class="sidebar-item {{ request()->is('riwayat-konseling') ? 'active' : '' }}">
                <a href="/riwayat-konseling" class="sidebar-link d-flex align-items-center gap-2">
                    <i class="bi bi-clock-fill"></i>
                    <span>Riwayat Konseling</span>
                </a>
            </li>
        @endif


        {{-- =========================
            ORANG TUA
        ========================== --}}
        @if (auth()->user()->role === 'orangtua')
            {{-- RIWAYAT → sebelum laporan --}}
            <li class="sidebar-item {{ request()->is('riwayat-konseling') ? 'active' : '' }}">
                <a href="/riwayat-konseling" class="sidebar-link d-flex align-items-center gap-2">
                    <i class="bi bi-clock-fill"></i>
                    <span>Riwayat Konseling</span>
                </a>
            </li>

            {{-- LAPORAN --}}
            <li class="sidebar-item {{ request()->is('laporan') ? 'active' : '' }}">
                <a href="/laporan" class="sidebar-link d-flex align-items-center gap-2">
                    <i class="bi bi-bar-chart-fill"></i>
                    <span>Laporan</span>
                </a>
            </li>
        @endif


        {{-- =========================
            KEPALA SEKOLAH
        ========================== --}}
        @if (auth()->user()->role === 'guru' && auth()->user()->guru && auth()->user()->guru->role_guru === 'kepala_sekolah')
            {{-- RIWAYAT --}}
            <li class="sidebar-item {{ request()->is('riwayat-konseling') ? 'active' : '' }}">
                <a href="/riwayat-konseling" class="sidebar-link d-flex align-items-center gap-2">
                    <i class="bi bi-clock-fill"></i>
                    <span>Riwayat Konseling</span>
                </a>
            </li>

            {{-- LAPORAN --}}
            <li class="sidebar-item {{ request()->is('laporan') ? 'active' : '' }}">
                <a href="/laporan" class="sidebar-link d-flex align-items-center gap-2">
                    <i class="bi bi-bar-chart-fill"></i>
                    <span>Laporan</span>
                </a>
            </li>
        @endif


        {{-- =========================
            PROFILE
        ========================== --}}
        <li class="sidebar-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
            <a href="{{ route('profile.edit') }}" class="sidebar-link d-flex align-items-center gap-2">
                <i class="bi bi-person-fill"></i>
                <span>Profile</span>
            </a>
        </li>

    </ul>
</div>
