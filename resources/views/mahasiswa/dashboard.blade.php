@extends('mahasiswa.app')
@section('title', 'Logbook Mahasiswa')

@section('content')
<!-- Inisialisasi Alpine.js -->
<div x-data="logbookApp()" class="space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b-4 border-blue-900 pb-4">
        <div>
            <h2 class="text-3xl font-black text-blue-900 tracking-wide">Logbook Mingguan</h2>
            <p class="text-gray-600 mt-1 font-medium">Catat aktivitas, jam lembur, dan absensi Anda secara terstruktur.</p>
        </div>
        <div class="mt-4 md:mt-0 bg-white border-2 border-blue-900 px-4 py-2 rounded-lg shadow-[4px_4px_0_0_#1e3a8a] text-sm font-bold flex items-center gap-3">
            <span class="text-gray-600">Progres Keseluruhan:</span>
            <span class="text-blue-700 bg-blue-100 px-2 py-1 rounded" x-text="getTotalCompletedWeeks() + ' / 20 Minggu'"></span>
        </div>
    </div>

    <!-- Statistik Rekap Jam -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white p-4 rounded-xl border-2 border-blue-900 shadow-[4px_4px_0_0_#1e3a8a]">
            <p class="text-xs font-bold text-gray-500 uppercase">Total Jam Kerja</p>
            <p class="text-3xl font-black text-blue-700 mt-1" x-text="getTotalHours() + ' Jam'"></p>
        </div>
        <div class="bg-yellow-50 p-4 rounded-xl border-2 border-blue-900 shadow-[4px_4px_0_0_#1e3a8a]">
            <p class="text-xs font-bold text-yellow-800 uppercase">PII</p>
            <p class="text-2xl font-black text-blue-900 mt-1" x-text="getHoursByMatkul('PII') + ' Jam'"></p>
        </div>
        <div class="bg-green-50 p-4 rounded-xl border-2 border-blue-900 shadow-[4px_4px_0_0_#1e3a8a]">
            <p class="text-xs font-bold text-green-800 uppercase">Supervisi</p>
            <p class="text-2xl font-black text-blue-900 mt-1" x-text="getHoursByMatkul('SUP') + ' Jam'"></p>
        </div>
        <div class="bg-red-50 p-4 rounded-xl border-2 border-blue-900 shadow-[4px_4px_0_0_#1e3a8a]">
            <p class="text-xs font-bold text-red-800 uppercase">K3 IT</p>
            <p class="text-2xl font-black text-blue-900 mt-1" x-text="getHoursByMatkul('K3') + ' Jam'"></p>
        </div>
        <div class="bg-purple-50 p-4 rounded-xl border-2 border-blue-900 shadow-[4px_4px_0_0_#1e3a8a]">
            <p class="text-xs font-bold text-purple-800 uppercase">Lap. Teknik</p>
            <p class="text-2xl font-black text-blue-900 mt-1" x-text="getHoursByMatkul('LTD') + ' Jam'"></p>
        </div>
    </div>

    <!-- Tombol Tambah -->
    <div class="pt-4">
        <button @click="showForm = !showForm" class="bg-blue-600 text-white font-bold py-3 px-6 rounded-lg border-2 border-blue-900 shadow-[4px_4px_0_0_#1e3a8a] hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all duration-200 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
            <span x-text="showForm ? 'Tutup Form' : 'Tambah Logbook Baru'"></span>
        </button>
    </div>

    <!-- PANEL FORM DINAMIS (Tersembunyi secara default) -->
    <div x-show="showForm" x-collapse>
        <div class="bg-white p-6 rounded-xl border-2 border-blue-900 shadow-[6px_6px_0_0_#1e3a8a] mb-8">
            <div class="flex gap-4 border-b-2 border-gray-200 pb-4 mb-4">
                <button @click="formType = 'kerja'" :class="formType === 'kerja' ? 'bg-blue-100 border-blue-900 text-blue-900 shadow-[2px_2px_0_0_#1e3a8a]' : 'bg-gray-50 border-gray-300 text-gray-500 hover:bg-gray-100'" class="px-6 py-2 rounded font-bold border-2 transition-all">Aktivitas & Lembur</button>
                <button @click="formType = 'izin'" :class="formType === 'izin' ? 'bg-red-100 border-red-900 text-red-900 shadow-[2px_2px_0_0_#7f1d1d]' : 'bg-gray-50 border-gray-300 text-gray-500 hover:bg-gray-100'" class="px-6 py-2 rounded font-bold border-2 transition-all">Izin / Sakit</button>
            </div>

            <form @submit.prevent="saveForm()" class="space-y-6">
                <!-- Pemilihan Tanggal Global Form -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg border-2 border-gray-200">
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Pilih Minggu</label>
                        <select x-model.number="form.minggu" required class="mt-1 w-full border-2 border-gray-300 rounded-lg p-2 focus:border-blue-600 outline-none font-bold text-blue-900">
                            <template x-for="w in 20" :key="w"><option :value="w" x-text="'Minggu ke-' + w"></option></template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Tanggal Kegiatan</label>
                        <input type="date" x-model="form.tanggal" required class="mt-1 w-full border-2 border-gray-300 rounded-lg p-2 focus:border-blue-600 outline-none font-bold">
                    </div>
                </div>

                <!-- JIKA PILIH KERJA (BISA MULTI-INPUT) -->
                <template x-if="formType === 'kerja'">
                    <div class="space-y-4">
                        <template x-for="(act, index) in form.activities" :key="index">
                            <div class="relative bg-white p-4 rounded-lg border-2 border-blue-200">
                                <!-- Tombol Hapus Baris -->
                                <button type="button" @click="removeActivityRow(index)" x-show="form.activities.length > 1" class="absolute top-2 right-2 text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                                
                                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                                    <div class="md:col-span-1">
                                        <label class="block text-xs font-bold text-gray-700">Tipe Jam</label>
                                        <select x-model="act.tipeKerja" class="mt-1 w-full border-2 border-gray-300 rounded p-2 focus:border-blue-600 outline-none font-bold text-sm">
                                            <option value="Reguler">Reguler</option>
                                            <option value="Lembur">Lembur</option>
                                        </select>
                                    </div>
                                    <div class="md:col-span-1">
                                        <label class="block text-xs font-bold text-gray-700">Mulai</label>
                                        <input type="time" x-model="act.jamMulai" required @change="calcRowDuration(act)" class="mt-1 w-full border-2 border-gray-300 rounded p-2 focus:border-blue-600 outline-none font-bold text-sm">
                                    </div>
                                    <div class="md:col-span-1">
                                        <label class="block text-xs font-bold text-gray-700">Selesai</label>
                                        <input type="time" x-model="act.jamSelesai" required @change="calcRowDuration(act)" class="mt-1 w-full border-2 border-gray-300 rounded p-2 focus:border-blue-600 outline-none font-bold text-sm">
                                    </div>
                                    <div class="md:col-span-1">
                                        <label class="block text-xs font-bold text-gray-700">Matkul</label>
                                        <select x-model="act.matkul" required class="mt-1 w-full border-2 border-gray-300 rounded p-2 focus:border-blue-600 outline-none font-bold text-sm">
                                            <option value="" disabled>Pilih</option>
                                            <option value="PII">PII</option>
                                            <option value="SUP">SUP</option>
                                            <option value="K3">K3</option>
                                            <option value="LTD">LTD</option>
                                        </select>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-bold text-gray-700">Kegiatan <span class="text-blue-600 ml-2" x-text="act.durasi + ' Jam'"></span></label>
                                        <input type="text" x-model="act.deskripsi" required class="mt-1 w-full border-2 border-gray-300 rounded p-2 focus:border-blue-600 outline-none font-medium text-sm" placeholder="Contoh: Maintenance server...">
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Tombol Tambah Baris -->
                        <button type="button" @click="addActivityRow()" class="text-sm font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1 border-2 border-dashed border-blue-300 rounded-lg px-4 py-2 w-full justify-center bg-blue-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Kegiatan Lain di Hari yang Sama
                        </button>
                    </div>
                </template>

                <!-- JIKA PILIH IZIN/SAKIT -->
                <template x-if="formType === 'izin'">
                    <div class="space-y-4 bg-red-50 p-4 rounded-lg border-2 border-red-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700">Kategori</label>
                                <select x-model="form.kategoriIzin" class="mt-1 w-full border-2 border-gray-300 rounded-lg p-2 focus:border-red-600 outline-none font-bold text-red-800">
                                    <option value="Sakit">Sakit</option>
                                    <option value="Izin">Izin (Keperluan Keluarga/Kampus)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700">Lama Absen</label>
                                <select x-model="form.durasiIzin" class="mt-1 w-full border-2 border-gray-300 rounded-lg p-2 focus:border-red-600 outline-none font-bold">
                                    <option value="Sehari Penuh">Sehari Penuh</option>
                                    <option value="Setengah Hari">Setengah Hari</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Keterangan / Alasan</label>
                            <input type="text" x-model="form.alasanIzin" class="mt-1 w-full border-2 border-gray-300 rounded-lg p-2 focus:border-red-600 outline-none font-medium" placeholder="Tuliskan alasan lengkapnya di sini...">
                        </div>
                    </div>
                </template>

                <div class="flex justify-end pt-2 border-t-2 border-gray-200">
                    <button type="submit" class="bg-green-500 text-white font-bold py-2 px-8 rounded-lg border-2 border-green-900 shadow-[4px_4px_0_0_#14532d] hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all active:bg-green-600">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- HIERARKI DATA: MINGGU -> HARI -> TABEL AKTIVITAS -->
    <div class="space-y-6">
        <template x-for="week in weeksConfig" :key="week.num">
            
            <!-- LEVEL 1: MINGGU -->
            <div class="bg-white rounded-xl border-2 border-blue-900 shadow-[6px_6px_0_0_#1e3a8a] overflow-hidden" x-data="{ weekExpanded: false }">
                <!-- Header Minggu -->
                <div class="p-4 bg-gray-50 flex flex-col md:flex-row justify-between items-start md:items-center cursor-pointer hover:bg-blue-50 transition-colors border-b-2 border-blue-100" @click="weekExpanded = !weekExpanded">
                    <div class="flex items-center gap-4">
                        <div class="w-5 h-5 rounded-full border-2 border-gray-800 shadow-sm" :class="isWeekComplete(week.num) ? 'bg-blue-500' : 'bg-orange-400'"></div>
                        <div>
                            <h3 class="text-xl font-black text-blue-900" x-text="'Minggu ' + week.num"></h3>
                            <p class="text-sm font-bold text-gray-500" x-text="getDaysFilled(week.num) + ' dari ' + week.target + ' Hari Terisi'"></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 mt-2 md:mt-0" @click.stop>
                        <div class="flex items-center gap-2">
                            <label class="text-xs font-bold text-gray-600 uppercase">Target Hari:</label>
                            <select x-model.number="week.target" class="border-2 border-gray-300 rounded p-1 text-sm font-bold outline-none cursor-pointer">
                                <option value="5">5 Hari</option>
                                <option value="6">6 Hari</option>
                                <option value="7">7 Hari</option>
                            </select>
                        </div>
                        <svg :class="weekExpanded ? 'rotate-180' : ''" class="w-6 h-6 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>

                <!-- Isi Minggu -->
                <div x-show="weekExpanded" x-collapse class="p-4 bg-blue-50/30">
                    <template x-if="getGroupedByDate(week.num).length === 0">
                        <div class="text-center py-6 text-gray-400 font-bold border-2 border-dashed border-gray-300 rounded-lg bg-white">
                            Belum ada pengisian di minggu ini.
                        </div>
                    </template>

                    <!-- LEVEL 2: GROUP PER HARI -->
                    <div class="space-y-4">
                        <template x-for="dayGroup in getGroupedByDate(week.num)" :key="dayGroup.tanggal">
                            <div class="border-2 border-blue-900 rounded-lg overflow-hidden bg-white shadow-sm" x-data="{ dayExpanded: true }">
                                
                                <!-- Header Hari -->
                                <div class="bg-blue-100 p-3 flex justify-between items-center cursor-pointer hover:bg-blue-200 transition-colors" @click="dayExpanded = !dayExpanded">
                                    <div class="flex items-center gap-2">
                                        <svg :class="dayExpanded ? 'rotate-90' : ''" class="w-5 h-5 text-blue-800 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        <span class="font-black text-blue-900" x-text="formatDate(dayGroup.tanggal)"></span>
                                    </div>
                                    <span class="font-black text-blue-700 bg-white px-2 py-1 rounded text-xs border border-blue-300" x-text="'Total: ' + dayGroup.totalDurasi.toFixed(1) + ' Jam'"></span>
                                </div>

                                <!-- LEVEL 3: TABEL DATA HARIAN -->
                                <div x-show="dayExpanded" x-collapse class="overflow-x-auto">
                                    <table class="w-full text-left border-collapse text-sm">
                                        <thead>
                                            <tr class="bg-gray-50 border-b-2 border-blue-200 text-gray-600">
                                                <th class="p-3 font-bold w-1/6">Waktu</th>
                                                <th class="p-3 font-bold w-1/6">Tipe / Matkul</th>
                                                <th class="p-3 font-bold w-2/6">Deskripsi Kegiatan</th>
                                                <th class="p-3 font-bold w-1/6 text-center">Durasi</th>
                                                <th class="p-3 font-bold w-1/6 text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="entry in dayGroup.items" :key="entry.id">
                                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                                    
                                                    <!-- JIKA MODE EDIT -->
                                                    <template x-if="entry.isEditing">
                                                        <td colspan="5" class="p-2 bg-yellow-50">
                                                            <div class="flex flex-wrap gap-2 items-center">
                                                                <input type="time" x-model="entry.jamMulai" @change="recalcEntry(entry)" class="border border-gray-400 p-1 rounded text-xs w-20">
                                                                <span>-</span>
                                                                <input type="time" x-model="entry.jamSelesai" @change="recalcEntry(entry)" class="border border-gray-400 p-1 rounded text-xs w-20">
                                                                <select x-model="entry.tipeKerja" class="border border-gray-400 p-1 rounded text-xs"><option value="Reguler">Reguler</option><option value="Lembur">Lembur</option></select>
                                                                <select x-model="entry.matkul" class="border border-gray-400 p-1 rounded text-xs"><option value="PII">PII</option><option value="SUP">SUP</option><option value="K3">K3</option><option value="LTD">LTD</option></select>
                                                                <input type="text" x-model="entry.deskripsi" class="border border-gray-400 p-1 rounded text-xs flex-1">
                                                                <button @click="entry.isEditing = false" class="bg-green-500 text-white px-3 py-1 rounded font-bold text-xs">Simpan</button>
                                                            </div>
                                                        </td>
                                                    </template>

                                                    <!-- JIKA MODE VIEW -->
                                                    <template x-if="!entry.isEditing">
                                                        <!-- Kolom 1: Waktu -->
                                                        <td class="p-3 font-semibold text-gray-700 whitespace-nowrap">
                                                            <template x-if="entry.type === 'kerja'">
                                                                <span x-text="entry.jamMulai + ' - ' + entry.jamSelesai"></span>
                                                            </template>
                                                            <template x-if="entry.type === 'izin'">
                                                                <span class="text-red-500" x-text="entry.durasiIzin"></span>
                                                            </template>
                                                        </td>
                                                    </template>

                                                    <template x-if="!entry.isEditing">
                                                        <!-- Kolom 2: Tipe & Matkul -->
                                                        <td class="p-3">
                                                            <template x-if="entry.type === 'kerja'">
                                                                <div>
                                                                    <span :class="entry.tipeKerja === 'Lembur' ? 'bg-orange-100 text-orange-800 border-orange-300' : 'bg-gray-100 text-gray-700 border-gray-300'" class="px-2 py-0.5 rounded text-xs font-bold border block w-fit mb-1" x-text="entry.tipeKerja"></span>
                                                                    <span class="bg-blue-900 text-white px-2 py-0.5 rounded text-xs font-bold" x-text="entry.matkul"></span>
                                                                </div>
                                                            </template>
                                                            <template x-if="entry.type === 'izin'">
                                                                <span class="bg-red-600 text-white px-2 py-0.5 rounded text-xs font-bold" x-text="entry.kategoriIzin"></span>
                                                            </template>
                                                        </td>
                                                    </template>

                                                    <template x-if="!entry.isEditing">
                                                        <!-- Kolom 3: Deskripsi -->
                                                        <td class="p-3 text-gray-800 font-medium">
                                                            <span x-text="entry.type === 'kerja' ? entry.deskripsi : entry.alasanIzin"></span>
                                                        </td>
                                                    </template>

                                                    <template x-if="!entry.isEditing">
                                                        <!-- Kolom 4: Durasi -->
                                                        <td class="p-3 text-center">
                                                            <span class="font-black text-blue-700" x-text="entry.type === 'kerja' ? entry.durasiTotal + ' J' : '-'"></span>
                                                        </td>
                                                    </template>

                                                    <template x-if="!entry.isEditing">
                                                        <!-- Kolom 5: Aksi -->
                                                        <td class="p-3 text-center space-x-2">
                                                            <template x-if="entry.type === 'kerja'">
                                                                <button @click="entry.isEditing = true" class="text-blue-500 hover:text-blue-700">
                                                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                                </button>
                                                            </template>
                                                            <button @click="deleteEntry(entry.id)" class="text-red-500 hover:text-red-700">
                                                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                            </button>
                                                        </td>
                                                    </template>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

<!-- Logika JS Alpine -->
<script>
    function logbookApp() {
        return {
            showForm: false,
            formType: 'kerja',
            entries: [], 
            entryIdCounter: 1,
            
            weeksConfig: Array.from({length: 20}, (_, i) => ({ num: i + 1, target: 5 })),

            form: {
                minggu: 1,
                tanggal: '',
                activities: [
                    { tipeKerja: 'Reguler', jamMulai: '', jamSelesai: '', matkul: '', deskripsi: '', durasi: 0 }
                ],
                kategoriIzin: 'Sakit', durasiIzin: 'Sehari Penuh', alasanIzin: ''
            },
            
            addActivityRow() {
                this.form.activities.push({ tipeKerja: 'Reguler', jamMulai: '', jamSelesai: '', matkul: '', deskripsi: '', durasi: 0 });
            },

            removeActivityRow(index) {
                this.form.activities.splice(index, 1);
            },

            calcRowDuration(row) {
                if (row.jamMulai && row.jamSelesai) {
                    let start = new Date(`1970-01-01T${row.jamMulai}:00`);
                    let end = new Date(`1970-01-01T${row.jamSelesai}:00`);
                    let diff = (end - start) / 1000 / 60 / 60; 
                    if(diff < 0) diff += 24; 
                    row.durasi = diff.toFixed(1);
                }
            },

            recalcEntry(entry) {
                if (entry.jamMulai && entry.jamSelesai) {
                    let start = new Date(`1970-01-01T${entry.jamMulai}:00`);
                    let end = new Date(`1970-01-01T${entry.jamSelesai}:00`);
                    let diff = (end - start) / 1000 / 60 / 60; 
                    if(diff < 0) diff += 24; 
                    entry.durasiTotal = diff.toFixed(1);
                }
            },

            saveForm() {
                if(!this.form.tanggal) { alert("Tanggal wajib diisi!"); return; }

                if (this.formType === 'kerja') {
                    this.form.activities.forEach(act => {
                        if(act.durasi > 0 && act.matkul && act.deskripsi) {
                            this.entries.push({
                                id: this.entryIdCounter++,
                                minggu: this.form.minggu,
                                tanggal: this.form.tanggal,
                                type: 'kerja',
                                tipeKerja: act.tipeKerja,
                                jamMulai: act.jamMulai,
                                jamSelesai: act.jamSelesai,
                                matkul: act.matkul,
                                deskripsi: act.deskripsi,
                                durasiTotal: act.durasi,
                                isEditing: false
                            });
                        }
                    });
                } else {
                    this.entries.push({
                        id: this.entryIdCounter++,
                        minggu: this.form.minggu,
                        tanggal: this.form.tanggal,
                        type: 'izin',
                        kategoriIzin: this.form.kategoriIzin,
                        durasiIzin: this.form.durasiIzin,
                        alasanIzin: this.form.alasanIzin,
                        durasiTotal: 0,
                        isEditing: false
                    });
                }
                
                // Reset Form state
                this.form.tanggal = '';
                this.form.activities = [{ tipeKerja: 'Reguler', jamMulai: '', jamSelesai: '', matkul: '', deskripsi: '', durasi: 0 }];
                this.form.alasanIzin = '';
                this.showForm = false;
            },

            deleteEntry(id) {
                if(confirm("Hapus baris kegiatan ini?")) {
                    this.entries = this.entries.filter(e => e.id !== id);
                }
            },

            // --- FUNGSI PENGELOMPOKAN --- //
            getGroupedByDate(weekNum) {
                let weekEntries = this.entries.filter(e => e.minggu === weekNum).sort((a,b) => new Date(a.tanggal) - new Date(b.tanggal));
                let grouped = {};
                weekEntries.forEach(e => {
                    if(!grouped[e.tanggal]) grouped[e.tanggal] = { tanggal: e.tanggal, items: [], totalDurasi: 0 };
                    grouped[e.tanggal].items.push(e);
                    grouped[e.tanggal].totalDurasi += parseFloat(e.durasiTotal || 0);
                });
                return Object.values(grouped);
            },

            getDaysFilled(weekNum) {
                let weekEntries = this.entries.filter(e => e.minggu === weekNum);
                let uniqueDates = new Set(weekEntries.map(e => e.tanggal));
                return uniqueDates.size;
            },

            isWeekComplete(weekNum) {
                let target = this.weeksConfig.find(w => w.num === weekNum).target;
                return this.getDaysFilled(weekNum) >= target;
            },

            getTotalCompletedWeeks() {
                return this.weeksConfig.filter(w => this.isWeekComplete(w.num)).length;
            },

            getTotalHours() {
                return this.entries.reduce((total, entry) => total + parseFloat(entry.durasiTotal || 0), 0).toFixed(1);
            },

            getHoursByMatkul(matkulCode) {
                return this.entries
                    .filter(e => e.type === 'kerja' && e.matkul === matkulCode)
                    .reduce((total, entry) => total + parseFloat(entry.durasiTotal), 0).toFixed(1);
            },

            formatDate(dateString) {
                if(!dateString) return '';
                const options = { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' };
                return new Date(dateString).toLocaleDateString('id-ID', options);
            }
        }
    }
</script>
@endsection