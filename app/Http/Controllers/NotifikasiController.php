<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    // 1. Tandai semua notifikasi menjadi sudah dibaca
    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back(); // Kembali ke halaman sebelumnya
    }

    // 2. Hapus satu notifikasi spesifik (berdasarkan ID)
    public function hapusNotifikasi($id)
    {
        // Cari notifikasi milik user yang sedang login, lalu hapus
        auth()->user()->notifications()->where('id', $id)->delete();
        return back();
    }

    // 3. Hapus SEMUA riwayat notifikasi
    public function bersihkanSemua()
    {
        auth()->user()->notifications()->delete();
        return back();
    }
}