<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Approval; // Pastikan model ini sudah Anda buat

class ApprovalController extends Controller
{
    public function show($token)
    {
        // CARI DATA BERDASARKAN TOKEN
        // Eager load relasi 'mahasiswa' agar view bisa menampilkan nama/NIM mahasiswa
        $approval = Approval::with('mahasiswa')->where('token_verifikasi', $token)->firstOrFail();

        // TAMBAHAN: Tandai notifikasi sebagai dibaca (read) jika User (Dosen/Mentor) mengkliknya lewat lonceng
        if (auth()->check()) {
            foreach (auth()->user()->unreadNotifications as $notif) {
                // Jika URL di notifikasi sama dengan token yang sedang dibuka, tandai read
                if (isset($notif->data['url']) && str_contains($notif->data['url'], $token)) {
                    $notif->markAsRead();
                }
            }
        }

        // Mengirimkan variabel $approval yang sudah ada datanya ke view
        return view('approval.show', compact('approval'));
    }

    public function process(Request $request, $token)
    {
        // 1. Ambil data pengajuan berdasarkan token
        $approval = Approval::where('token_verifikasi', $token)->firstOrFail();
        
        // 2. Validasi input dari request (harus accept atau reject)
        $request->validate([
            'action' => 'required|in:accept,reject',
            'alasan' => 'required_if:action,reject' // Alasan wajib diisi jika action = reject
        ]);

        // 3. Proses update status berdasarkan aksi yang dipilih
        if ($request->action === 'accept') {
            // Update status menjadi accepted
            $approval->update([
                'status' => 'accepted'
                // 'approved_at' => now() // Hapus komentar ini jika di tabel approvals Anda menambahkan kolom 'approved_at'
            ]);
            
            return response()->json([
                'status' => 'success', 
                'message' => 'Pengajuan TTD berhasil disetujui.'
            ]);
        } else {
            // Update status menjadi rejected dan simpan alasan penolakannya
            $approval->update([
                'status' => 'rejected', 
                'alasan_reject' => $request->alasan
            ]);
            
            return response()->json([
                'status' => 'success', 
                'message' => 'Pengajuan TTD berhasil ditolak.'
            ]);
        }
    }
}