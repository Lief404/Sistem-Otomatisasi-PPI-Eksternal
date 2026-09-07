@extends('admin.app')
@section('title', 'Manajemen Akun')

@section('content')
<div x-data="userManagementApp()" class="space-y-6">

    <!-- Notifikasi Sukses dari Backend Laravel -->
    @if(session('success'))
    <div class="bg-green-100 border-2 border-green-500 text-green-800 p-4 rounded-xl shadow-[4px_4px_0_0_#14532d] flex justify-between items-center animate-pulse">
        <div class="flex items-center gap-3">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="font-black">{{ session('success') }}</span>
        </div>
    </div>
    @endif

    <!-- Notifikasi Error (Bila validasi gagal) -->
    @if($errors->any())
    <div class="bg-red-100 border-2 border-red-500 text-red-800 p-4 rounded-xl shadow-[4px_4px_0_0_#7f1d1d]">
        <ul class="list-disc list-inside font-bold text-sm px-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Header Panel -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b-4 border-blue-900 pb-4">
        <div>
            <h2 class="text-3xl font-black text-blue-900 tracking-wide">Kelola Akun Sistem</h2>
            <p class="text-gray-600 mt-1 font-medium">Tambah, import Excel, dan filter akun berdasarkan Role.</p>
        </div>
        <button @click="openModal()" class="mt-4 md:mt-0 bg-blue-600 text-white font-bold py-2 px-6 rounded-lg border-2 border-blue-900 shadow-[4px_4px_0_0_#1e3a8a] hover:translate-y-px hover:translate-x-px hover:shadow-none transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
            Tambah Akun Manual
        </button>
    </div>

    <!-- MAIN CARD -->
    <div class="bg-white rounded-xl border-2 border-blue-900 shadow-[6px_6px_0_0_#1e3a8a] overflow-hidden flex flex-col md:flex-row min-h-[600px]">
        
        <!-- SIDEBAR FILTER (Kiri) -->
        <div class="w-full md:w-64 bg-blue-50 border-r-2 border-blue-200 p-4 flex flex-col gap-6 flex-shrink-0">
            
            <!-- Kategori Role Utama -->
            <div>
                <p class="text-xs font-black text-blue-900 uppercase tracking-widest mb-3 border-b-2 border-blue-200 pb-1">Kategori Akun</p>
                <div class="space-y-2">
                    <button @click="setRole('mahasiswa')" :class="activeRole === 'mahasiswa' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-gray-600 hover:bg-blue-100'" class="w-full text-left px-4 py-2 rounded-lg font-bold border border-blue-200 transition-all flex justify-between items-center">
                        Mahasiswa <span class="text-xs bg-black/10 px-2 py-0.5 rounded-full" x-text="countUsers('mahasiswa')"></span>
                    </button>
                    <button @click="setRole('dosen')" :class="activeRole === 'dosen' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-gray-600 hover:bg-blue-100'" class="w-full text-left px-4 py-2 rounded-lg font-bold border border-blue-200 transition-all flex justify-between items-center">
                        Dosen <span class="text-xs bg-black/10 px-2 py-0.5 rounded-full" x-text="countUsers('dosen')"></span>
                    </button>
                    <button @click="setRole('mentor')" :class="activeRole === 'mentor' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-gray-600 hover:bg-blue-100'" class="w-full text-left px-4 py-2 rounded-lg font-bold border border-blue-200 transition-all flex justify-between items-center">
                        Mentor PT <span class="text-xs bg-black/10 px-2 py-0.5 rounded-full" x-text="countUsers('mentor')"></span>
                    </button>
                    <button @click="setRole('admin')" :class="activeRole === 'admin' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-gray-600 hover:bg-blue-100'" class="w-full text-left px-4 py-2 rounded-lg font-bold border border-blue-200 transition-all flex justify-between items-center">
                        Admin <span class="text-xs bg-black/10 px-2 py-0.5 rounded-full" x-text="countUsers('admin')"></span>
                    </button>
                </div>
            </div>

            <!-- Sub-Filter (Hanya muncul jika role = Mahasiswa) -->
            <div x-show="activeRole === 'mahasiswa'" x-transition>
                <p class="text-xs font-black text-blue-900 uppercase tracking-widest mb-3 border-b-2 border-blue-200 pb-1">Filter Program Studi</p>
                <div class="space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer hover:bg-white p-1 rounded">
                        <input type="radio" name="prodi" value="ALL" x-model="filterProdi" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                        <span class="font-bold text-sm text-gray-700">Semua Prodi</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer hover:bg-white p-1 rounded">
                        <input type="radio" name="prodi" value="TRIN" x-model="filterProdi" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                        <span class="font-bold text-sm text-gray-700">TRIN (Informatika)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer hover:bg-white p-1 rounded">
                        <input type="radio" name="prodi" value="TRO" x-model="filterProdi" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                        <span class="font-bold text-sm text-gray-700">TRO (Otomasi)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer hover:bg-white p-1 rounded">
                        <input type="radio" name="prodi" value="TRMO" x-model="filterProdi" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                        <span class="font-bold text-sm text-gray-700">TRMO (Mekatronika)</span>
                    </label>
                </div>
            </div>

            <!-- Fitur Pencarian Cepat -->
            <div class="mt-auto">
                <p class="text-xs font-black text-blue-900 uppercase tracking-widest mb-2">Pencarian</p>
                <div class="relative">
                    <input type="text" x-model="searchQuery" placeholder="Cari nama/identitas..." class="w-full pl-8 pr-3 py-2 text-sm font-bold border-2 border-blue-300 rounded-lg outline-none focus:border-blue-600 bg-white">
                    <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

        </div>

        <!-- AREA TABEL (Kanan) -->
        <div class="flex-1 p-0 overflow-hidden flex flex-col">
            
            <!-- HEADER TABEL -->
            <div class="bg-blue-900 p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center text-white gap-4">
                <div>
                    <h3 class="text-xl font-black uppercase tracking-wide" x-text="'Data ' + activeRole"></h3>
                    <p class="text-xs font-medium text-blue-200" x-text="getFilteredUsers().length + ' data ditampilkan'"></p>
                </div>
                <div class="flex gap-2 w-full sm:w-auto">
                    <!-- Tombol Import Excel -->
                    <button @click="showImportModal = true" class="bg-green-500 text-white font-bold px-4 py-2 rounded-lg border-2 border-green-700 hover:bg-green-600 text-sm flex items-center justify-center gap-2 shadow-[2px_2px_0_0_#14532d] w-full sm:w-auto transition-transform active:translate-y-px">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Import Excel
                    </button>
                </div>
            </div>

            <!-- TABEL KONTEN -->
            <div class="overflow-y-auto flex-1 p-4 bg-white">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="border-b-2 border-gray-300 text-gray-600">
                            <th class="p-3 font-black text-sm uppercase">Nama / Identitas</th>
                            <th class="p-3 font-black text-sm uppercase">Username (Login)</th>
                            
                            <template x-if="activeRole === 'mahasiswa'">
                                <th class="p-3 font-black text-sm uppercase">Prodi & Kelas</th>
                            </template>
                            
                            <template x-if="activeRole === 'mahasiswa' || activeRole === 'mentor'">
                                <th class="p-3 font-black text-sm uppercase">Penempatan PT</th>
                            </template>

                            <th class="p-3 font-black text-sm uppercase text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="getFilteredUsers().length === 0">
                            <tr>
                                <td colspan="5" class="p-10 text-center text-gray-400 font-bold italic border-2 border-dashed border-gray-200 mt-4 rounded-xl">
                                    Data belum ada atau tidak ditemukan.
                                </td>
                            </tr>
                        </template>

                        <template x-for="user in getFilteredUsers()" :key="user.id">
                            <tr class="border-b border-gray-100 hover:bg-blue-50 transition-colors">
                                <td class="p-3">
                                    <p class="font-black text-blue-900 text-base" x-text="user.name"></p>
                                    <p class="text-xs font-bold text-gray-500 uppercase" x-show="user.identifier" x-text="user.identifier"></p>
                                </td>
                                <td class="p-3 font-bold text-gray-700 text-sm" x-text="user.username"></td>
                                
                                <template x-if="activeRole === 'mahasiswa'">
                                    <td class="p-3">
                                        <span class="bg-blue-100 text-blue-800 border border-blue-300 px-2 py-0.5 rounded text-xs font-black" x-text="(user.prodi || '-') + ' - ' + (user.kelas || '-')"></span>
                                    </td>
                                </template>

                                <template x-if="activeRole === 'mahasiswa' || activeRole === 'mentor'">
                                    <td class="p-3">
                                        <span class="bg-gray-100 border border-gray-300 text-gray-700 px-2 py-1 rounded text-xs font-bold whitespace-nowrap" x-text="user.pt || 'Belum Ditetapkan'"></span>
                                    </td>
                                </template>

                                <td class="p-3 text-center">
                                    <button @click="deleteUser(user.id)" class="text-red-500 hover:text-red-700 p-2 bg-red-50 border border-red-200 rounded hover:bg-red-100 transition-colors" title="Hapus User">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH MANUAL (Native Form Submit ke Laravel) -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showModal = false"></div>
            
            <div x-show="showModal" x-transition.scale class="relative inline-block bg-white text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle max-w-lg w-full border-4 border-blue-900 rounded-xl z-10">
                <div class="bg-blue-900 px-6 py-4 flex justify-between items-center text-white">
                    <h3 class="text-xl font-black uppercase" x-text="'Tambah ' + activeRole"></h3>
                    <button @click="showModal = false" type="button" class="hover:text-red-400 transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Form asli, mengarah ke Backend Laravel -->
                <form action="{{ url('/admin/users/store') }}" method="POST" class="p-6 bg-gray-50 space-y-4">
                    @csrf
                    <input type="hidden" name="role" :value="activeRole">
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1" x-text="activeRole === 'mentor' ? 'Nama Industri / Perusahaan' : 'Nama Lengkap'"></label>
                        <input type="text" name="name" x-model="formUser.name" required class="w-full border-2 border-gray-300 rounded-lg p-2 font-bold focus:border-blue-600 outline-none">
                    </div>

                    <template x-if="activeRole === 'mahasiswa' || activeRole === 'dosen'">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1" x-text="activeRole === 'mahasiswa' ? 'NIM' : 'NIP'"></label>
                                <input type="text" :name="activeRole === 'mahasiswa' ? 'nim' : 'nip'" x-model="formUser.identifier" required class="w-full border-2 border-gray-300 rounded-lg p-2 font-bold focus:border-blue-600 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Tgl Lahir (Password)</label>
                                <input type="date" name="tgl_lahir" x-model="formUser.tglLahir" required class="w-full border-2 border-gray-300 rounded-lg p-2 font-bold focus:border-blue-600 outline-none text-sm cursor-pointer">
                            </div>
                        </div>
                    </template>

                    <template x-if="activeRole === 'mahasiswa'">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Program Studi</label>
                                <select name="prodi" x-model="formUser.prodi" required class="w-full border-2 border-gray-300 rounded-lg p-2 font-bold outline-none focus:border-blue-600">
                                    <option value="" disabled>Pilih Prodi</option>
                                    <option value="TRIN">TRIN</option>
                                    <option value="TRO">TRO</option>
                                    <option value="TRMO">TRMO</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Kelas</label>
                                <input type="text" name="kelas" x-model="formUser.kelas" required class="w-full border-2 border-gray-300 rounded-lg p-2 font-bold outline-none focus:border-blue-600" placeholder="Cth: 3 AEC-2">
                            </div>
                        </div>
                    </template>

                    <template x-if="activeRole === 'mahasiswa' || activeRole === 'mentor'">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1" x-text="activeRole === 'mentor' ? 'Penempatan Sebagai Mentor (PT)' : 'Penempatan Magang (PT)'"></label>
                            <input type="text" name="pt" x-model="formUser.pt" required list="pt-list" class="w-full border-2 border-gray-300 rounded-lg p-2 font-bold outline-none focus:border-blue-600" placeholder="Ketik atau pilih PT...">
                            <datalist id="pt-list">
                                <template x-for="pt in companies" :key="pt"><option :value="pt"></option></template>
                            </datalist>
                        </div>
                    </template>

                    <div class="pt-6 flex justify-end gap-2">
                        <button type="button" @click="showModal = false" class="bg-gray-200 text-gray-700 font-bold px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">Batal</button>
                        <button type="submit" class="bg-blue-600 text-white font-bold px-6 py-2 rounded-lg border-2 border-blue-900 shadow-[2px_2px_0_0_#1e3a8a] hover:translate-y-px hover:translate-x-px hover:shadow-none transition-all">Simpan Akun</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL IMPORT EXCEL (Native Form Submit dengan Loading UI) -->
    <div x-show="showImportModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900/75" @click="closeImportModal()"></div>
            <div class="relative bg-white p-8 rounded-2xl border-4 border-green-900 w-full max-w-md shadow-2xl z-10">
                <h3 class="text-2xl font-black text-green-900 mb-1 uppercase tracking-tight">Upload Excel / CSV</h3>
                <p class="text-sm font-bold text-gray-500 mb-6" x-text="'Import massal data ' + activeRole"></p>
                
                <!-- Native Form Submission: memicu fungsi showProgress() sebelum mengirim data ke server -->
                <form action="{{ url('/admin/users/import') }}" method="POST" enctype="multipart/form-data" class="space-y-4" @submit="showProgress()">
                    @csrf
                    <input type="hidden" name="role" :value="activeRole">
                    
                    <!-- Dropzone / Input File Area -->
                    <div class="border-2 border-dashed border-gray-400 p-6 text-center rounded-xl transition-colors group relative" :class="isUploading ? 'bg-gray-100 opacity-60 pointer-events-none' : 'bg-gray-50 hover:bg-green-50 hover:border-green-400 cursor-pointer'">
                        <input type="file" name="file_excel" accept=".xlsx, .xls, .csv" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" @change="handleFileSelect" x-ref="fileInput">
                        
                        <!-- UI jika belum ada file yang dipilih -->
                        <div x-show="!selectedFileName">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-2 group-hover:text-green-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                            <p class="font-bold text-sm text-gray-600 group-hover:text-green-700">Pilih atau Tarik file Excel ke sini</p>
                        </div>

                        <!-- UI jika file sudah dipilih -->
                        <div x-show="selectedFileName" style="display: none;">
                            <svg class="w-12 h-12 text-green-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p class="font-bold text-sm text-green-800 break-words px-2" x-text="selectedFileName"></p>
                            <p class="text-xs font-bold text-green-600/70 mt-1" x-text="selectedFileSize"></p>
                        </div>
                    </div>

                    <!-- Progress Bar Animasi (Indeterminate saat kirim ke DB) -->
                    <div x-show="isUploading" style="display: none;" class="mt-4">
                        <div class="flex justify-between text-xs font-bold text-gray-600 mb-1.5">
                            <span>Mengunggah ke Database...</span>
                            <span x-text="uploadProgress + '%'"></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-green-500 h-2.5 rounded-full transition-all duration-300 ease-out" :style="'width: ' + uploadProgress + '%'"></div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-2">
                        <button type="button" @click="closeImportModal()" class="bg-gray-200 text-gray-700 font-bold px-6 py-2 rounded-lg hover:bg-gray-300 disabled:opacity-50" :disabled="isUploading">Batal</button>
                        <button type="submit" class="bg-green-600 text-white font-bold px-6 py-2 rounded-lg border-2 border-green-900 shadow-[2px_2px_0_0_#14532d] hover:translate-y-px hover:translate-x-px hover:shadow-none transition-all disabled:opacity-50 disabled:cursor-not-allowed" :disabled="!selectedFileName || isUploading">
                            <span x-text="isUploading ? 'Memproses...' : 'Mulai Import'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
    function userManagementApp() {
        return {
            activeRole: 'mahasiswa', 
            filterProdi: 'ALL',
            searchQuery: '',
            showModal: false,
            
            // State Upload Excel
            showImportModal: false,
            selectedFileName: '',
            selectedFileSize: '',
            isUploading: false,
            uploadProgress: 0,
            
            companies: ['PT Solusi Intek Indonesia', 'PT Dirgantara Indonesia', 'PT Pindad', 'PT Bukaka Teknik'],

            // DATA REAL DARI DATABASE
            users: @json($dbUsers ?? []),

            formUser: { name: '', identifier: '', prodi: '', kelas: '', pt: '', tglLahir: '' },

            setRole(role) {
                this.activeRole = role;
                this.filterProdi = 'ALL'; 
                this.searchQuery = '';
            },

            countUsers(role) {
                return this.users.filter(u => u.role === role).length;
            },

            getFilteredUsers() {
                return this.users.filter(u => {
                    let matchRole = u.role === this.activeRole;
                    let matchProdi = (this.activeRole === 'mahasiswa' && this.filterProdi !== 'ALL') ? u.prodi === this.filterProdi : true;
                    let query = this.searchQuery.toLowerCase();
                    let matchSearch = u.name.toLowerCase().includes(query) || 
                                      u.username.toLowerCase().includes(query) ||
                                      (u.identifier && u.identifier.toLowerCase().includes(query)) ||
                                      (u.pt && u.pt.toLowerCase().includes(query));

                    return matchRole && matchProdi && matchSearch;
                });
            },

            openModal() {
                this.formUser = { name: '', identifier: '', prodi: '', kelas: '', pt: '', tglLahir: '' };
                this.showModal = true;
            },

            deleteUser(id) {
                if(confirm('Hapus akun ini secara permanen?')) {
                    // TODO: Aksi AJAX untuk delete sebenarnya bisa ditambahkan di sini nantinya
                    alert('Di lingkungan produksi, ini akan menghapus ID: ' + id + ' dari Database.');
                }
            },

            // EVENT UPLOAD FILE
            handleFileSelect(event) {
                const file = event.target.files[0];
                if (file) {
                    this.selectedFileName = file.name;
                    let size = file.size / 1024;
                    this.selectedFileSize = size > 1024 ? (size / 1024).toFixed(2) + ' MB' : size.toFixed(1) + ' KB';
                } else {
                    this.selectedFileName = '';
                    this.selectedFileSize = '';
                }
            },

            closeImportModal() {
                this.showImportModal = false;
                this.selectedFileName = '';
                this.selectedFileSize = '';
                this.isUploading = false;
                this.uploadProgress = 0;
                if(this.$refs.fileInput) this.$refs.fileInput.value = '';
            },

            // Animasi Loading sambil browser mengirim form ke Laravel
            showProgress() {
                if (!this.selectedFileName) return;
                
                this.isUploading = true;
                this.uploadProgress = 0;

                // Naikkan progress bar perlahan sampai mentok di 90%
                // (100% akan tercapai secara otomatis saat Laravel me-reload halaman karena berhasil)
                let interval = setInterval(() => {
                    if (this.uploadProgress < 90) {
                        this.uploadProgress += Math.floor(Math.random() * 10) + 5;
                    } else {
                        clearInterval(interval);
                    }
                }, 300);
            }
        }
    }
</script>
@endsection