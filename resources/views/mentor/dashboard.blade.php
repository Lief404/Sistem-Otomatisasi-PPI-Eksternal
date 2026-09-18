@extends('mentor.app')
@section('title', 'Dashboard Mentor Industri')

@section('content')
<div x-data="mentorApp({{ json_encode($mahasiswas ?? []) }}, {{ json_encode($parameterSaranMentor ?? []) }}, {{ json_encode($parameterDisiplin ?? []) }}, {{ json_encode($parameterKuisioner ?? []) }})" class="space-y-6">

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
                        <button @click="openModal('transkrip_full', student)" class="flex-1 bg-white border-2 border-cyan-500 p-4 rounded-xl shadow-[4px_4px_0_0_#06b6d4] hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all group text-left block w-full">
                            <h5 class="font-black text-cyan-700 text-lg group-hover:text-cyan-600">Transkrip & Logbook</h5>
                            <p class="text-xs font-bold text-gray-500 mt-1">Lihat grafik jam & rekap nilai</p>
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
                                                <input type="number" min="0" max="100" placeholder="0" x-model.number="formLogbook[w-1].nilai" class="w-16 border-2 border-blue-300 rounded p-1 text-center font-bold outline-none focus:border-blue-600 text-blue-700">
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
                        <div class="bg-white p-5 md:p-8 rounded-xl border-4 border-purple-900 shadow-[6px_6px_0_0_#581c87]">
                            <h3 class="text-2xl font-black text-purple-900 mb-6 border-l-4 border-purple-500 pl-3">Form Disiplin & Prestasi</h3>

                            <!-- Jika sudah pernah menilai -->
                            <div x-show="selectedStudent.dataDisiplin" class="mb-6 p-4 bg-emerald-50 border-2 border-emerald-400 rounded-xl flex items-start gap-3">
                                <svg class="w-6 h-6 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="w-full">
                                    <h4 class="font-black text-emerald-900">Sudah Dinilai</h4>
                                    <p class="text-sm font-medium text-emerald-700 mb-3">Anda sudah memberikan penilaian untuk form ini.</p>
                                    <div class="bg-white border border-emerald-200 rounded-lg p-4 space-y-3" x-html="renderSaranTeks(selectedStudent.dataDisiplin.penilaian)"></div>
                                </div>
                            </div>

                            <!-- Form -->
                            <form x-show="!selectedStudent.dataDisiplin" @submit.prevent="saveData('disiplin')" class="space-y-4">
                                <template x-if="parameterDisiplin && parameterDisiplin.length > 0">
                                    <div class="space-y-4">
                                        <template x-for="param in parameterDisiplin" :key="param.id">
                                            <div class="bg-purple-50 p-4 rounded-xl border border-purple-200">
                                                <h4 class="font-black text-purple-900 mb-3" x-text="param.sub_kategori"></h4>
                                                <div class="space-y-4">
                                                    <template x-for="(indikator, idx) in param.indikator" :key="idx">
                                                        <div class="flex justify-between items-center bg-white p-2 rounded border border-purple-100">
                                                            <span class="text-sm font-bold text-gray-700" x-text="indikator"></span>
                                                            <input type="number" min="0" max="100" required x-model.number="formDisiplin.answers[indikator]" class="w-20 border-2 border-gray-300 rounded p-1 text-center font-bold focus:border-purple-600 outline-none">
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="!parameterDisiplin || parameterDisiplin.length === 0">
                                    <p class="text-gray-500 italic">Belum ada indikator disiplin dari Kaprodi.</p>
                                </template>
                            </form>
                        </div>
                    </template>

                    <!-- KONTEN 3: KUISIONER -->
                    <template x-if="activeModal === 'kuisioner'">
                        <div class="bg-white p-5 md:p-8 rounded-xl border-4 border-emerald-900 shadow-[6px_6px_0_0_#064e3b]">
                            <h3 class="text-2xl font-black text-emerald-900 mb-6 border-l-4 border-emerald-500 pl-3">Evaluasi Kuisioner</h3>
                            
                            <div class="flex flex-wrap items-center gap-4 border-b-2 border-gray-200 pb-2 mb-4">
                                <span class="bg-blue-100 text-blue-800 font-bold px-3 py-1 rounded text-xs border border-blue-300">4: Sangat Baik</span>
                                <span class="bg-blue-100 text-blue-800 font-bold px-3 py-1 rounded text-xs border border-blue-300">3: Baik</span>
                                <span class="bg-blue-100 text-blue-800 font-bold px-3 py-1 rounded text-xs border border-blue-300">2: Cukup</span>
                                <span class="bg-blue-100 text-blue-800 font-bold px-3 py-1 rounded text-xs border border-blue-300">1: Kurang</span>
                            </div>

                            <!-- Jika sudah pernah menilai -->
                            <div x-show="selectedStudent.dataKuisioner" class="mb-6 p-4 bg-emerald-50 border-2 border-emerald-400 rounded-xl flex items-start gap-3">
                                <svg class="w-6 h-6 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="w-full">
                                    <h4 class="font-black text-emerald-900">Sudah Dinilai</h4>
                                    <p class="text-sm font-medium text-emerald-700 mb-3">Anda sudah memberikan penilaian untuk form ini.</p>
                                    <div class="bg-white border border-emerald-200 rounded-lg p-4 space-y-3" x-html="renderSaranTeks(selectedStudent.dataKuisioner.penilaian)"></div>
                                </div>
                            </div>

                            <!-- Form -->
                            <form x-show="!selectedStudent.dataKuisioner" @submit.prevent="saveData('kuisioner')" class="space-y-4">
                                <template x-if="parameterKuisioner && parameterKuisioner.length > 0">
                                    <div class="space-y-4">
                                        <template x-for="param in parameterKuisioner" :key="param.id">
                                            <div class="bg-emerald-50 p-4 rounded-xl border border-emerald-200">
                                                <h4 class="font-black text-emerald-900 mb-3 bg-white p-2 rounded inline-block" x-text="param.sub_kategori"></h4>
                                                <div class="space-y-4">
                                                    <template x-for="(indikator, idx) in param.indikator" :key="idx">
                                                        <div class="flex justify-between items-center bg-white p-2 rounded border border-emerald-100">
                                                            <span class="text-sm font-bold text-gray-700" x-text="indikator"></span>
                                                            <select required x-model.number="formKuisioner.answers[indikator]" class="border-2 border-gray-300 rounded p-1 font-bold focus:border-emerald-600 outline-none">
                                                                <option value="">-</option>
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                            </select>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="!parameterKuisioner || parameterKuisioner.length === 0">
                                    <p class="text-gray-500 italic">Belum ada indikator kuisioner dari Kaprodi.</p>
                                </template>
                            </form>
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
                                    <div class="w-full">
                                        <h4 class="font-black text-emerald-900">Saran Sudah Dikirim</h4>
                                        <p class="text-sm font-medium text-emerald-700 mb-3">Anda sudah memberikan saran dan masukan untuk mahasiswa ini.</p>
                                        
                                        <div class="bg-white border border-emerald-200 rounded-lg p-4 space-y-3" x-html="renderSaranTeks(selectedStudent.saran_mentor_teks)"></div>
                                    </div>
                                </div>

                                <!-- Form Saran -->
                                <form x-show="!selectedStudent.saran_mentor" @submit.prevent="submitSaranMentor()" class="space-y-4">
                                    <div class="mb-4">
                                        <label class="block text-xs font-black text-yellow-900 uppercase mb-1">Tanggal</label>
                                        <input type="date" x-model="formSaran.tanggal" required class="w-full md:w-1/2 border-2 border-yellow-300 rounded-lg p-2 font-bold focus:border-yellow-600 outline-none">
                                    </div>

                                    <template x-if="parameterSaranMentor && parameterSaranMentor.length > 0">
                                        <div class="space-y-4">
                                            <template x-for="param in parameterSaranMentor" :key="param.id">
                                                <div class="bg-yellow-50 p-4 rounded-xl border border-yellow-200">
                                                    <h4 class="font-black text-yellow-900 mb-3" x-text="param.sub_kategori"></h4>
                                                    <div class="space-y-4">
                                                        <template x-for="(indikator, idx) in param.indikator" :key="idx">
                                                            <div>
                                                                <label class="block text-xs font-black text-yellow-800 uppercase mb-1" x-text="indikator"></label>
                                                                <textarea x-model="formSaran.answers[indikator]" required rows="3" class="w-full border-2 border-yellow-300 rounded-lg p-3 font-medium focus:border-yellow-600 outline-none"></textarea>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    
                                    <template x-if="!parameterSaranMentor || parameterSaranMentor.length === 0">
                                        <div>
                                            <label class="block text-xs font-black text-yellow-900 uppercase mb-1">Isi Saran / Masukan</label>
                                            <textarea x-model="formSaran.saran" required rows="4" class="w-full border-2 border-yellow-300 rounded-lg p-3 font-semibold focus:border-yellow-600 outline-none" placeholder="Tuliskan evaluasi, saran membangun, atau pesan..."></textarea>
                                        </div>
                                    </template>
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

                    <!-- KONTEN 5: REKAPAN JAM -->
                    <template x-if="activeModal === 'rekapan'">
                        <div class="bg-white p-6 rounded-xl border-2 border-cyan-900 shadow-[4px_4px_0_0_#164e63]">
                            <div class="flex justify-between items-center border-b-2 border-gray-100 pb-2 mb-4">
                                <h4 class="text-xl font-black text-cyan-900">Rekapan Jam Logbook</h4>
                            </div>
                            
                            <div x-show="rekapanLoading" class="text-center py-10">
                                <p class="text-gray-500 font-bold">Memuat data...</p>
                            </div>
                            
                            <div x-show="!rekapanLoading && rekapanData" class="space-y-6">
                                <div class="bg-cyan-50 border border-cyan-200 text-cyan-800 p-3 rounded-lg font-bold flex justify-between">
                                    <span>Total Keseluruhan</span>
                                    <span><span x-text="rekapanData?.totalJamKeseluruhan"></span> Jam</span>
                                </div>
                                
                                <div class="w-full flex justify-center bg-white border border-gray-100 rounded-xl p-4 shadow-sm" x-show="rekapanData?.rekapanJam?.length > 0">
                                    <div class="relative w-full max-w-lg" style="height: 350px;">
                                        <canvas id="mentorRekapanChart"></canvas>
                                    </div>
                                </div>
                                
                                <div class="overflow-x-auto rounded-xl border border-gray-200">
                                    <table class="w-full text-left text-sm whitespace-nowrap min-w-max">
                                        <thead class="bg-gray-50 text-gray-500 uppercase tracking-wider text-xs font-semibold">
                                            <tr>
                                                <th class="px-4 py-3 border-r border-gray-200 sticky left-0 bg-gray-50 z-10">Mata Kuliah</th>
                                                <th class="px-4 py-3 text-center border-r border-gray-200 font-bold text-gray-900">Total (Jam)</th>
                                                <template x-for="i in 20" :key="i">
                                                    <th class="px-2 py-3 text-center border-r border-gray-200 text-[10px]">M<span x-text="i"></span></th>
                                                </template>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                                            <template x-for="(item, index) in rekapanData?.rekapanJam || []" :key="index">
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-4 py-2 border-r border-gray-100 sticky left-0 bg-white z-10 min-w-[200px]">
                                                        <div class="truncate max-w-[250px]" :title="item.nama">
                                                            <span class="font-bold text-gray-900" x-text="item.kode"></span><br>
                                                            <span class="text-xs text-gray-500" x-text="item.nama"></span>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-2 text-center border-r border-gray-100 font-black text-cyan-600 bg-cyan-50/30" x-text="item.total"></td>
                                                    <template x-for="i in 20" :key="i">
                                                        <td class="px-2 py-2 text-center border-r border-gray-100 text-[10px]">
                                                            <span x-show="item.mingguan && item.mingguan[i] > 0" x-text="item.mingguan[i]" class="text-gray-800"></span>
                                                            <span x-show="!item.mingguan || item.mingguan[i] === 0 || !item.mingguan[i]" class="text-gray-300">-</span>
                                                        </td>
                                                    </template>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </template>
                    
                    <!-- KONTEN 6: TRANSKRIP FULL (IFRAME) -->
                    <template x-if="activeModal === 'transkrip_full'">
                        <div class="h-[75vh] w-full rounded-xl overflow-hidden border-2 border-cyan-900 shadow-sm relative bg-white">
                            <div x-show="iframeLoading" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-50 z-10">
                                <svg class="w-10 h-10 text-cyan-600 animate-spin mb-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <p class="text-gray-500 font-bold">Memuat Transkrip...</p>
                            </div>
                            <iframe :src="'/transkrip/' + selectedStudent.nim + '?mode=modal'" class="w-full h-full border-0" @load="iframeLoading = false"></iframe>
                        </div>
                    </template>
                </div>

                <!-- Footer Modal -->
                <div class="bg-gray-200 border-t-2 border-gray-300 p-4 flex justify-end flex-shrink-0 rounded-b-xl">
                    <button x-show="activeModal !== 'saran' && activeModal !== 'rekapan' && activeModal !== 'transkrip_full'" @click="saveData()" class="bg-green-500 text-white font-black px-8 py-3 rounded-xl border-2 border-green-900 shadow-[4px_4px_0_0_#14532d] hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all text-lg">
                        Simpan Penilaian
                    </button>
                    <button x-show="activeModal === 'saran' || activeModal === 'rekapan' || activeModal === 'transkrip_full'" @click="closeModal()" class="bg-gray-500 text-white font-black px-8 py-3 rounded-xl border-2 border-gray-800 shadow-[4px_4px_0_0_#1f2937] hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all text-lg">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function mentorApp(serverStudents = [], serverSaranMentor = [], serverDisiplin = [], serverKuisioner = []) {
        return {
            activeModal: null, // 'logbook', 'disiplin', 'kuisioner'
            selectedStudent: null,
            rekapanData: null,
            rekapanLoading: false,
            iframeLoading: true,
            
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
                            dataDisiplin: (mhs.disiplin_mahasiswas && mhs.disiplin_mahasiswas.length > 0 && mhs.disiplin_mahasiswas[0].penilaian) ? mhs.disiplin_mahasiswas[0] : null,
                            dataKuisioner: (mhs.kuisioner_mentors && mhs.kuisioner_mentors.length > 0 && mhs.kuisioner_mentors[0].penilaian) ? mhs.kuisioner_mentors[0] : null,
                            saran_mentor: (mhs.saran_mahasiswas && mhs.saran_mahasiswas.length > 0),
                            saran_mentor_teks: (mhs.saran_mahasiswas && mhs.saran_mahasiswas.length > 0) ? mhs.saran_mahasiswas[0].saran : '',
                            nilaiLogbook: Array.from({ length: 20 }, () => ({ status: 'Dinilai', catatan_mentor: '', nilai: null }))
                        };
                    });
                } else {
                    this.students = [];
                }
            },

            // State Form Modal
            formLogbook: Array.from({ length: 20 }, () => ({ status: 'Dinilai', catatan_mentor: '', nilai: null })),
            formDisiplin: { answers: {} },
            formKuisioner: { answers: {} },
            parameterSaranMentor: Array.isArray(serverSaranMentor) ? serverSaranMentor : Object.values(serverSaranMentor || {}),
            parameterDisiplin: Array.isArray(serverDisiplin) ? serverDisiplin : Object.values(serverDisiplin || {}),
            parameterKuisioner: Array.isArray(serverKuisioner) ? serverKuisioner : Object.values(serverKuisioner || {}),
            formSaran: { tanggal: new Date().toISOString().split('T')[0], saran: '', answers: {} },
            isSubmitting: false,

            getModalTitle() {
                if(this.activeModal === 'logbook') return 'Validasi Isi & Nilai Logbook Mingguan';
                if(this.activeModal === 'disiplin') return 'Form Penilaian Disiplin, Prestasi & Supervisi';
                if(this.activeModal === 'kuisioner') return 'Kuisioner Penilaian Hardskill & Softskill';
                if(this.activeModal === 'saran') return 'Form Saran Mahasiswa';
                if(this.activeModal === 'rekapan') return 'Rekapan Jam Logbook Mingguan';
                if(this.activeModal === 'transkrip_full') return 'Transkrip Nilai Lengkap';
                return '';
            },

            async openModal(type, student) {
                this.activeModal = type;
                this.selectedStudent = student;
                this.iframeLoading = true;

                // Load Data yang sudah pernah di-save
                if(type === 'logbook') {
                    this.formLogbook = [...student.nilaiLogbook];
                } else if(type === 'disiplin') {
                    this.formDisiplin = { answers: {} };
                } else if(type === 'kuisioner') {
                    this.formKuisioner = { answers: {} };
                } else if(type === 'rekapan' && student) {
                    this.rekapanData = null;
                    this.rekapanLoading = true;
                    try {
                        const response = await fetch('/api/rekapan-jam/' + student.nim);
                        this.rekapanData = await response.json();
                        setTimeout(() => {
                            this.renderRekapanChart();
                        }, 200);
                    } catch (e) {
                        alert('Gagal memuat data rekapan jam.');
                    } finally {
                        this.rekapanLoading = false;
                    }
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

            renderRekapanChart() {
                if(!this.rekapanData || this.rekapanData.rekapanJam.length === 0) return;
                
                let canvas = document.getElementById('mentorRekapanChart');
                if(!canvas) return;
                
                if(window.mentorRekapanChartObj) {
                    window.mentorRekapanChartObj.destroy();
                }
                
                const ctx = canvas.getContext('2d');
                const labels = [];
                const dataPoints = [];
                const backgroundColors = [];
                
                const colors = [
                    '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', 
                    '#ec4899', '#06b6d4', '#84cc16', '#f43f5e', '#6366f1'
                ];
                
                this.rekapanData.rekapanJam.forEach((item, index) => {
                    labels.push(item.kode + ' - ' + item.nama);
                    dataPoints.push(item.total);
                    backgroundColors.push(colors[index % colors.length]);
                });
                
                window.mentorRekapanChartObj = new Chart(ctx, {
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
                                        if (label) label += ': ';
                                        if (context.parsed !== null) label += context.parsed + ' Jam';
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            },


            async saveData() {
                if(this.activeModal === 'logbook') {
                    try {
                        let validLogbooks = this.selectedStudent.dataLogbookStudent.map(l => {
                            let weekIndex = l.minggu - 1;
                            let formEntry = this.formLogbook[weekIndex];
                            return {
                                id: l.id,
                                status: formEntry ? formEntry.status : 'Dinilai',
                                catatan_mentor: formEntry ? formEntry.catatan_mentor : '',
                                nilai: formEntry ? formEntry.nilai : null
                            };
                        }).filter(l => l.nilai !== null && l.nilai !== '');

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
                                penilaian: JSON.stringify(this.formDisiplin.answers)
                            })
                        });
                        let data = await res.json();
                        if (res.ok) {
                            this.selectedStudent.dataDisiplin = { penilaian: JSON.stringify(this.formDisiplin.answers) };
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
                                penilaian: JSON.stringify(this.formKuisioner.answers)
                            })
                        });
                        let data = await res.json();
                        if (res.ok) {
                            this.selectedStudent.dataKuisioner = { penilaian: JSON.stringify(this.formKuisioner.answers) };
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

                let finalSaran = '';
                if (Object.keys(this.formSaran.answers).length > 0) {
                    finalSaran = JSON.stringify(this.formSaran.answers);
                } else {
                    finalSaran = this.formSaran.saran;
                    if (!finalSaran.trim()) {
                        this.isSubmitting = false;
                        return;
                    }
                }

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
                            saran: finalSaran
                        })
                    });

                    const result = await response.json();

                    if (!response.ok) {
                        alert(result.message || 'Gagal mengirim saran.');
                    } else {
                        alert(result.message || 'Saran berhasil dikirim.');
                        this.selectedStudent.saran_mentor = true;
                        this.selectedStudent.saran_mentor_teks = finalSaran;
                        this.formSaran.saran = ''; // reset form
                        this.formSaran.answers = {};
                    }
                } catch (error) {
                    console.error('Error submitting saran:', error);
                    alert('Terjadi kesalahan sistem.');
                } finally {
                    this.isSubmitting = false;
                }
            },

            renderSaranTeks(teks) {
                if (!teks) return '';
                try {
                    const data = JSON.parse(teks);
                    if (typeof data === 'object' && data !== null) {
                        let html = '';
                        for (const [key, value] of Object.entries(data)) {
                            html += `<div class="mb-2"><p class="text-xs font-black text-emerald-900 uppercase tracking-wider mb-1">${key}</p><p class="text-gray-800 text-sm font-medium bg-gray-50 p-2 rounded border border-gray-100">${value}</p></div>`;
                        }
                        return html;
                    }
                } catch (e) {
                    // Not JSON
                    return `<p class="text-gray-700 italic">${teks}</p>`;
                }
                return `<p class="text-gray-700 italic">${teks}</p>`;
            }
        }
    }
</script>
@endsection