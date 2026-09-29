@extends(request('mode') === 'modal' ? 'layouts.blank' : 'mahasiswa.app')
@section('title', 'Transkrip Nilai PPI')

@section('content')
@php
    // Hitung nilai akhir
    $nilaiDosenPresentasi = $penilaianDosen->n_presentasi ?? 0;
    $nilaiDosenMakalah    = $penilaianDosen->n_makalah ?? 0;
    $nilaiMentorPrestasi  = $penilaianMentor->n_prestasi ?? 0;
    $nilaiMentorSupervisi = $penilaianMentor->n_supervisi ?? 0;

    // Formula nilai akhir: rata-rata dari 4 komponen
    $isComplete = ($penilaianDosen && $penilaianMentor);
    $total = 0;
    if ($penilaianDosen) { $total += ($nilaiDosenPresentasi + $nilaiDosenMakalah) / 2; }
    if ($penilaianMentor) { $total += ($nilaiMentorPrestasi + $nilaiMentorSupervisi) / 2; }
    $nilaiAkhir = $isComplete ? round($total / 2, 1) : null;

    // Konversi ke Huruf
    $nilaiHuruf = '-';
    $statusFinal = 'BELUM LENGKAP';
    if ($isComplete) {
        if ($nilaiAkhir >= 85)      { $nilaiHuruf = 'A';  $statusFinal = 'LULUS'; }
        elseif ($nilaiAkhir >= 75)  { $nilaiHuruf = 'B';  $statusFinal = 'LULUS'; }
        elseif ($nilaiAkhir >= 65)  { $nilaiHuruf = 'C';  $statusFinal = 'LULUS'; }
        elseif ($nilaiAkhir >= 55)  { $nilaiHuruf = 'D';  $statusFinal = 'TIDAK LULUS'; }
        else                        { $nilaiHuruf = 'E';  $statusFinal = 'TIDAK LULUS'; }
    }

    // Total durasi logbook per matkul (dalam jam)
    $totalJamKeseluruhan = round($logbooks->sum('durasi_mnt') / 60, 1);
    
    $rekapanJam = [];
    foreach($logbooks as $lb) {
        $matkulKode = $lb->kd_mat ?? 'Lainnya';
        $matkulNama = $lb->mataKuliah ? $lb->mataKuliah->nama_komp : $matkulKode;
        $minggu = $lb->minggu_ke;
        $durasiMnt = $lb->durasi_mnt;
        
        if (!isset($rekapanJam[$matkulKode])) {
            $rekapanJam[$matkulKode] = [
                'kode' => $matkulKode,
                'nama' => $matkulNama,
                'total_mnt' => 0,
                'mingguan_mnt' => array_fill(1, 20, 0)
            ];
        }
        
        if ($minggu >= 1 && $minggu <= 20) {
            $rekapanJam[$matkulKode]['mingguan_mnt'][$minggu] += $durasiMnt;
        }
        $rekapanJam[$matkulKode]['total_mnt'] += $durasiMnt;
    }
    
    // Convert to hours
    foreach ($rekapanJam as $k => $v) {
        $rekapanJam[$k]['total'] = round($v['total_mnt'] / 60, 1);
        $rekapanJam[$k]['mingguan'] = [];
        for ($i = 1; $i <= 20; $i++) {
            $rekapanJam[$k]['mingguan'][$i] = round($v['mingguan_mnt'][$i] / 60, 1);
        }
        unset($rekapanJam[$k]['total_mnt']);
        unset($rekapanJam[$k]['mingguan_mnt']);
    }
@endphp

<div x-data="nilaiApp()" id="transkrip-content" class="space-y-8 max-w-6xl mx-auto pb-16 font-sans">
    
    <!-- Header Panel -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-white/70 backdrop-blur-xl p-8 rounded-[2rem] border border-gray-100 shadow-sm relative overflow-hidden" data-html2canvas-ignore="true">
        <div>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 tracking-tight">Transkrip Penilaian PPI</h2>
            <p class="text-gray-500 mt-2 text-lg">Rincian evaluasi dari Dosen Pembimbing dan Mentor Industri.</p>
        </div>
        
        @if(request('mode') !== 'modal')
        <div class="mt-6 md:mt-0 flex flex-wrap gap-3 relative z-10">
            <a href="javascript:history.back()" class="bg-gray-100 border border-gray-200 text-gray-700 hover:bg-gray-200 hover:text-gray-900 font-semibold px-6 py-3 rounded-2xl shadow-sm transition-all duration-300 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
            <button @click="generatePDF()" class="bg-blue-600 border border-blue-700 text-white hover:bg-blue-700 font-semibold px-6 py-3 rounded-2xl shadow-sm transition-all duration-300 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Download PDF
            </button>
        </div>
        @endif
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

    <!-- BAGIAN 1: PENILAIAN INDUSTRI (MENTOR) -->
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
            <!-- A. Logbook Mingguan (Tanpa TTD) -->
            <div>
                <div class="flex justify-between items-end mb-4">
                    <div>
                        <h4 class="text-lg font-bold text-gray-800">A. Validasi Logbook Harian (Skala 0-100)</h4>
                        <p class="text-xs font-medium text-gray-400 mt-1">Nilai tersinkron otomatis dari penilaian mentor.</p>
                    </div>
                    <div class="text-right">
                        <span class="bg-emerald-50 text-emerald-700 font-black px-4 py-1.5 rounded-lg border border-emerald-200 text-sm">
                            Rata-rata: <span x-text="getAverageLogbook()"></span>
                        </span>
                        <p class="text-[10px] font-medium text-gray-400 mt-2" x-text="'Pembaruan: ' + data.mentor.logbookUpdatedAt"></p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-3">
                    <template x-for="item in data.mentor.logbook" :key="item.minggu">
                        <div class="border border-gray-200 rounded-xl p-3 flex justify-between items-center bg-gray-50/50 hover:border-emerald-300 transition-colors">
                            <span class="text-xs font-semibold text-gray-500">Minggu <span x-text="item.minggu"></span></span>
                            <template x-if="item.nilai !== null">
                                <div class="flex items-center gap-2">
                                    <span class="font-black text-gray-900 text-lg" x-text="formatScore(item.nilai)"></span>
                                    <span class="px-2 py-0.5 rounded border border-emerald-300 bg-emerald-50 text-xs font-black text-emerald-800" x-text="item.predikat || getLogbookGrade(item.nilai)"></span>
                                </div>
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
                
                <!-- TTD BOX DISIPLIN -->
                @if(auth()->check() && auth()->user()->role == 'mahasiswa')
                <div class="mt-5 flex justify-end">
                    <div class="border border-gray-200 bg-gray-50/80 rounded-2xl p-5 w-56 text-center flex flex-col items-center justify-center min-h-[160px] shadow-sm relative">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">TTD DISIPLIN</p>
                        
                        <div x-show="['belum_mengajukan'].includes(data.approval.disiplin.status)" class="w-full">
                            <button @click="ajukanQR('disiplin')" class="w-full bg-gray-900 text-white text-xs font-bold py-2 rounded-lg hover:bg-gray-800 transition shadow-sm mb-2">Ajukan TTD</button>
                            <span class="text-[10px] text-gray-400 font-medium italic">Belum disahkan</span>
                        </div>
                        
                        <div x-show="data.approval.disiplin.status === 'pending'" class="w-full flex flex-col items-center justify-center">
                            <span class="text-xs font-bold text-amber-600 bg-amber-50 border border-amber-200 px-3 py-1 rounded-full mb-3">Menunggu Acc</span>
                            <button @click="batalkanQR('disiplin')" class="flex items-center justify-center w-8 h-8 bg-red-100 text-red-600 hover:bg-red-600 hover:text-white rounded-full transition-colors shadow-sm" title="Batalkan Pengajuan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                            <span class="text-[9px] text-gray-400 mt-1">Batal</span>
                        </div>
                        
                        <div x-show="data.approval.disiplin.status === 'rejected'" class="w-full">
                            <span class="bg-red-100 text-red-800 border border-red-200 font-black px-3 py-1 rounded text-xs block mb-2">DITOLAK</span>
                            <p class="text-xs text-red-600 italic mb-3 leading-tight line-clamp-2" :title="data.approval.disiplin.alasan" x-text="data.approval.disiplin.alasan"></p>
                            <button @click="ajukanQR('disiplin')" class="w-full border-2 border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-xs font-bold py-1.5 rounded-lg transition">Ajukan Ulang</button>
                        </div>
                        
                        <div x-show="data.approval.disiplin.status === 'accepted'" class="w-full flex flex-col items-center">
                            <div class="w-24 h-24 bg-white border border-gray-200 p-1.5 mb-2 rounded-lg shadow-sm">
                                <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' + encodeURIComponent('{{ url('/approval') }}/' + data.approval.disiplin.token)" alt="QR Code" class="w-full h-full object-contain">
                            </div>
                            <span class="text-emerald-600 font-black text-xs uppercase tracking-wider flex items-center justify-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                Verified
                            </span>
                            <!-- TOMBOL TEST: Reset / Batalkan ACC agar bisa diajukan ulang -->
                            <button @click="resetQR('disiplin')" class="text-[10px] bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 font-bold px-2.5 py-1 rounded-md transition shadow-sm flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Reset / Acc Ulang
                            </button>
                        </div>
                    </div>
                </div>
                @endif
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

                <!-- TTD BOX KUISIONER -->
                @if(auth()->check() && auth()->user()->role == 'mahasiswa')
                <div class="mt-5 flex justify-end">
                    <div class="border border-gray-200 bg-gray-50/80 rounded-2xl p-5 w-56 text-center flex flex-col items-center justify-center min-h-[160px] shadow-sm relative">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">TTD KUISIONER</p>
                        
                        <div x-show="['belum_mengajukan'].includes(data.approval.kuisioner.status)" class="w-full">
                            <button @click="ajukanQR('kuisioner')" class="w-full bg-gray-900 text-white text-xs font-bold py-2 rounded-lg hover:bg-gray-800 transition shadow-sm mb-2">Ajukan TTD</button>
                            <span class="text-[10px] text-gray-400 font-medium italic">Belum disahkan</span>
                        </div>
                        
                        <div x-show="data.approval.kuisioner.status === 'pending'" class="w-full flex flex-col items-center justify-center">
                            <span class="text-xs font-bold text-amber-600 bg-amber-50 border border-amber-200 px-3 py-1 rounded-full mb-3">Menunggu Acc</span>
                            <button @click="batalkanQR('kuisioner')" class="flex items-center justify-center w-8 h-8 bg-red-100 text-red-600 hover:bg-red-600 hover:text-white rounded-full transition-colors shadow-sm" title="Batalkan Pengajuan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                            <span class="text-[9px] text-gray-400 mt-1">Batal</span>
                        </div>
                        
                        <div x-show="data.approval.kuisioner.status === 'rejected'" class="w-full">
                            <span class="bg-red-100 text-red-800 border border-red-200 font-black px-3 py-1 rounded text-xs block mb-2">DITOLAK</span>
                            <p class="text-xs text-red-600 italic mb-3 leading-tight line-clamp-2" :title="data.approval.kuisioner.alasan" x-text="data.approval.kuisioner.alasan"></p>
                            <button @click="ajukanQR('kuisioner')" class="w-full border-2 border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-xs font-bold py-1.5 rounded-lg transition">Ajukan Ulang</button>
                        </div>
                        
                        <div x-show="data.approval.kuisioner.status === 'accepted'" class="w-full flex flex-col items-center">
                            <div class="w-24 h-24 bg-white border border-gray-200 p-1.5 mb-2 rounded-lg shadow-sm">
                                <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' + encodeURIComponent('{{ url('/approval') }}/' + data.approval.kuisioner.token)" alt="QR Code" class="w-full h-full object-contain">
                            </div>
                            <span class="text-emerald-600 font-black text-xs uppercase tracking-wider flex items-center justify-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                Verified
                            </span>
                        </div>
                    </div>
                </div>
                @endif
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

    <!-- BAGIAN 2: PENILAIAN DOSEN PEMBIMBING -->
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
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Mutu Presentasi -->
                <div>
                    <h4 class="text-lg font-bold text-gray-800 mb-4">A. Mutu Presentasi (Skala 0-100)</h4>
                    <div class="rounded-2xl border border-gray-200 overflow-hidden mb-4">
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

                    <!-- TTD BOX PRESENTASI -->
                    @if(auth()->check() && auth()->user()->role == 'mahasiswa')
                    <div class="flex justify-end mt-5">
                        <div class="border border-gray-200 bg-gray-50/80 rounded-2xl p-5 w-56 text-center flex flex-col items-center justify-center min-h-[160px] shadow-sm relative">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">TTD PRESENTASI</p>
                            
                            <div x-show="['belum_mengajukan'].includes(data.approval.presentasi.status)" class="w-full">
                                <button @click="ajukanQR('presentasi')" class="w-full bg-gray-900 text-white text-xs font-bold py-2 rounded-lg hover:bg-gray-800 transition shadow-sm mb-2">Ajukan TTD</button>
                                <span class="text-[10px] text-gray-400 font-medium italic">Belum disahkan</span>
                            </div>
                            
                            <div x-show="data.approval.presentasi.status === 'pending'" class="w-full flex flex-col items-center justify-center">
                                <span class="text-xs font-bold text-amber-600 bg-amber-50 border border-amber-200 px-3 py-1 rounded-full mb-3">Menunggu Acc</span>
                                <button @click="batalkanQR('presentasi')" class="flex items-center justify-center w-8 h-8 bg-red-100 text-red-600 hover:bg-red-600 hover:text-white rounded-full transition-colors shadow-sm" title="Batalkan Pengajuan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                                <span class="text-[9px] text-gray-400 mt-1">Batal</span>
                            </div>
                            
                            <div x-show="data.approval.presentasi.status === 'rejected'" class="w-full">
                                <span class="bg-red-100 text-red-800 border border-red-200 font-black px-3 py-1 rounded text-xs block mb-2">DITOLAK</span>
                                <p class="text-xs text-red-600 italic mb-3 leading-tight line-clamp-2" :title="data.approval.presentasi.alasan" x-text="data.approval.presentasi.alasan"></p>
                                <button @click="ajukanQR('presentasi')" class="w-full border-2 border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-xs font-bold py-1.5 rounded-lg transition">Ajukan Ulang</button>
                            </div>
                            
                            <div x-show="data.approval.presentasi.status === 'accepted'" class="w-full flex flex-col items-center">
                                <div class="w-24 h-24 bg-white border border-gray-200 p-1.5 mb-2 rounded-lg shadow-sm">
                                    <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' + encodeURIComponent('{{ url('/approval') }}/' + data.approval.presentasi.token)" alt="QR Code" class="w-full h-full object-contain">
                                </div>
                                <span class="text-emerald-600 font-black text-xs uppercase tracking-wider flex items-center justify-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    Verified
                                </span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Penulisan Makalah -->
                <div>
                    <h4 class="text-lg font-bold text-gray-800 mb-4">B. Penulisan Makalah (Skala 0-100)</h4>
                    <div class="rounded-2xl border border-gray-200 overflow-hidden mb-4">
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

                    <!-- TTD BOX MAKALAH -->
                    @if(auth()->check() && auth()->user()->role == 'mahasiswa')
                    <div class="flex justify-end mt-5">
                        <div class="border border-gray-200 bg-gray-50/80 rounded-2xl p-5 w-56 text-center flex flex-col items-center justify-center min-h-[160px] shadow-sm relative">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">TTD MAKALAH</p>
                            
                            <div x-show="['belum_mengajukan'].includes(data.approval.makalah.status)" class="w-full">
                                <button @click="ajukanQR('makalah')" class="w-full bg-gray-900 text-white text-xs font-bold py-2 rounded-lg hover:bg-gray-800 transition shadow-sm mb-2">Ajukan TTD</button>
                                <span class="text-[10px] text-gray-400 font-medium italic">Belum disahkan</span>
                            </div>
                            
                            <div x-show="data.approval.makalah.status === 'pending'" class="w-full flex flex-col items-center justify-center">
                                <span class="text-xs font-bold text-amber-600 bg-amber-50 border border-amber-200 px-3 py-1 rounded-full mb-3">Menunggu Acc</span>
                                <button @click="batalkanQR('makalah')" class="flex items-center justify-center w-8 h-8 bg-red-100 text-red-600 hover:bg-red-600 hover:text-white rounded-full transition-colors shadow-sm" title="Batalkan Pengajuan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                                <span class="text-[9px] text-gray-400 mt-1">Batal</span>
                            </div>
                            
                            <div x-show="data.approval.makalah.status === 'rejected'" class="w-full">
                                <span class="bg-red-100 text-red-800 border border-red-200 font-black px-3 py-1 rounded text-xs block mb-2">DITOLAK</span>
                                <p class="text-xs text-red-600 italic mb-3 leading-tight line-clamp-2" :title="data.approval.makalah.alasan" x-text="data.approval.makalah.alasan"></p>
                                <button @click="ajukanQR('makalah')" class="w-full border-2 border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-xs font-bold py-1.5 rounded-lg transition">Ajukan Ulang</button>
                            </div>
                            
                            <div x-show="data.approval.makalah.status === 'accepted'" class="w-full flex flex-col items-center">
                                <div class="w-24 h-24 bg-white border border-gray-200 p-1.5 mb-2 rounded-lg shadow-sm">
                                    <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' + encodeURIComponent('{{ url('/approval') }}/' + data.approval.makalah.token)" alt="QR Code" class="w-full h-full object-contain">
                                </div>
                                <span class="text-emerald-600 font-black text-xs uppercase tracking-wider flex items-center justify-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    Verified
                                </span>
                            </div>
                        </div>
                    </div>
                    @endif
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

    <!-- BAGIAN 3: REKAPAN JAM LOGBOOK -->
    <div class="bg-white rounded-[2rem] border border-gray-200 shadow-sm overflow-hidden mb-8">
        <div class="bg-gray-50 border-b border-gray-200 p-6 md:p-8 flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-900 tracking-tight">III. Rekapan Jam Logbook</h3>
                <p class="text-sm font-medium text-gray-500 mt-0.5">Total Jam per Mata Kuliah (Mingguan & Keseluruhan)</p>
            </div>
        </div>

        <div class="p-6 md:p-8 space-y-8">
            <div class="flex justify-between items-end mb-4">
                <h4 class="text-lg font-bold text-gray-800">Detail Jam per Mata Kuliah</h4>
                <span class="bg-blue-50 text-blue-700 font-black px-4 py-1.5 rounded-lg border border-blue-200 text-sm">
                    Total Keseluruhan: <span x-text="data.totalJamKeseluruhan"></span> Jam
                </span>
            </div>

            <!-- Canvas Chart -->
            <div class="w-full bg-white border border-gray-100 rounded-2xl p-6 mb-6 shadow-sm flex flex-col items-center justify-center" x-show="data.rekapanJam.length > 0">
                <div class="relative w-full max-w-lg" style="height: 380px;">
                    <canvas id="rekapanChart"></canvas>
                </div>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-gray-200">
                <table class="w-full text-left text-sm whitespace-nowrap min-w-max">
                    <thead class="bg-gray-50 text-gray-500 uppercase tracking-wider text-xs font-semibold">
                        <tr>
                            <th class="px-4 py-4 border-r border-gray-200 sticky left-0 bg-gray-50 z-10 min-w-[200px]">Mata Kuliah</th>
                            <th class="px-4 py-4 text-center border-r border-gray-200 font-bold text-gray-900">Total (Jam)</th>
                            <template x-for="i in 20" :key="i">
                                <th class="px-2 py-4 text-center border-r border-gray-200 text-[10px]">M<span x-text="i"></span></th>
                            </template>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                        <template x-for="(item, index) in data.rekapanJam" :key="index">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 border-r border-gray-100 sticky left-0 bg-white z-10 min-w-[200px]">
                                    <div class="truncate max-w-[250px]" :title="item.nama">
                                        <span class="font-bold text-gray-900" x-text="item.kode"></span><br>
                                        <span class="text-xs text-gray-500" x-text="item.nama"></span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center border-r border-gray-100 font-black text-blue-600 bg-blue-50/30" x-text="item.total"></td>
                                <template x-for="i in 20" :key="i">
                                    <td class="px-2 py-3 text-center border-r border-gray-50 text-xs">
                                        <template x-if="item.mingguan[i] > 0">
                                            <span class="text-gray-900 font-semibold" x-text="item.mingguan[i]"></span>
                                        </template>
                                        <template x-if="item.mingguan[i] === 0">
                                            <span class="text-gray-300">-</span>
                                        </template>
                                    </td>
                                </template>
                            </tr>
                        </template>
                        
                        <template x-if="data.rekapanJam.length === 0">
                            <tr>
                                <td colspan="22" class="px-4 py-8 text-center text-gray-500 italic">Belum ada data logbook.</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function nilaiApp() {
        return {
            init() {
                this.$nextTick(() => {
                    this.renderChart();
                });
                this.refreshLogbookScores();
                setInterval(() => this.refreshLogbookScores(), 15000);
            },

            // State Data Mahasiswa & Approval
            data: {
                nama: '{{ $mahasiswa->nama_mhs ?? "-" }}',
                nim: '{{ $mahasiswa->nim ?? "-" }}',
                statusFinal: '{{ $statusFinal }}',
                nilaiAkhir: {{ $nilaiAkhir ?? 'null' }},
                nilaiHuruf: '{{ $nilaiHuruf }}',
                rekapanJam: {!! json_encode(array_values($rekapanJam)) !!},
                totalJamKeseluruhan: {{ $totalJamKeseluruhan }},
                
                // STATE APPROVAL DINAMIS
                approval: {
                    disiplin: { 
                        status: '{!! $approvals["disiplin"]->status ?? "belum_mengajukan" !!}', 
                        alasan: '{!! $approvals["disiplin"]->alasan_reject ?? "" !!}', 
                        token: '{!! $approvals["disiplin"]->token_verifikasi ?? "" !!}' 
                    },
                    kuisioner: { 
                        status: '{!! $approvals["kuisioner"]->status ?? "belum_mengajukan" !!}', 
                        alasan: '{!! $approvals["kuisioner"]->alasan_reject ?? "" !!}', 
                        token: '{!! $approvals["kuisioner"]->token_verifikasi ?? "" !!}' 
                    },
                    presentasi: { 
                        status: '{!! $approvals["presentasi"]->status ?? "belum_mengajukan" !!}', 
                        alasan: '{!! $approvals["presentasi"]->alasan_reject ?? "" !!}', 
                        token: '{!! $approvals["presentasi"]->token_verifikasi ?? "" !!}' 
                    },
                    makalah: { 
                        status: '{!! $approvals["makalah"]->status ?? "belum_mengajukan" !!}', 
                        alasan: '{!! $approvals["makalah"]->alasan_reject ?? "" !!}', 
                        token: '{!! $approvals["makalah"]->token_verifikasi ?? "" !!}' 
                    }
                },

                mentor: {
                    logbook: [
                        @for($i = 1; $i <= 20; $i++)
                            @php
                                $lbMinggu = $logbooks->where('minggu_ke', $i)->whereNotNull('nilai');
                                if ($lbMinggu->count() > 0) {
                                    $nilaiLb = round($lbMinggu->avg('nilai'), 2);
                                    $predikatLb = $lbMinggu->pluck('predikat_nilai')->filter()->first();
                                } else {
                                    $nilaiLb = 'null';
                                    $predikatLb = null;
                                }
                            @endphp
                            { minggu: {{ $i }}, nilai: {{ $nilaiLb }}, predikat: @json($predikatLb) },
                        @endfor
                    ],
                    logbookUpdatedAt: 'Memuat...',
                    catatan: '{{ $logbooks->where("catatan_mentor", "!=", "")->whereNotNull("catatan_mentor")->last()->catatan_mentor ?? "Tidak ada catatan khusus." }}',
                    @php
                        $disiplinList = [];
                        if ($disiplin && $disiplin->penilaian) {
                            $dData = json_decode($disiplin->penilaian, true);
                            if (is_array($dData)) {
                                foreach ($dData as $k => $v) {
                                    $disiplinList[] = ['kriteria' => $k, 'nilai' => $v];
                                }
                            }
                        }
                        
                        $hardskillList = [];
                        $softskillList = [];
                        if ($kuisioner && $kuisioner->penilaian) {
                            $kData = json_decode($kuisioner->penilaian, true);
                            if (is_array($kData)) {
                                foreach ($kData as $k => $v) {
                                    $isHard = false;
                                    foreach ($parameters as $param) {
                                        if ($param->jenis === 'kuisioner_mentor' && $param->sub_kategori === 'HARDSKILL') {
                                            if (in_array($k, $param->indikator)) {
                                                $isHard = true;
                                                break;
                                            }
                                        }
                                    }
                                    if ($isHard) {
                                        $hardskillList[] = ['kriteria' => $k, 'nilai' => $v];
                                    } else {
                                        $softskillList[] = ['kriteria' => $k, 'nilai' => $v];
                                    }
                                }
                            }
                        }
                    @endphp
                    disiplin: {!! json_encode($disiplinList) !!},
                    hardskill: {!! json_encode($hardskillList) !!},
                    softskill: {!! json_encode($softskillList) !!}
                },
                
                @php
                    $presentasiList = [];
                    $makalahList = [];
                    
                    if ($penilaianDosen) {
                        $detailPres = $penilaianDosen->detail_presentasi ?? [];
                        $detailMak = $penilaianDosen->detail_makalah ?? [];
                        
                        foreach ($parameters as $param) {
                            if ($param->jenis === 'presentasi' && isset($detailPres[$param->id])) {
                                foreach ($param->indikator as $index => $indName) {
                                    $presentasiList[] = [
                                        'kriteria' => $indName,
                                        'nilai' => $detailPres[$param->id][$index] ?? 0
                                    ];
                                }
                            } elseif ($param->jenis === 'makalah' && isset($detailMak[$param->id])) {
                                foreach ($param->indikator as $index => $indName) {
                                    $makalahList[] = [
                                        'kriteria' => $indName,
                                        'nilai' => $detailMak[$param->id][$index] ?? 0
                                    ];
                                }
                            }
                        }
                    }
                @endphp
                dosen: {
                    catatan: '-',
                    presentasi: {!! json_encode($presentasiList) !!},
                    makalah: {!! json_encode($makalahList) !!}
                }
            },

            async refreshLogbookScores() {
                try {
                    const response = await fetch('{{ route("mahasiswa.nilai.logbook") }}', {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (!response.ok) return;

                    const result = await response.json();
                    const scoresByWeek = new Map(result.logbook.map(item => [item.minggu, item]));
                    this.data.mentor.logbook = this.data.mentor.logbook.map(item => {
                        const score = scoresByWeek.get(item.minggu);
                        return score
                            ? { minggu: item.minggu, nilai: Number(score.nilai), predikat: score.predikat || this.getLogbookGrade(Number(score.nilai)) }
                            : { minggu: item.minggu, nilai: null, predikat: null };
                    });
                    this.data.mentor.logbookUpdatedAt = new Date(result.updated_at).toLocaleTimeString('id-ID', {
                        hour: '2-digit', minute: '2-digit', second: '2-digit'
                    });
                } catch (error) {
                    console.error('Gagal memperbarui nilai logbook:', error);
                }
            },
            
            renderChart() {
                if(this.data.rekapanJam.length === 0) return;
                
                const ctx = document.getElementById('rekapanChart').getContext('2d');
                
                const labels = [];
                const dataPoints = [];
                const backgroundColors = [];
                
                const colors = [
                    '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', 
                    '#ec4899', '#06b6d4', '#84cc16', '#f43f5e', '#6366f1'
                ];
                
                this.data.rekapanJam.forEach((item, index) => {
                    labels.push(item.kode + ' - ' + item.nama);
                    dataPoints.push(item.total);
                    backgroundColors.push(colors[index % colors.length]);
                });
                
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: dataPoints,
                            backgroundColor: backgroundColors,
                            borderWidth: 1,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { duration: 0 },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Total Jam Logbook per Mata Kuliah',
                                font: { size: 16 }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed !== null) {
                                            label += context.parsed + ' Jam';
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            },
            
// FUNGSI UNTUK REQUEST TTD
        async ajukanQR(formName) {
            this.data.approval[formName].status = 'pending';
            try {
                const response = await fetch('{{ route("mahasiswa.nilai.ajukan_qr") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ jenis_form: formName })
                });
                const result = await response.json();
                
                if (result.status !== 'success') {
                    alert(result.message);
                    this.data.approval[formName].status = 'belum_mengajukan'; 
                } else {
                    window.location.reload(); 
                }
            } catch (error) {
                console.error(error);
                alert('Terjadi kesalahan jaringan saat mengajukan TTD.');
                this.data.approval[formName].status = 'belum_mengajukan';
            }
        },

        // FUNGSI UNTUK MEMBATALKAN TTD DI UPDATE
        async batalkanQR(formName) {
            if(!confirm('Apakah Anda yakin ingin membatalkan pengajuan ini?')) return;
            
            // Ambil token khusus untuk form ini dari data state
            const token = this.data.approval[formName].token;
            
            this.data.approval[formName].status = 'belum_mengajukan';

            try {
                // PANGGIL ROUTE BACKEND UNTUK HAPUS DB
                const response = await fetch('{{ route("mahasiswa.nilai.batal_qr") }}', {
                    method: 'POST', 
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    // Kirim token ke controller
                    body: JSON.stringify({ 
                        jenis_form: formName,
                        token: token 
                    })
                });
                
                const result = await response.json();
                if(result.status !== 'success') {
                    alert(result.message);
                }
                // Refresh agar state bersih dan kembali ke tombol "Ajukan"
                window.location.reload();
                
            } catch (error) {
                console.error('Gagal batal di database', error);
                alert('Terjadi kesalahan saat membatalkan pengajuan.');
                window.location.reload();
            }
        },

        // FUNGSI UNTUK RESET QR YANG SUDAH ACC (UNTUK KEPERLUAN TESTING)
        async resetQR(formName) {
            if(!confirm('Apakah Anda ingin mereset status Verified ini? Data approval akan dihapus agar mahasiswa bisa mengajukan TTD ulang.')) return;
            
            const token = this.data.approval[formName].token;

            try {
                const response = await fetch('{{ route("mahasiswa.nilai.batal_qr") }}', {
                    method: 'POST', 
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ 
                        jenis_form: formName,
                        token: token 
                    })
                });
                
                const result = await response.json();
                if(result.status !== 'success') {
                    alert(result.message || 'Gagal mereset pengajuan.');
                } else {
                    window.location.reload();
                }
            } catch (error) {
                console.error('Gagal reset di database', error);
                alert('Terjadi kesalahan jaringan saat mereset pengajuan.');
                window.location.reload();
            }
        },
        
        getAverage(arr) {
            if (!arr || arr.length === 0) return 0;
            let sum = arr.reduce((total, item) => total + (parseFloat(item.nilai) || 0), 0);
            return Number((sum / arr.length).toFixed(1));
        },

            getAverageLogbook() {
                let validScores = this.data.mentor.logbook.filter(item => item.nilai !== null);
                if (validScores.length === 0) return 0;
                let sum = validScores.reduce((total, item) => total + item.nilai, 0);
                return Number((sum / validScores.length).toFixed(2));
            },

            formatScore(score) {
                return Number(score).toFixed(2);
            },

            getLogbookGrade(score) {
                if (score >= 85) return 'A';
                if (score >= 80) return 'AB';
                if (score >= 70) return 'B';
                if (score >= 65) return 'BC';
                if (score >= 55) return 'C';
                if (score >= 40) return 'D';
                return 'E';
            },
            
            generatePDF() {
                const element = document.getElementById('transkrip-content');
                const opt = {
                    margin:       [0.5, 0.5, 0.5, 0.5],
                    filename:     'Transkrip_Nilai_PPI_' + this.data.nim + '.pdf',
                    image:        { type: 'jpeg', quality: 0.98 },
                    html2canvas:  { scale: 2, useCORS: true },
                    jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
                };
                
                let btn = event.currentTarget;
                let originalText = btn.innerHTML;
                btn.innerHTML = `<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...`;

                const loadScriptAndGenerate = () => {
                    html2pdf().set(opt).from(element).save().then(() => {
                        btn.innerHTML = originalText;
                    });
                };

                if (typeof html2pdf === 'undefined') {
                    let script = document.createElement('script');
                    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
                    script.onload = loadScriptAndGenerate;
                    document.head.appendChild(script);
                } else {
                    loadScriptAndGenerate();
                }
            }
        }
    }
</script>
@endsection