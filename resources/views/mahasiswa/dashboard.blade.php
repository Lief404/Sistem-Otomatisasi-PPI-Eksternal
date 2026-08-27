@extends('mahasiswa.app')
@section('title', 'Logbook Mahasiswa')

@section('content')
<!-- Inisialisasi Alpine.js -->
<div x-data="logbookApp()" class="space-y-8 max-w-6xl mx-auto pb-16 font-sans">
    
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
                            <h3 class="text-2xl font-black text-blue-900" x-text="'Minggu ke-' + String(week.id).padStart(2, '0')"></h3>
                            <p class="text-sm font-bold text-gray-500 mt-1">
                                <span x-text="getWeekTotalMinutes(week) > 0 ? (getWeekTotalMinutes(week) / 60).toFixed(1) + ' Jam diselesaikan' : 'Belum ada pengisian data'"></span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 mt-4 md:mt-0 w-full md:w-auto" @click.stop>
                        <div class="flex items-center gap-3 bg-white p-2 rounded-lg border-2 border-gray-300 w-full sm:w-auto">
                            <span class="text-xs font-black text-gray-500 uppercase pl-2">Tanggal:</span>
                            <input type="date" x-model="week.startDate" class="bg-transparent border-none text-sm font-bold text-gray-800 outline-none w-[130px] cursor-pointer">
                            <span class="text-gray-300 font-bold">-</span>
                            <input type="date" x-model="week.endDate" class="bg-transparent border-none text-sm font-bold text-gray-800 outline-none w-[130px] cursor-pointer">
                        </div>
                        <div class="bg-white border-2 border-blue-900 p-2 rounded-lg text-blue-900 shadow-[2px_2px_0_0_#1e3a8a] group-hover:translate-y-px group-hover:translate-x-px group-hover:shadow-none transition-all hidden sm:block cursor-pointer">
                            <svg :class="week.expanded ? 'rotate-180' : ''" class="w-6 h-6 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Isi Minggu (Daftar Hari) -->
                <div x-show="week.expanded" x-collapse.duration.400ms>
                    <div class="p-4 md:p-6 bg-white space-y-4">
                        
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
                                            </div>
                                        </template>

                                        <!-- JIKA STATUS LIBUR/IZIN/SAKIT -->
                                        <template x-if="day.status !== 'Kerja'">
                                            <div class="bg-gray-50 border-2 border-gray-200 p-5 rounded-xl">
                                                <label class="block text-sm font-black text-gray-600 mb-2 uppercase tracking-wide">Keterangan / Alasan (Opsional)</label>
                                                <textarea x-model="day.notes" rows="2" class="w-full bg-white border-2 border-gray-300 rounded-lg p-3 text-sm font-bold text-gray-800 focus:border-blue-600 outline-none resize-none transition-colors" placeholder="Ketik keterangan di sini jika diperlukan..."></textarea>
                                            </div>
                                        </template>

                                        <!-- TOMBOL SIMPAN HARIAN -->
                                        <div class="mt-6 pt-5 border-t-2 border-gray-200 flex justify-end">
                                            <button @click="saveDay(week.id, day.name)" class="bg-green-500 text-white font-black py-2.5 px-8 rounded-lg border-2 border-green-900 shadow-[4px_4px_0_0_#064e3b] hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all duration-200 text-sm flex items-center gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                                Simpan Hari Ini
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
    function logbookApp() {
        return {
            weeks: [],
            
            init() {
                // Inisialisasi struktur data 20 minggu (Senin-Jumat)
                const dayNames = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
                
                for (let i = 1; i <= 20; i++) {
                    let daysArr = [];
                    for(let d = 0; d < 5; d++) {
                        daysArr.push({
                            id: d,
                            name: dayNames[d],
                            status: 'Kerja', // Default: Kerja
                            notes: '',
                            expanded: false,
                            activities: [this.emptyActivity()]
                        });
                    }

                    this.weeks.push({
                        id: i,
                        expanded: i === 1, // Buka minggu ke-1 secara otomatis
                        startDate: '',
                        endDate: '',
                        days: daysArr
                    });
                }
            },

            emptyActivity() {
                return { jamMulai: '', jamSelesai: '', kegiatan: '', kode: 'SUP', waktu: 0 };
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
                    
                    if (diffMinutes < 0) diffMinutes += 24 * 60; // Antisipasi lewat tengah malam
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
                // Asumsi: Minggu dianggap "dikerjakan" jika ada durasi menit > 0
                return this.weeks.filter(week => this.getWeekTotalMinutes(week) > 0).length;
            },

            saveDay(weekId, dayName) {
                alert(`Data Logbook untuk Minggu ${weekId} hari ${dayName} berhasil disimpan!`);
            }
        }
    }
</script>
@endsection