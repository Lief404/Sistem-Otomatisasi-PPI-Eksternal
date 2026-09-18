@extends('mahasiswa.app')
@section('title', 'Logbook Mahasiswa')

@section('content')
<!-- Inisialisasi Alpine.js -->
<div x-data="logbookApp({{ json_encode($logbooks ?? []) }}, '{{ $mahasiswa->nim ?? '' }}', {{ json_encode($fotosByDate ?? []) }}, {{ json_encode($perusahaan ?? null) }}, {{ json_encode($sarans ?? []) }}, {{ json_encode($saranDariMentor ?? null) }})" class="space-y-8 max-w-6xl mx-auto pb-16 font-sans">
    
    <!-- Header Panel -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b-4 border-blue-900 pb-6 bg-white p-6 rounded-2xl shadow-[6px_6px_0_0_#1e3a8a] border-2">
        <div>
            <h2 class="text-3xl md:text-4xl font-black text-blue-900 tracking-tight">Logbook Mingguan</h2>
            <p class="text-gray-600 mt-2 font-bold text-lg">Catat aktivitas harian, jam magang, dan absensi secara terstruktur.</p>
        </div>
        <div class="mt-4 md:mt-0 bg-blue-50 border-2 border-blue-900 px-5 py-3 rounded-xl shadow-[4px_4px_0_0_#1e3a8a] text-sm font-bold flex items-center gap-4 transition-transform hover:-translate-y-1">
            <div class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center border-2 border-blue-900">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <span class="text-gray-500 block text-xs uppercase tracking-wider mb-0.5">Progres Keseluruhan</span>
                <span class="text-blue-900 text-xl font-black" x-text="getTotalCompletedWeeks() + ' / 20 Minggu'"></span>
            </div>
        </div>
    </div>

    <!-- Statistik Rekap Jam (Warna Dinetralkan agar bersih) -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 md:gap-6">
        <div class="bg-blue-900 p-5 rounded-2xl border-2 border-blue-950 shadow-[4px_4px_0_0_#1e3a8a] transform transition hover:-translate-y-1">
            <p class="text-xs font-bold text-blue-200 uppercase tracking-widest">Total Jam Kerja</p>
            <p class="text-3xl font-black text-white mt-2"><span x-text="getTotalHours()"></span><span class="text-base text-blue-300 ml-1">Jam</span></p>
        </div>
        <div class="bg-white p-5 rounded-2xl border-2 border-gray-300 shadow-[4px_4px_0_0_#cbd5e1] transform transition hover:-translate-y-1">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">PII</p>
            <p class="text-3xl font-black text-gray-900 mt-2"><span x-text="getHoursByMatkul('PII')"></span><span class="text-base text-gray-400 ml-1">Jam</span></p>
        </div>
        <div class="bg-white p-5 rounded-2xl border-2 border-gray-300 shadow-[4px_4px_0_0_#cbd5e1] transform transition hover:-translate-y-1">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Supervisi</p>
            <p class="text-3xl font-black text-gray-900 mt-2"><span x-text="getHoursByMatkul('SUP')"></span><span class="text-base text-gray-400 ml-1">Jam</span></p>
        </div>
        <div class="bg-white p-5 rounded-2xl border-2 border-gray-300 shadow-[4px_4px_0_0_#cbd5e1] transform transition hover:-translate-y-1">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">K3 IT</p>
            <p class="text-3xl font-black text-gray-900 mt-2"><span x-text="getHoursByMatkul('K3')"></span><span class="text-base text-gray-400 ml-1">Jam</span></p>
        </div>
        <div class="bg-white p-5 rounded-2xl border-2 border-gray-300 shadow-[4px_4px_0_0_#cbd5e1] transform transition hover:-translate-y-1">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Lap. Teknik</p>
            <p class="text-3xl font-black text-gray-900 mt-2"><span x-text="getHoursByMatkul('LTD')"></span><span class="text-base text-gray-400 ml-1">Jam</span></p>
        </div>
    </div>

    <!-- Area Saran & Masukan untuk Perusahaan -->
    <template x-if="perusahaan">
        <div class="bg-white p-6 md:p-8 rounded-2xl border-4 border-yellow-500 shadow-[6px_6px_0_0_#eab308] relative overflow-hidden transition-all">
            <!-- Dekorasi Latar -->
            <div class="absolute -right-6 -top-6 opacity-10 pointer-events-none">
                <svg class="w-32 h-32 text-yellow-600" fill="currentColor" viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
            </div>
            
            <div class="relative z-10">
                <h3 class="text-2xl font-black text-yellow-700 tracking-tight flex items-center gap-2">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                    Saran & Masukan untuk PT
                </h3>
                <p class="text-gray-600 font-bold mb-6 mt-1 border-l-4 border-yellow-400 pl-3" x-show="sarans.length === 0">
                    Berikan masukan konstruktif Anda untuk <span x-text="perusahaan.nama_perusahaan" class="text-gray-900 font-black"></span>.
                </p>

                <!-- Pesan jika sudah mengisi -->
                <div x-show="sarans.length > 0" class="mb-6 p-4 bg-emerald-50 border-2 border-emerald-400 rounded-xl flex items-start gap-3">
                    <svg class="w-6 h-6 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <h4 class="font-black text-emerald-900">Terima Kasih!</h4>
                        <p class="text-sm font-medium text-emerald-700">Anda sudah memberikan saran dan masukan untuk perusahaan ini.</p>
                    </div>
                </div>
                
                <form x-show="sarans.length === 0" @submit.prevent="submitSaran()" class="space-y-4 bg-yellow-50 p-5 md:p-6 rounded-xl border-2 border-yellow-200">
                    <div class="mb-4">
                        <label class="block text-xs font-black text-yellow-900 uppercase tracking-wider mb-1">Alamat PT</label>
                        <input type="text" :value="perusahaan.alamat" disabled class="w-full border-2 border-yellow-200 rounded-lg p-2.5 font-bold bg-yellow-100/50 text-gray-500 cursor-not-allowed">
                    </div>
                    @if($parameterSaran->isEmpty())
                        <div>
                            <label class="block text-xs font-black text-yellow-900 uppercase tracking-wider mb-1">Isi Saran / Masukan</label>
                            <textarea x-model="saranForm.saran" required rows="3" class="w-full border-2 border-yellow-300 rounded-lg p-3 font-semibold focus:border-yellow-600 outline-none text-gray-800 bg-white" placeholder="Tuliskan saran yang membangun..."></textarea>
                        </div>
                    @else
                        @foreach($parameterSaran as $param)
                            <div class="mb-4">
                                <h4 class="font-bold text-yellow-900 mb-2 border-b-2 border-yellow-200 pb-1">{{ $param->sub_kategori }}</h4>
                                @foreach($param->indikator as $ind)
                                    <div class="mb-3">
                                        <label class="block text-xs font-black text-yellow-900 uppercase tracking-wider mb-1">{{ $ind }}</label>
                                        <textarea x-model="saranForm.answers['{{ addslashes($ind) }}']" required rows="2" class="w-full border-2 border-yellow-300 rounded-lg p-3 font-semibold focus:border-yellow-600 outline-none text-gray-800 bg-white" placeholder="Jawaban Anda..."></textarea>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    @endif
                    <div class="flex justify-end pt-2">
                        <button type="submit" class="bg-yellow-400 text-yellow-950 font-black py-3 px-8 rounded-xl border-4 border-yellow-600 shadow-[4px_4px_0_0_#ca8a04] hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            Kirim Masukan
                        </button>
                    </div>
                </form>

                <!-- Riwayat Saran -->
                <div class="mt-8 pt-6 border-t-2 border-gray-200" x-show="sarans.length > 0">
                    <h4 class="text-sm font-black text-gray-700 mb-4 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Riwayat Masukan Anda
                    </h4>
                    <div class="space-y-4 max-h-64 overflow-y-auto custom-scrollbar pr-2">
                        <template x-for="saran in sarans" :key="saran.id">
                            <div class="bg-white p-4 rounded-xl border-2 border-gray-200 shadow-sm hover:border-yellow-400 transition-colors">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-black bg-yellow-100 text-yellow-800 border border-yellow-300 px-2.5 py-1 rounded-md" x-text="saran.tanggal"></span>
                                </div>
                                <p class="text-gray-700 text-sm font-medium leading-relaxed" x-text="saran.saran"></p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Saran dari Mentor (Jika Ada) -->
        <div x-show="saranMentor" style="display: none;" class="mt-6 bg-white p-6 md:p-8 rounded-2xl border-4 border-emerald-500 shadow-[6px_6px_0_0_#10b981] relative overflow-hidden transition-all">
            <div class="absolute -right-6 -top-6 opacity-10 pointer-events-none">
                <svg class="w-32 h-32 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-1.177-7.86l-2.765-2.767L7 12.431l3.118 3.121a1 1 0 001.414 0l5.952-5.95-1.062-1.062-5.6 5.6z"></path></svg>
            </div>
            <div class="relative z-10">
                <h3 class="text-2xl font-black text-emerald-700 tracking-tight flex items-center gap-2 mb-4">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-.586-1.414l-4.5-4.5A2 2 0 0015.5 3H15m3 15h-3m-3-12h3m-3 0V3m-3 4H5"></path></svg>
                    Saran & Evaluasi dari Mentor
                </h3>
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg">
                    <p class="text-xs font-black text-emerald-800 uppercase tracking-widest mb-2 flex justify-between items-center">
                        <span>Tanggal Diterima</span>
                        <span x-text="saranMentor?.tanggal"></span>
                    </p>
                    <p class="text-gray-800 font-medium whitespace-pre-wrap italic" x-text="saranMentor?.saran"></p>
                </div>
            </div>
        </div>
    </template>

    <!-- HIERARKI DATA: MINGGU -> HARI -> TABEL AKTIVITAS -->
    <div class="space-y-6">
        <template x-for="(week, wIndex) in weeks" :key="week.id">
            
            <!-- LEVEL 1: CARD MINGGU -->
            <div class="bg-white rounded-2xl border-2 border-blue-900 shadow-[6px_6px_0_0_#1e3a8a] overflow-hidden transition-all duration-300">
                
                <!-- Header Minggu -->
                <div class="p-5 md:p-6 bg-gray-50 flex flex-col md:flex-row justify-between items-start md:items-center cursor-pointer hover:bg-blue-50 transition-colors border-b-2 border-blue-100 group" @click="week.expanded = !week.expanded">
                    <div class="flex items-center gap-5">
                        <!-- Icon Indikator -->
                        <div class="w-12 h-12 rounded-xl border-2 border-blue-900 flex items-center justify-center transition-colors shadow-[2px_2px_0_0_#1e3a8a]" :class="getWeekTotalMinutes(week) > 0 ? 'bg-blue-600 text-white' : 'bg-white text-blue-900 group-hover:bg-blue-100'">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-blue-900 flex items-center gap-3">
                                <span x-text="'Minggu ke-' + String(week.id).padStart(2, '0')"></span>
                                <template x-if="week.nilai !== null">
                                    <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-800 text-sm font-bold rounded-lg shadow-sm" x-text="'Nilai: ' + week.nilai"></span>
                                </template>
                            </h3>
                            <p class="text-sm font-bold text-gray-500 mt-1">
                                <span x-text="getWeekTotalMinutes(week) > 0 ? (getWeekTotalMinutes(week) / 60).toFixed(1) + ' Jam diselesaikan' : 'Belum ada pengisian data'"></span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 mt-4 md:mt-0 w-full md:w-auto" @click.stop>
                        <div class="flex items-center gap-3 bg-white p-2 rounded-lg border-2 border-gray-300 w-full sm:w-auto">
                            <span class="text-xs font-black text-gray-500 uppercase pl-2">Tanggal:</span>
                            <input type="date" x-model="week.startDate" @change="onWeekDateChange(week)" class="bg-transparent border-none text-sm font-bold text-gray-800 outline-none w-[130px] cursor-pointer">
                            <span class="text-gray-300 font-bold">-</span>
                            <input type="date" x-model="week.endDate" @change="saveWeekDatesToStorage()" class="bg-transparent border-none text-sm font-bold text-gray-800 outline-none w-[130px] cursor-pointer">
                        </div>
                        <div class="bg-white border-2 border-blue-900 p-2 rounded-lg text-blue-900 shadow-[2px_2px_0_0_#1e3a8a] group-hover:translate-y-px group-hover:translate-x-px group-hover:shadow-none transition-all hidden sm:block cursor-pointer">
                            <svg :class="week.expanded ? 'rotate-180' : ''" class="w-6 h-6 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Isi Minggu (Daftar Hari) -->
                <div x-show="week.expanded" x-collapse.duration.400ms>
                    <div class="p-4 md:p-6 bg-white space-y-4">
                        
                        <!-- Catatan Mentor untuk Minggu ini -->
                        <template x-if="week.catatan_mentor">
                            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg mb-4">
                                <p class="text-xs font-black text-emerald-800 uppercase tracking-widest mb-1 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                                    Catatan Mentor
                                </p>
                                <p class="text-gray-800 text-sm font-medium italic" x-text="week.catatan_mentor"></p>
                            </div>
                        </template>
                        
                        <template x-for="(day, dIndex) in week.days" :key="day.id">
                            
                            <!-- LEVEL 2: CARD HARI -->
                            <div class="border-2 border-gray-200 rounded-xl overflow-hidden transition-all duration-300" :class="day.expanded ? 'border-gray-400 shadow-[2px_2px_0_0_#cbd5e1]' : 'hover:border-gray-300'">
                                
                                <!-- Header Hari -->
                                <div class="px-5 py-4 bg-gray-50 flex flex-col sm:flex-row justify-between items-start sm:items-center cursor-pointer select-none group" @click="day.expanded = !day.expanded">
                                    <div class="flex items-center gap-4">
                                        <div class="p-1 rounded text-gray-400 group-hover:text-gray-800 transition-colors">
                                            <svg :class="day.expanded ? 'rotate-90 text-gray-800' : ''" class="w-6 h-6 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                                        </div>
                                        <h4 class="text-lg font-black text-gray-800 w-20" x-text="day.name"></h4>
                                        
                                        <!-- Indikator Status Neo-Brutal -->
                                        <span class="px-3 py-1 text-xs font-black uppercase tracking-widest rounded border-2" 
                                              :class="{
                                                  'bg-blue-50 text-blue-800 border-blue-200': day.status === 'Kerja',
                                                  'bg-gray-100 text-gray-700 border-gray-300': day.status === 'Libur',
                                                  'bg-yellow-50 text-yellow-800 border-yellow-200': day.status === 'Izin',
                                                  'bg-red-50 text-red-800 border-red-200': day.status === 'Sakit'
                                              }" x-text="day.status"></span>
                                    </div>
                                    
                                    <div class="flex items-center mt-3 sm:mt-0 ml-14 sm:ml-0">
                                        <span class="text-sm font-bold text-gray-500 bg-white border-2 border-gray-200 px-3 py-1.5 rounded-lg">
                                            Total: <span class="text-gray-900 font-black ml-1" x-text="getDayTotalMinutes(day) + ' menit'"></span>
                                        </span>
                                    </div>
                                </div>

                                <!-- Form Hari (Expand) -->
                                <div x-show="day.expanded" x-collapse.duration.300ms>
                                    <div class="p-4 md:p-5 border-t-2 border-gray-200 bg-white">
                                        
                                        <!-- Pilihan Status Hari Ini -->
                                        <div class="flex flex-wrap gap-2 mb-5 bg-gray-50 p-1.5 rounded-lg border-2 border-gray-200 w-fit">
                                            <button @click="day.status = 'Kerja'" :class="day.status === 'Kerja' ? 'bg-blue-600 text-white border-blue-900 shadow-[2px_2px_0_0_#1e3a8a]' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-100'" class="px-4 py-1.5 rounded font-bold border-2 transition-all text-xs uppercase">Kerja</button>
                                            <button @click="day.status = 'Libur'" :class="day.status === 'Libur' ? 'bg-gray-700 text-white border-black shadow-[2px_2px_0_0_#000000]' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-100'" class="px-4 py-1.5 rounded font-bold border-2 transition-all text-xs uppercase">Libur</button>
                                            <button @click="day.status = 'Izin'" :class="day.status === 'Izin' ? 'bg-yellow-400 text-yellow-900 border-yellow-700 shadow-[2px_2px_0_0_#a16207]' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-100'" class="px-4 py-1.5 rounded font-bold border-2 transition-all text-xs uppercase">Izin</button>
                                            <button @click="day.status = 'Sakit'" :class="day.status === 'Sakit' ? 'bg-red-500 text-white border-red-900 shadow-[2px_2px_0_0_#7f1d1d]' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-100'" class="px-4 py-1.5 rounded font-bold border-2 transition-all text-xs uppercase">Sakit</button>
                                        </div>

                                        <!-- JIKA STATUS KERJA (Tabel Logbook Clean & Rapi) -->
                                        <template x-if="day.status === 'Kerja'">
                                            <div>
                                                <div class="overflow-x-auto rounded-xl border-2 border-gray-300 bg-white">
                                                    <table class="w-full text-left text-sm whitespace-nowrap">
                                                        <thead>
                                                            <tr class="bg-gray-100 text-gray-800 border-b-2 border-gray-300">
                                                                <th class="px-4 py-3 font-black w-1/4 text-center border-r-2 border-gray-300">JAM <span class="font-bold text-[10px] text-gray-500 block uppercase mt-0.5 tracking-widest">Mulai - Selesai</span></th>
                                                                <th class="px-4 py-3 font-black w-2/4 border-r-2 border-gray-300">KEGIATAN / DESKRIPSI</th>
                                                                <th class="px-4 py-3 font-black w-1/6 text-center leading-tight border-r-2 border-gray-300">KODE<br>MATKUL</th>
                                                                <th class="px-4 py-3 font-black w-1/6 text-center border-r-2 border-gray-300">WAKTU <span class="font-bold text-[10px] text-gray-500 block uppercase mt-0.5 tracking-widest">(Menit)</span></th>
                                                                <th class="px-2 py-3 w-10"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y-2 divide-gray-100">
                                                            <template x-for="(act, aIndex) in day.activities" :key="aIndex">
                                                                <tr class="hover:bg-blue-50/50 transition-colors group">
                                                                    <!-- Jam Input -->
                                                                    <td class="px-3 py-3 border-r-2 border-gray-100 align-top">
                                                                        <div class="flex items-center justify-center gap-1">
                                                                            <input type="time" x-model="act.jamMulai" @change="calcTime(act)" class="bg-white border-2 border-gray-200 rounded p-1.5 text-sm font-bold text-gray-800 focus:border-blue-600 outline-none w-24 text-center transition-colors shadow-sm">
                                                                            <span class="text-gray-400 font-black">-</span>
                                                                            <input type="time" x-model="act.jamSelesai" @change="calcTime(act)" class="bg-white border-2 border-gray-200 rounded p-1.5 text-sm font-bold text-gray-800 focus:border-blue-600 outline-none w-24 text-center transition-colors shadow-sm">
                                                                        </div>
                                                                    </td>
                                                                    <!-- Kegiatan Input -->
                                                                    <td class="px-3 py-3 border-r-2 border-gray-100 align-top">
                                                                        <textarea x-model="act.kegiatan" rows="2" class="w-full bg-white border-2 border-gray-200 rounded p-2 text-sm font-semibold text-gray-800 focus:border-blue-600 outline-none resize-none transition-colors shadow-sm" placeholder="Ketik deskripsi kegiatan..."></textarea>
                                                                    </td>
                                                                    <!-- Kode Matkul -->
                                                                    <td class="px-3 py-3 border-r-2 border-gray-100 align-top text-center pt-4">
                                                                        <select x-model="act.kode" class="bg-white border-2 border-gray-200 rounded p-1.5 text-sm font-black text-gray-800 text-center focus:border-blue-600 outline-none w-full max-w-[80px] appearance-none cursor-pointer shadow-sm transition-all mx-auto block">
                                                                            <option value="SUP">SUP</option>
                                                                            <option value="PII">PII</option>
                                                                            <option value="K3">K3</option>
                                                                            <option value="LTD">LTD</option>
                                                                        </select>
                                                                    </td>
                                                                    <!-- Waktu Terkalkulasi -->
                                                                    <td class="px-4 py-3 border-r-2 border-gray-100 align-top text-center pt-5">
                                                                        <span class="font-mono font-black text-lg text-gray-800" x-text="act.waktu + ' \''"></span>
                                                                    </td>
                                                                    <!-- Aksi Hapus -->
                                                                    <td class="px-2 py-3 align-top pt-4 text-center">
                                                                        <button @click="removeActivity(day, aIndex)" x-show="day.activities.length > 1" class="text-gray-400 hover:text-red-600 transition-colors p-1.5 hover:bg-red-50 rounded" title="Hapus baris">
                                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            </template>
                                                            
                                                            <!-- Footer Total Harian (Bersih) -->
                                                            <tr class="bg-gray-100 border-t-2 border-gray-300">
                                                                <td colspan="3" class="px-4 py-3 text-right font-black text-gray-700 uppercase tracking-widest text-xs border-r-2 border-gray-300">JUMLAH MENIT</td>
                                                                <td class="px-4 py-3 text-center border-r-2 border-gray-300">
                                                                    <span class="font-mono font-black text-gray-900 text-lg" x-text="getDayTotalMinutes(day) + ' \''"></span>
                                                                </td>
                                                                <td></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                
                                                <!-- Tombol Tambah Baris -->
                                                <div class="mt-4">
                                                    <button @click="addActivity(day)" class="text-sm font-bold text-gray-600 bg-white hover:text-blue-700 hover:border-blue-400 border-2 border-dashed border-gray-300 px-4 py-2 rounded-lg transition-all flex items-center gap-2 w-full justify-center">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                                                        Tambah Baris Kegiatan Baru
                                                    </button>
                                                </div>

                                                <!-- === AREA MULTI UPLOAD FOTO DOKUMENTASI === -->
                                                <div class="mt-5 border-t-2 border-dashed border-blue-100 pt-4">
                                                    <div class="flex items-center justify-between mb-3">
                                                        <p class="text-xs font-black text-blue-700 uppercase tracking-widest flex items-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                            Foto Dokumentasi Kegiatan
                                                        </p>
                                                        <span class="text-[10px] font-bold text-blue-400 bg-blue-50 px-2 py-1 rounded-full border border-blue-200" x-text="day.fotos.length + ' foto tersimpan'"></span>
                                                    </div>

                                                    <!-- Grid thumbnail tersimpan -->
                                                    <template x-if="day.fotos.length > 0">
                                                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2 mb-3">
                                                            <template x-for="(foto, fIdx) in day.fotos" :key="foto.id">
                                                                <div class="relative group">
                                                                    <a :href="foto.url" target="_blank">
                                                                        <img :src="foto.url" :alt="foto.filename"
                                                                             class="w-full aspect-square object-cover rounded-lg border-2 border-blue-200 group-hover:opacity-75 transition-opacity shadow-sm">
                                                                    </a>
                                                                    <!-- Badge nomor -->
                                                                    <span class="absolute top-1 left-1 bg-blue-600 text-white text-[10px] font-black px-1.5 py-0.5 rounded leading-none" x-text="fIdx+1"></span>
                                                                    <!-- Tombol hapus -->
                                                                    <button @click.prevent="deleteFotoById(day, foto.id)"
                                                                            class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow"
                                                                            title="Hapus foto ini">
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                                    </button>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>

                                                    <!-- Preview file baru yang dipilih (belum upload) -->
                                                    <template x-if="day.fotoPreviews.length > 0">
                                                        <div class="mb-3">
                                                            <p class="text-[10px] font-black text-yellow-600 uppercase tracking-widest mb-2">📎 Antrian upload (<span x-text="day.fotoPreviews.length"></span> foto baru — belum disimpan)</p>
                                                            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">
                                                                <template x-for="(prev, pIdx) in day.fotoPreviews" :key="pIdx">
                                                                    <div class="relative group">
                                                                        <img :src="prev.dataUrl" :alt="prev.name"
                                                                             class="w-full aspect-square object-cover rounded-lg border-2 border-yellow-300 shadow-sm">
                                                                        <button @click.prevent="removeFotoPreviewByIndex(day, pIdx, 'foto-kerja-' + week.id + '-' + day.id)"
                                                                                class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow">
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                                        </button>
                                                                        <span class="absolute bottom-1 left-1 bg-yellow-400 text-yellow-900 text-[9px] font-black px-1 rounded leading-tight">BARU</span>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </template>

                                                    <!-- Input upload multi-foto -->
                                                    <label :for="'foto-kerja-' + week.id + '-' + day.id"
                                                           class="flex flex-col items-center justify-center gap-1.5 border-2 border-dashed rounded-xl px-4 py-4 cursor-pointer transition-all"
                                                           :class="day.fotoPreviews.length > 0 ? 'border-yellow-300 bg-yellow-50 hover:bg-yellow-100' : 'border-gray-300 bg-gray-50 hover:border-blue-400 hover:bg-blue-50'">
                                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                                        <span class="text-xs font-bold text-gray-500">Klik atau seret foto ke sini</span>
                                                        <span class="text-[10px] text-gray-400">Bisa pilih banyak sekaligus · JPG/PNG/WEBP/GIF · Maks 5MB/foto</span>
                                                        <input :id="'foto-kerja-' + week.id + '-' + day.id" type="file" accept="image/*" multiple class="hidden" @change="onFotosSelected($event, day)">
                                                    </label>
                                                </div>
                                                <!-- === END MULTI UPLOAD FOTO === -->
                                            </div>
                                        </template>

                                        <!-- JIKA STATUS LIBUR/IZIN/SAKIT -->
                                        <template x-if="day.status !== 'Kerja'">
                                            <div class="bg-gray-50 border-2 border-gray-200 p-5 rounded-xl space-y-4">
                                                <div>
                                                    <label class="block text-sm font-black text-gray-600 mb-2 uppercase tracking-wide">Keterangan / Alasan (Opsional)</label>
                                                    <textarea x-model="day.notes" rows="2" class="w-full bg-white border-2 border-gray-300 rounded-lg p-3 text-sm font-bold text-gray-800 focus:border-blue-600 outline-none resize-none transition-colors" placeholder="Ketik keterangan di sini jika diperlukan..."></textarea>
                                                </div>

                                                <!-- === AREA MULTI UPLOAD FOTO (Libur/Izin/Sakit) === -->
                                                <div class="border-t-2 border-dashed border-gray-200 pt-4">
                                                    <div class="flex items-center justify-between mb-3">
                                                        <p class="text-xs font-black text-gray-500 uppercase tracking-widest flex items-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                            Foto Bukti / Dokumentasi (Opsional)
                                                        </p>
                                                        <span class="text-[10px] font-bold text-gray-400 bg-gray-100 px-2 py-1 rounded-full border border-gray-200" x-text="day.fotos.length + ' foto tersimpan'"></span>
                                                    </div>

                                                    <!-- Grid thumbnail tersimpan -->
                                                    <template x-if="day.fotos.length > 0">
                                                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2 mb-3">
                                                            <template x-for="(foto, fIdx) in day.fotos" :key="foto.id">
                                                                <div class="relative group">
                                                                    <a :href="foto.url" target="_blank">
                                                                        <img :src="foto.url" :alt="foto.filename"
                                                                             class="w-full aspect-square object-cover rounded-lg border-2 border-gray-300 group-hover:opacity-75 transition-opacity shadow-sm">
                                                                    </a>
                                                                    <span class="absolute top-1 left-1 bg-gray-700 text-white text-[10px] font-black px-1.5 py-0.5 rounded leading-none" x-text="fIdx+1"></span>
                                                                    <button @click.prevent="deleteFotoById(day, foto.id)"
                                                                            class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow">
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                                    </button>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>

                                                    <!-- Preview file baru -->
                                                    <template x-if="day.fotoPreviews.length > 0">
                                                        <div class="mb-3">
                                                            <p class="text-[10px] font-black text-yellow-600 uppercase tracking-widest mb-2">📎 Antrian upload (<span x-text="day.fotoPreviews.length"></span> foto baru — belum disimpan)</p>
                                                            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">
                                                                <template x-for="(prev, pIdx) in day.fotoPreviews" :key="pIdx">
                                                                    <div class="relative group">
                                                                        <img :src="prev.dataUrl" :alt="prev.name"
                                                                             class="w-full aspect-square object-cover rounded-lg border-2 border-yellow-300 shadow-sm">
                                                                        <button @click.prevent="removeFotoPreviewByIndex(day, pIdx, 'foto-abs-' + week.id + '-' + day.id)"
                                                                                class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow">
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                                        </button>
                                                                        <span class="absolute bottom-1 left-1 bg-yellow-400 text-yellow-900 text-[9px] font-black px-1 rounded leading-tight">BARU</span>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </template>

                                                    <!-- Input upload multi-foto -->
                                                    <label :for="'foto-abs-' + week.id + '-' + day.id"
                                                           class="flex flex-col items-center justify-center gap-1.5 border-2 border-dashed rounded-xl px-4 py-4 cursor-pointer transition-all"
                                                           :class="day.fotoPreviews.length > 0 ? 'border-yellow-300 bg-yellow-50 hover:bg-yellow-100' : 'border-gray-300 bg-white hover:border-gray-400 hover:bg-gray-50'">
                                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                                        <span class="text-xs font-bold text-gray-500">Klik atau seret foto ke sini</span>
                                                        <span class="text-[10px] text-gray-400">Bisa pilih banyak sekaligus · JPG/PNG/WEBP/GIF · Maks 5MB/foto</span>
                                                        <input :id="'foto-abs-' + week.id + '-' + day.id" type="file" accept="image/*" multiple class="hidden" @change="onFotosSelected($event, day)">
                                                    </label>
                                                </div>
                                                <!-- === END MULTI UPLOAD FOTO === -->
                                            </div>
                                        </template>

                                        <!-- TOMBOL SIMPAN HARIAN -->
                                        <div class="mt-6 pt-5 border-t-2 border-gray-200 flex justify-end">
                                            <button @click="saveDay(week, day, dIndex)" :disabled="day.uploadingFoto"
                                                class="bg-green-500 text-white font-black py-2.5 px-8 rounded-lg border-2 border-green-900 shadow-[4px_4px_0_0_#064e3b] hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all duration-200 text-sm flex items-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed disabled:translate-x-0 disabled:translate-y-0 disabled:shadow-[4px_4px_0_0_#064e3b]">
                                                <template x-if="!day.uploadingFoto">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                                </template>
                                                <template x-if="day.uploadingFoto">
                                                    <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                                </template>
                                                <span x-text="day.uploadingFoto ? 'Mengupload...' : 'Simpan Hari Ini'"></span>
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </template>

                    </div>
                </div>
            </div>

        </template>
    </div>

</div>

<script>
    function logbookApp(existingLogbooks, nimStr, existingFotosByDate, perusahaanData, saranData, saranMentorData) {
        return {
            currentNim: nimStr,
            weeks: [],
            perusahaan: perusahaanData,
            sarans: saranData || [],
            saranMentor: saranMentorData || null,
            saranForm: {
                tanggal: new Date().toISOString().split('T')[0],
                saran: '',
                answers: {}
            },

            addDays(dateStr, days) {
                if (!dateStr) return '';
                let parts = dateStr.split('-');
                if (parts.length !== 3) return '';
                let dt = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
                dt.setDate(dt.getDate() + days);
                let y = dt.getFullYear();
                let m = String(dt.getMonth() + 1).padStart(2, '0');
                let day = String(dt.getDate()).padStart(2, '0');
                return `${y}-${m}-${day}`;
            },

            saveWeekDatesToStorage() {
                if (!this.currentNim) return;
                let dates = {};
                this.weeks.forEach(w => {
                    if (w.startDate || w.endDate) {
                        dates[w.id] = { startDate: w.startDate, endDate: w.endDate };
                    }
                });
                try {
                    localStorage.setItem('logbook_week_dates_' + this.currentNim, JSON.stringify(dates));
                } catch(e) {}
            },

            onWeekDateChange(week) {
                if (week.startDate) {
                    week.endDate = this.addDays(week.startDate, 4);
                }
                this.saveWeekDatesToStorage();
            },
            
            init() {
                const dayNames = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
                
                let savedWeekDates = {};
                if (this.currentNim) {
                    try {
                        savedWeekDates = JSON.parse(localStorage.getItem('logbook_week_dates_' + this.currentNim) || '{}');
                    } catch(e) {}
                }

                // Group logbook entries by date (YYYY-MM-DD) and by minggu_ke
                const logbooksByDate = {};
                const weekStartFromLogbook = {};
                const weekDataFromLogbook = {};

                if (Array.isArray(existingLogbooks)) {
                    existingLogbooks.forEach(item => {
                        if (item.tanggal) {
                            if (!logbooksByDate[item.tanggal]) {
                                logbooksByDate[item.tanggal] = [];
                            }
                            logbooksByDate[item.tanggal].push(item);

                            if (item.minggu_ke) {
                                if (!weekStartFromLogbook[item.minggu_ke] || item.tanggal < weekStartFromLogbook[item.minggu_ke]) {
                                    weekStartFromLogbook[item.minggu_ke] = item.tanggal;
                                }
                                
                                if (!weekDataFromLogbook[item.minggu_ke]) {
                                    weekDataFromLogbook[item.minggu_ke] = { nilai: null, catatan_mentor: null };
                                }
                                if (item.nilai !== null && item.nilai !== undefined) {
                                    weekDataFromLogbook[item.minggu_ke].nilai = item.nilai;
                                }
                                if (item.catatan_mentor) {
                                    weekDataFromLogbook[item.minggu_ke].catatan_mentor = item.catatan_mentor;
                                }
                            }
                        }
                    });
                }

                for (let i = 1; i <= 20; i++) {
                    let weekStartDate = savedWeekDates[i]?.startDate || weekStartFromLogbook[i] || '';
                    let weekEndDate = savedWeekDates[i]?.endDate || (weekStartDate ? this.addDays(weekStartDate, 4) : '');

                    let daysArr = [];
                    for (let d = 0; d < 5; d++) {
                        let dayDateStr = weekStartDate ? this.addDays(weekStartDate, d) : '';
                        let dayStatus = 'Kerja';
                        let dayNotes = '';
                        let dayActivities = [];

                        if (dayDateStr && logbooksByDate[dayDateStr]) {
                            let entries = logbooksByDate[dayDateStr];
                            if (entries.length > 0) {
                                dayStatus = entries[0].status || 'Kerja';
                                
                                // FIX: Jika status di database adalah 'Dinilai' (diupdate oleh mentor),
                                // kita tentukan status aslinya agar UI mahasiswa tetap bisa menampilkan data Kerja/Libur.
                                if (dayStatus === 'Dinilai') {
                                    if (entries[0].jam_mulai !== null || (entries[0].durasi_mnt && entries[0].durasi_mnt > 0)) {
                                        dayStatus = 'Kerja';
                                    } else {
                                        dayStatus = 'Libur';
                                    }
                                }

                                if (dayStatus !== 'Kerja') {
                                    dayNotes = entries[0].kegiatan || '';
                                } else {
                                    entries.forEach(e => {
                                        if ((e.durasi_mnt && e.durasi_mnt > 0) || e.kegiatan) {
                                            dayActivities.push({
                                                jamMulai: e.jam_mulai || '',
                                                jamSelesai: e.jam_selesai || '',
                                                kegiatan: e.kegiatan || '',
                                                kode: e.kd_mat || 'SUP',
                                                waktu: e.durasi_mnt || 0
                                            });
                                        }
                                    });
                                }
                            }
                        }

                        if (dayActivities.length === 0) {
                            dayActivities.push(this.emptyActivity());
                        }

                        // Ambil fotos (array) untuk hari ini dari data server
                        let dayFotos = [];
                        if (dayDateStr && existingFotosByDate[dayDateStr]) {
                            dayFotos = existingFotosByDate[dayDateStr];
                        }

                        daysArr.push({
                            id: d,
                            name: dayNames[d],
                            status: dayStatus,
                            notes: dayNotes,
                            expanded: false,
                            activities: dayActivities,
                            fotos: dayFotos,          // foto tersimpan di DB
                            fotoPreviews: [],          // preview file baru (belum upload)
                            fotoFiles: [],             // File objects baru
                            uploadingFoto: false,
                        });
                    }

                    let hasData = daysArr.some(day => day.status !== 'Kerja' || (day.notes && day.notes.trim() !== '') || day.activities.some(a => a.waktu > 0 || (a.kegiatan && a.kegiatan.trim() !== '')));

                    this.weeks.push({
                        id: i,
                        expanded: i === 1 || hasData,
                        startDate: weekStartDate,
                        endDate: weekEndDate,
                        nilai: weekDataFromLogbook[i] ? weekDataFromLogbook[i].nilai : null,
                        catatan_mentor: weekDataFromLogbook[i] ? weekDataFromLogbook[i].catatan_mentor : null,
                        days: daysArr
                    });
                }
            },

            emptyActivity() {
                return { jamMulai: '', jamSelesai: '', kegiatan: '', kode: 'SUP', waktu: 0 };
            },

            // Saat user memilih file (bisa banyak sekaligus)
            onFotosSelected(event, day) {
                const files = Array.from(event.target.files);
                const invalid = files.filter(f => f.size > 5 * 1024 * 1024);
                if (invalid.length > 0) {
                    alert(`${invalid.length} file melebihi batas 5MB dan akan diabaikan:\n` + invalid.map(f => f.name).join('\n'));
                }
                const valid = files.filter(f => f.size <= 5 * 1024 * 1024);
                valid.forEach(file => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        day.fotoPreviews.push({ dataUrl: e.target.result, name: file.name });
                        day.fotoFiles.push(file);
                    };
                    reader.readAsDataURL(file);
                });
                event.target.value = ''; // reset agar bisa pilih file yang sama lagi
            },

            // Hapus satu preview dari antrian
            removeFotoPreviewByIndex(day, index, inputId) {
                day.fotoPreviews.splice(index, 1);
                day.fotoFiles.splice(index, 1);
            },

            // Hapus 1 foto tersimpan berdasarkan ID
            async deleteFotoById(day, fotoId) {
                if (!confirm('Hapus foto ini?')) return;
                try {
                    let response = await fetch(`/mahasiswa/logbook/foto/${fotoId}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    let result = await response.json();
                    if (response.ok) {
                        day.fotos = day.fotos.filter(f => f.id !== fotoId);
                    } else {
                        alert('Gagal hapus foto: ' + (result.message || 'Error'));
                    }
                } catch(e) {
                    alert('Gagal hapus foto: ' + e.message);
                }
            },

            addActivity(day) {
                day.activities.push(this.emptyActivity());
            },

            removeActivity(day, index) {
                day.activities.splice(index, 1);
            },

            calcTime(act) {
                if (act.jamMulai && act.jamSelesai) {
                    let start = new Date(`1970-01-01T${act.jamMulai}:00`);
                    let end = new Date(`1970-01-01T${act.jamSelesai}:00`);
                    let diffMinutes = (end - start) / 1000 / 60; 
                    
                    if (diffMinutes < 0) diffMinutes += 24 * 60;
                    act.waktu = Math.round(diffMinutes);
                } else {
                    act.waktu = 0;
                }
            },

            getDayTotalMinutes(day) {
                if (day.status !== 'Kerja') return 0;
                return day.activities.reduce((sum, act) => sum + (act.waktu || 0), 0);
            },

            getWeekTotalMinutes(week) {
                return week.days.reduce((sum, day) => sum + this.getDayTotalMinutes(day), 0);
            },

            getTotalHours() {
                let totalMinutes = 0;
                this.weeks.forEach(week => {
                    totalMinutes += this.getWeekTotalMinutes(week);
                });
                return (totalMinutes / 60).toFixed(1);
            },

            getHoursByMatkul(kode) {
                let totalMinutes = 0;
                this.weeks.forEach(week => {
                    week.days.forEach(day => {
                        if (day.status === 'Kerja') {
                            day.activities.forEach(act => {
                                if (act.kode === kode) totalMinutes += (act.waktu || 0);
                            });
                        }
                    });
                });
                return (totalMinutes / 60).toFixed(1);
            },

            getTotalCompletedWeeks() {
                return this.weeks.filter(week => this.getWeekTotalMinutes(week) > 0).length;
            },

            async saveDay(week, day, dIndex) {
                if (!week.startDate) {
                    alert('Mohon isi tanggal mulai minggu terlebih dahulu (Tanggal di header minggu).');
                    return;
                }

                let formattedDate = this.addDays(week.startDate, dIndex);
                if (!formattedDate) {
                    alert('Format tanggal mulai minggu tidak valid.');
                    return;
                }

                // Gunakan FormData agar bisa mengirim file
                let formData = new FormData();
                formData.append('tanggal', formattedDate);
                formData.append('status', day.status);
                formData.append('kegiatan', day.notes || '');
                formData.append('minggu_ke', week.id);
                formData.append('_token', '{{ csrf_token() }}');

                // Kirim activities sebagai JSON string
                if (day.status === 'Kerja') {
                    let activitiesData = day.activities.map(a => ({
                        kode: a.kode,
                        kegiatan: a.kegiatan || '-',
                        waktu: a.waktu,
                        jamMulai: a.jamMulai,
                        jamSelesai: a.jamSelesai
                    }));
                    formData.append('activities_json', JSON.stringify(activitiesData));
                } else {
                    formData.append('activities_json', JSON.stringify([]));
                }

                // Lampirkan semua foto baru yang dipilih
                day.fotoFiles.forEach(file => {
                    formData.append('fotos[]', file);
                });

                day.uploadingFoto = true;

                try {
                    let response = await fetch('{{ route("mahasiswa.logbook.store") }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            // Jangan set Content-Type — browser otomatis set multipart boundary
                        },
                        body: formData
                    });

                    let result;
                    try {
                        result = await response.json();
                    } catch (e) {
                        alert('Gagal menyimpan: Server mengembalikan respons tidak terduga (kode ' + response.status + '). Coba refresh halaman.');
                        day.uploadingFoto = false;
                        return;
                    }

                    if (response.ok) {
                        this.saveWeekDatesToStorage();
                        // Tambahkan foto baru ke array fotos hari ini
                        if (result.new_fotos && result.new_fotos.length > 0) {
                            day.fotos = day.fotos.concat(result.new_fotos);
                        }
                        // Bersihkan antrian
                        day.fotoPreviews = [];
                        day.fotoFiles = [];
                        const msg = result.new_fotos && result.new_fotos.length > 0
                            ? ` (+${result.new_fotos.length} foto berhasil diupload)`
                            : '';
                        alert(`Logbook Minggu ke-${week.id} hari ${day.name} (${formattedDate}) berhasil disimpan!${msg}`);
                    } else {
                        let errMsg = result.message || 'Terjadi kesalahan';
                        if (result.errors) {
                            errMsg += ': ' + Object.values(result.errors).flat().join(', ');
                        }
                        alert('Gagal menyimpan: ' + errMsg);
                        console.error(result);
                    }
                } catch (error) {
                    alert('Gagal menyimpan data: ' + error.message);
                    console.error(error);
                } finally {
                    day.uploadingFoto = false;
                }
            },

            async submitSaran() {
                let finalSaran = '';
                if (Object.keys(this.saranForm.answers).length > 0) {
                    finalSaran = JSON.stringify(this.saranForm.answers);
                } else {
                    finalSaran = this.saranForm.saran;
                    if (!finalSaran.trim()) return;
                }
                
                try {
                    let payload = {
                        tanggal: this.saranForm.tanggal,
                        saran: finalSaran,
                        perusahaan: this.perusahaan.nama_perusahaan
                    };

                    let response = await fetch('{{ route("mahasiswa.saran.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(payload)
                    });

                    let result = await response.json();

                    if (response.ok) {
                        alert(result.message);
                        location.reload();
                    } else {
                        let errMsg = result.message || 'Terjadi kesalahan';
                        if (result.errors) {
                            errMsg += ': ' + Object.values(result.errors).flat().join(', ');
                        }
                        alert('Gagal mengirim saran: ' + errMsg);
                    }
                } catch (error) {
                    alert('Gagal mengirim saran: ' + error.message);
                }
            }
        }
    }
</script>
@endsection