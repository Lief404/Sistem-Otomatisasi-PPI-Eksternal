@extends('dosen.app')
@section('title', 'Dashboard Dosen Pembimbing')

@section('content')
<div x-data="dosenApp()" class="space-y-6">

    <!-- Header Panel -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b-4 border-blue-900 pb-4">
        <div>
            <h2 class="text-3xl font-black text-blue-900 tracking-wide">Monitoring & Penilaian</h2>
            <p class="text-gray-600 mt-1 font-medium">Jadwal kunjungan PT dan penilaian presentasi mahasiswa bimbingan.</p>
        </div>
    </div>

    <!-- Statistik Ringkas (Ditambah Draft) -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-blue-100 p-4 rounded-xl border-2 border-blue-900 shadow-[4px_4px_0_0_#1e3a8a]">
            <h3 class="text-sm font-bold text-blue-900 uppercase">Total Bimbingan</h3>
            <p class="text-3xl font-black text-blue-700 mt-1" x-text="students.length"></p>
        </div>
        <div class="bg-red-50 p-4 rounded-xl border-2 border-red-900 shadow-[4px_4px_0_0_#7f1d1d]">
            <h3 class="text-sm font-bold text-red-900 uppercase">Belum Dinilai</h3>
            <div class="flex items-center gap-2 mt-1">
                <span class="w-3 h-3 rounded-full bg-red-500 border-2 border-red-900 animate-pulse"></span>
                <p class="text-3xl font-black text-red-700" x-text="students.filter(s => s.status === 'Belum Dinilai').length"></p>
            </div>
        </div>
        <div class="bg-orange-50 p-4 rounded-xl border-2 border-orange-900 shadow-[4px_4px_0_0_#7c2d12]">
            <h3 class="text-sm font-bold text-orange-900 uppercase">Draft (Cicilan)</h3>
            <div class="flex items-center gap-2 mt-1">
                <span class="w-3 h-3 rounded-full bg-orange-500 border-2 border-orange-900"></span>
                <p class="text-3xl font-black text-orange-700" x-text="students.filter(s => s.status === 'Draft').length"></p>
            </div>
        </div>
        <div class="bg-green-50 p-4 rounded-xl border-2 border-green-900 shadow-[4px_4px_0_0_#14532d]">
            <h3 class="text-sm font-bold text-green-900 uppercase">Sudah Dinilai</h3>
            <div class="flex items-center gap-2 mt-1">
                <span class="w-3 h-3 rounded-full bg-green-500 border-2 border-green-900"></span>
                <p class="text-3xl font-black text-green-700" x-text="students.filter(s => s.status === 'Sudah Dinilai').length"></p>
            </div>
        </div>
    </div>

    <!-- Daftar Mahasiswa Bimbingan -->
    <div class="bg-white rounded-xl border-2 border-blue-900 shadow-[6px_6px_0_0_#1e3a8a] overflow-hidden mt-8">
        <div class="bg-blue-50 p-4 border-b-2 border-blue-900">
            <h3 class="text-xl font-black text-blue-900">Jadwal Monitoring & Daftar Mahasiswa</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 border-b-2 border-blue-200">
                        <th class="p-4 font-bold text-gray-700">Nama Mahasiswa</th>
                        <th class="p-4 font-bold text-gray-700">Tempat Magang & Jadwal</th>
                        <th class="p-4 font-bold text-gray-700 text-center">Status</th>
                        <th class="p-4 font-bold text-gray-700 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="student in students" :key="student.id">
                        <tr class="border-b border-gray-200 hover:bg-blue-50 transition-colors">
                            
                            <!-- Info Mahasiswa -->
                            <td class="p-4">
                                <p class="font-black text-blue-900 text-lg" x-text="student.name"></p>
                                <p class="text-sm font-bold text-gray-500" x-text="student.nim + ' | ' + student.kelas"></p>
                            </td>
                            
                            <!-- Jadwal & PT -->
                            <td class="p-4">
                                <p class="font-bold text-gray-800" x-text="student.company"></p>
                                <div class="flex items-center gap-2 mt-1">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span class="text-xs font-bold text-blue-600" x-text="formatDate(student.monitoringDate)"></span>
                                </div>
                            </td>
                            
                            <!-- Indikator Status Dinamis (Merah/Oren/Hijau) -->
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2 bg-gray-50 border border-gray-200 py-2 px-3 rounded-lg w-fit mx-auto">
                                    <span class="w-4 h-4 rounded-full border-2 border-white shadow-sm" 
                                          :class="{
                                              'bg-red-500': student.status === 'Belum Dinilai',
                                              'bg-orange-500': student.status === 'Draft',
                                              'bg-green-500': student.status === 'Sudah Dinilai'
                                          }"></span>
                                    <span class="text-sm font-bold text-gray-700" x-text="student.status"></span>
                                </div>
                            </td>
                            
                            <!-- Tombol Aksi Dinamis -->
                            <td class="p-4 text-center">
                                <template x-if="student.status === 'Belum Dinilai'">
                                    <button @click="openModal(student)" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold border-2 border-blue-900 shadow-[3px_3px_0_0_#1e3a8a] hover:translate-y-px hover:translate-x-px hover:shadow-none transition-all text-sm">
                                        Beri Penilaian
                                    </button>
                                </template>
                                <template x-if="student.status === 'Draft'">
                                    <button @click="openModal(student)" class="bg-orange-500 text-white px-4 py-2 rounded-lg font-bold border-2 border-orange-900 shadow-[3px_3px_0_0_#7c2d12] hover:translate-y-px hover:translate-x-px hover:shadow-none transition-all text-sm">
                                        Lanjut Isi Draft
                                    </button>
                                </template>
                                <template x-if="student.status === 'Sudah Dinilai'">
                                    <button @click="openModal(student)" class="bg-white text-green-700 px-4 py-2 rounded-lg font-bold border-2 border-green-600 shadow-[3px_3px_0_0_#14532d] hover:translate-y-px hover:translate-x-px hover:shadow-none transition-all text-sm">
                                        Lihat / Edit Nilai
                                    </button>
                                </template>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL FORM PENILAIAN -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <!-- Background Overlay dipasang klik closeModal agar auto-draft bekerja saat di-klik sembarang -->
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="closeModal()"></div>

            <div x-show="showModal" x-transition.scale class="relative inline-block bg-white text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle max-w-5xl w-full border-4 border-blue-900 rounded-xl">
                
                <div class="bg-blue-900 px-6 py-4 flex justify-between items-center text-white">
                    <h3 class="text-2xl font-black">Penilaian Presentasi PPI</h3>
                    <!-- Tombol X dipasang closeModal -->
                    <button @click="closeModal()" class="hover:text-red-400 transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 bg-gray-50 max-h-[80vh] overflow-y-auto">
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
                        <div class="w-full flex gap-4">
                            <div class="flex-1">
                                <p class="text-xs font-bold text-gray-500 uppercase mb-1">Judul Presentasi/Makalah</p>
                                <input type="text" x-model="form.judul" class="w-full border-2 border-gray-300 rounded p-2 font-bold text-gray-800 outline-none focus:border-blue-500" placeholder="Masukkan judul laporan akhir...">
                            </div>
                            <div class="w-1/4">
                                <p class="text-xs font-bold text-gray-500 uppercase mb-1">Status Data</p>
                                <p class="text-lg font-black" :class="{
                                    'text-red-600': selectedStudent?.status === 'Belum Dinilai',
                                    'text-orange-500': selectedStudent?.status === 'Draft',
                                    'text-green-600': selectedStudent?.status === 'Sudah Dinilai'
                                }" x-text="selectedStudent?.status"></p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        
                        <!-- BAGIAN A: MUTU PRESENTASI -->
                        <div class="bg-white p-5 rounded-lg border-2 border-blue-900 shadow-[4px_4px_0_0_#1e3a8a]">
                            <div class="flex justify-between items-center mb-4 border-b-2 border-blue-100 pb-2">
                                <h4 class="font-black text-blue-900 text-lg">A. Mutu Presentasi (40%)</h4>
                                <span class="bg-blue-100 text-blue-800 font-bold px-3 py-1 rounded border border-blue-300" x-text="'Rata-rata: ' + avgA"></span>
                            </div>
                            
                            <div class="space-y-4">
                                <div>
                                    <h5 class="font-bold text-gray-700 bg-gray-100 p-2 rounded">I. Teknik Presentasi</h5>
                                    <div class="grid grid-cols-2 gap-2 mt-2">
                                        <div><label class="text-xs font-bold text-gray-600">a. Persiapan</label><input type="number" min="0" max="100" x-model.number="form.a1_a" class="w-full border-2 border-gray-300 p-1 rounded font-bold text-center"></div>
                                        <div><label class="text-xs font-bold text-gray-600">b. Sistematika Penyajian</label><input type="number" min="0" max="100" x-model.number="form.a1_b" class="w-full border-2 border-gray-300 p-1 rounded font-bold text-center"></div>
                                        <div><label class="text-xs font-bold text-gray-600">c. Penggunaan Alat</label><input type="number" min="0" max="100" x-model.number="form.a1_c" class="w-full border-2 border-gray-300 p-1 rounded font-bold text-center"></div>
                                        <div><label class="text-xs font-bold text-gray-600">d. Bahasa Lisan</label><input type="number" min="0" max="100" x-model.number="form.a1_d" class="w-full border-2 border-gray-300 p-1 rounded font-bold text-center"></div>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="font-bold text-gray-700 bg-gray-100 p-2 rounded">II. Diskusi / Tanya Jawab</h5>
                                    <div class="grid grid-cols-2 gap-2 mt-2">
                                        <div><label class="text-xs font-bold text-gray-600">a. Penguasaan Materi</label><input type="number" min="0" max="100" x-model.number="form.a2_a" class="w-full border-2 border-gray-300 p-1 rounded font-bold text-center"></div>
                                        <div><label class="text-xs font-bold text-gray-600">b. Sistematika Menjawab</label><input type="number" min="0" max="100" x-model.number="form.a2_b" class="w-full border-2 border-gray-300 p-1 rounded font-bold text-center"></div>
                                        <div><label class="text-xs font-bold text-gray-600">c. Mempertahankan Pendapat</label><input type="number" min="0" max="100" x-model.number="form.a2_c" class="w-full border-2 border-gray-300 p-1 rounded font-bold text-center"></div>
                                        <div><label class="text-xs font-bold text-gray-600">d. Sikap Penerimaan</label><input type="number" min="0" max="100" x-model.number="form.a2_d" class="w-full border-2 border-gray-300 p-1 rounded font-bold text-center"></div>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-600 block mb-1">Catatan Evaluasi (Opsional)</label>
                                    <textarea x-model="form.catatanA" rows="2" class="w-full border-2 border-gray-300 rounded p-2 text-sm font-medium"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- BAGIAN B: MUTU PENULISAN -->
                        <div class="bg-white p-5 rounded-lg border-2 border-blue-900 shadow-[4px_4px_0_0_#1e3a8a]">
                            <div class="flex justify-between items-center mb-4 border-b-2 border-blue-100 pb-2">
                                <h4 class="font-black text-blue-900 text-lg">B. Penulisan Makalah (60%)</h4>
                                <span class="bg-blue-100 text-blue-800 font-bold px-3 py-1 rounded border border-blue-300" x-text="'Rata-rata: ' + avgB"></span>
                            </div>
                            
                            <div class="space-y-4">
                                <div>
                                    <h5 class="font-bold text-gray-700 bg-gray-100 p-2 rounded">I. Teknik Penulisan</h5>
                                    <div class="grid grid-cols-2 gap-2 mt-2">
                                        <div><label class="text-xs font-bold text-gray-600">a. Sistematika Penulisan</label><input type="number" min="0" max="100" x-model.number="form.b1_a" class="w-full border-2 border-gray-300 p-1 rounded font-bold text-center"></div>
                                        <div><label class="text-xs font-bold text-gray-600">b. Kelengkapan Isi</label><input type="number" min="0" max="100" x-model.number="form.b1_b" class="w-full border-2 border-gray-300 p-1 rounded font-bold text-center"></div>
                                        <div><label class="text-xs font-bold text-gray-600">c. Review Kepustakaan</label><input type="number" min="0" max="100" x-model.number="form.b1_c" class="w-full border-2 border-gray-300 p-1 rounded font-bold text-center"></div>
                                        <div><label class="text-xs font-bold text-gray-600">d. Bahasa Tulisan</label><input type="number" min="0" max="100" x-model.number="form.b1_d" class="w-full border-2 border-gray-300 p-1 rounded font-bold text-center"></div>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="font-bold text-gray-700 bg-gray-100 p-2 rounded">II. Pokok Bahasan</h5>
                                    <div class="grid grid-cols-2 gap-2 mt-2">
                                        <div><label class="text-xs font-bold text-gray-600">a. Perumusan Masalah</label><input type="number" min="0" max="100" x-model.number="form.b2_a" class="w-full border-2 border-gray-300 p-1 rounded font-bold text-center"></div>
                                        <div><label class="text-xs font-bold text-gray-600">b. Analisa Uraian</label><input type="number" min="0" max="100" x-model.number="form.b2_b" class="w-full border-2 border-gray-300 p-1 rounded font-bold text-center"></div>
                                        <div><label class="text-xs font-bold text-gray-600">c. Penyelesaian Masalah</label><input type="number" min="0" max="100" x-model.number="form.b2_c" class="w-full border-2 border-gray-300 p-1 rounded font-bold text-center"></div>
                                        <div><label class="text-xs font-bold text-gray-600">d. Kesimpulan & Saran</label><input type="number" min="0" max="100" x-model.number="form.b2_d" class="w-full border-2 border-gray-300 p-1 rounded font-bold text-center"></div>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-600 block mb-1">Catatan Evaluasi (Opsional)</label>
                                    <textarea x-model="form.catatanB" rows="2" class="w-full border-2 border-gray-300 rounded p-2 text-sm font-medium"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Modal -->
                <div class="bg-gray-200 border-t-2 border-gray-300 p-6 flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="w-full md:w-auto">
                        <p class="text-xs font-bold text-gray-600 mb-1 uppercase">Kriteria Penilaian:</p>
                        <div class="grid grid-cols-3 gap-x-4 gap-y-1 text-xs font-bold text-gray-500 bg-white p-2 border border-gray-300 rounded">
                            <span>85-100: Sangat Memuaskan</span>
                            <span>70-77: Baik</span>
                            <span>55-62: Kurang</span>
                            <span>78-84: Memuaskan</span>
                            <span>63-69: Cukup Baik</span>
                            <span>0-54: Sangat Kurang</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-6 w-full md:w-auto justify-end">
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-600 uppercase">Nilai Akhir</p>
                            <div class="flex items-end gap-2">
                                <span class="text-4xl font-black text-blue-900" x-text="nilaiAkhir"></span>
                                <span class="text-lg font-bold" :class="getGradeColor()" x-text="'(' + getGradeText() + ')'"></span>
                            </div>
                        </div>
                        <button @click="submitPenilaian()" class="bg-green-500 text-white font-black px-8 py-3 rounded-xl border-2 border-green-900 shadow-[4px_4px_0_0_#14532d] hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all text-lg">
                            <span x-text="selectedStudent?.status === 'Sudah Dinilai' ? 'Update Nilai' : 'Simpan Final'"></span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    function dosenApp() {
        return {
            showModal: false,
            selectedStudent: null,
            
            // Dummy Data Mahasiswa dengan object 'scores' untuk menyimpan riwayat input
            students: [
                { id: 1, name: 'Alief Muhammad S', nim: '223443026', kelas: '3 AEC-2', company: 'PT Solusi Intek Indonesia', monitoringDate: '2026-08-25', status: 'Belum Dinilai', scores: null },
                { id: 2, name: 'Budi Santoso', nim: '223443027', kelas: '3 AEC-2', company: 'PT Dirgantara Indonesia', monitoringDate: '2026-08-28', status: 'Belum Dinilai', scores: null },
                { id: 3, name: 'Rina Melati', nim: '223443028', kelas: '3 AEC-2', company: 'PT Solusi Intek Indonesia', monitoringDate: '2026-08-25', status: 'Draft', 
                  scores: { judul: 'Sistem Terintegrasi', a1_a: 80, a1_b: 0, a1_c: 0, a1_d: 0, a2_a: 0, a2_b: 0, a2_c: 0, a2_d: 0, catatanA: '', b1_a: 0, b1_b: 0, b1_c: 0, b1_d: 0, b2_a: 0, b2_b: 0, b2_c: 0, b2_d: 0, catatanB: '' }
                },
                { id: 4, name: 'Siti Aminah', nim: '223443029', kelas: '3 AEC-2', company: 'PT Pindad', monitoringDate: '2026-09-02', status: 'Sudah Dinilai',
                  scores: { judul: 'Analisis K3 Industri', a1_a: 85, a1_b: 80, a1_c: 90, a1_d: 85, a2_a: 80, a2_b: 85, a2_c: 80, a2_d: 90, catatanA: 'Sangat baik', b1_a: 85, b1_b: 85, b1_c: 80, b1_d: 85, b2_a: 90, b2_b: 85, b2_c: 80, b2_d: 85, catatanB: '' }
                }
            ],

            form: {},

            getEmptyForm() {
                return {
                    judul: '',
                    a1_a: 0, a1_b: 0, a1_c: 0, a1_d: 0,
                    a2_a: 0, a2_b: 0, a2_c: 0, a2_d: 0,
                    catatanA: '',
                    b1_a: 0, b1_b: 0, b1_c: 0, b1_d: 0,
                    b2_a: 0, b2_b: 0, b2_c: 0, b2_d: 0,
                    catatanB: ''
                };
            },

            formatDate(dateString) {
                if(!dateString) return '';
                const options = { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' };
                return new Date(dateString).toLocaleDateString('id-ID', options);
            },

            openModal(student) {
                this.selectedStudent = student;
                // Copy data nilai lama (jika ada) ke form, atau gunakan form kosong
                this.form = student.scores ? JSON.parse(JSON.stringify(student.scores)) : this.getEmptyForm();
                this.showModal = true;
            },

            closeModal() {
                if(!this.selectedStudent) return;

                // 1. Simpan apa yang ada di form saat ini ke data mahasiswa (sebagai draft)
                this.selectedStudent.scores = JSON.parse(JSON.stringify(this.form));

                // 2. Cek apakah ada inputan yang diisi (judul atau angka > 0)
                let hasValue = false;
                for (let key in this.form) {
                    if (key === 'judul' || key === 'catatanA' || key === 'catatanB') {
                        if (this.form[key].trim() !== '') hasValue = true;
                    } else {
                        if (this.form[key] > 0) hasValue = true;
                    }
                }

                // 3. Ubah status otomatis jika statusnya belum final ("Sudah Dinilai")
                if (this.selectedStudent.status !== 'Sudah Dinilai') {
                    this.selectedStudent.status = hasValue ? 'Draft' : 'Belum Dinilai';
                }

                this.showModal = false;
                this.selectedStudent = null;
            },

            // Kalkulasi
            get avgA() {
                let sum = (this.form.a1_a || 0) + (this.form.a1_b || 0) + (this.form.a1_c || 0) + (this.form.a1_d || 0) +
                          (this.form.a2_a || 0) + (this.form.a2_b || 0) + (this.form.a2_c || 0) + (this.form.a2_d || 0);
                return (sum / 8).toFixed(1);
            },
            
            get avgB() {
                let sum = (this.form.b1_a || 0) + (this.form.b1_b || 0) + (this.form.b1_c || 0) + (this.form.b1_d || 0) +
                          (this.form.b2_a || 0) + (this.form.b2_b || 0) + (this.form.b2_c || 0) + (this.form.b2_d || 0);
                return (sum / 8).toFixed(1);
            },

            get nilaiAkhir() {
                return ((this.avgA * 0.4) + (this.avgB * 0.6)).toFixed(1);
            },

            getGradeText() {
                let n = parseFloat(this.nilaiAkhir);
                if(n >= 85) return 'Sangat Memuaskan';
                if(n >= 78) return 'Memuaskan';
                if(n >= 70) return 'Baik';
                if(n >= 63) return 'Cukup Baik';
                if(n >= 55) return 'Kurang';
                return 'Sangat Kurang';
            },

            getGradeColor() {
                let n = parseFloat(this.nilaiAkhir);
                if(n >= 78) return 'text-green-600';
                if(n >= 63) return 'text-yellow-600';
                return 'text-red-600';
            },

            submitPenilaian() {
                // Simpan permanen dan ubah status ke Sudah Dinilai
                this.selectedStudent.scores = JSON.parse(JSON.stringify(this.form));
                this.selectedStudent.status = 'Sudah Dinilai';
                
                alert(`Data disimpan! Nilai Akhir untuk ${this.selectedStudent.name} adalah ${this.nilaiAkhir}.`);
                
                this.showModal = false;
                this.selectedStudent = null;
            }
        }
    }
</script>
@endsection