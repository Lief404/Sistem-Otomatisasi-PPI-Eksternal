    <?php

    use App\Http\Controllers\ProfileController;
    use App\Http\Controllers\AdminController;
    use App\Http\Controllers\MahasiswaController;
    use App\Http\Controllers\DosenController;
    use App\Http\Controllers\MentorController;
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
        Route::delete('/admin/users/{id}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
        Route::post('/admin/perusahaan', [AdminController::class, 'storePerusahaan'])->name('admin.perusahaan.store');
        Route::delete('/admin/perusahaan/{id}', [AdminController::class, 'destroyPerusahaan'])->name('admin.perusahaan.destroy');
        Route::post('/admin/mahasiswa/assign-pembimbing', [AdminController::class, 'assignPembimbing'])->name('admin.mahasiswa.assign');
        Route::post('/admin/matkul', [AdminController::class, 'storeMatkul'])->name('admin.matkul.store');
        Route::put('/admin/matkul/{id}', [AdminController::class, 'updateMatkul'])->name('admin.matkul.update');
        Route::delete('/admin/matkul/{id}', [AdminController::class, 'deleteMatkul'])->name('admin.matkul.destroy');
        Route::post('/admin/jadwal', [AdminController::class, 'storeJadwal'])->name('admin.jadwal.store');
        Route::delete('/admin/jadwal/{id}', [AdminController::class, 'destroyJadwal'])->name('admin.jadwal.destroy');
    });

    // Group Route untuk Mahasiswa
    Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
        Route::get('/mahasiswa/dashboard', [MahasiswaController::class, 'index'])->name('mahasiswa.dashboard');
        Route::post('/mahasiswa/logbook', [MahasiswaController::class, 'storeLogbook'])->name('mahasiswa.logbook.store');
        Route::delete('/mahasiswa/logbook/foto/{id}', [MahasiswaController::class, 'deleteLogbookFoto'])->name('mahasiswa.logbook.foto.delete');
        Route::post('/mahasiswa/saran', [MahasiswaController::class, 'storeSaran'])->name('mahasiswa.saran.store');
        Route::get('/mahasiswa/nilai', [MahasiswaController::class, 'nilai'])->name('mahasiswa.nilai');
    });

    // Group Route untuk Dosen
    Route::middleware(['auth', 'role:dosen'])->group(function () {
        Route::get('/dosen/dashboard', [DosenController::class, 'index'])->name('dosen.dashboard');
        Route::post('/dosen/penilaian', [DosenController::class, 'storePenilaian'])->name('dosen.penilaian.store');
    });

    // Group Route untuk Mentor
    Route::middleware(['auth', 'role:mentor'])->group(function () {
        Route::get('/mentor/dashboard', [MentorController::class, 'index'])->name('mentor.dashboard');
        Route::post('/mentor/saran', [MentorController::class, 'storeSaran'])->name('mentor.saran.store');
        Route::post('/mentor/disiplin', [MentorController::class, 'storeDisiplin'])->name('mentor.disiplin.store');
        Route::post('/mentor/kuisioner', [MentorController::class, 'storeKuisioner'])->name('mentor.kuisioner.store');
        Route::post('/mentor/logbook', [MentorController::class, 'storeLogbook'])->name('mentor.logbook.store');
    });

    // Group Route untuk Kaprodi
    Route::middleware(['auth', 'role:kaprodi'])->group(function () {
        Route::get('/kaprodi/dashboard', [App\Http\Controllers\KaprodiController::class, 'index'])->name('kaprodi.dashboard');
    });

    // Route bawaan Breeze untuk Profile (Bisa diakses semua role yang login)
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', function () {
            $role = auth()->user()->role;
            if ($role === 'admin') return redirect()->route('admin.dashboard');
            if ($role === 'dosen') return redirect()->route('dosen.dashboard');
            if ($role === 'mentor') return redirect()->route('mentor.dashboard');
            if ($role === 'kaprodi') return redirect()->route('kaprodi.dashboard');
            return redirect()->route('mahasiswa.dashboard');
        })->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    require __DIR__.'/auth.php';