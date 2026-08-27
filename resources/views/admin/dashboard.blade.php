@extends('admin.app')
@section('title', 'Pusat Kendali Administrator')

@section('content')
<div x-data="adminDashboardApp()" class="space-y-8 pb-10">

    <!-- Header Panel -->
    <div class="bg-white p-6 md:p-8 rounded-2xl border-4 border-blue-900 shadow-[8px_8px_0_0_#1e3a8a] flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative overflow-hidden">
        <!-- Dekorasi Latar -->
        <div class="absolute -right-10 -top-10 opacity-10 pointer-events-none">
            <svg class="w-64 h-64 text-blue-900" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 22h20L12 2zm0 4.5l6.5 13.5h-13L12 6.5z"/></svg>
        </div>
        
        <div class="relative z-10">
            <h2 class="text-4xl font-black text-blue-900 tracking-tight uppercase">Pusat Kendali Sistem</h2>
            <p class="text-gray-600 mt-2 font-bold text-lg border-l-4 border-blue-500 pl-3">
                Manajemen pengaturan global PPI, penugasan Dosen, dan batas jam minimum.
            </p>
        </div>
        <div class="relative z-10">
            <a href="{{ route('admin.users') }}" class="bg-yellow-400 text-blue-900 font-black py-3 px-8 rounded-xl border-4 border-blue-900 shadow-[4px_4px_0_0_#1e3a8a] hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all flex items-center gap-3 text-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Kelola Semua Akun
            </a>
        </div>
    </div>

    <!-- PENGATURAN SISTEM (Grid 2 Kolom) -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        
        <!-- BAGIAN KIRI: Setting Jam Minimum (Format Frame Accordion) -->
        <div class="bg-white p-6 md:p-8 rounded-2xl border-4 border-blue-900 shadow-[8px_8px_0_0_#1e3a8a] flex flex-col h-full">
            <div class="flex items-center gap-4 border-b-4 border-gray-100 pb-4 mb-6">
                <div class="bg-blue-100 p-3 rounded-xl border-2 border-blue-300 text-blue-800">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-blue-900">Standar Jam Minimum</h3>
                    <p class="text-sm font-bold text-gray-500 mt-1">Atur jam berdasarkan kurikulum tiap Program Studi.</p>
                </div>
            </div>

            <!-- Area Scrollable untuk Frame Prodi -->
            <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar max-h-[480px] space-y-4">

                <!-- Frame TRIN (Terbuka Default) -->
                <div class="border-4 border-blue-200 rounded-xl bg-blue-50/50 overflow-hidden" x-data="{ expanded: true }">
                    <div @click="expanded = !expanded" class="p-4 flex justify-between items-center cursor-pointer hover:bg-blue-100/70 transition-colors select-none">
                        <h4 class="font-black text-blue-900 text-lg">TRIN (Informatika Industri)</h4>
                        <svg :class="expanded ? 'rotate-180' : ''" class="w-6 h-6 text-blue-800 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                    <!-- Konten Collapse -->
                    <div x-show="expanded" x-collapse.duration.300ms>
                        <div class="p-4 pt-0 border-t-2 border-blue-200 mt-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <template x-for="(subject, idx) in prodiSettings.TRIN" :key="idx">
                                <div class="bg-white p-3 rounded-xl border-2 border-gray-200 focus-within:border-blue-500 transition-colors shadow-sm">
                                    <label class="block text-xs font-black text-gray-700 mb-1" x-text="subject.name"></label>
                                    <div class="flex items-center rounded bg-gray-50 border border-gray-200 overflow-hidden">
                                        <input type="number" x-model.number="subject.hours" class="w-full p-2 font-black text-blue-900 outline-none text-center bg-transparent text-lg">
                                        <span class="bg-gray-200 px-3 py-2 text-xs font-bold text-gray-600 border-l border-gray-300 h-full flex items-center">Jam</span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Frame TRO (Tertutup Default) -->
                <div class="border-4 border-orange-200 rounded-xl bg-orange-50/50 overflow-hidden" x-data="{ expanded: false }">
                    <div @click="expanded = !expanded" class="p-4 flex justify-between items-center cursor-pointer hover:bg-orange-100/70 transition-colors select-none">
                        <h4 class="font-black text-orange-900 text-lg">TRO (Teknik Otomasi)</h4>
                        <svg :class="expanded ? 'rotate-180' : ''" class="w-6 h-6 text-orange-800 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                    <div x-show="expanded" x-collapse.duration.300ms>
                        <div class="p-4 pt-0 border-t-2 border-orange-200 mt-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <template x-for="(subject, idx) in prodiSettings.TRO" :key="idx">
                                <div class="bg-white p-3 rounded-xl border-2 border-gray-200 focus-within:border-orange-500 transition-colors shadow-sm">
                                    <label class="block text-xs font-black text-gray-700 mb-1" x-text="subject.name"></label>
                                    <div class="flex items-center rounded bg-gray-50 border border-gray-200 overflow-hidden">
                                        <input type="number" x-model.number="subject.hours" class="w-full p-2 font-black text-orange-900 outline-none text-center bg-transparent text-lg">
                                        <span class="bg-gray-200 px-3 py-2 text-xs font-bold text-gray-600 border-l border-gray-300 h-full flex items-center">Jam</span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Frame TRMO (Tertutup Default) -->
                <div class="border-4 border-green-200 rounded-xl bg-green-50/50 overflow-hidden" x-data="{ expanded: false }">
                    <div @click="expanded = !expanded" class="p-4 flex justify-between items-center cursor-pointer hover:bg-green-100/70 transition-colors select-none">
                        <h4 class="font-black text-green-900 text-lg">TRMO (Teknik Mekatronika)</h4>
                        <svg :class="expanded ? 'rotate-180' : ''" class="w-6 h-6 text-green-800 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                    <div x-show="expanded" x-collapse.duration.300ms>
                        <div class="p-4 pt-0 border-t-2 border-green-200 mt-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <template x-for="(subject, idx) in prodiSettings.TRMO" :key="idx">
                                <div class="bg-white p-3 rounded-xl border-2 border-gray-200 focus-within:border-green-500 transition-colors shadow-sm">
                                    <label class="block text-xs font-black text-gray-700 mb-1" x-text="subject.name"></label>
                                    <div class="flex items-center rounded bg-gray-50 border border-gray-200 overflow-hidden">
                                        <input type="number" x-model.number="subject.hours" class="w-full p-2 font-black text-green-900 outline-none text-center bg-transparent text-lg">
                                        <span class="bg-gray-200 px-3 py-2 text-xs font-bold text-gray-600 border-l border-gray-300 h-full flex items-center">Jam</span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

            </div>

            <div class="mt-6 pt-6 border-t-4 border-gray-100 flex justify-end">
                <button @click="saveSettings()" class="bg-green-500 text-white font-black py-3 px-8 rounded-xl border-4 border-green-900 shadow-[4px_4px_0_0_#14532d] hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all flex items-center gap-2 text-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    Simpan Perubahan
                </button>
            </div>
        </div>

        <!-- BAGIAN KANAN: Mapping Dosen ke PT -->
        <div class="bg-white p-8 rounded-2xl border-4 border-blue-900 shadow-[8px_8px_0_0_#1e3a8a] flex flex-col h-full">
            <div class="flex items-center gap-4 border-b-4 border-gray-100 pb-4 mb-6">
                <div class="bg-blue-100 p-3 rounded-xl border-2 border-blue-300 text-blue-800">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-blue-900">Jadwal Penugasan Dosen</h3>
                    <p class="text-sm font-bold text-gray-500 mt-1">Tetapkan dosen pembimbing ke lokasi industri.</p>
                </div>
            </div>
            
            <!-- Form Input Jadwal -->
            <form @submit.prevent="addMapping()" class="bg-blue-50 p-5 rounded-xl border-2 border-blue-200 mb-6 space-y-4 shadow-inner">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-black text-blue-900 uppercase mb-1">Pilih Dosen</label>
                        <select x-model="mapForm.dosenId" required class="w-full border-2 border-blue-300 rounded-lg p-2.5 text-sm font-bold outline-none focus:border-blue-600 bg-white">
                            <option value="" disabled selected>-- Pilih Dosen --</option>
                            <option value="3">Supriyadi, S.T., M.T.</option>
                            <option value="4">Ahmad Fakhri, S.T.</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-blue-900 uppercase mb-1">Pilih / Ketik PT</label>
                        <input type="text" x-model="mapForm.ptName" required placeholder="Nama PT..." class="w-full border-2 border-blue-300 rounded-lg p-2.5 text-sm font-bold outline-none focus:border-blue-600 bg-white">
                    </div>
                </div>
                <div class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="w-full md:w-2/3">
                        <label class="block text-xs font-black text-blue-900 uppercase mb-1">Tanggal Kunjungan</label>
                        <input type="date" x-model="mapForm.date" required class="w-full border-2 border-blue-300 rounded-lg p-2.5 text-sm font-bold outline-none focus:border-blue-600 bg-white">
                    </div>
                    <button type="submit" class="w-full md:w-1/3 bg-blue-600 text-white font-black py-3 px-4 rounded-xl border-4 border-blue-900 shadow-[4px_4px_0_0_#1e3a8a] hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all h-[52px]">
                        + Tetapkan
                    </button>
                </div>
            </form>

            <!-- List Jadwal -->
            <div class="flex-1 overflow-y-auto max-h-[300px] pr-2 space-y-3 custom-scrollbar">
                <template x-if="mappings.length === 0">
                    <div class="h-full flex flex-col items-center justify-center text-gray-400 p-8 border-2 border-dashed border-gray-300 rounded-xl">
                        <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="font-bold text-center">Belum ada jadwal yang ditetapkan.</p>
                    </div>
                </template>
                
                <template x-for="(map, idx) in mappings" :key="idx">
                    <div class="bg-white p-4 rounded-xl border-2 border-gray-300 flex justify-between items-center shadow-sm hover:border-blue-500 hover:shadow-md transition-all group">
                        <div class="flex items-start gap-4">
                            <!-- Tanggal Badge -->
                            <div class="bg-blue-100 text-blue-900 rounded-lg p-2 text-center min-w-[60px] border border-blue-200">
                                <span class="block text-xs font-bold uppercase" x-text="getMonthShort(map.date)"></span>
                                <span class="block text-xl font-black leading-none mt-1" x-text="getDay(map.date)"></span>
                            </div>
                            <!-- Detail -->
                            <div>
                                <p class="font-black text-gray-800 text-lg group-hover:text-blue-700 transition-colors" x-text="map.ptName"></p>
                                <div class="flex items-center gap-1 text-gray-500 mt-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    <p class="font-bold text-sm" x-text="map.dosenId == 3 ? 'Supriyadi, S.T., M.T.' : 'Ahmad Fakhri, S.T.'"></p>
                                </div>
                            </div>
                        </div>
                        <button @click="mappings.splice(idx, 1)" class="text-red-500 hover:text-white hover:bg-red-500 font-bold p-2 rounded-lg border-2 border-transparent hover:border-red-700 transition-colors" title="Hapus Jadwal">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </template>
            </div>
        </div>

    </div>
</div>

<style>
    /* Styling scrollbar elegan */
    .custom-scrollbar::-webkit-scrollbar { width: 8px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<script>
    function adminDashboardApp() {
        return {
            // Data Dinamis Mata Kuliah per Prodi
            prodiSettings: {
                TRIN: [
                    { name: 'PII (Perekayasa)', hours: 400 },
                    { name: 'Supervisi (SUP)', hours: 100 },
                    { name: 'Keselamatan Kerja (K3)', hours: 50 },
                    { name: 'Laporan Teknik (LTD)', hours: 50 }
                ],
                TRO: [
                    { name: 'PII (Perekayasa)', hours: 400 },
                    { name: 'Supervisi (SUP)', hours: 100 },
                    { name: 'Keselamatan Kerja (K3)', hours: 50 },
                    { name: 'Laporan Teknik (LTD)', hours: 50 }
                ],
                TRMO: [
                    { name: 'PII (Perekayasa)', hours: 400 },
                    { name: 'Supervisi (SUP)', hours: 100 },
                    { name: 'Keselamatan Kerja (K3)', hours: 50 },
                    { name: 'Laporan Teknik (LTD)', hours: 50 }
                ]
            },

            mappings: [
                { dosenId: 3, ptName: 'PT Solusi Intek Indonesia', date: '2026-08-25' },
                { dosenId: 4, ptName: 'PT Pindad Persero', date: '2026-09-02' }
            ],
            mapForm: { dosenId: '', ptName: '', date: '' },

            addMapping() {
                this.mappings.push({...this.mapForm});
                // Urutkan jadwal berdasarkan tanggal
                this.mappings.sort((a, b) => new Date(a.date) - new Date(b.date));
                this.mapForm = { dosenId: '', ptName: '', date: '' };
            },
            
            saveSettings() { 
                alert('Pengaturan jam minimum untuk TRIN, TRO, dan TRMO berhasil diperbarui!'); 
            },

            getMonthShort(dateStr) {
                if(!dateStr) return '';
                const date = new Date(dateStr);
                return date.toLocaleString('id-ID', { month: 'short' });
            },
            getDay(dateStr) {
                if(!dateStr) return '';
                const date = new Date(dateStr);
                return date.toLocaleString('id-ID', { day: '2-digit' });
            }
        }
    }
</script>
@endsection