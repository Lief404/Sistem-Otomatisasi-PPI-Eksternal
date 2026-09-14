@extends('mentor.app')
@section('title', 'Dashboard Mentor Industri')

@section('content')
<div x-data="mentorApp({{ json_encode($mahasiswas ?? []) }})" class="space-y-6">

    <!-- Header Panel -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b-4 border-blue-900 pb-4">
        <div>
            <h2 class="text-3xl font-black text-blue-900 tracking-wide">Validasi & Penilaian Industri</h2>
            <p class="text-gray-600 mt-1 font-medium">Evaluasi kinerja, kedisiplinan, dan hardskill/softskill mahasiswa magang.</p>
        </div>
    </div>

    <!-- Statistik Mentor -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-blue-100 p-6 rounded-xl border-2 border-blue-900 shadow-[4px_4px_0_0_#1e3a8a]">
            <h3 class="text-lg font-bold text-blue-900">Mahasiswa Aktif</h3>
            <p class="text-4xl font-black text-blue-700 mt-2" x-text="students.length"></p>
        </div>
        <div class="bg-orange-100 p-6 rounded-xl border-2 border-orange-900 shadow-[4px_4px_0_0_#7c2d12]">
            <h3 class="text-lg font-bold text-orange-900">Menunggu Validasi</h3>
            <p class="text-4xl font-black text-orange-700 mt-2" x-text="students.filter(s => s.status === 'Pending').length"></p>
        </div>
        <div class="bg-green-100 p-6 rounded-xl border-2 border-green-900 shadow-[4px_4px_0_0_#14532d]">
            <h3 class="text-lg font-bold text-green-900">Selesai Evaluasi</h3>
            <p class="text-4xl font-black text-green-700 mt-2" x-text="students.filter(s => s.status === 'Selesai').length"></p>
        </div>
    </div>

    <!-- Daftar Mahasiswa (Accordion Style) -->
    <div class="bg-white rounded-xl border-2 border-blue-900 shadow-[6px_6px_0_0_#1e3a8a] mt-8 overflow-hidden">
        <div class="bg-blue-50 p-4 border-b-2 border-blue-900">
            <h3 class="text-xl font-black text-blue-900">Daftar Mahasiswa di Perusahaan Anda</h3>
        </div>
        
        <div class="divide-y-2 divide-blue-100">
            <template x-for="student in students" :key="student.id">
                <div class="bg-white transition-colors" x-data="{ expanded: false }">
                    <!-- Header Baris (Clickable) -->
                    <div @click="expanded = !expanded" class="p-4 cursor-pointer hover:bg-blue-50 flex justify-between items-center">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-blue-200 border-2 border-blue-900 rounded-lg flex items-center justify-center font-black text-blue-900 text-xl shadow-[2px_2px_0_0_#1e3a8a]">
                                <span x-text="student.name.charAt(0)"></span>
                            </div>
                            <div>
                                <h4 class="text-xl font-black text-blue-900" x-text="student.name"></h4>
                                <p class="text-sm font-bold text-gray-500" x-text="student.nim + ' | ' + student.kelas"></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="px-3 py-1 rounded text-xs font-bold border-2" 
                                  :class="student.status === 'Selesai' ? 'bg-green-100 text-green-800 border-green-300' : 'bg-orange-100 text-orange-800 border-orange-300'" 
                                  x-text="student.status"></span>
                            <svg :class="expanded ? 'rotate-180' : ''" class="w-6 h-6 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>

                    <!-- Panel Aksi Tersembunyi -->
                    <div x-show="expanded" x-collapse class="p-4 bg-gray-50 border-t-2 border-blue-100 flex flex-col md:flex-row gap-4 justify-center">
                        <button @click="openModal('logbook', student)" class="flex-1 bg-white border-2 border-blue-900 p-4 rounded-xl shadow-[4px_4px_0_0_#1e3a8a] hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all group">
                            <h5 class="font-black text-blue-900 text-lg group-hover:text-blue-600">Validasi Logbook</h5>
                            <p class="text-xs font-bold text-gray-500 mt-1">Cek deskripsi kegiatan & Beri Nilai</p>
                        </button>
                        <button @click="openModal('disiplin', student)" class="flex-1 bg-white border-2 border-blue-900 p-4 rounded-xl shadow-[4px_4px_0_0_#1e3a8a] hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all group">
                            <h5 class="font-black text-blue-900 text-lg group-hover:text-blue-600">Disiplin & Prestasi</h5>
                            <p class="text-xs font-bold text-gray-500 mt-1">Evaluasi kerajinan & supervisi kerja</p>
                        </button>
                        <button @click="openModal('kuisioner', student)" class="flex-1 bg-white border-2 border-blue-900 p-4 rounded-xl shadow-[4px_4px_0_0_#1e3a8a] hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all group">
                            <h5 class="font-black text-blue-900 text-lg group-hover:text-blue-600">Form Kuisioner</h5>
                            <p class="text-xs font-bold text-gray-500 mt-1">Penilaian Hardskill & Softskill (1-4)</p>
                        </button>
                        <button @click="openModal('saran', student)" class="flex-1 bg-white border-2 border-yellow-500 p-4 rounded-xl shadow-[4px_4px_0_0_#eab308] hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all group">
                            <h5 class="font-black text-yellow-700 text-lg group-hover:text-yellow-600">Saran Mahasiswa</h5>
                            <p class="text-xs font-bold text-gray-500 mt-1">Berikan masukan & saran ke mahasiswa</p>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- MODAL GLOBAL -->
    <div x-show="activeModal !== null" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="activeModal !== null" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="closeModal()"></div>

            <div x-show="activeModal !== null" x-transition.scale class="relative inline-block bg-white text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle max-w-5xl w-full border-4 border-blue-900 rounded-xl flex flex-col max-h-[90vh]">
                
                <!-- Modal Header -->
                <div class="bg-blue-900 px-6 py-4 flex justify-between items-center text-white flex-shrink-0">
                    <h3 class="text-xl md:text-2xl font-black" x-text="getModalTitle()"></h3>
                    <button @click="closeModal()" class="hover:text-red-400 transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="p-6 bg-gray-50 flex-1 overflow-y-auto">
                    
                    <!-- Info Mahasiswa -->
                    <div class="mb-6 bg-white p-4 rounded-lg border-2 border-blue-200 flex flex-wrap gap-6">
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase">Nama Mahasiswa</p>
                            <p class="font-black text-blue-900 text-lg" x-text="selectedStudent?.name"></p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase">NIM / Kelas</p>
                            <p class="font-black text-blue-900 text-lg" x-text="selectedStudent?.nim + ' / ' + selectedStudent?.kelas"></p>
                        </div>
                    </div>

                    <!-- KONTEN 1: LOGBOOK (REVISI: BISA BACA DESKRIPSI) -->
                    <template x-if="activeModal === 'logbook'">
                        <div class="bg-white p-6 rounded-xl border-2 border-blue-900 shadow-[4px_4px_0_0_#1e3a8a]">
                            <div class="flex justify-between items-center border-b-2 border-gray-100 pb-2 mb-4">
                                <p class="text-sm font-bold text-gray-600">Periksa deskripsi kegiatan mahasiswa (pastikan tidak melanggar NDA) lalu berikan nilai (0-100).</p>
                            </div>
                            
                            <div class="space-y-4">
                                <!-- Looping 20 Minggu -->
                                <template x-for="w in 20" :key="w">
                                    <div class="border-2 border-blue-200 rounded-lg overflow-hidden bg-white" x-data="{ weekExpanded: false }">
                                        
                                        <!-- Header Minggu (Klik untuk expand tabel) -->
                                        <div class="bg-blue-50 p-3 flex justify-between items-center cursor-pointer hover:bg-blue-100 transition-colors" @click="weekExpanded = !weekExpanded">
                                            <div class="flex items-center gap-3">
                                                <svg :class="weekExpanded ? 'rotate-90' : ''" class="w-5 h-5 text-blue-800 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                                <span class="font-black text-blue-900" x-text="'Minggu ke-' + w"></span>
                                                <!-- Indikator jika ada isinya -->
                                                <template x-if="hasLogbookData(w)">
                                                    <span class="bg-blue-600 text-white text-[10px] px-2 py-0.5 rounded-full font-bold ml-2">Ada Data</span>
                                                </template>
                                            </div>
                                            
                                            <!-- Input Nilai Mingguan -->
                                            <div class="flex items-center gap-3" @click.stop>
                                                <label class="text-sm font-bold text-gray-700">Nilai:</label>
                                                <input type="number" min="0" max="100" placeholder="0" x-model.number="formLogbook[w-1]" class="w-16 border-2 border-blue-300 rounded p-1 text-center font-bold outline-none focus:border-blue-600 text-blue-700">
                                            </div>
                                        </div>
                                        
                                        <!-- Tabel Detail Kegiatan Harian -->
                                        <div x-show="weekExpanded" x-collapse class="border-t-2 border-blue-100">
                                            <template x-if="!hasLogbookData(w)">
                                                <div class="p-4 text-center text-gray-400 font-bold text-sm italic">
                                                    Mahasiswa belum mengisi logbook pada minggu ini.
                                                </div>
                                            </template>
                                            
                                            <template x-if="hasLogbookData(w)">
                                                <div class="overflow-x-auto p-4">
                                                    <table class="w-full text-left text-sm border-collapse">
                                                        <thead>
                                                            <tr class="border-b-2 border-gray-200 text-gray-600">
                                                                <th class="pb-2 font-bold w-1/5">Tanggal</th>
                                                                <th class="pb-2 font-bold w-1/5">Mata Kuliah</th>
                                                                <th class="pb-2 font-bold w-2/5">Deskripsi Pekerjaan</th>
                                                                <th class="pb-2 font-bold w-1/5 text-center">Durasi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <template x-for="entry in getLogbookData(w)" :key="entry.id">
                                                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                                                    <td class="py-3 font-semibold text-gray-700" x-text="formatDate(entry.tanggal)"></td>
                                                                    <td class="py-3">
                                                                        <span class="bg-blue-900 text-white px-2 py-1 rounded text-xs font-bold" x-text="entry.matkul"></span>
                                                                    </td>
                                                                    <!-- DESKRIPSI KEGIATAN -->
                                                                    <td class="py-3 text-gray-800 font-medium leading-relaxed pr-4" x-text="entry.deskripsi"></td>
                                                                    <td class="py-3 text-center font-black text-blue-700" x-text="entry.durasiTotal + ' J'"></td>
                                                                </tr>
                                                            </template>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </template>
                                        </div>

                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- KONTEN 2: DISIPLIN & PRESTASI -->
                    <template x-if="activeModal === 'disiplin'">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Prestasi -->
                            <div class="bg-white p-5 rounded-xl border-2 border-blue-900 shadow-[4px_4px_0_0_#1e3a8a]">
                                <div class="flex justify-between items-center mb-4 border-b-2 border-blue-100 pb-2">
                                    <h4 class="font-black text-blue-900 text-lg">Penilaian Prestasi</h4>
                                    <span class="bg-blue-100 text-blue-800 font-bold px-3 py-1 rounded border border-blue-300" x-text="'Total: ' + totalPrestasi"></span>
                                </div>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center"><label class="text-sm font-bold text-gray-700 w-2/3">1. Kerajinan</label><input type="number" min="0" max="100" x-model.number="formDisiplin.p1" class="w-20 border-2 border-gray-300 rounded p-1 text-center font-bold"></div>
                                    <div class="flex justify-between items-center"><label class="text-sm font-bold text-gray-700 w-2/3">2. Kesungguhan Kerja</label><input type="number" min="0" max="100" x-model.number="formDisiplin.p2" class="w-20 border-2 border-gray-300 rounded p-1 text-center font-bold"></div>
                                    <div class="flex justify-between items-center"><label class="text-sm font-bold text-gray-700 w-2/3">3. Kecakapan</label><input type="number" min="0" max="100" x-model.number="formDisiplin.p3" class="w-20 border-2 border-gray-300 rounded p-1 text-center font-bold"></div>
                                    <div class="flex justify-between items-center"><label class="text-sm font-bold text-gray-700 w-2/3">4. Kemandirian</label><input type="number" min="0" max="100" x-model.number="formDisiplin.p4" class="w-20 border-2 border-gray-300 rounded p-1 text-center font-bold"></div>
                                    <div class="flex justify-between items-center"><label class="text-sm font-bold text-gray-700 w-2/3">5. Inisiatif</label><input type="number" min="0" max="100" x-model.number="formDisiplin.p5" class="w-20 border-2 border-gray-300 rounded p-1 text-center font-bold"></div>
                                </div>
                            </div>
                            <!-- Supervisi -->
                            <div class="bg-white p-5 rounded-xl border-2 border-blue-900 shadow-[4px_4px_0_0_#1e3a8a]">
                                <div class="flex justify-between items-center mb-4 border-b-2 border-blue-100 pb-2">
                                    <h4 class="font-black text-blue-900 text-lg">Penilaian Supervisi</h4>
                                    <span class="bg-blue-100 text-blue-800 font-bold px-3 py-1 rounded border border-blue-300" x-text="'Total: ' + totalSupervisi"></span>
                                </div>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center"><label class="text-sm font-bold text-gray-700 w-2/3">3. Kecakapan (Kemampuan)</label><input type="number" min="0" max="100" x-model.number="formDisiplin.s3" class="w-20 border-2 border-gray-300 rounded p-1 text-center font-bold"></div>
                                    <div class="flex justify-between items-center"><label class="text-sm font-bold text-gray-700 w-2/3">5. Inisiatif</label><input type="number" min="0" max="100" x-model.number="formDisiplin.s5" class="w-20 border-2 border-gray-300 rounded p-1 text-center font-bold"></div>
                                    <div class="flex justify-between items-center"><label class="text-sm font-bold text-gray-700 w-2/3">6. Kepemimpinan</label><input type="number" min="0" max="100" x-model.number="formDisiplin.s6" class="w-20 border-2 border-gray-300 rounded p-1 text-center font-bold"></div>
                                    <div class="flex justify-between items-center"><label class="text-sm font-bold text-gray-700 w-2/3">7. Komunikasi</label><input type="number" min="0" max="100" x-model.number="formDisiplin.s7" class="w-20 border-2 border-gray-300 rounded p-1 text-center font-bold"></div>
                                    <div class="flex justify-between items-center"><label class="text-sm font-bold text-gray-700 w-2/3">8. Kerjasama</label><input type="number" min="0" max="100" x-model.number="formDisiplin.s8" class="w-20 border-2 border-gray-300 rounded p-1 text-center font-bold"></div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- KONTEN 3: KUISIONER -->
                    <template x-if="activeModal === 'kuisioner'">
                        <div class="bg-white p-5 rounded-xl border-2 border-blue-900 shadow-[4px_4px_0_0_#1e3a8a] space-y-8">
                            <div class="flex flex-wrap items-center gap-4 border-b-2 border-gray-200 pb-2">
                                <span class="bg-blue-100 text-blue-800 font-bold px-3 py-1 rounded text-xs border border-blue-300">4: Sangat Baik</span>
                                <span class="bg-blue-100 text-blue-800 font-bold px-3 py-1 rounded text-xs border border-blue-300">3: Baik</span>
                                <span class="bg-blue-100 text-blue-800 font-bold px-3 py-1 rounded text-xs border border-blue-300">2: Cukup</span>
                                <span class="bg-blue-100 text-blue-800 font-bold px-3 py-1 rounded text-xs border border-blue-300">1: Kurang</span>
                            </div>

                            <!-- Hardskill -->
                            <div>
                                <h4 class="font-black text-blue-900 text-lg mb-4 bg-gray-100 p-2 rounded">HARDSKILL</h4>
                                <div class="space-y-4">
                                    <div>
                                        <p class="font-bold text-gray-700 mb-2 text-sm">1. Keahlian pada kompetensi utama:</p>
                                        <div class="pl-4 space-y-2">
                                            <div class="flex justify-between items-center bg-gray-50 p-2 rounded border border-gray-200"><span class="text-sm font-bold text-gray-600">Informatika / Pemrograman</span><select x-model.number="formKuisioner.h1a" class="border-2 border-gray-300 rounded p-1 font-bold"><option value="0">-</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option></select></div>
                                            <div class="flex justify-between items-center bg-gray-50 p-2 rounded border border-gray-200"><span class="text-sm font-bold text-gray-600">Keamanan Jaringan</span><select x-model.number="formKuisioner.h1b" class="border-2 border-gray-300 rounded p-1 font-bold"><option value="0">-</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option></select></div>
                                            <div class="flex justify-between items-center bg-gray-50 p-2 rounded border border-gray-200"><span class="text-sm font-bold text-gray-600">Kontrol (Mikrokontroler, PLC)</span><select x-model.number="formKuisioner.h1c" class="border-2 border-gray-300 rounded p-1 font-bold"><option value="0">-</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option></select></div>
                                            <div class="flex justify-between items-center bg-gray-50 p-2 rounded border border-gray-200"><span class="text-sm font-bold text-gray-600">Listrik dan Elektronika</span><select x-model.number="formKuisioner.h1d" class="border-2 border-gray-300 rounded p-1 font-bold"><option value="0">-</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option></select></div>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center bg-gray-50 p-2 rounded border border-gray-200"><span class="text-sm font-bold text-gray-700">2. Kemampuan komunikasi global (B. Inggris)</span><select x-model.number="formKuisioner.h2" class="border-2 border-gray-300 rounded p-1 font-bold"><option value="0">-</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option></select></div>
                                    <div class="flex justify-between items-center bg-gray-50 p-2 rounded border border-gray-200"><span class="text-sm font-bold text-gray-700">3. Kemampuan penggunaan TI (Software Aplikasi)</span><select x-model.number="formKuisioner.h3" class="border-2 border-gray-300 rounded p-1 font-bold"><option value="0">-</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option></select></div>
                                    <div class="flex justify-between items-center bg-gray-50 p-2 rounded border border-gray-200"><span class="text-sm font-bold text-gray-700">4. Kemampuan merawat/memakai alat dengan benar</span><select x-model.number="formKuisioner.h4" class="border-2 border-gray-300 rounded p-1 font-bold"><option value="0">-</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option></select></div>
                                </div>
                            </div>

                            <!-- Softskill -->
                            <div>
                                <h4 class="font-black text-blue-900 text-lg mb-4 bg-gray-100 p-2 rounded">SOFTSKILL</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex justify-between items-center bg-gray-50 p-2 rounded border border-gray-200"><span class="text-sm font-bold text-gray-700">1. Integritas (Etika/Moral)</span><select x-model.number="formKuisioner.s1" class="border-2 border-gray-300 rounded p-1 font-bold"><option value="0">-</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option></select></div>
                                    <div class="flex justify-between items-center bg-gray-50 p-2 rounded border border-gray-200"><span class="text-sm font-bold text-gray-700">2. Kemampuan berkomunikasi</span><select x-model.number="formKuisioner.s2" class="border-2 border-gray-300 rounded p-1 font-bold"><option value="0">-</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option></select></div>
                                    <div class="flex justify-between items-center bg-gray-50 p-2 rounded border border-gray-200"><span class="text-sm font-bold text-gray-700">3. Kerja sama dalam tim</span><select x-model.number="formKuisioner.s3" class="border-2 border-gray-300 rounded p-1 font-bold"><option value="0">-</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option></select></div>
                                    <div class="flex justify-between items-center bg-gray-50 p-2 rounded border border-gray-200"><span class="text-sm font-bold text-gray-700">4. Kepemimpinan</span><select x-model.number="formKuisioner.s4" class="border-2 border-gray-300 rounded p-1 font-bold"><option value="0">-</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option></select></div>
                                    <div class="flex justify-between items-center bg-gray-50 p-2 rounded border border-gray-200"><span class="text-sm font-bold text-gray-700">5. Inisiatif</span><select x-model.number="formKuisioner.s5" class="border-2 border-gray-300 rounded p-1 font-bold"><option value="0">-</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option></select></div>
                                    <div class="flex justify-between items-center bg-gray-50 p-2 rounded border border-gray-200"><span class="text-sm font-bold text-gray-700">6. Kemauan untuk belajar</span><select x-model.number="formKuisioner.s6" class="border-2 border-gray-300 rounded p-1 font-bold"><option value="0">-</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option></select></div>
                                    <div class="flex justify-between items-center bg-gray-50 p-2 rounded border border-gray-200"><span class="text-sm font-bold text-gray-700">7. Bekerja Keras & Motivasi</span><select x-model.number="formKuisioner.s7" class="border-2 border-gray-300 rounded p-1 font-bold"><option value="0">-</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option></select></div>
                                    <div class="flex justify-between items-center bg-gray-50 p-2 rounded border border-gray-200"><span class="text-sm font-bold text-gray-700">8. Kedisiplinan & Tanggung Jawab</span><select x-model.number="formKuisioner.s8" class="border-2 border-gray-300 rounded p-1 font-bold"><option value="0">-</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option></select></div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- KONTEN 4: SARAN MAHASISWA -->
                    <template x-if="activeModal === 'saran'">
                        <div class="bg-white p-5 md:p-8 rounded-xl border-4 border-yellow-500 shadow-[6px_6px_0_0_#eab308] relative overflow-hidden">
                            <!-- Dekorasi -->
                            <div class="absolute -right-6 -top-6 opacity-10 pointer-events-none">
                                <svg class="w-32 h-32 text-yellow-600" fill="currentColor" viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                            </div>
                            
                            <div class="relative z-10">
                                <h3 class="text-2xl font-black text-yellow-700 mb-6 border-l-4 border-yellow-400 pl-3">Saran & Masukan untuk Mahasiswa</h3>

                                <!-- Jika sudah pernah mengirim saran -->
                                <div x-show="selectedStudent.saran_mentor" class="mb-6 p-4 bg-emerald-50 border-2 border-emerald-400 rounded-xl flex items-start gap-3">
                                    <svg class="w-6 h-6 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <div>
                                        <h4 class="font-black text-emerald-900">Saran Sudah Dikirim</h4>
                                        <p class="text-sm font-medium text-emerald-700">Anda sudah memberikan saran dan masukan untuk mahasiswa ini.</p>
                                        <div class="mt-3 p-3 bg-white border border-emerald-200 rounded-lg text-sm text-gray-700 italic" x-text="selectedStudent.saran_mentor_teks"></div>
                                    </div>
                                </div>

                                <!-- Form Saran -->
                                <form x-show="!selectedStudent.saran_mentor" @submit.prevent="submitSaranMentor()" class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-black text-yellow-900 uppercase mb-1">Tanggal</label>
                                        <input type="date" x-model="formSaran.tanggal" required class="w-full md:w-1/2 border-2 border-yellow-300 rounded-lg p-2 font-bold focus:border-yellow-600 outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-black text-yellow-900 uppercase mb-1">Isi Saran / Masukan</label>
                                        <textarea x-model="formSaran.saran" required rows="4" class="w-full border-2 border-yellow-300 rounded-lg p-3 font-semibold focus:border-yellow-600 outline-none" placeholder="Tuliskan evaluasi, saran membangun, atau pesan..."></textarea>
                                    </div>
                                    <div class="flex justify-end mt-4">
                                        <button type="submit" :disabled="isSubmitting" class="bg-yellow-400 text-yellow-950 font-black py-2.5 px-6 rounded-xl border-4 border-yellow-600 shadow-[4px_4px_0_0_#ca8a04] hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all flex items-center gap-2 disabled:opacity-50">
                                            <span x-show="!isSubmitting">Kirim Saran</span>
                                            <span x-show="isSubmitting">Mengirim...</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Footer Modal -->
                <div class="bg-gray-200 border-t-2 border-gray-300 p-4 flex justify-end flex-shrink-0 rounded-b-xl">
                    <button x-show="activeModal !== 'saran'" @click="saveData()" class="bg-green-500 text-white font-black px-8 py-3 rounded-xl border-2 border-green-900 shadow-[4px_4px_0_0_#14532d] hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all text-lg">
                        Simpan Penilaian
                    </button>
                    <button x-show="activeModal === 'saran'" @click="closeModal()" class="bg-gray-500 text-white font-black px-8 py-3 rounded-xl border-2 border-gray-800 shadow-[4px_4px_0_0_#1f2937] hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all text-lg">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    function mentorApp(serverStudents = []) {
        return {
            activeModal: null, // 'logbook', 'disiplin', 'kuisioner'
            selectedStudent: null,
            
            students: [],

            init() {
                // Konversi ke array jika berupa object (karena PHP json_encode associative array menjadi object)
                const studentsArray = Array.isArray(serverStudents) ? serverStudents : Object.values(serverStudents);
                
                if (studentsArray.length > 0) {
                    this.students = studentsArray.map(mhs => {
                        let hasPenilaian = mhs.penilaians && mhs.penilaians.length > 0;
                        let dataLogbookStudent = (mhs.logbooks || []).map(l => ({
                            id: l.id_log,
                            minggu: l.minggu_ke || 1,
                            tanggal: l.tanggal,
                            matkul: l.kd_mat || '',
                            deskripsi: l.kegiatan,
                            durasiTotal: l.durasi_mnt ? ((l.durasi_mnt || 0) / 60).toFixed(1) : '0.0',
                            status: l.status,
                            catatan_mentor: l.catatan_mentor,
                            fotos: (l.fotos || []).map(f => f.foto_path)
                        }));
                        return {
                            id: mhs.id_mhs || mhs.nim,
                            name: mhs.nama_mhs,
                            nim: mhs.nim,
                            kelas: mhs.kelas || '-',
                            status: hasPenilaian ? 'Selesai' : 'Aktif',
                            dataLogbookStudent: dataLogbookStudent,
                            dataDisiplin: (mhs.disiplin_mahasiswas && mhs.disiplin_mahasiswas.length > 0) ? mhs.disiplin_mahasiswas[0] : null,
                            dataKuisioner: (mhs.kuisioner_mentors && mhs.kuisioner_mentors.length > 0) ? mhs.kuisioner_mentors[0] : null,
                            saran_mentor: (mhs.saran_mahasiswas && mhs.saran_mahasiswas.length > 0),
                            saran_mentor_teks: (mhs.saran_mahasiswas && mhs.saran_mahasiswas.length > 0) ? mhs.saran_mahasiswas[0].saran : '',
                            nilaiLogbook: Array.from({ length: 20 }, () => ({ status: '', catatan_mentor: '' }))
                        };
                    });
                } else {
                    this.students = [];
                }
            },

            // State Form Modal
            formLogbook: Array.from({ length: 20 }, () => ({ status: '', catatan_mentor: '' })),
            formDisiplin: { p1: 0, p2: 0, p3: 0, p4: 0, p5: 0, s3: 0, s5: 0, s6: 0, s7: 0, s8: 0 },
            formKuisioner: { h1a: 0, h1b: 0, h1c: 0, h1d: 0, h2: 0, h3: 0, h4: 0, s1: 0, s2: 0, s3: 0, s4: 0, s5: 0, s6: 0, s7: 0, s8: 0 },
            formSaran: { tanggal: new Date().toISOString().split('T')[0], saran: '' },
            isSubmitting: false,

            getModalTitle() {
                if(this.activeModal === 'logbook') return 'Validasi Isi & Nilai Logbook Mingguan';
                if(this.activeModal === 'disiplin') return 'Form Penilaian Disiplin, Prestasi & Supervisi';
                if(this.activeModal === 'kuisioner') return 'Kuisioner Penilaian Hardskill & Softskill';
                if(this.activeModal === 'saran') return 'Form Saran Mahasiswa';
                return '';
            },

            openModal(type, student) {
                this.activeModal = type;
                this.selectedStudent = student;

                // Load Data yang sudah pernah di-save
                if(type === 'logbook') {
                    this.formLogbook = [...student.nilaiLogbook];
                } else if(type === 'disiplin') {
                    this.formDisiplin = student.dataDisiplin ? { ...student.dataDisiplin } : { p1: 0, p2: 0, p3: 0, p4: 0, p5: 0, s3: 0, s5: 0, s6: 0, s7: 0, s8: 0 };
                } else if(type === 'kuisioner') {
                    this.formKuisioner = student.dataKuisioner ? { ...student.dataKuisioner } : { h1a: 0, h1b: 0, h1c: 0, h1d: 0, h2: 0, h3: 0, h4: 0, s1: 0, s2: 0, s3: 0, s4: 0, s5: 0, s6: 0, s7: 0, s8: 0 };
                }
            },

            closeModal() {
                this.activeModal = null;
                this.selectedStudent = null;
            },

            // Cek apakah mahasiswa punya data logbook di minggu ke-w
            hasLogbookData(weekNum) {
                if(!this.selectedStudent) return false;
                return this.selectedStudent.dataLogbookStudent.some(entry => entry.minggu === weekNum);
            },

            // Mengambil daftar kegiatan mahasiswa di minggu ke-w
            getLogbookData(weekNum) {
                if(!this.selectedStudent) return [];
                return this.selectedStudent.dataLogbookStudent.filter(entry => entry.minggu === weekNum);
            },

            formatDate(dateString) {
                if(!dateString) return '';
                const options = { weekday: 'short', day: '2-digit', month: 'short' };
                return new Date(dateString).toLocaleDateString('id-ID', options);
            },

            get totalPrestasi() {
                return (this.formDisiplin.p1 || 0) + (this.formDisiplin.p2 || 0) + (this.formDisiplin.p3 || 0) + (this.formDisiplin.p4 || 0) + (this.formDisiplin.p5 || 0);
            },

            get totalSupervisi() {
                return (this.formDisiplin.s3 || 0) + (this.formDisiplin.s5 || 0) + (this.formDisiplin.s6 || 0) + (this.formDisiplin.s7 || 0) + (this.formDisiplin.s8 || 0);
            },

            async saveData() {
                if(this.activeModal === 'logbook') {
                    try {
                        let validLogbooks = this.selectedStudent.dataLogbookStudent.map(l => {
                            let weekIndex = l.minggu - 1;
                            let formEntry = this.formLogbook[weekIndex];
                            return {
                                id: l.id,
                                status: formEntry ? formEntry.status : '',
                                catatan_mentor: formEntry ? formEntry.catatan_mentor : ''
                            };
                        }).filter(l => l.status !== '' || l.catatan_mentor !== '');

                        let res = await fetch('{{ route("mentor.logbook.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                nim: this.selectedStudent.nim,
                                logbooks: validLogbooks
                            })
                        });
                        let data = await res.json();
                        if (res.ok) {
                            this.selectedStudent.nilaiLogbook = [...this.formLogbook];
                            this.selectedStudent.status = 'Selesai';
                            alert(data.message || `Data ${this.getModalTitle()} untuk ${this.selectedStudent.name} berhasil disimpan!`);
                            this.closeModal();
                        } else {
                            alert(data.message || 'Gagal menyimpan evaluasi logbook');
                        }
                    } catch (e) { console.error(e); }
                } else if(this.activeModal === 'disiplin') {
                    try {
                        let res = await fetch('{{ route("mentor.disiplin.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                nim: this.selectedStudent.nim,
                                ...this.formDisiplin
                            })
                        });
                        let data = await res.json();
                        if (res.ok) {
                            this.selectedStudent.dataDisiplin = { ...this.formDisiplin };
                            alert(data.message);
                            this.closeModal();
                        } else {
                            alert(data.message);
                        }
                    } catch (e) { console.error(e); }
                } else if(this.activeModal === 'kuisioner') {
                    try {
                        let res = await fetch('{{ route("mentor.kuisioner.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                nim: this.selectedStudent.nim,
                                ...this.formKuisioner
                            })
                        });
                        let data = await res.json();
                        if (res.ok) {
                            this.selectedStudent.dataKuisioner = { ...this.formKuisioner };
                            alert(data.message);
                            this.closeModal();
                        } else {
                            alert(data.message);
                        }
                    } catch (e) { console.error(e); }
                }
            },

            async submitSaranMentor() {
                if (!this.selectedStudent) return;
                this.isSubmitting = true;

                try {
                    const response = await fetch('{{ route("mentor.saran.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            nim: this.selectedStudent.nim,
                            tanggal: this.formSaran.tanggal,
                            saran: this.formSaran.saran
                        })
                    });

                    const result = await response.json();

                    if (!response.ok) {
                        alert(result.message || 'Gagal mengirim saran.');
                    } else {
                        alert(result.message || 'Saran berhasil dikirim.');
                        this.selectedStudent.saran_mentor = true;
                        this.selectedStudent.saran_mentor_teks = this.formSaran.saran;
                        this.formSaran.saran = ''; // reset form
                    }
                } catch (error) {
                    console.error('Error submitting saran:', error);
                    alert('Terjadi kesalahan sistem.');
                } finally {
                    this.isSubmitting = false;
                }
            }
        }
    }
</script>
@endsection