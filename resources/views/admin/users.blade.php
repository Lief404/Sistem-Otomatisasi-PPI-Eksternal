@extends('admin.app')
@section('title', 'Manajemen Akun')

@section('content')
<div x-data="userManagementApp()" class="space-y-6">

    <!-- Header Panel -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b-4 border-blue-900 pb-4">
        <div>
            <h2 class="text-3xl font-black text-blue-900 tracking-wide">Kelola Akun Sistem</h2>
            <p class="text-gray-600 mt-1 font-medium">Tambah, hapus, dan filter akun mahasiswa, dosen, mentor, serta admin.</p>
        </div>
        <button @click="openModal()" class="mt-4 md:mt-0 bg-blue-600 text-white font-bold py-2 px-6 rounded-lg border-2 border-blue-900 shadow-[4px_4px_0_0_#1e3a8a] hover:translate-y-px hover:translate-x-px hover:shadow-none transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
            Tambah Akun
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
            <div class="bg-blue-900 p-4 flex justify-between items-center text-white">
                <h3 class="text-xl font-black uppercase" x-text="'Daftar ' + activeRole"></h3>
                <span class="text-sm font-bold bg-blue-800 px-3 py-1 rounded-full" x-text="getFilteredUsers().length + ' Data Ditampilkan'"></span>
            </div>

            <div class="overflow-y-auto flex-1 p-4 bg-white">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b-2 border-gray-300 text-gray-600">
                            <th class="p-3 font-black text-sm uppercase">Nama / Identitas</th>
                            <th class="p-3 font-black text-sm uppercase">Email Login</th>
                            
                            <!-- Kolom Dinamis Berdasarkan Role -->
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
                                    Data tidak ditemukan.
                                </td>
                            </tr>
                        </template>

                        <template x-for="user in getFilteredUsers()" :key="user.id">
                            <tr class="border-b border-gray-100 hover:bg-blue-50 transition-colors">
                                <td class="p-3">
                                    <p class="font-black text-blue-900 text-base" x-text="user.name"></p>
                                    <p class="text-xs font-bold text-gray-500 uppercase" x-show="user.identifier" x-text="user.identifier"></p>
                                </td>
                                <td class="p-3 font-bold text-gray-700 text-sm" x-text="user.email"></td>
                                
                                <!-- Kolom Prodi Mhs -->
                                <template x-if="activeRole === 'mahasiswa'">
                                    <td class="p-3">
                                        <span class="bg-blue-100 text-blue-800 border border-blue-300 px-2 py-0.5 rounded text-xs font-black" x-text="user.prodi + ' - ' + user.kelas"></span>
                                    </td>
                                </template>

                                <!-- Kolom PT Mhs/Mentor -->
                                <template x-if="activeRole === 'mahasiswa' || activeRole === 'mentor'">
                                    <td class="p-3">
                                        <span class="bg-gray-100 border border-gray-300 text-gray-700 px-2 py-1 rounded text-xs font-bold whitespace-nowrap" x-text="user.pt || 'Belum Ditetapkan'"></span>
                                    </td>
                                </template>

                                <td class="p-3 text-center">
                                    <button @click="deleteUser(user.id)" class="text-red-500 hover:text-red-700 p-2 bg-red-50 border border-red-200 rounded hover:bg-red-100 transition-colors">
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

    <!-- MODAL TAMBAH AKUN (Disempurnakan) -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showModal = false"></div>

            <div x-show="showModal" x-transition.scale class="relative inline-block bg-white text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle max-w-lg w-full border-4 border-blue-900 rounded-xl">
                
                <div class="bg-blue-900 px-6 py-4 flex justify-between items-center text-white">
                    <h3 class="text-xl font-black uppercase" x-text="'Tambah Akun ' + activeRole"></h3>
                    <button @click="showModal = false" class="hover:text-red-400 transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form @submit.prevent="saveUser()" class="p-6 bg-gray-50 space-y-4">
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1" x-text="activeRole === 'mentor' ? 'Nama Industri / Perusahaan' : 'Nama Lengkap'"></label>
                        <input type="text" x-model="formUser.name" required class="w-full border-2 border-gray-300 rounded p-2 font-bold outline-none focus:border-blue-600" placeholder="Masukkan nama...">
                    </div>

                    <!-- Input Identitas Khusus Mahasiswa -->
                    <template x-if="activeRole === 'mahasiswa'">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-1">NIM Mahasiswa</label>
                                <input type="text" x-model="formUser.identifier" required class="w-full border-2 border-gray-300 rounded p-2 font-bold outline-none focus:border-blue-600" placeholder="Contoh: 223443026">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Program Studi</label>
                                <select x-model="formUser.prodi" required class="w-full border-2 border-gray-300 rounded p-2 font-bold outline-none focus:border-blue-600">
                                    <option value="" disabled>Pilih Prodi</option>
                                    <option value="TRIN">TRIN</option>
                                    <option value="TRO">TRO</option>
                                    <option value="TRMO">TRMO</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Kelas</label>
                                <input type="text" x-model="formUser.kelas" required class="w-full border-2 border-gray-300 rounded p-2 font-bold outline-none focus:border-blue-600" placeholder="Cth: 3 AEC-2">
                            </div>
                        </div>
                    </template>

                    <!-- Input Identitas Khusus Dosen -->
                    <template x-if="activeRole === 'dosen'">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">NIP Dosen</label>
                            <input type="text" x-model="formUser.identifier" required class="w-full border-2 border-gray-300 rounded p-2 font-bold outline-none focus:border-blue-600" placeholder="Masukkan NIP...">
                        </div>
                    </template>

                    <!-- Input Penempatan PT Khusus Mhs/Mentor -->
                    <template x-if="activeRole === 'mahasiswa' || activeRole === 'mentor'">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1" x-text="activeRole === 'mentor' ? 'Penempatan Sebagai Mentor di PT' : 'Penempatan Magang (PT)'"></label>
                            <input type="text" x-model="formUser.pt" required list="pt-list" class="w-full border-2 border-gray-300 rounded p-2 font-bold outline-none focus:border-blue-600" placeholder="Ketik atau pilih PT...">
                            <datalist id="pt-list">
                                <template x-for="pt in companies" :key="pt"><option :value="pt"></option></template>
                            </datalist>
                        </div>
                    </template>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Email Login</label>
                        <div class="flex items-center border-2 border-gray-300 rounded overflow-hidden focus-within:border-blue-600 transition-colors bg-white">
                            <input type="text" x-model="formUser.emailPrefix" required class="w-full p-2 font-black outline-none" placeholder="username">
                            <span class="bg-gray-100 px-3 py-2 text-sm font-bold text-gray-600 border-l-2 border-gray-300 whitespace-nowrap" x-text="getEmailDomain()"></span>
                        </div>
                    </div>

                    <div class="pt-4 border-t-2 border-gray-200 flex justify-end">
                        <button type="submit" class="bg-green-500 text-white font-black px-6 py-2 rounded-lg border-2 border-green-900 shadow-[3px_3px_0_0_#14532d] hover:translate-y-px hover:translate-x-px hover:shadow-none transition-all">
                            Simpan Akun Baru
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
            
            companies: ['PT Solusi Intek Indonesia', 'PT Dirgantara Indonesia', 'PT Pindad', 'PT Bukaka Teknik'],

            // DUMMY DATA AKUN DENGAN PRODI & KELAS (Khusus Mhs)
            users: [
                { id: 1, role: 'mahasiswa', name: 'Alief Muhammad S', identifier: 'NIM: 223443026', prodi: 'TRIN', kelas: '3 AEC-2', email: '223443026@mhs.polman', pt: 'PT Solusi Intek Indonesia' },
                { id: 2, role: 'mahasiswa', name: 'Daffa Khairul Ammar', identifier: 'NIM: 223443025', prodi: 'TRO', kelas: '3 AEC-2', email: '223443025@mhs.polman', pt: 'PT Dirgantara Indonesia' },
                { id: 3, role: 'dosen', name: 'Supriyadi, S.T., M.T.', identifier: 'NIP: 198001012005', email: 'supriyadi@dosen.polman', pt: null },
                { id: 4, role: 'dosen', name: 'Ahmad Fakhri, S.T.', identifier: 'NIP: 198502022010', email: 'fakhri@dosen.polman', pt: null },
                { id: 5, role: 'mentor', name: 'PT Bukaka Teknik', identifier: null, email: 'bukaka@industri.id', pt: 'PT Bukaka Teknik' },
                { id: 6, role: 'mentor', name: 'PT Solusi Intek Indonesia', identifier: null, email: 'solusi@industri.id', pt: 'PT Solusi Intek Indonesia' },
                { id: 7, role: 'admin', name: 'Super Administrator', identifier: null, email: 'admin@admin.polman', pt: null },
            ],

            formUser: { name: '', identifier: '', prodi: '', kelas: '', pt: '', emailPrefix: '' },

            setRole(role) {
                this.activeRole = role;
                this.filterProdi = 'ALL'; // Reset sub-filter tiap ganti tab
                this.searchQuery = '';
            },

            countUsers(role) {
                return this.users.filter(u => u.role === role).length;
            },

            getFilteredUsers() {
                return this.users.filter(u => {
                    // 1. Filter Role
                    let matchRole = u.role === this.activeRole;
                    // 2. Filter Prodi (Hanya jika role mahasiswa dan filter bukan ALL)
                    let matchProdi = (this.activeRole === 'mahasiswa' && this.filterProdi !== 'ALL') ? u.prodi === this.filterProdi : true;
                    // 3. Filter Pencarian Teks
                    let query = this.searchQuery.toLowerCase();
                    let matchSearch = u.name.toLowerCase().includes(query) || 
                                      (u.identifier && u.identifier.toLowerCase().includes(query)) ||
                                      u.email.toLowerCase().includes(query) ||
                                      (u.pt && u.pt.toLowerCase().includes(query));

                    return matchRole && matchProdi && matchSearch;
                });
            },

            openModal() {
                this.formUser = { name: '', identifier: '', prodi: '', kelas: '', pt: '', emailPrefix: '' };
                this.showModal = true;
            },

            getEmailDomain() {
                if(this.activeRole === 'mahasiswa') return '@mhs.polman';
                if(this.activeRole === 'dosen') return '@dosen.polman';
                if(this.activeRole === 'mentor') return '@industri.id';
                return '@admin.polman'; 
            },

            saveUser() {
                let newId = this.users.length ? Math.max(...this.users.map(u => u.id)) + 1 : 1;
                let fullEmail = this.formUser.emailPrefix + this.getEmailDomain();
                
                let idText = null;
                if(this.activeRole === 'mahasiswa') idText = 'NIM: ' + this.formUser.identifier;
                if(this.activeRole === 'dosen') idText = 'NIP: ' + this.formUser.identifier;

                this.users.push({
                    id: newId,
                    role: this.activeRole,
                    name: this.formUser.name,
                    identifier: idText,
                    prodi: this.activeRole === 'mahasiswa' ? this.formUser.prodi : null,
                    kelas: this.activeRole === 'mahasiswa' ? this.formUser.kelas : null,
                    email: fullEmail,
                    pt: (this.activeRole === 'mahasiswa' || this.activeRole === 'mentor') ? this.formUser.pt : null
                });

                if(this.formUser.pt && !this.companies.includes(this.formUser.pt)) {
                    this.companies.push(this.formUser.pt);
                }

                alert(`Akun ${this.activeRole} baru berhasil ditambahkan!`);
                this.showModal = false;
            },

            deleteUser(id) {
                if(confirm('Yakin ingin menghapus akun ini secara permanen?')) {
                    this.users = this.users.filter(u => u.id !== id);
                }
            }
        }
    }
</script>
@endsection