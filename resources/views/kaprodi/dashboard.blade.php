@extends('kaprodi.app')
@section('title', 'Dashboard Kaprodi')

@section('content')
<div class="space-y-6" x-data="kaprodiApp()">

    <!-- Header Panel -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b-4 border-blue-900 pb-4">
        <div>
            <h2 class="text-3xl font-black text-blue-900 tracking-wide">Dashboard KPS</h2>
            <p class="text-gray-600 mt-1 font-medium">Pantau dan kelola saran masukan dari mahasiswa untuk perusahaan.</p>
        </div>
    </div>

    <!-- MAIN CATEGORY: DARI MAHASISWA -->
    <div class="mb-10">
        <div class="flex items-center gap-3 mb-4">
            <div class="p-3 bg-blue-100 text-blue-900 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <h2 class="text-2xl font-black text-gray-800">Data dari Mahasiswa</h2>
        </div>

        <!-- MAIN CARD: SARAN -->
        <div class="bg-white rounded-xl border-2 border-blue-900 shadow-[6px_6px_0_0_#1e3a8a] overflow-hidden">
            <div class="bg-blue-900 p-4 flex justify-between items-center text-white">
                <h3 class="text-xl font-black uppercase">Daftar Saran dan Masukan</h3>
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
                                        Dari: 
                                        @if(isset($saran->mahasiswa))
                                            <button type="button" @click.prevent="openRekapanModal('{{ $saran->mahasiswa->nim }}', '{{ $saran->mahasiswa->nama_mhs }}')" class="text-blue-600 hover:text-blue-800 hover:underline cursor-pointer font-bold focus:outline-none">{{ $saran->mahasiswa->nama_mhs }}</button>
                                        @else
                                            <span class="text-gray-800">Mahasiswa</span>
                                        @endif
                                        @if($saran->mahasiswa && $saran->mahasiswa->programStudi)
                                            <span class="text-xs text-gray-400">({{ $saran->mahasiswa->programStudi->nama_prodi }})</span>
                                        @endif
                                    </div>
                                </div>
                                @php
                                    $saranData = json_decode($saran->saran, true);
                                    $isJson = (json_last_error() === JSON_ERROR_NONE) && is_array($saranData);
                                @endphp
                                @if($isJson)
                                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 space-y-3">
                                        @foreach($saranData as $question => $answer)
                                            <div>
                                                <p class="text-xs font-black text-blue-900 uppercase tracking-wider mb-1">{{ $question }}</p>
                                                <p class="text-gray-800 font-medium leading-relaxed bg-white p-3 rounded-lg border border-gray-200">{{ $answer }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-700 font-medium leading-relaxed bg-gray-50 p-4 rounded-lg border border-gray-100">
                                        "{{ $saran->saran }}"
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- MAIN CATEGORY: DARI MENTOR -->
    <div class="mb-10">
        <div class="flex items-center gap-3 mb-4">
            <div class="p-3 bg-purple-100 text-purple-900 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <h2 class="text-2xl font-black text-gray-800">Data dari Mentor (Perusahaan)</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- MAIN CARD: DISIPLIN -->
            <div class="bg-white rounded-xl border-2 border-purple-900 shadow-[6px_6px_0_0_#581c87] overflow-hidden">
                <div class="bg-purple-900 p-4 flex justify-between items-center text-white">
                    <h3 class="text-lg font-black uppercase">Form Disiplin & Prestasi</h3>
                    <span class="text-sm font-bold bg-black/20 px-3 py-1 rounded-full">{{ count($disiplins) }} Data</span>
                </div>
                <div class="p-6 bg-gray-50 h-full max-h-[600px] overflow-y-auto">
                    @if(count($disiplins) === 0)
                        <div class="p-10 text-center text-gray-400 font-bold italic border-2 border-dashed border-gray-300 rounded-xl">
                            Belum ada data disiplin dan prestasi.
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($disiplins as $disiplin)
                                <div class="bg-white p-5 rounded-xl border-2 border-gray-200 shadow-sm hover:border-purple-400 transition-colors">
                                    <div class="flex flex-col gap-2 mb-3">
                                        <div class="flex justify-between items-center">
                                            <span class="bg-purple-100 text-purple-800 border border-purple-300 px-2 py-1 rounded text-xs font-black">{{ $disiplin->tanggal }}</span>
                                        </div>
                                        <div class="text-sm font-bold text-gray-500">
                                            Mhs: 
                                            @if(isset($disiplin->mahasiswa))
                                                <button type="button" @click.prevent="openRekapanModal('{{ $disiplin->mahasiswa->nim }}', '{{ $disiplin->mahasiswa->nama_mhs }}')" class="text-blue-600 hover:text-blue-800 hover:underline cursor-pointer font-bold focus:outline-none">{{ $disiplin->mahasiswa->nama_mhs }}</button>
                                            @else
                                                <span class="text-gray-800">Mahasiswa</span>
                                            @endif
                                            <br>
                                            Mentor: <span class="text-gray-800">{{ $disiplin->mentor->nama_pem ?? 'Mentor' }}</span>
                                        </div>
                                    </div>
                                    <div class="mt-4 border-t pt-4">
                                        @php
                                            $disiplinData = json_decode($disiplin->penilaian, true);
                                            $isJson = (json_last_error() === JSON_ERROR_NONE) && is_array($disiplinData);
                                        @endphp
                                        @if($isJson)
                                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 space-y-2">
                                                @foreach($disiplinData as $question => $answer)
                                                    <div class="flex justify-between items-center border-b border-gray-200 pb-2 mb-2 last:border-0 last:mb-0 last:pb-0">
                                                        <span class="text-xs font-black text-purple-900 uppercase tracking-wider">{{ $question }}</span>
                                                        <span class="text-gray-800 font-bold bg-white px-3 py-1 rounded border border-gray-200">{{ $answer }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-gray-500 italic text-sm">Data penilaian dalam format lama atau kosong.</p>
                                        @endif
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
                    <h3 class="text-lg font-black uppercase">Evaluasi Kuisioner</h3>
                    <span class="text-sm font-bold bg-black/20 px-3 py-1 rounded-full">{{ count($kuisioners) }} Data</span>
                </div>
                <div class="p-6 bg-gray-50 h-full max-h-[600px] overflow-y-auto">
                    @if(count($kuisioners) === 0)
                        <div class="p-10 text-center text-gray-400 font-bold italic border-2 border-dashed border-gray-300 rounded-xl">
                            Belum ada data evaluasi kuisioner.
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($kuisioners as $kuis)

                                <div class="bg-white p-5 rounded-xl border-2 border-gray-200 shadow-sm hover:border-emerald-400 transition-colors">
                                    <div class="flex flex-col gap-2 mb-3">
                                        <div class="flex justify-between items-center">
                                            <span class="bg-emerald-100 text-emerald-800 border border-emerald-300 px-2 py-1 rounded text-xs font-black">{{ $kuis->tanggal }}</span>
                                        </div>
                                        <div class="text-sm font-bold text-gray-500">
                                            Mhs: 
                                            @if(isset($kuis->mahasiswa))
                                                <button type="button" @click.prevent="openRekapanModal('{{ $kuis->mahasiswa->nim }}', '{{ $kuis->mahasiswa->nama_mhs }}')" class="text-blue-600 hover:text-blue-800 hover:underline cursor-pointer font-bold focus:outline-none">{{ $kuis->mahasiswa->nama_mhs }}</button>
                                            @else
                                                <span class="text-gray-800">Mahasiswa</span>
                                            @endif
                                            <br>
                                            Mentor: <span class="text-gray-800">{{ $kuis->mentor->nama_pem ?? 'Mentor' }}</span>
                                        </div>
                                    </div>
                                    <div class="mt-4 border-t pt-4">
                                        @php
                                            $kuisData = json_decode($kuis->penilaian, true);
                                            $isJsonKuis = (json_last_error() === JSON_ERROR_NONE) && is_array($kuisData);
                                        @endphp
                                        @if($isJsonKuis)
                                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 space-y-2">
                                                @foreach($kuisData as $question => $answer)
                                                    <div class="flex justify-between items-center border-b border-gray-200 pb-2 mb-2 last:border-0 last:mb-0 last:pb-0">
                                                        <span class="text-xs font-black text-emerald-900 uppercase tracking-wider">{{ $question }}</span>
                                                        <span class="text-gray-800 font-bold bg-white px-3 py-1 rounded border border-gray-200">{{ $answer }} / 4</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-gray-500 italic text-sm">Data evaluasi dalam format lama atau kosong.</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN CARD: SARAN MENTOR -->
    <div class="mb-10">
        <div class="bg-white rounded-xl border-2 border-fuchsia-900 shadow-[6px_6px_0_0_#701a75] overflow-hidden">
            <div class="bg-fuchsia-900 p-4 flex justify-between items-center text-white">
                <h3 class="text-xl font-black uppercase">Daftar Saran dan Masukan dari Mentor</h3>
                <span class="text-sm font-bold bg-black/20 px-3 py-1 rounded-full">{{ count($saranMentors ?? []) }} Data</span>
            </div>

            <div class="p-6 bg-gray-50 max-h-[600px] overflow-y-auto">
                @if(isset($saranMentors) && count($saranMentors) === 0)
                    <div class="p-10 text-center text-gray-400 font-bold italic border-2 border-dashed border-gray-300 rounded-xl">
                        Belum ada saran atau masukan dari mentor ke mahasiswa.
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($saranMentors as $saran)
                            <div class="bg-white p-5 rounded-xl border-2 border-gray-200 shadow-sm hover:border-fuchsia-400 transition-colors">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-3 gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="bg-fuchsia-100 text-fuchsia-800 border border-fuchsia-300 px-2 py-1 rounded text-xs font-black">{{ $saran->tanggal }}</span>
                                    </div>
                                    <div class="text-sm font-bold text-gray-500">
                                        Dari: <span class="text-gray-800">{{ $saran->pembimbingIndustri->nama_pem ?? 'Mentor' }}</span><br>
                                        Ke: 
                                        @if(isset($saran->mahasiswa))
                                            <button type="button" @click.prevent="openRekapanModal('{{ $saran->mahasiswa->nim }}', '{{ $saran->mahasiswa->nama_mhs }}')" class="text-blue-600 hover:text-blue-800 hover:underline cursor-pointer font-bold focus:outline-none">{{ $saran->mahasiswa->nama_mhs }}</button>
                                        @else
                                            <span class="text-gray-800">Mahasiswa</span>
                                        @endif
                                        @if($saran->mahasiswa && $saran->mahasiswa->programStudi)
                                            <span class="text-xs text-gray-400">({{ $saran->mahasiswa->programStudi->nama_prodi }})</span>
                                        @endif
                                    </div>
                                </div>
                                @php
                                    $saranData = json_decode($saran->saran, true);
                                    $isJson = (json_last_error() === JSON_ERROR_NONE) && is_array($saranData);
                                @endphp
                                @if($isJson)
                                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 space-y-3">
                                        @foreach($saranData as $question => $answer)
                                            <div>
                                                <p class="text-xs font-black text-fuchsia-900 uppercase tracking-wider mb-1">{{ $question }}</p>
                                                <p class="text-gray-800 font-medium leading-relaxed bg-white p-3 rounded-lg border border-gray-200">{{ $answer }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-700 font-medium leading-relaxed bg-gray-50 p-4 rounded-lg border border-gray-100">
                                        "{{ $saran->saran }}"
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
    </div>

    <!-- MODAL REKAPAN JAM -->
    <div x-show="showRekapanModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div x-show="showRekapanModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="closeRekapanModal()"></div>

        <div x-show="showRekapanModal" x-transition.scale class="relative inline-block bg-white text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle max-w-5xl w-full border-4 border-cyan-900 rounded-xl flex flex-col max-h-[90vh]">
            
            <div class="bg-cyan-900 px-6 py-4 flex justify-between items-center text-white flex-shrink-0">
                <h3 class="text-xl md:text-2xl font-black">Rekapan Jam Logbook: <span x-text="selectedMahasiswaNama"></span></h3>
                <button @click="closeRekapanModal()" class="hover:text-red-400 transition-colors">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-6 bg-gray-50 flex-1 overflow-y-auto">
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
                            <canvas id="kaprodiRekapanChart"></canvas>
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

            <div class="bg-gray-200 border-t-2 border-gray-300 p-4 flex justify-end flex-shrink-0 rounded-b-xl">
                <button @click="closeRekapanModal()" class="bg-gray-500 text-white font-black px-8 py-3 rounded-xl border-2 border-gray-800 shadow-[4px_4px_0_0_#1f2937] hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all text-lg">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div> <!-- Akhir dari div x-data="kaprodiApp()" -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function kaprodiApp() {
        return {
            showRekapanModal: false,
            selectedMahasiswaNim: null,
            selectedMahasiswaNama: '',
            rekapanData: null,
            rekapanLoading: false,

            async openRekapanModal(nim, nama) {
                this.selectedMahasiswaNim = nim;
                this.selectedMahasiswaNama = nama;
                this.showRekapanModal = true;
                this.rekapanData = null;
                this.rekapanLoading = true;

                try {
                    const response = await fetch('/api/rekapan-jam/' + nim);
                    this.rekapanData = await response.json();
                    setTimeout(() => {
                        this.renderRekapanChart();
                    }, 200);
                } catch (e) {
                    alert('Gagal memuat data rekapan jam.');
                } finally {
                    this.rekapanLoading = false;
                }
            },

            closeRekapanModal() {
                this.showRekapanModal = false;
                this.selectedMahasiswaNim = null;
                this.selectedMahasiswaNama = '';
            },

            renderRekapanChart() {
                if(!this.rekapanData || this.rekapanData.rekapanJam.length === 0) return;
                
                let canvas = document.getElementById('kaprodiRekapanChart');
                if(!canvas) return;
                
                if(window.kaprodiRekapanChartObj) {
                    window.kaprodiRekapanChartObj.destroy();
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
                
                window.kaprodiRekapanChartObj = new Chart(ctx, {
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
            }
        }
    }
</script>
@endsection
