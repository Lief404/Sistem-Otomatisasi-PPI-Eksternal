<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\NotifikasiController;
use Illuminate\Support\Facades\Route;

// Landing Page bawaan kita
Route::get('/', function () {
    return view('welcome');
});

// Group Route untuk Admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::put('/admin/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::put('/admin/users/{id}/password', [AdminController::class, 'changePassword'])->name('admin.users.password');
    Route::delete('/admin/users/{id}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
    Route::post('/admin/perusahaan', [AdminController::class, 'storePerusahaan'])->name('admin.perusahaan.store');
    Route::delete('/admin/perusahaan/{id}', [AdminController::class, 'destroyPerusahaan'])->name('admin.perusahaan.destroy');
    Route::post('/admin/mahasiswa/assign-pembimbing', [AdminController::class, 'assignPembimbing'])->name('admin.mahasiswa.assign');
    Route::post('/admin/matkul', [AdminController::class, 'storeMatkul'])->name('admin.matkul.store');
    Route::put('/admin/matkul/{id}', [AdminController::class, 'updateMatkul'])->name('admin.matkul.update');
    Route::delete('/admin/matkul/{id}', [AdminController::class, 'deleteMatkul'])->name('admin.matkul.destroy');
    Route::post('/admin/jadwal', [AdminController::class, 'storeJadwal'])->name('admin.jadwal.store');
    Route::delete('/admin/jadwal/{id}', [AdminController::class, 'destroyJadwal'])->name('admin.jadwal.destroy');
    Route::get('/admin/backup/list', [AdminController::class, 'listBackups']);
    Route::post('/admin/backup/create', [AdminController::class, 'createBackup']);
    Route::post('/admin/backup/delete', [AdminController::class, 'deleteBackupFile']);
    Route::post('/admin/backup/rename', [AdminController::class, 'renameBackup']);
    Route::post('/admin/category/delete-all', [AdminController::class, 'deleteCategoryData']);
});

// Group Route untuk Mahasiswa
Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
    Route::get('/mahasiswa/dashboard', [MahasiswaController::class, 'index'])->name('mahasiswa.dashboard');
    Route::post('/mahasiswa/logbook', [MahasiswaController::class, 'storeLogbook'])->name('mahasiswa.logbook.store');
    Route::delete('/mahasiswa/logbook/foto/{id}', [MahasiswaController::class, 'deleteLogbookFoto'])->name('mahasiswa.logbook.foto.delete');
    Route::post('/mahasiswa/saran', [MahasiswaController::class, 'storeSaran'])->name('mahasiswa.saran.store');
    Route::get('/mahasiswa/nilai', [MahasiswaController::class, 'nilai'])->name('mahasiswa.nilai');
    Route::get('/mahasiswa/nilai/logbook', [MahasiswaController::class, 'apiNilaiLogbook'])->name('mahasiswa.nilai.logbook');
    Route::post('/mahasiswa/nilai/ajukan-qr', [MahasiswaController::class, 'ajukanQR'])->name('mahasiswa.nilai.ajukan_qr');
    Route::post('/mahasiswa/pengajuan-ttd', [MahasiswaController::class, 'requestTtd'])->name('mahasiswa.request-ttd');
    Route::post('/mahasiswa/nilai/batal-qr', [MahasiswaController::class, 'batalQR'])->name('mahasiswa.nilai.batal_qr');
});

// Group Route untuk Dosen
Route::middleware(['auth', 'role:dosen'])->group(function () {
    Route::get('/dosen/dashboard', [DosenController::class, 'index'])->name('dosen.dashboard');
    Route::post('/dosen/penilaian', [DosenController::class, 'storePenilaian'])->name('dosen.penilaian.store');
    Route::get('/dosen/riwayat-ttd', [DosenController::class, 'riwayatTtd'])->name('dosen.riwayat_ttd');
});

// Group Route untuk Mentor
Route::middleware(['auth', 'role:mentor'])->group(function () {
    Route::get('/mentor/dashboard', [MentorController::class, 'index'])->name('mentor.dashboard');
    Route::post('/mentor/saran', [MentorController::class, 'storeSaran'])->name('mentor.saran.store');
    Route::post('/mentor/disiplin', [MentorController::class, 'storeDisiplin'])->name('mentor.disiplin.store');
    Route::post('/mentor/kuisioner', [MentorController::class, 'storeKuisioner'])->name('mentor.kuisioner.store');
    Route::post('/mentor/logbook', [MentorController::class, 'storeLogbook'])->name('mentor.logbook.store');
    Route::get('/mentor/riwayat-ttd', [MentorController::class, 'riwayatTtd'])->name('mentor.riwayat_ttd');
});

// Group Route untuk Kaprodi
Route::middleware(['auth', 'role:kaprodi'])->group(function () {
    Route::get('/kaprodi/dashboard', [App\Http\Controllers\KaprodiController::class, 'index'])->name('kaprodi.dashboard');
    Route::get('/kaprodi/export', [App\Http\Controllers\KaprodiController::class, 'exportIndex'])->name('kaprodi.export');
    Route::get('/kaprodi/export/{nim}', [App\Http\Controllers\KaprodiController::class, 'transkrip'])->name('kaprodi.transkrip');
    
    // Route untuk manajemen parameter penilaian presentasi
    Route::get('/kaprodi/parameter', [App\Http\Controllers\KaprodiController::class, 'parameter'])->name('kaprodi.parameter');
    Route::post('/kaprodi/parameter', [App\Http\Controllers\KaprodiController::class, 'storeParameter'])->name('kaprodi.parameter.store');
    Route::put('/kaprodi/parameter/{id}', [App\Http\Controllers\KaprodiController::class, 'updateParameter'])->name('kaprodi.parameter.update');
    Route::delete('/kaprodi/parameter/{id}', [App\Http\Controllers\KaprodiController::class, 'destroyParameter'])->name('kaprodi.parameter.destroy');
    Route::get('/kaprodi/rekap-all', [App\Http\Controllers\KaprodiController::class, 'cetakRekap'])->name('kaprodi.rekap_all');
});

// Group Route untuk Approval Tanda Tangan Digital (QR Code)
// (Tidak dimasukkan ke dalam middleware auth agar Dosen/Mentor bisa langsung scan dan acc dari HP tanpa harus login dulu. Autentikasi menggunakan token unik).
Route::get('/approval/{token}', [App\Http\Controllers\ApprovalController::class, 'show'])->name('approval.show');
Route::post('/approval/{token}/process', [App\Http\Controllers\ApprovalController::class, 'process'])->name('approval.process');


// Route bawaan Breeze untuk Profile (Bisa diakses semua role yang login)
Route::middleware('auth')->group(function () {
    
    // ROUTE NOTIFIKASI DIPINDAHKAN KE SINI
    Route::post('/notifikasi/tandai-semua-dibaca', [NotifikasiController::class, 'markAllRead'])->name('notifikasi.read_all');
    Route::delete('/notifikasi/hapus/{id}', [NotifikasiController::class, 'hapusNotifikasi'])->name('notifikasi.delete');
    Route::delete('/notifikasi/bersihkan-semua', [NotifikasiController::class, 'bersihkanSemua'])->name('notifikasi.clear_all');
    Route::post('/notifikasi/{id}/read', [NotifikasiController::class, 'markAsRead'])->name('notifikasi.read');

    Route::get('/dashboard', function () {
        $role = auth()->user()->role;
        if ($role === 'admin') return redirect()->route('admin.dashboard');
        if ($role === 'dosen') return redirect()->route('dosen.dashboard');
        if ($role === 'mentor') return redirect()->route('mentor.dashboard');
        if ($role === 'kaprodi') return redirect()->route('kaprodi.dashboard');
        return redirect()->route('mahasiswa.dashboard');
    })->name('dashboard');

    Route::get('/transkrip/{nim}', [MahasiswaController::class, 'transkrip'])->name('transkrip.show');
    Route::get('/api/rekapan-jam/{nim}', [MahasiswaController::class, 'apiRekapanJam'])->name('api.rekapan-jam');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';