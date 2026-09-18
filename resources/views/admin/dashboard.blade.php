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

                <!-- Loop Data Prodi dari Backend -->
                <template x-for="prodi in prodis" :key="prodi.id_prodi">
                    <div class="border-4 border-gray-200 rounded-xl bg-gray-50 overflow-hidden mb-4" x-data="{ expanded: false }">
                        <div @click="expanded = !expanded" class="p-4 flex justify-between items-center cursor-pointer hover:bg-gray-100 transition-colors select-none">
                            <h4 class="font-black text-gray-800 text-lg" x-text="prodi.nama_prodi"></h4>
                            <svg :class="expanded ? 'rotate-180' : ''" class="w-6 h-6 text-gray-600 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                        <div x-show="expanded" x-collapse.duration.300ms>
                            <div class="p-4 pt-0 border-t-2 border-gray-200 mt-2">
                                <div class="flex justify-end mb-3">
                                    <button @click="openMatkulModal(prodi)" class="bg-blue-600 text-white text-xs font-black px-4 py-2 rounded-lg border-b-4 border-blue-900 hover:translate-y-1 hover:border-b-0 transition-all">+ Tambah Matkul</button>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <template x-for="matkul in prodi.mata_kuliahs" :key="matkul.id">
                                        <div class="bg-white p-3 rounded-xl border-2 border-gray-200 focus-within:border-blue-500 transition-colors shadow-sm relative group">
                                            <div class="flex justify-between items-start mb-1">
                                                <div>
                                                    <span class="text-xs font-bold bg-blue-100 text-blue-800 px-2 py-0.5 rounded" x-text="matkul.kd_mat"></span>
                                                    <label class="block text-sm font-black text-gray-700 mt-1" x-text="matkul.nama_komp"></label>
                                                </div>
                                                <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <button @click="editMatkul(prodi, matkul)" class="text-yellow-500 hover:text-yellow-700 p-1" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button>
                                                    <button @click="deleteMatkul(matkul.id)" class="text-red-500 hover:text-red-700 p-1" title="Hapus"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                                </div>
                                            </div>
                                            <div class="flex items-center rounded bg-gray-50 border border-gray-200 overflow-hidden mt-2">
                                                <div class="w-full p-2 font-black text-gray-900 text-center bg-transparent text-lg" x-text="matkul.jam_min"></div>
                                                <span class="bg-gray-200 px-3 py-2 text-xs font-bold text-gray-600 border-l border-gray-300 h-full flex items-center">Jam</span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <template x-if="prodi.mata_kuliahs.length === 0">
                                    <div class="text-center text-gray-400 py-4 font-bold text-sm">Belum ada mata kuliah.</div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
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
                            @foreach($dosens as $dosen)
                                <option value="{{ $dosen->nidn }}">{{ $dosen->nama_dosen }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-blue-900 uppercase mb-1">Pilih PT</label>
                        <select x-model="mapForm.ptName" required class="w-full border-2 border-blue-300 rounded-lg p-2.5 text-sm font-bold outline-none focus:border-blue-600 bg-white">
                            <option value="" disabled selected>-- Pilih PT --</option>
                            @foreach($perusahaans as $pt)
                                <option value="{{ $pt->nama_perusahaan }}">{{ $pt->nama_perusahaan }}</option>
                            @endforeach
                        </select>
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
                            <div class="flex-1">
                                <p class="font-black text-gray-800 text-lg group-hover:text-blue-700 transition-colors" x-text="map.ptName"></p>
                                <div class="text-xs text-gray-500 mt-1 mb-2">
                                    <p x-show="getPTAlamat(map.ptName)"><span class="font-bold">Alamat:</span> <span x-text="getPTAlamat(map.ptName)"></span></p>
                                    <p x-show="getPTKontak(map.ptName)"><span class="font-bold">Kontak:</span> <span x-text="getPTKontak(map.ptName)"></span></p>
                                    <p><span class="font-bold">Mentor PT:</span> <span x-text="getMentorName(map.ptName)"></span></p>
                                </div>
                                
                                <div class="mt-2 pt-2 border-t border-gray-100">
                                    <p class="text-xs font-bold text-gray-700 mb-1">Daftar Mahasiswa:</p>
                                    <ul class="text-xs text-gray-600 list-disc list-inside">
                                        <template x-for="(mhs, i) in getMahasiswaList(map.ptName, map.dosenId)" :key="i">
                                            <li><span x-text="mhs.nama_mhs" class="font-bold"></span> <span class="text-gray-400" x-text="`(${mhs.prodi})`"></span></li>
                                        </template>
                                        <template x-if="getMahasiswaList(map.ptName, map.dosenId).length === 0">
                                            <li class="text-gray-400 italic list-none text-xs">Belum ada mahasiswa terkait</li>
                                        </template>
                                    </ul>
                                </div>

                                <div class="flex items-center gap-1 text-blue-700 mt-3 bg-blue-50 w-fit px-2 py-1 rounded border border-blue-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    <p class="font-bold text-sm" x-text="getDosenName(map.dosenId)"></p>
                                </div>
                            </div>
                        </div>
                        <button @click="deleteMapping(map.id, idx)" class="text-red-500 hover:text-white hover:bg-red-500 font-bold p-2 rounded-lg border-2 border-transparent hover:border-red-700 transition-colors" title="Hapus Jadwal">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </template>
            </div>
        </div>

    </div>
    
    <!-- Modal Matkul -->
    <div x-show="isMatkulModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" x-cloak>
        <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md border-4 border-blue-900" @click.away="isMatkulModalOpen = false">
            <h3 class="text-2xl font-black text-blue-900 mb-6" x-text="matkulForm.id ? 'Edit Mata Kuliah' : 'Tambah Mata Kuliah'"></h3>
            <form @submit.prevent="saveMatkul()">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-black text-gray-700 uppercase mb-1">Kode Matkul</label>
                        <input type="text" x-model="matkulForm.kd_mat" required class="w-full border-2 border-gray-300 rounded-lg p-2.5 font-bold focus:border-blue-500 outline-none" placeholder="Contoh: PRO">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-700 uppercase mb-1">Nama Mata Kuliah</label>
                        <input type="text" x-model="matkulForm.nama_komp" required class="w-full border-2 border-gray-300 rounded-lg p-2.5 font-bold focus:border-blue-500 outline-none" placeholder="Contoh: Praktik Produksi Otomasi">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-700 uppercase mb-1">Jam Minimum</label>
                        <input type="number" step="0.01" x-model.number="matkulForm.jam_min" required class="w-full border-2 border-gray-300 rounded-lg p-2.5 font-bold focus:border-blue-500 outline-none" placeholder="0">
                    </div>
                </div>
                <div class="mt-8 flex justify-end gap-3">
                    <button type="button" @click="isMatkulModalOpen = false" class="px-5 py-2 rounded-xl font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white font-black py-2 px-6 rounded-xl border-b-4 border-blue-900 hover:translate-y-1 hover:border-b-0 transition-all">Simpan</button>
                </div>
            </form>
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
            prodis: @json($prodis),
            
            // State for Matkul Modal
            isMatkulModalOpen: false,
            matkulForm: { id: null, id_prodi: null, kd_mat: '', nama_komp: '', jam_min: '' },

            mappings: @json($jadwals).map(j => ({ id: j.id, dosenId: j.nidn, ptName: j.perusahaan, date: j.tanggal })),
            mapForm: { dosenId: '', ptName: '', date: '' },

            addMapping() {
                fetch('{{ route("admin.jadwal.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        nidn: this.mapForm.dosenId,
                        perusahaan: this.mapForm.ptName,
                        tanggal: this.mapForm.date
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.mappings.push({ id: data.data.id, dosenId: data.data.nidn, ptName: data.data.perusahaan, date: data.data.tanggal });
                        this.mappings.sort((a, b) => new Date(a.date) - new Date(b.date));
                        this.mapForm = { dosenId: '', ptName: '', date: '' };
                        alert(data.message);
                    } else {
                        alert(data.message);
                    }
                });
            },

            deleteMapping(id, idx) {
                if(!confirm('Yakin ingin menghapus jadwal ini?')) return;
                fetch(`/admin/jadwal/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.mappings.splice(idx, 1);
                        alert(data.message);
                    } else {
                        alert(data.message);
                    }
                });
            },
            
            // Matkul Logic
            openMatkulModal(prodi) {
                this.matkulForm = { id: null, id_prodi: prodi.id_prodi, kd_mat: '', nama_komp: '', jam_min: '' };
                this.isMatkulModalOpen = true;
            },
            editMatkul(prodi, matkul) {
                this.matkulForm = { 
                    id: matkul.id, 
                    id_prodi: prodi.id_prodi, 
                    kd_mat: matkul.kd_mat, 
                    nama_komp: matkul.nama_komp, 
                    jam_min: matkul.jam_min 
                };
                this.isMatkulModalOpen = true;
            },
            saveMatkul() {
                const url = this.matkulForm.id ? `/admin/matkul/${this.matkulForm.id}` : '/admin/matkul';
                const method = this.matkulForm.id ? 'PUT' : 'POST';
                
                fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.matkulForm)
                })
                .then(res => {
                    if(!res.ok) throw new Error('Network error');
                    return res.json();
                })
                .then(data => {
                    alert(data.message);
                    location.reload();
                })
                .catch(err => {
                    alert('Gagal menyimpan mata kuliah.');
                    console.error(err);
                });
            },
            deleteMatkul(id) {
                if(!confirm('Yakin ingin menghapus mata kuliah ini?')) return;
                fetch(`/admin/matkul/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    location.reload();
                });
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
            },
            getDosenName(nidn) {
                const dosensList = @json($dosens);
                const dosen = dosensList.find(d => d.nidn == nidn);
                return dosen ? dosen.nama_dosen : 'Dosen Tidak Diketahui';
            },
            getPTAlamat(ptName) {
                const ptList = @json($perusahaans);
                const pt = ptList.find(p => p.nama_perusahaan == ptName);
                return pt ? pt.alamat : '-';
            },
            getPTKontak(ptName) {
                const ptList = @json($perusahaans);
                const pt = ptList.find(p => p.nama_perusahaan == ptName);
                return pt ? pt.kontak : '-';
            },
            getMentorName(ptName) {
                const mentorsList = @json($mentors);
                const mentor = mentorsList.find(m => m.perusahaan == ptName);
                return mentor ? mentor.nama_pem : '-';
            },
            getMahasiswaList(ptName, dosenId) {
                const mhsList = @json($mahasiswas);
                const assigned = mhsList.filter(m => m.nidn == dosenId && m.pembimbing_industri?.perusahaan == ptName);
                if (assigned.length === 0) return [];
                return assigned.map(m => ({
                    nama_mhs: m.nama_mhs,
                    prodi: m.program_studi?.nama_prodi || '-'
                }));
            }
        }
    }
</script>
@endsection