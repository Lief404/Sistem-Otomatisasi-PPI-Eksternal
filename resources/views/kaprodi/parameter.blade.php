@extends('kaprodi.app')
@section('title', 'Manajemen Formulir')

@section('content')
<div x-data="parameterApp()" class="space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b-4 border-blue-900 pb-4">
        <div>
            <h2 class="text-3xl font-black text-blue-900 tracking-wide">Manajemen Formulir</h2>
            <p class="text-gray-600 mt-1 font-medium">Atur sub-kategori dan indikator penilaian yang digunakan oleh Mahasiswa, Dosen, dan Mentor.</p>
        </div>
        <div class="flex gap-2 mt-4 md:mt-0">
            <button x-show="activeTab === 'mahasiswa'" @click="openModal()" class="bg-yellow-500 hover:bg-yellow-600 text-yellow-950 font-bold py-2 px-4 rounded-xl shadow-[4px_4px_0_0_#ca8a04] transition-all hover:translate-y-px hover:translate-x-px hover:shadow-none" style="display: none;">
                + Tambah Form Saran
            </button>
            <button x-show="activeTab === 'dosen'" @click="openModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-xl shadow-[4px_4px_0_0_#1e3a8a] transition-all hover:translate-y-px hover:translate-x-px hover:shadow-none" style="display: none;">
                + Tambah Parameter Dosen
            </button>
            <button x-show="activeTab === 'mentor'" @click="openModal()" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-xl shadow-[4px_4px_0_0_#581c87] transition-all hover:translate-y-px hover:translate-x-px hover:shadow-none" style="display: none;">
                + Tambah Form Saran Mentor
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm" role="alert">
            <p class="font-bold">Sukses!</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <!-- TABS NAVIGATION -->
    <div class="flex border-b-2 border-gray-200 mb-6 flex-wrap">
        <button @click="activeTab = 'mahasiswa'" :class="{'border-blue-900 text-blue-900 border-b-4 font-black': activeTab === 'mahasiswa', 'text-gray-500 hover:text-blue-700 font-bold': activeTab !== 'mahasiswa'}" class="px-6 py-3 transition-colors text-lg">
            Mahasiswa
        </button>
        <button @click="activeTab = 'dosen'" :class="{'border-blue-900 text-blue-900 border-b-4 font-black': activeTab === 'dosen', 'text-gray-500 hover:text-blue-700 font-bold': activeTab !== 'dosen'}" class="px-6 py-3 transition-colors text-lg">
            Dosen
        </button>
        <button @click="activeTab = 'mentor'" :class="{'border-blue-900 text-blue-900 border-b-4 font-black': activeTab === 'mentor', 'text-gray-500 hover:text-blue-700 font-bold': activeTab !== 'mentor'}" class="px-6 py-3 transition-colors text-lg">
            Mentor PT
        </button>
    </div>

    <!-- TAB CONTENT: MAHASISWA -->
    <div x-show="activeTab === 'mahasiswa'" style="display: none;" class="mb-10 animate-fade-in">
        <div class="flex items-center gap-3 mb-4">
            <div class="p-3 bg-yellow-100 text-yellow-900 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <h2 class="text-2xl font-black text-gray-800">Formulir Mahasiswa</h2>
        </div>
        <div class="bg-white rounded-xl border-2 border-yellow-500 shadow-[6px_6px_0_0_#eab308] overflow-hidden">
            <div class="bg-yellow-500 p-4 text-yellow-950">
                <h3 class="text-xl font-black uppercase">Kategori: Saran & Masukan PT</h3>
            </div>
            <div class="p-4 space-y-4">
                @forelse($parameters->where('jenis', 'saran_mahasiswa') as $param)
                    <div class="border-2 border-gray-200 rounded-lg p-4 bg-gray-50 hover:border-yellow-400 transition-colors">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 border-b-2 border-gray-200 pb-3 gap-3">
                            <h4 class="font-black text-xl text-yellow-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                                {{ $param->sub_kategori }}
                            </h4>
                            <div class="flex gap-2 w-full sm:w-auto">
                                <button @click="openModal({{ $param }})" class="flex-1 sm:flex-none text-sm bg-yellow-400 text-yellow-900 px-4 py-1.5 rounded-lg font-bold shadow-[2px_2px_0_0_#713f12] transition-all hover:translate-y-px hover:translate-x-px hover:shadow-none text-center">Edit</button>
                                <form action="{{ route('kaprodi.parameter.destroy', $param->id) }}" method="POST" class="flex-1 sm:flex-none" onsubmit="return confirm('Yakin ingin menghapus form saran ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full text-sm bg-red-500 text-white px-4 py-1.5 rounded-lg font-bold shadow-[2px_2px_0_0_#7f1d1d] transition-all hover:translate-y-px hover:translate-x-px hover:shadow-none text-center">Hapus</button>
                                </form>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Indikator Pertanyaan:</p>
                            <div class="flex flex-col gap-2">
                                @foreach($param->indikator as $ind)
                                    <div class="px-3 py-2 rounded-lg text-sm font-semibold bg-white border-2 border-yellow-200 text-gray-700 shadow-sm flex items-start gap-2">
                                        <span class="text-yellow-500 mt-0.5"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg></span>
                                        <span>{{ $ind }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl border-2 border-dashed border-gray-300 p-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <h3 class="text-xl font-black text-gray-700 mb-2">Belum Ada Form Saran</h3>
                        <p class="text-gray-500 font-medium">Klik tombol tambah di atas untuk membuat indikator form saran bagi mahasiswa.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- TAB CONTENT: DOSEN -->
    <div x-show="activeTab === 'dosen'" class="mb-10 animate-fade-in">
        <div class="flex items-center gap-3 mb-4">
            <div class="p-3 bg-blue-100 text-blue-900 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <h2 class="text-2xl font-black text-gray-800">Form Penilaian Presentasi PPI oleh Dosen Pembimbing</h2>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
            <!-- Presentasi -->
            <div class="bg-white rounded-xl border-2 border-blue-900 shadow-[6px_6px_0_0_#1e3a8a] overflow-hidden">
                <div class="bg-blue-900 p-4 text-white">
                    <h3 class="text-xl font-black uppercase">Bagian A: Mutu Presentasi</h3>
                </div>
                <div class="p-4 space-y-4">
                    @forelse($parameters->where('jenis', 'presentasi') as $param)
                        <div class="border-2 border-gray-200 rounded-lg p-4 bg-gray-50 hover:border-blue-300 transition-colors">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 border-b-2 border-gray-200 pb-3 gap-3">
                                <h4 class="font-black text-xl text-blue-900 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ $param->sub_kategori }}
                                </h4>
                                <div class="flex gap-2 w-full sm:w-auto">
                                    <button @click="openModal({{ $param }})" class="flex-1 sm:flex-none text-sm bg-yellow-400 text-yellow-900 px-4 py-1.5 rounded-lg font-bold shadow-[2px_2px_0_0_#713f12] transition-all hover:translate-y-px hover:translate-x-px hover:shadow-none text-center">Edit</button>
                                    <form action="{{ route('kaprodi.parameter.destroy', $param->id) }}" method="POST" class="flex-1 sm:flex-none" onsubmit="return confirm('Yakin ingin menghapus parameter ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full text-sm bg-red-500 text-white px-4 py-1.5 rounded-lg font-bold shadow-[2px_2px_0_0_#7f1d1d] transition-all hover:translate-y-px hover:translate-x-px hover:shadow-none text-center">Hapus</button>
                                    </form>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Indikator Penilaian:</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($param->indikator as $ind)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-white border-2 border-blue-200 text-blue-800 shadow-sm hover:border-blue-400 hover:bg-blue-50 transition-colors">
                                            {{ $ind }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 italic font-medium p-4 text-center">Belum ada parameter presentasi.</p>
                    @endforelse
                </div>
            </div>

            <!-- Makalah -->
            <div class="bg-white rounded-xl border-2 border-emerald-900 shadow-[6px_6px_0_0_#064e3b] overflow-hidden">
                <div class="bg-emerald-900 p-4 text-white">
                    <h3 class="text-xl font-black uppercase">Bagian B: Penulisan Makalah</h3>
                </div>
                <div class="p-4 space-y-4">
                    @forelse($parameters->where('jenis', 'makalah') as $param)
                        <div class="border-2 border-gray-200 rounded-lg p-4 bg-gray-50 hover:border-emerald-300 transition-colors">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 border-b-2 border-gray-200 pb-3 gap-3">
                                <h4 class="font-black text-xl text-emerald-900 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    {{ $param->sub_kategori }}
                                </h4>
                                <div class="flex gap-2 w-full sm:w-auto">
                                    <button @click="openModal({{ $param }})" class="flex-1 sm:flex-none text-sm bg-yellow-400 text-yellow-900 px-4 py-1.5 rounded-lg font-bold shadow-[2px_2px_0_0_#713f12] transition-all hover:translate-y-px hover:translate-x-px hover:shadow-none text-center">Edit</button>
                                    <form action="{{ route('kaprodi.parameter.destroy', $param->id) }}" method="POST" class="flex-1 sm:flex-none" onsubmit="return confirm('Yakin ingin menghapus parameter ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full text-sm bg-red-500 text-white px-4 py-1.5 rounded-lg font-bold shadow-[2px_2px_0_0_#7f1d1d] transition-all hover:translate-y-px hover:translate-x-px hover:shadow-none text-center">Hapus</button>
                                    </form>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Indikator Penilaian:</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($param->indikator as $ind)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-white border-2 border-emerald-200 text-emerald-800 shadow-sm hover:border-emerald-400 hover:bg-emerald-50 transition-colors">
                                            {{ $ind }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 italic font-medium p-4 text-center">Belum ada parameter makalah.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- TAB CONTENT: MENTOR PT -->
    <div x-show="activeTab === 'mentor'" style="display: none;" class="mb-10 animate-fade-in">
        <div class="flex items-center gap-3 mb-4">
            <div class="p-3 bg-purple-100 text-purple-900 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <h2 class="text-2xl font-black text-gray-800">Parameter Penilaian oleh Mentor Perusahaan</h2>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
            <!-- Disiplin & Prestasi -->
            <div class="bg-white rounded-xl border-2 border-purple-900 shadow-[6px_6px_0_0_#581c87] overflow-hidden">
                <div class="bg-purple-900 p-4 text-white">
                    <h3 class="text-xl font-black uppercase">Form Disiplin & Prestasi</h3>
                </div>
                <div class="p-4 space-y-4">
                    @forelse($parameters->where('jenis', 'disiplin_prestasi') as $param)
                        <div class="border-2 border-gray-200 rounded-lg p-4 bg-gray-50 hover:border-purple-400 transition-colors">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 border-b-2 border-gray-200 pb-3 gap-3">
                                <h4 class="font-black text-xl text-purple-900 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    {{ $param->sub_kategori }}
                                </h4>
                                <div class="flex gap-2 w-full sm:w-auto">
                                    <button @click="openModal({{ $param }})" class="flex-1 sm:flex-none text-sm bg-yellow-400 text-yellow-900 px-4 py-1.5 rounded-lg font-bold shadow-[2px_2px_0_0_#713f12] transition-all hover:translate-y-px hover:translate-x-px hover:shadow-none text-center">Edit</button>
                                    <form action="{{ route('kaprodi.parameter.destroy', $param->id) }}" method="POST" class="flex-1 sm:flex-none" onsubmit="return confirm('Yakin ingin menghapus form disiplin ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full text-sm bg-red-500 text-white px-4 py-1.5 rounded-lg font-bold shadow-[2px_2px_0_0_#7f1d1d] transition-all hover:translate-y-px hover:translate-x-px hover:shadow-none text-center">Hapus</button>
                                    </form>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Indikator Pertanyaan:</p>
                                <div class="flex flex-col gap-2">
                                    @foreach($param->indikator as $ind)
                                        <div class="px-3 py-2 rounded-lg text-sm font-semibold bg-white border-2 border-purple-200 text-gray-700 shadow-sm flex items-start gap-2">
                                            <span class="text-purple-500 mt-0.5"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg></span>
                                            <span>{{ $ind }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-xl border-2 border-dashed border-gray-300 p-12 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <h3 class="text-xl font-black text-gray-700 mb-2">Belum Ada Form Disiplin</h3>
                            <p class="text-gray-500 font-medium">Klik tombol tambah di atas untuk membuat indikator form disiplin bagi mentor.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Kuisioner Evaluasi -->
            <div class="bg-white rounded-xl border-2 border-emerald-900 shadow-[6px_6px_0_0_#064e3b] overflow-hidden">
                <div class="bg-emerald-900 p-4 text-white">
                    <h3 class="text-xl font-black uppercase">Evaluasi Kuisioner Soft & Hard Skill</h3>
                </div>
                <div class="p-4 space-y-4">
                    @forelse($parameters->where('jenis', 'kuisioner_mentor') as $param)
                        <div class="border-2 border-gray-200 rounded-lg p-4 bg-gray-50 hover:border-emerald-400 transition-colors">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 border-b-2 border-gray-200 pb-3 gap-3">
                                <h4 class="font-black text-xl text-emerald-900 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ $param->sub_kategori }}
                                </h4>
                                <div class="flex gap-2 w-full sm:w-auto">
                                    <button @click="openModal({{ $param }})" class="flex-1 sm:flex-none text-sm bg-yellow-400 text-yellow-900 px-4 py-1.5 rounded-lg font-bold shadow-[2px_2px_0_0_#713f12] transition-all hover:translate-y-px hover:translate-x-px hover:shadow-none text-center">Edit</button>
                                    <form action="{{ route('kaprodi.parameter.destroy', $param->id) }}" method="POST" class="flex-1 sm:flex-none" onsubmit="return confirm('Yakin ingin menghapus form kuisioner ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full text-sm bg-red-500 text-white px-4 py-1.5 rounded-lg font-bold shadow-[2px_2px_0_0_#7f1d1d] transition-all hover:translate-y-px hover:translate-x-px hover:shadow-none text-center">Hapus</button>
                                    </form>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Indikator Pertanyaan:</p>
                                <div class="flex flex-col gap-2">
                                    @foreach($param->indikator as $ind)
                                        <div class="px-3 py-2 rounded-lg text-sm font-semibold bg-white border-2 border-emerald-200 text-gray-700 shadow-sm flex items-start gap-2">
                                            <span class="text-emerald-500 mt-0.5"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg></span>
                                            <span>{{ $ind }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-xl border-2 border-dashed border-gray-300 p-12 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <h3 class="text-xl font-black text-gray-700 mb-2">Belum Ada Form Kuisioner</h3>
                            <p class="text-gray-500 font-medium">Klik tombol tambah di atas untuk membuat indikator form kuisioner bagi mentor.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Saran & Masukan dari Mentor -->
            <div class="bg-white rounded-xl border-2 border-fuchsia-900 shadow-[6px_6px_0_0_#701a75] overflow-hidden col-span-1 xl:col-span-2 mt-8">
                <div class="bg-fuchsia-900 p-4 text-white">
                    <h3 class="text-xl font-black uppercase">Kategori: Saran & Masukan Mentor</h3>
                </div>
                <div class="p-4 space-y-4">
                    @forelse($parameters->where('jenis', 'saran_mentor') as $param)
                        <div class="border-2 border-gray-200 rounded-lg p-4 bg-gray-50 hover:border-fuchsia-400 transition-colors">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 border-b-2 border-gray-200 pb-3 gap-3">
                                <h4 class="font-black text-xl text-fuchsia-900 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-fuchsia-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                                    {{ $param->sub_kategori }}
                                </h4>
                                <div class="flex gap-2 w-full sm:w-auto">
                                    <button @click="openModal({{ $param }})" class="flex-1 sm:flex-none text-sm bg-yellow-400 text-yellow-900 px-4 py-1.5 rounded-lg font-bold shadow-[2px_2px_0_0_#713f12] transition-all hover:translate-y-px hover:translate-x-px hover:shadow-none text-center">Edit</button>
                                    <form action="{{ route('kaprodi.parameter.destroy', $param->id) }}" method="POST" class="flex-1 sm:flex-none" onsubmit="return confirm('Yakin ingin menghapus form saran mentor ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full text-sm bg-red-500 text-white px-4 py-1.5 rounded-lg font-bold shadow-[2px_2px_0_0_#7f1d1d] transition-all hover:translate-y-px hover:translate-x-px hover:shadow-none text-center">Hapus</button>
                                    </form>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Indikator Pertanyaan:</p>
                                <div class="flex flex-col gap-2">
                                    @foreach($param->indikator as $ind)
                                        <div class="px-3 py-2 rounded-lg text-sm font-semibold bg-white border-2 border-fuchsia-200 text-gray-700 shadow-sm flex items-start gap-2">
                                            <span class="text-fuchsia-500 mt-0.5"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg></span>
                                            <span>{{ $ind }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-xl border-2 border-dashed border-gray-300 p-12 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <h3 class="text-xl font-black text-gray-700 mb-2">Belum Ada Form Saran</h3>
                            <p class="text-gray-500 font-medium">Klik tombol tambah di atas untuk membuat indikator form saran bagi mentor.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form (For Dosen Parameters) -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="closeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showModal" x-transition.scale class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border-4 border-blue-900">
                <form :action="formAction" method="POST">
                    @csrf
                    <template x-if="isEditing">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="bg-blue-900 px-6 py-4 flex justify-between items-center text-white">
                        <h3 class="text-xl font-black" x-text="isEditing ? 'Edit Parameter' : 'Tambah Parameter Baru'"></h3>
                        <button type="button" @click="closeModal()" class="text-white hover:text-red-400">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Kategori Utama</label>
                            <select name="jenis" x-model="form.jenis" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-medium">
                                <template x-if="activeTab === 'dosen' || isEditing">
                                    <option value="presentasi">Penilaian Presentasi PPI (A. Mutu Presentasi)</option>
                                </template>
                                <template x-if="activeTab === 'dosen' || isEditing">
                                    <option value="makalah">Penilaian Presentasi PPI (B. Penulisan Makalah)</option>
                                </template>
                                <template x-if="activeTab === 'mahasiswa' || isEditing">
                                    <option value="saran_mahasiswa">Saran & Masukan PT</option>
                                </template>
                                <template x-if="activeTab === 'mentor' || isEditing">
                                    <option value="disiplin_prestasi">Disiplin & Prestasi</option>
                                </template>
                                <template x-if="activeTab === 'mentor' || isEditing">
                                    <option value="kuisioner_mentor">Evaluasi Kuisioner</option>
                                </template>
                                <template x-if="activeTab === 'mentor' || isEditing">
                                    <option value="saran_mentor">Saran & Masukan Mentor</option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Nama Sub Kategori</label>
                            <input type="text" name="sub_kategori" x-model="form.sub_kategori" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-medium" placeholder="Contoh: Teknik Presentasi">
                        </div>
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-sm font-bold text-gray-700">Indikator Penilaian</label>
                                <button type="button" @click="addIndikator()" class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded font-bold hover:bg-blue-200">+ Tambah Indikator</button>
                            </div>
                            <template x-for="(ind, index) in form.indikator" :key="index">
                                <div class="flex items-center gap-2 mb-2">
                                    <input type="text" :name="'indikator['+index+']'" x-model="form.indikator[index]" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-medium" placeholder="Nama indikator...">
                                    <button type="button" @click="removeIndikator(index)" class="text-red-500 hover:text-red-700 p-1" title="Hapus indikator">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </template>
                            <p x-show="form.indikator.length === 0" class="text-sm text-red-500 italic">Tambahkan minimal 1 indikator.</p>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 flex justify-end gap-2 border-t">
                        <button type="button" @click="closeModal()" class="px-4 py-2 border-2 border-gray-300 rounded-md font-bold text-gray-700 bg-white hover:bg-gray-50">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 border-2 border-blue-900 text-white rounded-md font-bold shadow-[2px_2px_0_0_#1e3a8a] hover:translate-y-px hover:translate-x-px hover:shadow-none">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .animate-fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
    function parameterApp() {
        return {
            activeTab: 'dosen',
            showModal: false,
            isEditing: false,
            formAction: '',
            form: {
                id: null,
                jenis: 'presentasi',
                sub_kategori: '',
                indikator: ['']
            },
            openModal(param = null) {
                if (param) {
                    this.isEditing = true;
                    this.formAction = `/kaprodi/parameter/${param.id}`;
                    this.form = {
                        id: param.id,
                        jenis: param.jenis,
                        sub_kategori: param.sub_kategori,
                        indikator: param.indikator ? [...param.indikator] : ['']
                    };
                } else {
                    this.isEditing = false;
                    this.formAction = '{{ route("kaprodi.parameter.store") }}';
                    let defaultJenis = 'presentasi';
                    if (this.activeTab === 'mahasiswa') defaultJenis = 'saran_mahasiswa';
                    if (this.activeTab === 'mentor') defaultJenis = 'saran_mentor';
                    
                    this.form = {
                        id: null,
                        jenis: defaultJenis,
                        sub_kategori: '',
                        indikator: ['']
                    };
                }
                this.showModal = true;
            },
            closeModal() {
                this.showModal = false;
            },
            addIndikator() {
                this.form.indikator.push('');
            },
            removeIndikator(index) {
                if (this.form.indikator.length > 1) {
                    this.form.indikator.splice(index, 1);
                }
            }
        }
    }
</script>
@endsection
