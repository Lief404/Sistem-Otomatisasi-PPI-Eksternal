@extends('mahasiswa.app')
@section('title', 'Transkrip Nilai PPI')

@section('content')
@php
    // Hitung nilai akhir
    $nilaiDosenPresentasi = $penilaianDosen->n_presentasi ?? 0;
    $nilaiDosenMakalah    = $penilaianDosen->n_makalah ?? 0;
    $nilaiMentorPrestasi  = $penilaianMentor->n_prestasi ?? 0;
    $nilaiMentorSupervisi = $penilaianMentor->n_supervisi ?? 0;

    // Formula nilai akhir: rata-rata dari 4 komponen
    $komponenCount = 0;
    $total = 0;
    if ($penilaianDosen) { $total += ($nilaiDosenPresentasi + $nilaiDosenMakalah) / 2; $komponenCount++; }
    if ($penilaianMentor) { $total += ($nilaiMentorPrestasi + $nilaiMentorSupervisi) / 2; $komponenCount++; }
    $nilaiAkhir = $komponenCount > 0 ? round($total / $komponenCount, 1) : null;

    // Konversi ke Huruf
    $nilaiHuruf = '-';
    $statusFinal = 'BELUM ADA PENILAIAN';
    if ($nilaiAkhir !== null) {
        if ($nilaiAkhir >= 85)      { $nilaiHuruf = 'A';  $statusFinal = 'LULUS'; }
        elseif ($nilaiAkhir >= 75)  { $nilaiHuruf = 'B';  $statusFinal = 'LULUS'; }
        elseif ($nilaiAkhir >= 65)  { $nilaiHuruf = 'C';  $statusFinal = 'LULUS'; }
        elseif ($nilaiAkhir >= 55)  { $nilaiHuruf = 'D';  $statusFinal = 'TIDAK LULUS'; }
        else                        { $nilaiHuruf = 'E';  $statusFinal = 'TIDAK LULUS'; }
    }

    // Total durasi logbook per matkul (dalam jam)
    $totalJamPerMatkul = $logbooks->groupBy('kd_mat')->map(fn($group) => round($group->sum('durasi_mnt') / 60, 1));
    $totalJamKeseluruhan = round($logbooks->sum('durasi_mnt') / 60, 1);
@endphp

<div class="space-y-8 max-w-6xl mx-auto pb-16 font-sans">

    {{-- Header Panel --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-white/70 backdrop-blur-xl p-8 rounded-[2rem] border border-gray-100 shadow-sm">
        <div>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 tracking-tight">Transkrip Penilaian PPI</h2>
            <p class="text-gray-500 mt-2 text-lg">Rincian evaluasi dari Dosen Pembimbing dan Mentor Industri.</p>
        </div>
        <div class="mt-6 md:mt-0 flex flex-wrap gap-3">
            <a href="{{ route('mahasiswa.dashboard') }}" class="bg-gray-100 border border-gray-200 text-gray-700 hover:bg-gray-200 font-semibold px-6 py-3 rounded-2xl shadow-sm transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
            <button onclick="window.print()" class="bg-blue-600 border border-blue-700 text-white hover:bg-blue-700 font-semibold px-6 py-3 rounded-2xl shadow-sm transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak
            </button>
        </div>
    </div>

    {{-- Status Akhir (Hero Card) --}}
    <div class="bg-gray-900 rounded-[2rem] p-8 md:p-10 shadow-lg text-white flex flex-col md:flex-row items-center justify-between gap-8 border border-gray-800">
        <div class="w-full md:w-2/3">
            <p class="text-gray-400 font-semibold uppercase tracking-widest text-sm mb-2">Status PPI Keseluruhan</p>
            <h3 class="text-4xl md:text-5xl font-black tracking-tight mb-2">{{ $statusFinal }}</h3>
            <p class="text-gray-400 font-medium text-lg mt-4">
                Mahasiswa: <span class="text-white font-bold">{{ $mahasiswa->nama_mhs ?? '-' }}</span>
                <span class="mx-2 text-gray-600">|</span>
                NIM: <span class="text-white font-bold">{{ $mahasiswa->nim ?? '-' }}</span>
            </p>
        </div>
        <div class="w-full md:w-1/3 flex justify-start md:justify-end">
            <div class="bg-white/10 backdrop-blur-md border border-white/10 p-6 rounded-3xl text-center w-full max-w-[200px]">
                <p class="text-gray-300 text-sm font-semibold uppercase tracking-wider mb-1">Nilai Akhir</p>
                <p class="text-6xl font-black text-white tracking-tighter">{{ $nilaiHuruf }}</p>
                <p class="text-xl font-bold text-gray-400 mt-2">
                    <span class="text-white">{{ $nilaiAkhir ?? '-' }}</span> / 100
                </p>
            </div>
        </div>
    </div>

    {{-- ============================= --}}
    {{-- BAGIAN 1: REKAP LOGBOOK       --}}
    {{-- ============================= --}}
    <div class="bg-white rounded-[2rem] border border-gray-200 shadow-sm overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-200 p-6 md:p-8 flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-900">I. Rekap Logbook Harian</h3>
                <p class="text-sm font-medium text-gray-500 mt-0.5">Total: <strong>{{ $totalJamKeseluruhan }} Jam</strong> dari {{ $logbooks->count() }} entri</p>
            </div>
        </div>
        <div class="p-6 md:p-8">
            @if($logbooks->isEmpty())
                <p class="text-center text-gray-400 italic py-8">Belum ada data logbook yang tersimpan.</p>
            @else
                {{-- Ringkasan per Mata Kuliah --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                    @foreach(['PII' => 'Praktik Pengalaman Industri', 'SUP' => 'Supervisi', 'K3' => 'K3 IT', 'LTD' => 'Lap. Teknik'] as $kode => $nama)
                    <div class="bg-gray-50 border border-gray-200 rounded-2xl p-4 text-center">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">{{ $kode }}</p>
                        <p class="text-3xl font-black text-gray-900 mt-1">{{ $totalJamPerMatkul[$kode] ?? 0 }}</p>
                        <p class="text-xs text-gray-400 font-semibold mt-1">Jam</p>
                    </div>
                    @endforeach
                </div>

                {{-- Tabel Logbook Terbaru (10 Entri) --}}
                <div class="rounded-2xl border border-gray-200 overflow-hidden">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-gray-500 uppercase tracking-wider text-xs font-semibold border-b border-gray-200">
                            <tr>
                                <th class="px-5 py-3">Tanggal</th>
                                <th class="px-5 py-3 border-l border-gray-200">Kegiatan</th>
                                <th class="px-5 py-3 border-l border-gray-200 text-center">Mata Kuliah</th>
                                <th class="px-5 py-3 border-l border-gray-200 text-center">Durasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @foreach($logbooks->take(10) as $log)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3 font-semibold text-gray-900 whitespace-nowrap">{{ \Carbon\Carbon::parse($log->tanggal)->format('d M Y') }}</td>
                                <td class="px-5 py-3 border-l border-gray-100">{{ $log->kegiatan }}</td>
                                <td class="px-5 py-3 border-l border-gray-100 text-center">
                                    <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded font-bold text-xs">{{ $log->kd_mat }}</span>
                                </td>
                                <td class="px-5 py-3 border-l border-gray-100 text-center font-bold">{{ $log->durasi_mnt }} menit</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($logbooks->count() > 10)
                    <p class="text-center text-gray-400 text-sm mt-3">Menampilkan 10 dari {{ $logbooks->count() }} entri logbook.</p>
                @endif
            @endif
        </div>
    </div>

    {{-- ============================= --}}
    {{-- BAGIAN 2: PENILAIAN MENTOR    --}}
    {{-- ============================= --}}
    <div class="bg-white rounded-[2rem] border border-gray-200 shadow-sm overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-200 p-6 md:p-8 flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-900">II. Penilaian Kinerja Industri</h3>
                <p class="text-sm font-medium text-gray-500 mt-0.5">
                    Penilai: {{ $mahasiswa->pembimbingIndustri->nama_pem ?? 'Mentor Industri' }}
                    @if($mahasiswa->pembimbingIndustri) — {{ $mahasiswa->pembimbingIndustri->perusahaan }} @endif
                </p>
            </div>
        </div>
        <div class="p-6 md:p-8">
            @if(!$penilaianMentor)
                <p class="text-center text-gray-400 italic py-8">Belum ada penilaian dari Mentor Industri.</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="rounded-2xl border border-gray-200 overflow-hidden">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-semibold border-b border-gray-200">
                                <tr>
                                    <th class="px-5 py-3">Komponen Penilaian</th>
                                    <th class="px-5 py-3 text-center border-l border-gray-200">Nilai (0-100)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-4">Prestasi Kerja & Disiplin</td>
                                    <td class="px-5 py-4 text-center font-black text-gray-900 border-l border-gray-100 text-lg">{{ $penilaianMentor->n_prestasi }}</td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-4">Supervisi & Kompetensi</td>
                                    <td class="px-5 py-4 text-center font-black text-gray-900 border-l border-gray-100 text-lg">{{ $penilaianMentor->n_supervisi }}</td>
                                </tr>
                                <tr class="bg-emerald-50 border-t-2 border-emerald-200">
                                    <td class="px-5 py-4 font-bold text-emerald-800 uppercase text-xs tracking-wider text-right">Rata-rata Mentor</td>
                                    <td class="px-5 py-4 text-center font-black text-emerald-600 text-xl border-l border-emerald-200">
                                        {{ round(($penilaianMentor->n_prestasi + $penilaianMentor->n_supervisi) / 2, 1) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ============================= --}}
    {{-- BAGIAN 3: PENILAIAN DOSEN     --}}
    {{-- ============================= --}}
    <div class="bg-white rounded-[2rem] border border-gray-200 shadow-sm overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-200 p-6 md:p-8 flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-900">III. Penilaian Akademik</h3>
                <p class="text-sm font-medium text-gray-500 mt-0.5">
                    Penilai: {{ $mahasiswa->dosen->nama_dosen ?? 'Dosen Pembimbing' }}
                </p>
            </div>
        </div>
        <div class="p-6 md:p-8">
            @if(!$penilaianDosen)
                <p class="text-center text-gray-400 italic py-8">Belum ada penilaian dari Dosen Pembimbing.</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="rounded-2xl border border-gray-200 overflow-hidden">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-semibold border-b border-gray-200">
                                <tr>
                                    <th class="px-5 py-3">Komponen Penilaian</th>
                                    <th class="px-5 py-3 text-center border-l border-gray-200">Nilai (0-100)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-4">Mutu Presentasi</td>
                                    <td class="px-5 py-4 text-center font-black text-gray-900 border-l border-gray-100 text-lg">{{ $penilaianDosen->n_presentasi }}</td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-4">Penulisan Makalah / Laporan</td>
                                    <td class="px-5 py-4 text-center font-black text-gray-900 border-l border-gray-100 text-lg">{{ $penilaianDosen->n_makalah }}</td>
                                </tr>
                                <tr class="bg-indigo-50 border-t-2 border-indigo-200">
                                    <td class="px-5 py-4 font-bold text-indigo-800 uppercase text-xs tracking-wider text-right">Rata-rata Dosen</td>
                                    <td class="px-5 py-4 text-center font-black text-indigo-600 text-xl border-l border-indigo-200">
                                        {{ round(($penilaianDosen->n_presentasi + $penilaianDosen->n_makalah) / 2, 1) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>

<style>
    @media print {
        body * { visibility: hidden; }
        main, main * { visibility: visible; }
        main { position: absolute; left: 0; top: 0; width: 100%; }
        button, a { display: none !important; }
        .shadow-sm, .shadow-lg { box-shadow: none !important; }
    }
</style>
@endsection


@section('content')
<div x-data="nilaiApp()" class="space-y-8 max-w-6xl mx-auto pb-16 font-sans">
    
    <!-- Header Panel -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-white/70 backdrop-blur-xl p-8 rounded-[2rem] border border-gray-100 shadow-sm relative overflow-hidden">
        <div>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 tracking-tight">Transkrip Penilaian PPI</h2>
            <p class="text-gray-500 mt-2 text-lg">Rincian evaluasi dari Dosen Pembimbing dan Mentor Industri.</p>
        </div>
        <div class="mt-6 md:mt-0 flex flex-wrap gap-3 relative z-10">
            <!-- Tombol Kembali -->
            <a href="{{ route('mahasiswa.dashboard') }}" class="bg-gray-100 border border-gray-200 text-gray-700 hover:bg-gray-200 hover:text-gray-900 font-semibold px-6 py-3 rounded-2xl shadow-sm transition-all duration-300 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Dashboard
            </a>
            <button onclick="window.print()" class="bg-blue-600 border border-blue-700 text-white hover:bg-blue-700 font-semibold px-6 py-3 rounded-2xl shadow-sm transition-all duration-300 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Dokumen
            </button>
        </div>
    </div>

    <!-- Nilai Akhir Keseluruhan (Hero Card) -->
    <div class="bg-gray-900 rounded-[2rem] p-8 md:p-10 shadow-lg text-white relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-8 border border-gray-800">
        <div class="relative z-10 w-full md:w-2/3">
            <p class="text-gray-400 font-semibold uppercase tracking-widest text-sm mb-2">Status PPI Keseluruhan</p>
            <h3 class="text-4xl md:text-5xl font-black tracking-tight mb-2 text-white" x-text="data.statusFinal"></h3>
            <p class="text-gray-400 font-medium text-lg mt-4">Mahasiswa: <span class="text-white font-bold" x-text="data.nama"></span> <span class="mx-2 text-gray-600">|</span> NIM: <span class="text-white font-bold" x-text="data.nim"></span></p>
        </div>
        <div class="relative z-10 w-full md:w-1/3 flex justify-start md:justify-end">
            <div class="bg-white/10 backdrop-blur-md border border-white/10 p-6 rounded-3xl text-center w-full max-w-[200px]">
                <p class="text-gray-300 text-sm font-semibold uppercase tracking-wider mb-1">Index Prestasi</p>
                <p class="text-6xl font-black text-white tracking-tighter" x-text="data.nilaiHuruf"></p>
                <p class="text-xl font-bold text-gray-400 mt-2"><span class="text-white" x-text="data.nilaiAkhir"></span> / 100</p>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- BAGIAN 1: PENILAIAN INDUSTRI (MENTOR)      -->
    <!-- ========================================== -->
    <div class="bg-white rounded-[2rem] border border-gray-200 shadow-sm overflow-hidden mb-8">
        <div class="bg-gray-50 border-b border-gray-200 p-6 md:p-8 flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-900 tracking-tight">I. Penilaian Kinerja Industri</h3>
                <p class="text-sm font-medium text-gray-500 mt-0.5">Penilai: Mentor PT / Pembimbing Lapangan</p>
            </div>
        </div>

        <div class="p-6 md:p-8 space-y-10">
            
            <!-- A. Logbook Mingguan (Rincian 20 Minggu) -->
            <div>
                <div class="flex justify-between items-end mb-4">
                    <h4 class="text-lg font-bold text-gray-800">A. Validasi Logbook Harian (Skala 0-100)</h4>
                    <span class="bg-emerald-50 text-emerald-700 font-black px-4 py-1.5 rounded-lg border border-emerald-200 text-sm">
                        Rata-rata: <span x-text="getAverageLogbook()"></span>
                    </span>
                </div>
                
                <!-- Grid 20 Kotak Minggu -->
                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-3">
                    <template x-for="item in data.mentor.logbook" :key="item.minggu">
                        <div class="border border-gray-200 rounded-xl p-3 flex justify-between items-center bg-gray-50/50 hover:border-emerald-300 transition-colors">
                            <span class="text-xs font-semibold text-gray-500">Minggu <span x-text="item.minggu"></span></span>
                            <template x-if="item.nilai !== null">
                                <span class="font-black text-gray-900 text-lg" x-text="item.nilai"></span>
                            </template>
                            <template x-if="item.nilai === null">
                                <span class="font-bold text-gray-300 text-sm">-</span>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            <!-- B. Tabel Disiplin & Supervisi -->
            <div>
                <h4 class="text-lg font-bold text-gray-800 mb-4">B. Disiplin & Prestasi Kerja (Skala 0-100)</h4>
                <div class="rounded-2xl border border-gray-200 overflow-hidden">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-gray-50 text-gray-500 uppercase tracking-wider text-xs font-semibold">
                            <tr>
                                <th class="px-6 py-4 w-16 text-center">No</th>
                                <th class="px-6 py-4 border-l border-gray-200">Kriteria Penilaian</th>
                                <th class="px-6 py-4 w-32 text-center border-l border-gray-200">Nilai Raw</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                            <template x-for="(item, index) in data.mentor.disiplin" :key="index">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-3 text-center text-gray-400" x-text="index + 1"></td>
                                    <td class="px-6 py-3 border-l border-gray-100" x-text="item.kriteria"></td>
                                    <td class="px-6 py-3 text-center border-l border-gray-100 font-bold text-gray-900" x-text="item.nilai"></td>
                                </tr>
                            </template>
                            <tr class="bg-gray-50 border-t-2 border-gray-200">
                                <td colspan="2" class="px-6 py-4 text-right font-bold text-gray-900 uppercase tracking-widest text-xs">Total Rata-rata Prestasi & Supervisi</td>
                                <td class="px-6 py-4 text-center border-l border-gray-200 font-black text-blue-600 text-base" x-text="getAverage(data.mentor.disiplin)"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- C. Tabel Kuisioner Hardskill & Softskill -->
            <div>
                <h4 class="text-lg font-bold text-gray-800 mb-4">C. Kuisioner Kompetensi (Skala 1-4)</h4>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    
                    <!-- Hardskill -->
                    <div class="rounded-2xl border border-gray-200 overflow-hidden">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="bg-gray-50 text-gray-500 uppercase tracking-wider text-[10px] font-semibold">
                                <tr>
                                    <th class="px-4 py-3 border-r border-gray-200">Hardskill</th>
                                    <th class="px-4 py-3 w-24 text-center">Nilai (1-4)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                                <template x-for="(item, index) in data.mentor.hardskill" :key="index">
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 border-r border-gray-100 whitespace-normal" x-text="item.kriteria"></td>
                                        <td class="px-4 py-3 text-center font-bold text-gray-900" x-text="item.nilai"></td>
                                    </tr>
                                </template>
                                <tr class="bg-gray-50 border-t border-gray-200">
                                    <td class="px-4 py-3 text-right font-bold text-gray-900 uppercase text-xs border-r border-gray-200">Rata-rata</td>
                                    <td class="px-4 py-3 text-center font-black text-blue-600" x-text="getAverage(data.mentor.hardskill)"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Softskill -->
                    <div class="rounded-2xl border border-gray-200 overflow-hidden">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="bg-gray-50 text-gray-500 uppercase tracking-wider text-[10px] font-semibold">
                                <tr>
                                    <th class="px-4 py-3 border-r border-gray-200">Softskill</th>
                                    <th class="px-4 py-3 w-24 text-center">Nilai (1-4)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                                <template x-for="(item, index) in data.mentor.softskill" :key="index">
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 border-r border-gray-100 whitespace-normal" x-text="item.kriteria"></td>
                                        <td class="px-4 py-3 text-center font-bold text-gray-900" x-text="item.nilai"></td>
                                    </tr>
                                </template>
                                <tr class="bg-gray-50 border-t border-gray-200">
                                    <td class="px-4 py-3 text-right font-bold text-gray-900 uppercase text-xs border-r border-gray-200">Rata-rata</td>
                                    <td class="px-4 py-3 text-center font-black text-blue-600" x-text="getAverage(data.mentor.softskill)"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- D. Catatan Mentor -->
            <div class="pt-4 border-t border-gray-100">
                <div class="bg-emerald-50/50 p-6 rounded-2xl border border-emerald-100">
                    <p class="text-sm font-semibold text-emerald-800 uppercase tracking-wider mb-2">D. Catatan Industri / Evaluasi Khusus</p>
                    <p class="text-sm text-gray-700 font-medium italic" x-text="data.mentor.catatan"></p>
                </div>
            </div>

        </div>
    </div>

    <!-- ========================================== -->
    <!-- BAGIAN 2: PENILAIAN DOSEN PEMBIMBING       -->
    <!-- ========================================== -->
    <div class="bg-white rounded-[2rem] border border-gray-200 shadow-sm overflow-hidden mb-8">
        <div class="bg-gray-50 border-b border-gray-200 p-6 md:p-8 flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477-4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-900 tracking-tight">II. Penilaian Akademik</h3>
                <p class="text-sm font-medium text-gray-500 mt-0.5">Penilai: Dosen Pembimbing Polman</p>
            </div>
        </div>

        <div class="p-6 md:p-8 space-y-8">
            <!-- Tabel Mutu Presentasi & Makalah -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Mutu Presentasi -->
                <div>
                    <h4 class="text-lg font-bold text-gray-800 mb-4">A. Mutu Presentasi (Skala 0-100)</h4>
                    <div class="rounded-2xl border border-gray-200 overflow-hidden">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="bg-gray-50 text-gray-500 uppercase tracking-wider text-[10px] font-semibold">
                                <tr>
                                    <th class="px-4 py-3 border-r border-gray-200">Aspek Evaluasi</th>
                                    <th class="px-4 py-3 w-24 text-center">Nilai Raw</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                                <template x-for="(item, index) in data.dosen.presentasi" :key="index">
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 border-r border-gray-100 whitespace-normal" x-text="item.kriteria"></td>
                                        <td class="px-4 py-3 text-center font-bold text-gray-900" x-text="item.nilai"></td>
                                    </tr>
                                </template>
                                <tr class="bg-gray-50 border-t border-gray-200">
                                    <td class="px-4 py-3 text-right font-bold text-gray-900 uppercase text-xs border-r border-gray-200">Rata-rata Presentasi</td>
                                    <td class="px-4 py-3 text-center font-black text-indigo-600" x-text="getAverage(data.dosen.presentasi)"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Penulisan Makalah -->
                <div>
                    <h4 class="text-lg font-bold text-gray-800 mb-4">B. Penulisan Makalah (Skala 0-100)</h4>
                    <div class="rounded-2xl border border-gray-200 overflow-hidden">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="bg-gray-50 text-gray-500 uppercase tracking-wider text-[10px] font-semibold">
                                <tr>
                                    <th class="px-4 py-3 border-r border-gray-200">Aspek Evaluasi</th>
                                    <th class="px-4 py-3 w-24 text-center">Nilai Raw</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                                <template x-for="(item, index) in data.dosen.makalah" :key="index">
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 border-r border-gray-100 whitespace-normal" x-text="item.kriteria"></td>
                                        <td class="px-4 py-3 text-center font-bold text-gray-900" x-text="item.nilai"></td>
                                    </tr>
                                </template>
                                <tr class="bg-gray-50 border-t border-gray-200">
                                    <td class="px-4 py-3 text-right font-bold text-gray-900 uppercase text-xs border-r border-gray-200">Rata-rata Makalah</td>
                                    <td class="px-4 py-3 text-center font-black text-indigo-600" x-text="getAverage(data.dosen.makalah)"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Catatan Dosen -->
            <div class="pt-4 border-t border-gray-100">
                <div class="bg-indigo-50/50 p-6 rounded-2xl border border-indigo-100 w-full">
                    <p class="text-sm font-semibold text-indigo-800 uppercase tracking-wider mb-2">C. Catatan Dosen Pembimbing</p>
                    <p class="text-sm text-gray-700 font-medium italic" x-text="data.dosen.catatan"></p>
                </div>
            </div>

        </div>
    </div>

</div>

<!-- Style khusus untuk cetak (Print) -->
<style>
    @media print {
        body * { visibility: hidden; }
        #content, #content * { visibility: visible; }
        #content { position: absolute; left: 0; top: 0; width: 100%; }
        button, a[href] { display: none !important; }
        .shadow-sm, .shadow-md, .shadow-lg { box-shadow: none !important; border: 1px solid #e5e7eb !important; }
    }
</style>

<script>
    function nilaiApp() {
        return {
            // Data Dummy Raw Penilaian Mahasiswa
            data: {
                nama: 'Alief Muhammad S',
                nim: '223443026',
                statusFinal: 'LULUS',
                nilaiAkhir: 88.5,
                nilaiHuruf: 'A',
                
                mentor: {
                    // Array Logbook 20 Minggu
                    logbook: [
                        { minggu: 1, nilai: 90 }, { minggu: 2, nilai: 95 }, { minggu: 3, nilai: 92 }, { minggu: 4, nilai: 88 },
                        { minggu: 5, nilai: 90 }, { minggu: 6, nilai: 85 }, { minggu: 7, nilai: 95 }, { minggu: 8, nilai: 90 },
                        { minggu: 9, nilai: 88 }, { minggu: 10, nilai: 92 }, { minggu: 11, nilai: 90 }, { minggu: 12, nilai: 85 },
                        { minggu: 13, nilai: null }, { minggu: 14, nilai: null }, { minggu: 15, nilai: null }, { minggu: 16, nilai: null },
                        { minggu: 17, nilai: null }, { minggu: 18, nilai: null }, { minggu: 19, nilai: null }, { minggu: 20, nilai: null }
                    ],
                    catatan: 'Mahasiswa sangat rajin dan cepat beradaptasi dengan teknologi perusahaan. Komunikasi dengan tim sangat baik.',
                    disiplin: [
                        { kriteria: "1. Kerajinan", nilai: 90 },
                        { kriteria: "2. Kesungguhan kerja (Sikap & Tanggung Jawab)", nilai: 90 },
                        { kriteria: "3. Kecakapan (Kemampuan teknis)", nilai: 85 },
                        { kriteria: "4. Kemandirian dalam bekerja", nilai: 85 },
                        { kriteria: "5. Inisiatif di lapangan", nilai: 90 },
                        { kriteria: "6. Kepemimpinan", nilai: 80 },
                        { kriteria: "7. Komunikasi interpersonal", nilai: 90 },
                        { kriteria: "8. Kerjasama tim", nilai: 95 }
                    ],
                    hardskill: [
                        { kriteria: "Informatika / Pemrograman", nilai: 4 },
                        { kriteria: "Keamanan Jaringan", nilai: 3 },
                        { kriteria: "Kontrol (Mikrokontroler, PLC, dll)", nilai: 3 },
                        { kriteria: "Listrik dan Elektronika", nilai: 4 },
                        { kriteria: "Kemampuan komunikasi global (Bahasa Inggris)", nilai: 3 },
                        { kriteria: "Penggunaan teknologi informasi (Software)", nilai: 4 },
                        { kriteria: "Pemakaian dan perawatan peralatan", nilai: 4 }
                    ],
                    softskill: [
                        { kriteria: "Integritas (Etika & moral)", nilai: 4 },
                        { kriteria: "Kemampuan berkomunikasi", nilai: 4 },
                        { kriteria: "Kerjasama dalam tim", nilai: 4 },
                        { kriteria: "Kepemimpinan (Leadership)", nilai: 3 },
                        { kriteria: "Inisiatif bekerja", nilai: 4 },
                        { kriteria: "Kemauan untuk belajar", nilai: 4 },
                        { kriteria: "Kerja keras & Motivasi", nilai: 4 },
                        { kriteria: "Kedisiplinan & Tanggung Jawab", nilai: 4 }
                    ]
                },
                
                dosen: {
                    catatan: 'Sistematika penulisan laporan akhir sangat baik. Analisis K3 dan penyelesaian masalah diuraikan dengan jelas.',
                    presentasi: [
                        { kriteria: "Persiapan Presentasi", nilai: 85 },
                        { kriteria: "Sistematika Penyajian", nilai: 90 },
                        { kriteria: "Penggunaan Alat Bantu", nilai: 85 },
                        { kriteria: "Bahasa Lisan & Sikap", nilai: 85 },
                        { kriteria: "Penguasaan Materi (Tanya Jawab)", nilai: 90 },
                        { kriteria: "Sistematika Menjawab", nilai: 85 },
                        { kriteria: "Mempertahankan Pendapat", nilai: 85 }
                    ],
                    makalah: [
                        { kriteria: "Sistematika Penulisan", nilai: 90 },
                        { kriteria: "Kelengkapan Isi & Review Pustaka", nilai: 95 },
                        { kriteria: "Bahasa Tulisan", nilai: 85 },
                        { kriteria: "Perumusan Masalah", nilai: 90 },
                        { kriteria: "Analisa Uraian", nilai: 85 },
                        { kriteria: "Penyelesaian Masalah", nilai: 90 },
                        { kriteria: "Kesimpulan & Saran", nilai: 85 }
                    ]
                }
            },

            getAverage(arr) {
                if (!arr || arr.length === 0) return 0;
                let sum = arr.reduce((total, item) => total + item.nilai, 0);
                return Number((sum / arr.length).toFixed(1));
            },

            getAverageLogbook() {
                let validScores = this.data.mentor.logbook.filter(item => item.nilai !== null);
                if (validScores.length === 0) return 0;
                let sum = validScores.reduce((total, item) => total + item.nilai, 0);
                return Number((sum / validScores.length).toFixed(1));
            }
        }
    }
</script>
@endsection