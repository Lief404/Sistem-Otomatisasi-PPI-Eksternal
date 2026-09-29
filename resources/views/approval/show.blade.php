<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Tanda Tangan Digital</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Pastikan Alpine.js sudah ter-load -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased min-h-screen flex items-center justify-center p-4">

    <!-- Alpine.js Component -->
    <div x-data="approvalState()" class="w-full max-w-md relative">
        
        <!-- ============================================== -->
        <!-- STATE 1: KOTAK KONFIRMASI (PENDING)            -->
        <!-- ============================================== -->
        <div x-show="view === 'pending'" style="display: none;"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden absolute w-full"
             style="transform-origin: center;">
            
            <div class="bg-blue-600 p-6 text-center">
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <h2 class="text-xl font-bold text-white">Verifikasi Dokumen</h2>
                <p class="text-blue-100 text-sm mt-1">Permintaan Tanda Tangan Digital</p>
            </div>

            <div class="p-6 space-y-4">
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-1">Nama Mahasiswa</p>
                    <p class="font-bold text-gray-900">{{ $approval->mahasiswa->nama_mhs ?? $approval->mahasiswa_nama ?? 'Mahasiswa' }}</p>
                    
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider mt-4 mb-1">Dokumen</p>
                    <p class="font-bold text-gray-900 uppercase">{{ $approval->jenis_form }}</p>
                </div>

                <div class="flex gap-3 pt-2">
                    <button @click="changeView('rejecting')" class="flex-1 py-3 px-4 rounded-xl font-bold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 transition-colors">
                        Tolak
                    </button>
                    <button @click="processAction('accept')" :disabled="isLoading" class="flex-1 py-3 px-4 rounded-xl font-bold text-white bg-emerald-500 hover:bg-emerald-600 transition-colors flex justify-center items-center gap-2">
                        <span x-show="!isLoading">Setujui</span>
                        <svg x-show="isLoading" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- STATE 2: KOTAK ALASAN PENOLAKAN (REJECTING)    -->
        <!-- ============================================== -->
        <div x-show="view === 'rejecting'" style="display: none;"
             x-transition:enter="transition ease-out duration-300 delay-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden absolute w-full z-10">
            
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Alasan Penolakan</h3>
                <p class="text-sm text-gray-500 mb-4">Silakan berikan alasan mengapa dokumen ini ditolak agar mahasiswa dapat memperbaikinya.</p>
                
                <textarea x-model="alasanInput" rows="4" class="w-full border-gray-200 rounded-xl focus:ring-red-500 focus:border-red-500 text-sm mb-4 p-3 border" placeholder="Misal: Laporan minggu ini kurang detail..."></textarea>
                
                <div class="flex gap-3">
                    <button @click="changeView('pending')" class="flex-1 py-3 px-4 rounded-xl font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button @click="processAction('reject')" :disabled="isLoading" class="flex-1 py-3 px-4 rounded-xl font-bold text-white bg-red-600 hover:bg-red-700 transition-colors flex justify-center items-center gap-2">
                        <span x-show="!isLoading">Kirim Tolakan</span>
                        <svg x-show="isLoading" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- STATE 3: KOTAK SUKSES DISETUJUI (ACCEPTED)     -->
        <!-- ============================================== -->
        <div x-show="view === 'accepted'" style="display: none;"
             x-transition:enter="transition ease-out duration-500 delay-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden text-center p-8 absolute w-full z-20">
            
            <div class="w-24 h-24 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h2 class="text-2xl font-black text-gray-900 mb-2">Disetujui!</h2>
            <p class="text-gray-500 text-sm">Form penilaian ini sudah disetujui. Tanda Tangan Digital Anda telah sah dan tersinkronisasi.</p>
        </div>

        <!-- ============================================== -->
        <!-- STATE 4: KOTAK SUKSES DITOLAK (REJECTED DONE)  -->
        <!-- ============================================== -->
        <div x-show="view === 'rejected_done'" style="display: none;"
             x-transition:enter="transition ease-out duration-500 delay-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden text-center p-8 absolute w-full z-20">
            
            <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
            </div>
            <h2 class="text-2xl font-black text-gray-900 mb-2">Ditolak</h2>
            <p class="text-gray-500 text-sm mb-4">Form penilaian ini telah ditolak. Mahasiswa terkait akan menerima notifikasi.</p>
            
            <div x-show="alasanDb !== ''" class="bg-red-50 border border-red-100 p-3 rounded-xl text-sm text-red-700 italic text-left">
                "<span x-text="alasanDb"></span>"
            </div>
        </div>

        <!-- Spacer to maintain height in absolute positioning -->
        <div class="h-[420px] invisible"></div>
    </div>

    <script>
        function approvalState() {
            // Cek status dari database saat load pertama kali
            let dbStatus = '{{ $approval->status }}';
            let initialView = 'pending';
            
            if (dbStatus === 'accepted') {
                initialView = 'accepted';
            } else if (dbStatus === 'rejected') {
                initialView = 'rejected_done';
            }

            return {
                view: initialView, 
                alasanInput: '',
                alasanDb: '{{ $approval->alasan_reject ?? "" }}',
                isLoading: false,
                token: '{{ $approval->token_verifikasi }}',

                init() {
                    // Alpine init hook jika diperlukan
                },

                changeView(newView) {
                    this.view = newView;
                },

                async processAction(action) {
                    if (action === 'reject' && this.alasanInput.trim() === '') {
                        alert('Harap isi alasan penolakan terlebih dahulu.');
                        return;
                    }

                    this.isLoading = true;

                    try {
                        const response = await fetch(`/approval/${this.token}/process`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                action: action,
                                alasan: this.alasanInput
                            })
                        });

                        const result = await response.json();

                        if (result.status === 'success') {
                            if (action === 'reject') {
                                this.alasanDb = this.alasanInput;
                            }
                            // Ganti page dengan efek transisi
                            this.view = action === 'accept' ? 'accepted' : 'rejected_done';
                        } else {
                            alert('Terjadi kesalahan sistem: ' + (result.message || 'Unknown error'));
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Gagal terhubung ke server. Pastikan rute backend sesuai.');
                    } finally {
                        this.isLoading = false;
                    }
                }
            }
        }
    </script>
</body>
</html>