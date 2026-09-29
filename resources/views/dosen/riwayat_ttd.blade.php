@extends('dosen.app')
@section('title', 'Riwayat TTD & Approval')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12 font-sans">

    <!-- Header -->
    <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Riwayat TTD & Approval</h2>
            <p class="text-gray-500 mt-1">Log aktivitas persetujuan dokumen mahasiswa bimbingan Anda.</p>
        </div>
        <div class="bg-blue-50 text-blue-600 p-4 rounded-2xl flex items-center gap-4 border border-blue-100">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-blue-400">Total Disahkan</p>
                <p class="text-2xl font-black">{{ $riwayat->where('status', 'accepted')->count() }}</p>
            </div>
            <div class="w-px h-8 bg-blue-200"></div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-red-400">Total Ditolak</p>
                <p class="text-2xl font-black text-red-600">{{ $riwayat->where('status', 'rejected')->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Tabel Riwayat -->
    <div class="bg-white rounded-[2rem] border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 text-gray-500 uppercase tracking-wider text-xs font-semibold border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4">Waktu Keputusan</th>
                        <th class="px-6 py-4">Nama Mahasiswa</th>
                        <th class="px-6 py-4">Jenis Dokumen</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                    @forelse($riwayat as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-gray-500 text-xs">
                            {{ \Carbon\Carbon::parse($item->updated_at)->format('d M Y, H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-gray-900">{{ $item->mahasiswa->nama_mhs ?? 'Tanpa Nama' }}</span>
                            <br>
                            <span class="text-xs text-gray-400">{{ $item->mahasiswa->nim ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs font-bold uppercase">
                                {{ str_replace('_', ' ', $item->jenis_form) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($item->status == 'accepted')
                            <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-md text-xs font-bold">Verified (Acc)</span>
                            @else
                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-md text-xs font-bold">Ditolak</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <!-- Link menuju detail penilaian/approval -->
                            <a href="{{ route('approval.show', $item->token_verifikasi) }}" class="text-blue-600 hover:text-blue-800 font-bold text-xs bg-blue-50 hover:bg-blue-100 px-3 py-2 rounded-lg transition">
                                Lihat Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">Belum ada riwayat TTD.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-gray-100">
            {{ $riwayat->links() }}
        </div>
    </div>
</div>
@endsection