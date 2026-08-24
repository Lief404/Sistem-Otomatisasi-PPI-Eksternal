@extends('admin.app')
@section('title', 'Dashboard Administrator')

@section('content')
<div x-data="adminApp()" class="space-y-8">

    <!-- Header Panel -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b-4 border-blue-900 pb-4">
        <div>
            <h2 class="text-3xl font-black text-blue-900 tracking-wide">Pusat Kendali Sistem</h2>
            <p class="text-gray-600 mt-1 font-medium">Manajemen Master Data, Pengaturan Jam Minimum, dan Mapping Penempatan PPI.</p>
        </div>
    </div>

    <!-- Statistik Ringkas -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-blue-100 p-4 rounded-xl border-2 border-blue-900 shadow-[4px_4px_0_0_#1e3a8a]">
            <h3 class="text-sm font-bold text-blue-900 uppercase">Mahasiswa Aktif</h3>
            <p class="text-3xl font-black text-blue-700 mt-1" x-text="users.filter(u => u.role === 'mahasiswa').length"></p>
        </div>
        <div class="bg-yellow-50 p-4 rounded-xl border-2 border-yellow-900 shadow-[4px_4px_0_0_#713f12]">
            <h3 class="text-sm font-bold text-yellow-900 uppercase">Dosen Pembimbing</h3>
            <p class="text-3xl font-black text-yellow-700 mt-1" x-text="users.filter(u => u.role === 'dosen').length"></p>
        </div>
        <div class="bg-green-50 p-4 rounded-xl border-2 border-green-900 shadow-[4px_4px_0_0_#14532d]">
            <h3 class="text-sm font-bold text-green-900 uppercase">Mentor Industri</h3>
            <p class="text-3xl font-black text-green-700 mt-1" x-text="users.filter(u => u.role === 'mentor').length"></p>
        </div>
        <div class="bg-purple-50 p-4 rounded-xl border-2 border-purple-900 shadow-[4px_4px_0_0_#4c1d95]">
            <h3 class="text-sm font-bold text-purple-900 uppercase">Perusahaan (PT)</h3>
            <p class="text-3xl font-black text-purple-700 mt-1" x-text="companies.length"></p>
        </div>
    </div>

    <!-- PENGATURAN SISTEM (Grid 2 Kolom) -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        
        <!-- Setting Jam Minimum -->
        <div class="bg-white p-6 rounded-xl border-2 border-blue-900 shadow-[6px_6px_0_0_#1e3a8a]">
            <div class="flex items-center gap-3 border-b-2 border-blue-100 pb-3 mb-4">
                <svg class="w-8 h-8 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h3 class="text-xl font-black text-blue-900">Setting Jam Minimum PPI</h3>
            </div>
            <p class="text-sm font-bold text-gray-500 mb-4">Atur target jam minimal untuk setiap komponen mata kuliah yang berlaku bagi seluruh mahasiswa.</p>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">PII (Perekayasa)</label>
                    <div class="flex items-center border-2 border-gray-300 rounded overflow-hidden focus-within:border-blue-600 transition-colors">
                        <input type="number" x-model.number="minHours.pii" class="w-full p-2 font-black text-blue-900 outline-none text-center">
                        <span class="bg-gray-100 px-3 py-2 text-xs font-bold text-gray-600 border-l-2 border-gray-300">Jam</span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Supervisi (SUP)</label>
                    <div class="flex items-center border-2 border-gray-300 rounded overflow-hidden focus-within:border-blue-600 transition-colors">
                        <input type="number" x-model.number="minHours.sup" class="w-full p-2 font-black text-blue-900 outline-none text-center">
                        <span class="bg-gray-100 px-3 py-2 text-xs font-bold text-gray-600 border-l-2 border-gray-300">Jam</span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Keselamatan Kerja (K3)</label>
                    <div class="flex items-center border-2 border-gray-300 rounded overflow-hidden focus-within:border-blue-600 transition-colors">
                        <input type="number" x-model.number="minHours.k3" class="w-full p-2 font-black text-blue-900 outline-none text-center">
                        <span class="bg-gray-100 px-3 py-2 text-xs font-bold text-gray-600 border-l-2 border-gray-300">Jam</span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Laporan Teknik (LTD)</label>
                    <div class="flex items-center border-2 border-gray-300 rounded overflow-hidden focus-within:border-blue-600 transition-colors">
                        <input type="number" x-model.number="minHours.ltd" class="w-full p-2 font-black text-blue-900 outline-none text-center">
                        <span class="bg-gray-100 px-3 py-2 text-xs font-bold text-gray-600 border-l-2 border-gray-300">Jam</span>
                    </div>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button @click="saveSettings()" class="bg-green-500 text-white font-bold py-2 px-6 rounded-lg border-2 border-green-900 shadow-[3px_3px_0_0_#14532d] hover:translate-y-px hover:translate-x-px hover:shadow-none transition-all">
                    Simpan Pengaturan Jam
                </button>
            </div>
        </div>

        <!-- Mapping Dosen ke PT -->
        <div class="bg-white p-6 rounded-xl border-2 border-blue-900 shadow-[6px_6px_0_0_#1e3a8a] flex flex-col">
            <div class="flex items-center gap-3 border-b-2 border-blue-100 pb-3 mb-4">
                <svg class="w-8 h-8 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <h3 class="text-xl font-black text-blue-900">Jadwal Monitoring Dosen Pembimbing</h3>
            </div>
            
            <form @submit.prevent="addMapping()" class="flex flex-col gap-3 mb-6 bg-blue-50 p-4 rounded-lg border-2 border-blue-200">
                <p class="text-xs font-bold text-gray-500 uppercase mb-1">Tetapkan Jadwal & Penempatan</p>
                <div class="flex flex-col md:flex-row gap-2">
                    <select x-model="mapForm.dosenId" required class="flex-1 border-2 border-gray-300 rounded p-2 text-sm font-bold outline-none focus:border-blue-600">
                        <option value="" disabled>-- Pilih Dosen --</option>
                        <template x-for="d in users.filter(u => u.role === 'dosen')" :key="d.id">
                            <option :value="d.id" x-text="d.name"></option>
                        </template>
                    </select>
                    <select x-model="mapForm.ptName" required class="flex-1 border-2 border-gray-300 rounded p-2 text-sm font-bold outline-none focus:border-blue-600">
                        <option value="" disabled>-- Pilih PT --</option>
                        <template x-for="pt in companies" :key="pt">
                            <option :value="pt" x-text="pt"></option>
                        </template>
                    </select>
                </div>
                <div class="flex flex-col md:flex-row gap-2">
                    <input type="date" x-model="mapForm.date" required class="flex-1 border-2 border-gray-300 rounded p-2 text-sm font-bold outline-none focus:border-blue-600">
                    <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-4 rounded border-2 border-blue-900 hover:bg-blue-700 transition-colors whitespace-nowrap">
                        Tetapkan
                    </button>
                </div>
            </form>

            <div class="flex-1 overflow-y-auto max-h-40 border-2 border-gray-200 rounded-lg p-2 bg-gray-50">
                <template x-if="mappings.length === 0">
                    <p class="text-center text-xs font-bold text-gray-400 py-4">Belum ada jadwal yang ditetapkan.</p>
                </template>
                <div class="space-y-2">
                    <template x-for="(map, idx) in mappings" :key="index">
                        <div class="bg-white p-2 rounded border border-gray-300 flex justify-between items-center text-sm shadow-sm">
                            <div>
                                <p class="font-black text-blue-900" x-text="getUserName(map.dosenId)"></p>
                                <p class="font-bold text-gray-600 text-xs" x-text="map.ptName + ' | ' + formatDate(map.date)"></p>
                            </div>
                            <button @click="deleteMapping(idx)" class="text-red-500 hover:text-red-700 font-bold p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

    </div>

    <!-- MANAJEMEN AKUN (CRUD) -->
    <div class="bg-white rounded-xl border-2 border-blue-900 shadow-[6px_6px_0_0_#1e3a8a] overflow-hidden">
        
        <!-- Tab Navigasi Role -->
        <div class="flex flex-wrap bg-blue-50 border-b-2 border-blue-900 p-2 gap-2">
            <button @click="activeTab = 'mahasiswa'" :class="activeTab === 'mahasiswa' ? 'bg-blue-600 text-white border-blue-900 shadow-[2px_2px_0_0_#1e3a8a]' : 'bg-white text-gray-600 border-gray-300 hover:bg-blue-100'" class="px-6 py-2 rounded-lg font-black border-2 transition-all">Mahasiswa</button>
            <button @click="activeTab = 'dosen'" :class="activeTab === 'dosen' ? 'bg-blue-600 text-white border-blue-900 shadow-[2px_2px_0_0_#1e3a8a]' : 'bg-white text-gray-600 border-gray-300 hover:bg-blue-100'" class="px-6 py-2 rounded-lg font-black border-2 transition-all">Dosen</button>
            <button @click="activeTab = 'mentor'" :class="activeTab === 'mentor' ? 'bg-blue-600 text-white border-blue-900 shadow-[2px_2px_0_0_#1e3a8a]' : 'bg-white text-gray-600 border-gray-300 hover:bg-blue-100'" class="px-6 py-2 rounded-lg font-black border-2 transition-all">Mentor PT</button>
            <button @click="activeTab = 'admin'" :class="activeTab === 'admin' ? 'bg-blue-600 text-white border-blue-900 shadow-[2px_2px_0_0_#1e3a8a]' : 'bg-white text-gray-600 border-gray-300 hover:bg-blue-100'" class="px-6 py-2 rounded-lg font-black border-2 transition-all">Admin</button>
        </div>

        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-2xl font-black text-blue-900 uppercase" x-text="'Manajemen ' + activeTab"></h3>
                    <p class="text-sm font-bold text-gray-500" x-text="'Kelola data akun ' + activeTab + ' di sistem.'"></p>
                </div>
                <button @click="openModal('add')" class="bg-blue-600 text-white font-bold py-2 px-6 rounded-lg border-2 border-blue-900 shadow-[3px_3px_0_0_#1e3a8a] hover:translate-y-px hover:translate-x-px hover:shadow-none transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Akun
                </button>
            </div>

            <!-- Tabel Dinamis -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 border-y-2 border-gray-300">
                            <th class="p-4 font-black text-gray-700 w-16">ID</th>
                            <th class="p-4 font-black text-gray-700">Nama / Identitas</th>
                            <th class="p-4 font-black text-gray-700">Akun Email</th>
                            <!-- Kolom Khusus PT: Muncul untuk Mhs dan Mentor -->
                            <th class="p-4 font-black text-gray-700" x-show="activeTab === 'mahasiswa' || activeTab === 'mentor'">Penempatan PT</th>
                            <!-- Kolom Khusus Dosen: Daftar PT -->
                            <th class="p-4 font-black text-gray-700" x-show="activeTab === 'dosen'">PT Binaan Terjadwal</th>
                            <th class="p-4 font-black text-gray-700 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="getFilteredUsers().length === 0">
                            <tr>
                                <td colspan="5" class="p-8 text-center text-gray-400 font-bold italic">Belum ada akun terdaftar untuk role ini.</td>
                            </tr>
                        </template>
                        <template x-for="(user, index) in getFilteredUsers()" :key="user.id">
                            <tr class="border-b border-gray-200 hover:bg-blue-50 transition-colors">
                                <td class="p-4 font-bold text-gray-500" x-text="user.id"></td>
                                <td class="p-4">
                                    <p class="font-black text-blue-900 text-lg" x-text="user.name"></p>
                                    <p class="text-xs font-bold text-gray-500 uppercase" x-show="user.identifier" x-text="user.identifier"></p>
                                </td>
                                <td class="p-4 font-bold text-gray-700" x-text="user.email"></td>
                                
                                <!-- Kolom PT Mhs/Mentor -->
                                <td class="p-4 font-bold text-gray-700" x-show="activeTab === 'mahasiswa' || activeTab === 'mentor'">
                                    <span class="bg-gray-200 px-2 py-1 rounded text-xs" x-text="user.pt || '-'"></span>
                                </td>
                                
                                <!-- Kolom PT Dosen (Dihitung dari Mappings) -->
                                <td class="p-4" x-show="activeTab === 'dosen'">
                                    <div class="flex flex-wrap gap-1">
                                        <template x-if="getDosenMappings(user.id).length === 0">
                                            <span class="text-xs font-bold text-gray-400 italic">Belum ada penugasan</span>
                                        </template>
                                        <template x-for="pt in getDosenMappings(user.id)">
                                            <span class="bg-blue-100 text-blue-800 border border-blue-300 px-2 py-0.5 rounded text-[10px] font-black" x-text="pt"></span>
                                        </template>
                                    </div>
                                </td>

                                <td class="p-4 text-center">
                                    <button @click="deleteUser(user.id)" class="text-red-500 hover:text-red-700 p-2 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH AKUN -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showModal = false"></div>

            <div x-show="showModal" x-transition.scale class="relative inline-block bg-white text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle max-w-lg w-full border-4 border-blue-900 rounded-xl">
                
                <div class="bg-blue-900 px-6 py-4 flex justify-between items-center text-white">
                    <h3 class="text-xl font-black uppercase" x-text="'Tambah Akun ' + activeTab"></h3>
                    <button @click="showModal = false" class="hover:text-red-400 transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form @submit.prevent="saveUser()" class="p-6 bg-gray-50 space-y-4">
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1" x-text="activeTab === 'mentor' ? 'Nama Industri / Perusahaan' : 'Nama Lengkap'"></label>
                        <input type="text" x-model="formUser.name" required class="w-full border-2 border-gray-300 rounded p-2 font-bold outline-none focus:border-blue-600" placeholder="Masukkan nama...">
                    </div>

                    <!-- Input Identitas Khusus -->
                    <template x-if="activeTab === 'mahasiswa'">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">NIM Mahasiswa</label>
                            <input type="text" x-model="formUser.identifier" required class="w-full border-2 border-gray-300 rounded p-2 font-bold outline-none focus:border-blue-600" placeholder="Contoh: 223443026">
                        </div>
                    </template>
                    <template x-if="activeTab === 'dosen'">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">NIP Dosen</label>
                            <input type="text" x-model="formUser.identifier" required class="w-full border-2 border-gray-300 rounded p-2 font-bold outline-none focus:border-blue-600" placeholder="Masukkan NIP...">
                        </div>
                    </template>

                    <!-- Input Penempatan PT Khusus Mhs/Mentor -->
                    <template x-if="activeTab === 'mahasiswa' || activeTab === 'mentor'">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1" x-text="activeTab === 'mentor' ? 'Penempatan Sebagai Mentor di PT' : 'Penempatan Magang (PT)'"></label>
                            <!-- Input bisa diketik atau pilih -->
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
    function adminApp() {
        return {
            activeTab: 'mahasiswa', // Default tab
            showModal: false,
            
            // Pengaturan Jam Minimum Global
            minHours: {
                pii: 400,
                sup: 100,
                k3: 50,
                ltd: 50
            },

            // Dummy Master Data PT
            companies: ['PT Solusi Intek Indonesia', 'PT Dirgantara Indonesia', 'PT Pindad', 'PT Bukaka Teknik'],

            // Dummy Data Akun Sistem
            users: [
                { id: 1, role: 'mahasiswa', name: 'Alief Muhammad S', identifier: 'NIM: 223443026', email: '223443026@mhs.polman', pt: 'PT Solusi Intek Indonesia' },
                { id: 2, role: 'mahasiswa', name: 'Daffa Khairul Ammar', identifier: 'NIM: 223443025', email: '223443025@mhs.polman', pt: 'PT Dirgantara Indonesia' },
                { id: 3, role: 'dosen', name: 'Supriyadi, S.T., M.T.', identifier: 'NIP: 198001012005', email: 'supriyadi@dosen.polman', pt: null },
                { id: 4, role: 'dosen', name: 'Ahmad Fakhri, S.T.', identifier: 'NIP: 198502022010', email: 'fakhri@dosen.polman', pt: null },
                { id: 5, role: 'mentor', name: 'PT Bukaka Teknik', identifier: null, email: 'bukaka@industri.id', pt: 'PT Bukaka Teknik' },
                { id: 6, role: 'mentor', name: 'PT Solusi Intek Indonesia', identifier: null, email: 'solusi@industri.id', pt: 'PT Solusi Intek Indonesia' },
                { id: 7, role: 'admin', name: 'Super Administrator', identifier: null, email: 'admin@admin.polman', pt: null },
            ],

            // State Data Jadwal Mapping Dosen -> PT
            mappings: [
                { dosenId: 3, ptName: 'PT Solusi Intek Indonesia', date: '2026-08-25' },
                { dosenId: 3, ptName: 'PT Dirgantara Indonesia', date: '2026-08-28' },
            ],

            // State Form
            mapForm: { dosenId: '', ptName: '', date: '' },
            formUser: { name: '', identifier: '', pt: '', emailPrefix: '' },

            // FUNGSI TABEL AKUN
            getFilteredUsers() {
                return this.users.filter(u => u.role === this.activeTab);
            },

            openModal() {
                this.formUser = { name: '', identifier: '', pt: '', emailPrefix: '' };
                this.showModal = true;
            },

            getEmailDomain() {
                if(this.activeTab === 'mahasiswa') return '@mhs.polman';
                if(this.activeTab === 'dosen') return '@dosen.polman';
                if(this.activeTab === 'mentor') return '@industri.id';
                return '@admin.polman'; // admin
            },

            saveUser() {
                let newId = this.users.length ? Math.max(...this.users.map(u => u.id)) + 1 : 1;
                let fullEmail = this.formUser.emailPrefix + this.getEmailDomain();
                
                // Format ulang identifier
                let idText = null;
                if(this.activeTab === 'mahasiswa') idText = 'NIM: ' + this.formUser.identifier;
                if(this.activeTab === 'dosen') idText = 'NIP: ' + this.formUser.identifier;

                this.users.push({
                    id: newId,
                    role: this.activeTab,
                    name: this.formUser.name,
                    identifier: idText,
                    email: fullEmail,
                    pt: (this.activeTab === 'mahasiswa' || this.activeTab === 'mentor') ? this.formUser.pt : null
                });

                // Jika PT yang diinput belum ada di master data perusahaan, tambahkan otomatis
                if(this.formUser.pt && !this.companies.includes(this.formUser.pt)) {
                    this.companies.push(this.formUser.pt);
                }

                alert(`Akun ${this.activeTab} baru berhasil ditambahkan!`);
                this.showModal = false;
            },

            deleteUser(id) {
                if(confirm('Yakin ingin menghapus akun ini secara permanen?')) {
                    this.users = this.users.filter(u => u.id !== id);
                }
            },

            // FUNGSI MAPPING DOSEN
            getUserName(id) {
                let u = this.users.find(u => u.id == id);
                return u ? u.name : 'Unknown';
            },

            getDosenMappings(dosenId) {
                // Mengembalikan daftar PT yang dibina oleh dosen bersangkutan
                return this.mappings.filter(m => m.dosenId === dosenId).map(m => m.ptName);
            },

            formatDate(dateString) {
                if(!dateString) return '';
                const options = { day: '2-digit', month: 'short', year: 'numeric' };
                return new Date(dateString).toLocaleDateString('id-ID', options);
            },

            addMapping() {
                this.mappings.push({
                    dosenId: parseInt(this.mapForm.dosenId),
                    ptName: this.mapForm.ptName,
                    date: this.mapForm.date
                });
                alert('Jadwal monitoring berhasil ditetapkan!');
                this.mapForm = { dosenId: '', ptName: '', date: '' };
            },

            deleteMapping(index) {
                if(confirm('Hapus jadwal ini?')) {
                    this.mappings.splice(index, 1);
                }
            },

            // PENGATURAN UMUM
            saveSettings() {
                alert(`Pengaturan Jam Minimum berhasil disimpan!\nPII: ${this.minHours.pii} | SUP: ${this.minHours.sup} | K3: ${this.minHours.k3} | LTD: ${this.minHours.ltd}`);
            }
        }
    }
</script>
@endsection