@extends('admin.app')
@section('title', 'Manajemen Akun & Perusahaan')

@section('content')
<div x-data="userManagementApp()" class="space-y-6">

    <!-- Header Panel -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b-4 border-blue-900 pb-4">
        <div>
            <h2 class="text-3xl font-black text-blue-900 tracking-wide">Kelola Akun & Perusahaan</h2>
            <p class="text-gray-600 mt-1 font-medium">Tambah, hapus, filter akun, serta atur pembimbing dosen & mentor untuk mahasiswa.</p>
        </div>
        <div class="mt-4 md:mt-0 flex flex-wrap gap-3">
            <button @click="openPerusahaanModal()" class="bg-emerald-600 text-white font-bold py-2 px-5 rounded-lg border-2 border-emerald-900 shadow-[4px_4px_0_0_#065f46] hover:translate-y-px hover:translate-x-px hover:shadow-none transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9m4 0v10"></path></svg>
                + Perusahaan Baru
            </button>
            <button @click="openAddModal()" class="bg-blue-600 text-white font-bold py-2 px-6 rounded-lg border-2 border-blue-900 shadow-[4px_4px_0_0_#1e3a8a] hover:translate-y-px hover:translate-x-px hover:shadow-none transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                + Tambah Akun
            </button>
        </div>
    </div>

    <!-- MAIN CARD -->
    <div class="bg-white rounded-xl border-2 border-blue-900 shadow-[6px_6px_0_0_#1e3a8a] overflow-hidden flex flex-col md:flex-row min-h-[600px]">
        
        <!-- SIDEBAR FILTER (Kiri) -->
        <div class="w-full md:w-64 bg-blue-50 border-r-2 border-blue-200 p-4 flex flex-col gap-6 flex-shrink-0">
            
            <!-- Kategori Role Utama -->
            <div>
                <p class="text-xs font-black text-blue-900 uppercase tracking-widest mb-3 border-b-2 border-blue-200 pb-1">Kategori Data</p>
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
                    <button @click="setRole('kaprodi')" :class="activeRole === 'kaprodi' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-gray-600 hover:bg-blue-100'" class="w-full text-left px-4 py-2 rounded-lg font-bold border border-blue-200 transition-all flex justify-between items-center">
                        KPS <span class="text-xs bg-black/10 px-2 py-0.5 rounded-full" x-text="countUsers('kaprodi')"></span>
                    </button>
                    <button @click="setRole('perusahaan')" :class="activeRole === 'perusahaan' ? 'bg-emerald-600 text-white shadow-md' : 'bg-white text-gray-600 hover:bg-emerald-100'" class="w-full text-left px-4 py-2 rounded-lg font-bold border border-emerald-200 transition-all flex justify-between items-center">
                        Perusahaan / PT <span class="text-xs bg-black/10 px-2 py-0.5 rounded-full" x-text="perusahaans.length"></span>
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
            <div :class="activeRole === 'perusahaan' ? 'bg-emerald-800' : 'bg-blue-900'" class="p-4 flex justify-between items-center text-white transition-colors">
                <h3 class="text-xl font-black uppercase" x-text="activeRole === 'perusahaan' ? 'Daftar Perusahaan / Industri' : 'Daftar ' + activeRole"></h3>
                <span class="text-sm font-bold bg-black/20 px-3 py-1 rounded-full" x-text="activeRole === 'perusahaan' ? getFilteredPerusahaans().length + ' Data Ditampilkan' : getFilteredUsers().length + ' Data Ditampilkan'"></span>
            </div>

            <div class="overflow-y-auto flex-1 p-4 bg-white">
                
                <!-- TABEL AKUN USER -->
                <template x-if="activeRole !== 'perusahaan'">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b-2 border-gray-300 text-gray-600">
                                <th class="p-3 font-black text-sm uppercase">Nama / Identitas</th>
                                <th class="p-3 font-black text-sm uppercase">Email Login</th>
                                <th class="p-3 font-black text-sm uppercase">Password</th>
                                
                                <template x-if="activeRole === 'mahasiswa'">
                                    <th class="p-3 font-black text-sm uppercase">Prodi & Kelas</th>
                                </template>
                                
                                <template x-if="activeRole === 'mahasiswa'">
                                    <th class="p-3 font-black text-sm uppercase">Dosen Pembimbing</th>
                                </template>

                                <template x-if="activeRole === 'mahasiswa' || activeRole === 'mentor'">
                                    <th class="p-3 font-black text-sm uppercase">Mentor / Penempatan PT</th>
                                </template>

                                <th class="p-3 font-black text-sm uppercase text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-if="getFilteredUsers().length === 0">
                                <tr>
                                    <td :colspan="activeRole === 'mahasiswa' ? 7 : (activeRole === 'mentor' ? 5 : 4)" class="p-10 text-center text-gray-400 font-bold italic border-2 border-dashed border-gray-200 mt-4 rounded-xl">
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
                                    
                                    <!-- Kolom Password (Posisi ke-3, sejajar dengan Header Password) -->
                                    <td class="p-3" x-data="{ showPw: false }">
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-mono text-xs font-bold" :class="showPw ? 'text-gray-800' : 'text-gray-400'" x-text="showPw ? (user.plain_password || '—') : '••••••••'"></span>
                                            <button type="button" @click="showPw = !showPw" class="text-gray-400 hover:text-gray-700 transition-colors p-1 rounded hover:bg-gray-100" title="Lihat/Sembunyikan Password">
                                                <svg x-show="!showPw" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                <svg x-show="showPw" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                            </button>
                                        </div>
                                    </td>

                                    <template x-if="activeRole === 'mahasiswa'">
                                        <td class="p-3">
                                            <span class="bg-blue-100 text-blue-800 border border-blue-300 px-2 py-0.5 rounded text-xs font-black" x-text="user.prodi + ' - ' + user.kelas"></span>
                                        </td>
                                    </template>

                                    <!-- Kolom Dosen Pembimbing untuk Mahasiswa -->
                                    <template x-if="activeRole === 'mahasiswa'">
                                        <td class="p-3">
                                            <span :class="user.dosen_name ? 'bg-purple-100 border-purple-300 text-purple-900' : 'bg-gray-100 border-gray-300 text-gray-400 italic'" class="border px-2 py-1 rounded text-xs font-bold whitespace-nowrap" x-text="user.dosen_name || 'Belum Ditentukan'"></span>
                                        </td>
                                    </template>

                                    <!-- Kolom PT / Mentor -->
                                    <template x-if="activeRole === 'mahasiswa' || activeRole === 'mentor'">
                                        <td class="p-3">
                                            <template x-if="activeRole === 'mahasiswa'">
                                                <div>
                                                    <p class="font-bold text-gray-800 text-xs" x-text="user.pt || 'PT Belum Set'"></p>
                                                    <p class="text-[11px] text-amber-700 font-semibold" x-show="user.mentor_name" x-text="'Mentor: ' + user.mentor_name"></p>
                                                </div>
                                            </template>
                                            <template x-if="activeRole === 'mentor'">
                                                <span class="bg-gray-100 border border-gray-300 text-gray-700 px-2 py-1 rounded text-xs font-bold whitespace-nowrap" x-text="user.pt || 'Belum Ditetapkan'"></span>
                                            </template>
                                        </td>
                                    </template>

                                    <td class="p-3 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <template x-if="activeRole === 'mahasiswa'">
                                                <button @click="openAssignModal(user)" class="text-blue-700 hover:text-blue-900 p-2 bg-blue-100 border border-blue-300 rounded hover:bg-blue-200 transition-colors" title="Atur Pembimbing Dosen & Mentor">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                                </button>
                                            </template>
                                            <button @click="openEditModal(user)" class="text-yellow-600 hover:text-yellow-800 p-2 bg-yellow-50 border border-yellow-200 rounded hover:bg-yellow-100 transition-colors" title="Edit Akun">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>
                                            <button @click="openPasswordModal(user)" class="text-purple-600 hover:text-purple-800 p-2 bg-purple-50 border border-purple-200 rounded hover:bg-purple-100 transition-colors" title="Ubah Password">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                            </button>
                                            <button @click="deleteUser(user.id)" class="text-red-500 hover:text-red-700 p-2 bg-red-50 border border-red-200 rounded hover:bg-red-100 transition-colors" title="Hapus Akun">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </template>

                <!-- TABEL PERUSAHAAN -->
                <template x-if="activeRole === 'perusahaan'">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b-2 border-gray-300 text-gray-600">
                                <th class="p-3 font-black text-sm uppercase">Nama Perusahaan / PT</th>
                                <th class="p-3 font-black text-sm uppercase">Alamat</th>
                                <th class="p-3 font-black text-sm uppercase">Kontak</th>
                                <th class="p-3 font-black text-sm uppercase text-center w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-if="getFilteredPerusahaans().length === 0">
                                <tr>
                                    <td colspan="4" class="p-10 text-center text-gray-400 font-bold italic border-2 border-dashed border-gray-200 mt-4 rounded-xl">
                                        Belum ada data perusahaan. Klik tombol "+ Perusahaan Baru" untuk membuat.
                                    </td>
                                </tr>
                            </template>

                            <template x-for="p in getFilteredPerusahaans()" :key="p.id">
                                <tr class="border-b border-gray-100 hover:bg-emerald-50 transition-colors">
                                    <td class="p-3 font-black text-emerald-900 text-base" x-text="p.nama_perusahaan"></td>
                                    <td class="p-3 font-bold text-gray-700 text-sm" x-text="p.alamat || '-'"></td>
                                    <td class="p-3 font-bold text-gray-700 text-sm" x-text="p.kontak || '-'"></td>
                                    <td class="p-3 text-center">
                                        <button @click="deletePerusahaan(p.id)" class="text-red-500 hover:text-red-700 p-2 bg-red-50 border border-red-200 rounded hover:bg-red-100 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </template>

            </div>
        </div>

    </div>

    <!-- MODAL TAMBAH AKUN -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showModal = false"></div>

            <div x-show="showModal" x-transition.scale class="relative inline-block bg-white text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle max-w-lg w-full border-4 border-blue-900 rounded-xl">
                
                <div class="bg-blue-900 px-6 py-4 flex justify-between items-center text-white">
                    <h3 class="text-xl font-black uppercase" x-text="(isEdit ? 'Edit Akun ' : 'Tambah Akun ') + (activeRole === 'perusahaan' ? 'Mahasiswa' : activeRole)"></h3>
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
                    <template x-if="activeRole === 'mahasiswa' || activeRole === 'perusahaan'">
                        <div class="space-y-4">
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

                            <!-- Pilihan Dosen Pembimbing untuk Mahasiswa Baru -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Dosen Pembimbing (Opsional)</label>
                                <select x-model="formUser.nidn" class="w-full border-2 border-gray-300 rounded p-2 font-bold outline-none focus:border-blue-600 bg-white">
                                    <option value="">-- Belum Ditentukan --</option>
                                    <template x-for="d in dosens" :key="d.nidn">
                                        <option :value="d.nidn" x-text="d.nama_dosen + ' (' + d.nidn + ')'"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Pilihan Mentor Industri untuk Mahasiswa Baru -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Mentor Industri (Opsional)</label>
                                <select x-model="formUser.id_pem" class="w-full border-2 border-gray-300 rounded p-2 font-bold outline-none focus:border-blue-600 bg-white">
                                    <option value="">-- Belum Ditentukan --</option>
                                    <template x-for="m in mentors" :key="m.id_pem">
                                        <option :value="m.id_pem" x-text="m.nama_pem + ' - ' + m.perusahaan"></option>
                                    </template>
                                </select>
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
                    <template x-if="activeRole === 'mahasiswa' || activeRole === 'mentor' || activeRole === 'perusahaan'">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1" x-text="activeRole === 'mentor' ? 'Penempatan Sebagai Mentor di PT' : 'Penempatan Magang (PT)'"></label>
                            <input type="text" x-model="formUser.pt" required list="pt-list" class="w-full border-2 border-gray-300 rounded p-2 font-bold outline-none focus:border-blue-600 bg-white" placeholder="Ketik atau pilih PT...">
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
                            <span x-text="isEdit ? 'Simpan Perubahan' : 'Simpan Akun Baru'"></span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- MODAL ATUR PEMBIMBING MAHASISWA -->
    <div x-show="showAssignModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showAssignModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showAssignModal = false"></div>

            <div x-show="showAssignModal" x-transition.scale class="relative inline-block bg-white text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle max-w-lg w-full border-4 border-blue-900 rounded-xl">
                
                <div class="bg-blue-900 px-6 py-4 flex justify-between items-center text-white">
                    <div>
                        <h3 class="text-xl font-black uppercase">Atur Pembimbing Mahasiswa</h3>
                        <p class="text-xs text-blue-200 font-medium" x-text="formAssign.name + ' (' + formAssign.identifier + ')'"></p>
                    </div>
                    <button @click="showAssignModal = false" class="hover:text-red-400 transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form @submit.prevent="saveAssignPembimbing()" class="p-6 bg-gray-50 space-y-4">
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Dosen Pembimbing</label>
                        <select x-model="formAssign.nidn" class="w-full border-2 border-gray-300 rounded p-2.5 font-bold outline-none focus:border-blue-600 bg-white">
                            <option value="">-- Belum Ditentukan --</option>
                            <template x-for="d in dosens" :key="d.nidn">
                                <option :value="d.nidn" x-text="d.nama_dosen + ' (NIP: ' + d.nidn + ')'"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Mentor Industri (Pembimbing Lapangan)</label>
                        <select x-model="formAssign.id_pem" class="w-full border-2 border-gray-300 rounded p-2.5 font-bold outline-none focus:border-blue-600 bg-white">
                            <option value="">-- Belum Ditentukan --</option>
                            <template x-for="m in mentors" :key="m.id_pem">
                                <option :value="m.id_pem" x-text="m.nama_pem + ' (' + m.perusahaan + ')'"></option>
                            </template>
                        </select>
                    </div>

                    <div class="pt-4 border-t-2 border-gray-200 flex justify-end gap-2">
                        <button type="button" @click="showAssignModal = false" class="px-4 py-2 bg-gray-200 text-gray-700 font-bold rounded-lg border border-gray-300 hover:bg-gray-300 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="bg-blue-600 text-white font-black px-6 py-2 rounded-lg border-2 border-blue-900 shadow-[3px_3px_0_0_#1e3a8a] hover:translate-y-px hover:translate-x-px hover:shadow-none transition-all">
                            Simpan Pembimbing
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- MODAL UBAH PASSWORD -->
    <div x-show="showPasswordModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-password-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showPasswordModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showPasswordModal = false"></div>

            <div x-show="showPasswordModal" x-transition.scale class="relative inline-block bg-white text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle max-w-md w-full border-4 border-purple-900 rounded-xl">
                
                <div class="bg-purple-900 px-6 py-4 flex justify-between items-center text-white">
                    <div>
                        <h3 class="text-xl font-black uppercase">Ubah Password</h3>
                        <p class="text-xs text-purple-200 font-medium" x-text="formPassword.name + ' (' + formPassword.email + ')'"></p>
                    </div>
                    <button @click="showPasswordModal = false" class="hover:text-red-400 transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form @submit.prevent="savePassword()" class="p-6 bg-gray-50 space-y-4">
                    <div class="bg-purple-50 border-2 border-purple-200 rounded-lg p-3 text-sm text-purple-800 font-bold">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Password baru akan langsung aktif setelah disimpan.
                    </div>

                    <div x-data="{ show1: false }">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Password Baru <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input :type="show1 ? 'text' : 'password'" x-model="formPassword.password" required minlength="1" class="w-full border-2 border-gray-300 rounded p-2 pr-10 font-bold outline-none focus:border-purple-600" placeholder="Masukkan password baru (cth: 123)...">
                            <button type="button" @click="show1 = !show1" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 transition-colors">
                                <svg x-show="!show1" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg x-show="show1" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                            </button>
                        </div>
                    </div>

                    <div x-data="{ show2: false }">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Konfirmasi Password <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input :type="show2 ? 'text' : 'password'" x-model="formPassword.password_confirmation" required minlength="1" class="w-full border-2 border-gray-300 rounded p-2 pr-10 font-bold outline-none focus:border-purple-600" placeholder="Ulangi password baru (cth: 123)...">
                            <button type="button" @click="show2 = !show2" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 transition-colors">
                                <svg x-show="!show2" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg x-show="show2" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                            </button>
                        </div>
                    </div>

                    <div class="pt-4 border-t-2 border-gray-200 flex justify-end gap-2">
                        <button type="button" @click="showPasswordModal = false" class="px-4 py-2 bg-gray-200 text-gray-700 font-bold rounded-lg border border-gray-300 hover:bg-gray-300 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="bg-purple-600 text-white font-black px-6 py-2 rounded-lg border-2 border-purple-900 shadow-[3px_3px_0_0_#581c87] hover:translate-y-px hover:translate-x-px hover:shadow-none transition-all">
                            Simpan Password
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH PERUSAHAAN -->
    <div x-show="showPerusahaanModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showPerusahaanModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showPerusahaanModal = false"></div>

            <div x-show="showPerusahaanModal" x-transition.scale class="relative inline-block bg-white text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle max-w-lg w-full border-4 border-emerald-900 rounded-xl">
                
                <div class="bg-emerald-900 px-6 py-4 flex justify-between items-center text-white">
                    <h3 class="text-xl font-black uppercase">Tambah Perusahaan Baru</h3>
                    <button @click="showPerusahaanModal = false" class="hover:text-red-400 transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form @submit.prevent="savePerusahaan()" class="p-6 bg-gray-50 space-y-4">
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nama Perusahaan / PT *</label>
                        <input type="text" x-model="formPerusahaan.nama_perusahaan" required class="w-full border-2 border-gray-300 rounded p-2 font-bold outline-none focus:border-emerald-600" placeholder="Contoh: PT Astra Honda Motor">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Alamat Industri</label>
                        <textarea x-model="formPerusahaan.alamat" rows="2" class="w-full border-2 border-gray-300 rounded p-2 font-bold outline-none focus:border-emerald-600" placeholder="Kota / Alamat lengkap perusahaan..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Kontak / No. Telepon / Email</label>
                        <input type="text" x-model="formPerusahaan.kontak" class="w-full border-2 border-gray-300 rounded p-2 font-bold outline-none focus:border-emerald-600" placeholder="Contoh: 021-1234567 / hr@astra.co.id">
                    </div>

                    <div class="pt-4 border-t-2 border-gray-200 flex justify-end">
                        <button type="submit" class="bg-emerald-600 text-white font-black px-6 py-2 rounded-lg border-2 border-emerald-950 shadow-[3px_3px_0_0_#064e3b] hover:translate-y-px hover:translate-x-px hover:shadow-none transition-all">
                            Simpan Perusahaan Baru
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
            isEdit: false,
            showPerusahaanModal: false,
            showAssignModal: false,
            showPasswordModal: false,
            
            companies: {!! $companies ?? '[]' !!},
            perusahaans: {!! $perusahaans ?? '[]' !!},
            dosens: {!! $dosens ?? '[]' !!},
            mentors: {!! $mentors ?? '[]' !!},

            // DATA AKUN DARI DATABASE
            users: {!! $mappedUsers ?? '[]' !!},

            formUser: { name: '', identifier: '', prodi: '', kelas: '', pt: '', nidn: '', id_pem: '', emailPrefix: '' },
            formPerusahaan: { nama_perusahaan: '', alamat: '', kontak: '' },
            formAssign: { nim: '', name: '', identifier: '', nidn: '', id_pem: '' },
            formPassword: { id: '', name: '', email: '', password: '', password_confirmation: '' },

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
                                      (u.identifier && u.identifier.toLowerCase().includes(query)) ||
                                      u.email.toLowerCase().includes(query) ||
                                      (u.pt && u.pt.toLowerCase().includes(query)) ||
                                      (u.dosen_name && u.dosen_name.toLowerCase().includes(query)) ||
                                      (u.mentor_name && u.mentor_name.toLowerCase().includes(query));

                    return matchRole && matchProdi && matchSearch;
                });
            },

            getFilteredPerusahaans() {
                let query = this.searchQuery.toLowerCase();
                if (!query) return this.perusahaans;
                return this.perusahaans.filter(p => {
                    return p.nama_perusahaan.toLowerCase().includes(query) ||
                           (p.alamat && p.alamat.toLowerCase().includes(query)) ||
                           (p.kontak && p.kontak.toLowerCase().includes(query));
                });
            },

            openModal() {
                this.isEdit = false;
                this.formUser = { name: '', identifier: '', prodi: '', kelas: '', pt: '', nidn: '', id_pem: '', emailPrefix: '' };
                this.showModal = true;
            },

            openAddModal() {
                this.isEdit = false;
                this.formUser = {
                    id: '',
                    name: '',
                    emailPrefix: '',
                    identifier: '',
                    kelas: '',
                    pt: '',
                    prodi: 'TRO'
                };
                this.showModal = true;
            },

            openEditModal(user) {
                this.isEdit = true;
                this.formUser = {
                    id: user.id,
                    name: user.name,
                    emailPrefix: user.email.split('@')[0],
                    identifier: user.identifier || '',
                    kelas: user.kelas || '',
                    pt: user.pt || '',
                    prodi: user.prodi || 'TRO'
                };
                this.showModal = true;
            },

            openPerusahaanModal() {
                this.formPerusahaan = { nama_perusahaan: '', alamat: '', kontak: '' };
                this.showPerusahaanModal = true;
            },

            openAssignModal(user) {
                this.formAssign = {
                    nim: user.nim || (user.identifier ? user.identifier.replace('NIM: ', '') : ''),
                    name: user.name,
                    identifier: user.identifier,
                    nidn: user.nidn || '',
                    id_pem: user.id_pem || ''
                };
                this.showAssignModal = true;
            },

            openPasswordModal(user) {
                this.formPassword = {
                    id: user.id,
                    name: user.name,
                    email: user.email,
                    password: '',
                    password_confirmation: ''
                };
                this.showPasswordModal = true;
            },

            async savePassword() {
                if (this.formPassword.password !== this.formPassword.password_confirmation) {
                    alert('Password dan konfirmasi password tidak cocok!');
                    return;
                }
                try {
                    let response = await fetch(`{{ url('/admin/users') }}/${this.formPassword.id}/password`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ password: this.formPassword.password })
                    });
                    let result = await response.json();
                    if (response.ok && result.success) {
                        alert(`Password untuk ${this.formPassword.name} berhasil diubah!`);
                        const targetUser = this.users.find(u => u.id === this.formPassword.id);
                        if (targetUser) {
                            targetUser.plain_password = this.formPassword.password;
                        }
                        this.showPasswordModal = false;
                    } else {
                        alert('Gagal: ' + (result.message || 'Terjadi kesalahan'));
                    }
                } catch (error) {
                    alert('Gagal mengubah password.');
                    console.error(error);
                }
            },

            getEmailDomain() {
                if(this.activeRole === 'mahasiswa' || this.activeRole === 'perusahaan') return '@mhs.polman';
                if(this.activeRole === 'dosen') return '@dosen.polman';
                if(this.activeRole === 'mentor') return '@industri.id';
                return '@admin.polman'; 
            },

            async saveUser() {
                let roleToSave = this.activeRole === 'perusahaan' ? 'mahasiswa' : this.activeRole;
                let fullEmail = this.formUser.emailPrefix + this.getEmailDomain();
                
                let payload = {
                    role: roleToSave,
                    name: this.formUser.name,
                    email: fullEmail,
                    identifier: this.formUser.identifier,
                    prodi: (roleToSave === 'mahasiswa') ? this.formUser.prodi : null,
                    kelas: (roleToSave === 'mahasiswa') ? this.formUser.kelas : null,
                    pt: (roleToSave === 'mahasiswa' || roleToSave === 'mentor') ? this.formUser.pt : null,
                    nidn: (roleToSave === 'mahasiswa') ? this.formUser.nidn : null,
                    id_pem: (roleToSave === 'mahasiswa') ? this.formUser.id_pem : null
                };

                try {
                    let method = this.isEdit ? 'PUT' : 'POST';
                    let url = this.isEdit ? `{{ url('/admin/users') }}/${this.formUser.id}` : '{{ route("admin.users.store") }}';
                    
                    let response = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(payload)
                    });

                    let result = await response.json();
                    if (response.ok && result.success) {
                        alert(this.isEdit ? `Akun ${roleToSave} berhasil diperbarui!` : `Akun ${roleToSave} baru berhasil ditambahkan!`);
                        window.location.reload();
                    } else {
                        alert('Gagal menyimpan: ' + (result.message || 'Terjadi kesalahan'));
                    }
                } catch (error) {
                    alert('Gagal menyimpan data.');
                    console.error(error);
                }
            },

            async deleteUser(id) {
                if(confirm('Yakin ingin menghapus akun ini secara permanen?')) {
                    try {
                        let response = await fetch(`{{ url('/admin/users') }}/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        let result = await response.json();
                        if (response.ok && result.success) {
                            this.users = this.users.filter(u => u.id !== id);
                        } else {
                            alert('Gagal menghapus: ' + (result.message || 'Terjadi kesalahan'));
                        }
                    } catch (error) {
                        alert('Gagal menghapus data.');
                        console.error(error);
                    }
                }
            },

            async savePerusahaan() {
                try {
                    let response = await fetch('{{ route("admin.perusahaan.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.formPerusahaan)
                    });

                    let result = await response.json();
                    if (response.ok && result.success) {
                        alert('Perusahaan baru berhasil ditambahkan!');
                        if (result.data) {
                            this.perusahaans.push(result.data);
                            if (!this.companies.includes(result.data.nama_perusahaan)) {
                                this.companies.push(result.data.nama_perusahaan);
                            }
                        } else {
                            window.location.reload();
                        }
                        this.showPerusahaanModal = false;
                    } else {
                        alert('Gagal menyimpan perusahaan: ' + (result.message || 'Terjadi kesalahan'));
                    }
                } catch (error) {
                    alert('Gagal menyimpan data perusahaan.');
                    console.error(error);
                }
            },

            async deletePerusahaan(id) {
                if(confirm('Yakin ingin menghapus perusahaan ini?')) {
                    try {
                        let response = await fetch(`{{ url('/admin/perusahaan') }}/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        let result = await response.json();
                        if (response.ok && result.success) {
                            this.perusahaans = this.perusahaans.filter(p => p.id !== id);
                        } else {
                            alert('Gagal menghapus: ' + (result.message || 'Terjadi kesalahan'));
                        }
                    } catch (error) {
                        alert('Gagal menghapus data perusahaan.');
                        console.error(error);
                    }
                }
            },

            async saveAssignPembimbing() {
                try {
                    let response = await fetch('{{ route("admin.mahasiswa.assign") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            nim: this.formAssign.nim,
                            nidn: this.formAssign.nidn,
                            id_pem: this.formAssign.id_pem
                        })
                    });

                    let result = await response.json();
                    if (response.ok && result.success) {
                        alert('Dosen & Mentor pembimbing berhasil diperbarui!');
                        
                        // Update local user state
                        let userIndex = this.users.findIndex(u => u.nim === this.formAssign.nim || u.identifier === 'NIM: ' + this.formAssign.nim);
                        if (userIndex !== -1) {
                            this.users[userIndex].nidn = this.formAssign.nidn;
                            this.users[userIndex].id_pem = this.formAssign.id_pem;
                            this.users[userIndex].dosen_name = result.dosen_name;
                            this.users[userIndex].mentor_name = result.mentor_name;
                            if (result.pt) {
                                this.users[userIndex].pt = result.pt;
                            }
                        }

                        this.showAssignModal = false;
                    } else {
                        alert('Gagal memperbarui pembimbing: ' + (result.message || 'Terjadi kesalahan'));
                    }
                } catch (error) {
                    alert('Gagal mengirim data penugasan pembimbing.');
                    console.error(error);
                }
            }
        }
    }
</script>
@endsection