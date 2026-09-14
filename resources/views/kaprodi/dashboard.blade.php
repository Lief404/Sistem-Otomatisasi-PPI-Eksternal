@extends('kaprodi.app')
@section('title', 'Dashboard Kaprodi')

@section('content')
<div class="space-y-6">

    <!-- Header Panel -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b-4 border-blue-900 pb-4">
        <div>
            <h2 class="text-3xl font-black text-blue-900 tracking-wide">Dashboard KPS</h2>
            <p class="text-gray-600 mt-1 font-medium">Pantau dan kelola saran masukan dari mahasiswa untuk perusahaan.</p>
        </div>
    </div>

    <!-- MAIN CARD: SARAN -->
    <div class="bg-white rounded-xl border-2 border-blue-900 shadow-[6px_6px_0_0_#1e3a8a] overflow-hidden mb-8">
        <div class="bg-blue-900 p-4 flex justify-between items-center text-white">
            <h3 class="text-xl font-black uppercase">Daftar Saran dan Masukan Mahasiswa</h3>
            <span class="text-sm font-bold bg-black/20 px-3 py-1 rounded-full">{{ count($sarans) }} Data</span>
        </div>

        <div class="p-6 bg-gray-50">
            @if(count($sarans) === 0)
                <div class="p-10 text-center text-gray-400 font-bold italic border-2 border-dashed border-gray-300 rounded-xl">
                    Belum ada saran atau masukan dari mahasiswa.
                </div>
            @else
                <div class="space-y-4">
                    @foreach($sarans as $saran)
                        <div class="bg-white p-5 rounded-xl border-2 border-gray-200 shadow-sm hover:border-yellow-400 transition-colors">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-3 gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="bg-blue-100 text-blue-800 border border-blue-300 px-2 py-1 rounded text-xs font-black">{{ $saran->tanggal }}</span>
                                    <span class="bg-emerald-100 text-emerald-800 border border-emerald-300 px-2 py-1 rounded text-xs font-black">{{ $saran->perusahaan }}</span>
                                </div>
                                <div class="text-sm font-bold text-gray-500">
                                    Dari: <span class="text-gray-800">{{ $saran->mahasiswa->nama_mhs ?? 'Mahasiswa' }}</span> 
                                    @if($saran->mahasiswa && $saran->mahasiswa->programStudi)
                                        <span class="text-xs text-gray-400">({{ $saran->mahasiswa->programStudi->nama_prodi }})</span>
                                    @endif
                                </div>
                            </div>
                            <p class="text-gray-700 font-medium leading-relaxed bg-gray-50 p-4 rounded-lg border border-gray-100">
                                "{{ $saran->saran }}"
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- MAIN CARD: DISIPLIN -->
    <div class="bg-white rounded-xl border-2 border-purple-900 shadow-[6px_6px_0_0_#581c87] overflow-hidden mb-8">
        <div class="bg-purple-900 p-4 flex justify-between items-center text-white">
            <h3 class="text-xl font-black uppercase">Form Disiplin & Prestasi Mahasiswa</h3>
            <span class="text-sm font-bold bg-black/20 px-3 py-1 rounded-full">{{ count($disiplins) }} Data</span>
        </div>
        <div class="p-6 bg-gray-50">
            @if(count($disiplins) === 0)
                <div class="p-10 text-center text-gray-400 font-bold italic border-2 border-dashed border-gray-300 rounded-xl">
                    Belum ada data disiplin dan prestasi dari mentor.
                </div>
            @else
                <div class="space-y-4">
                    @foreach($disiplins as $disiplin)
                        <div class="bg-white p-5 rounded-xl border-2 border-gray-200 shadow-sm hover:border-purple-400 transition-colors">
                            <div class="flex justify-between items-center mb-3">
                                <span class="bg-purple-100 text-purple-800 border border-purple-300 px-2 py-1 rounded text-xs font-black">{{ $disiplin->tanggal }}</span>
                                <div class="text-sm font-bold text-gray-500">
                                    Mhs: <span class="text-gray-800">{{ $disiplin->mahasiswa->nama_mhs ?? 'Mahasiswa' }}</span> | 
                                    Mentor: <span class="text-gray-800">{{ $disiplin->mentor->nama_pem ?? 'Mentor' }}</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                    <p class="font-bold text-xs text-gray-500">Total Nilai Prestasi (Hard Skill):</p>
                                    <p class="text-xl font-black text-purple-900">{{ $disiplin->p1 + $disiplin->p2 + $disiplin->p3 + $disiplin->p4 + $disiplin->p5 }} / 500</p>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                    <p class="font-bold text-xs text-gray-500">Total Nilai Supervisi (Soft Skill):</p>
                                    <p class="text-xl font-black text-purple-900">{{ $disiplin->s3 + $disiplin->s5 + $disiplin->s6 + $disiplin->s7 + $disiplin->s8 }} / 500</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- MAIN CARD: KUISIONER -->
    <div class="bg-white rounded-xl border-2 border-emerald-900 shadow-[6px_6px_0_0_#064e3b] overflow-hidden">
        <div class="bg-emerald-900 p-4 flex justify-between items-center text-white">
            <h3 class="text-xl font-black uppercase">Hasil Kuisioner Evaluasi Mentor</h3>
            <span class="text-sm font-bold bg-black/20 px-3 py-1 rounded-full">{{ count($kuisioners) }} Data</span>
        </div>
        <div class="p-6 bg-gray-50">
            @if(count($kuisioners) === 0)
                <div class="p-10 text-center text-gray-400 font-bold italic border-2 border-dashed border-gray-300 rounded-xl">
                    Belum ada data evaluasi kuisioner dari mentor.
                </div>
            @else
                <div class="space-y-4">
                    @foreach($kuisioners as $kuis)
                        @php
                            $totalHard = $kuis->h1a + $kuis->h1b + $kuis->h1c + $kuis->h1d + $kuis->h2 + $kuis->h3 + $kuis->h4;
                            $totalSoft = $kuis->s1 + $kuis->s2 + $kuis->s3 + $kuis->s4 + $kuis->s5 + $kuis->s6 + $kuis->s7 + $kuis->s8;
                        @endphp
                        <div class="bg-white p-5 rounded-xl border-2 border-gray-200 shadow-sm hover:border-emerald-400 transition-colors">
                            <div class="flex justify-between items-center mb-3">
                                <span class="bg-emerald-100 text-emerald-800 border border-emerald-300 px-2 py-1 rounded text-xs font-black">{{ $kuis->tanggal }}</span>
                                <div class="text-sm font-bold text-gray-500">
                                    Mhs: <span class="text-gray-800">{{ $kuis->mahasiswa->nama_mhs ?? 'Mahasiswa' }}</span> | 
                                    Mentor: <span class="text-gray-800">{{ $kuis->mentor->nama_pem ?? 'Mentor' }}</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                    <p class="font-bold text-xs text-gray-500">Nilai Hard Skill (Max 70):</p>
                                    <p class="text-xl font-black text-emerald-900">{{ $totalHard }} / 70</p>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                    <p class="font-bold text-xs text-gray-500">Nilai Soft Skill (Max 80):</p>
                                    <p class="text-xl font-black text-emerald-900">{{ $totalSoft }} / 80</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
