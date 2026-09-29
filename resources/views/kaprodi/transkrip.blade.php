<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Daftar Nilai PPI - {{ $mahasiswa->nim }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; color: #000; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 16pt; font-weight: bold; text-transform: uppercase; }
        .header p { margin: 5px 0 0 0; font-size: 12pt; }
        .student-info { width: 100%; margin-bottom: 20px; }
        .student-info td { padding: 3px 5px; vertical-align: top; }
        .student-info .label { width: 120px; font-weight: bold; }
        
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 10pt; text-align: center; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 6px; }
        table.data-table th { background-color: #f2f2f2; font-weight: bold; }
        table.data-table .text-left { text-align: left; }
        
        .footer { width: 100%; margin-top: 40px; }
        .footer td { width: 33%; text-align: center; vertical-align: bottom; height: 100px; }
        .notes { font-size: 9pt; margin-top: 30px; border: 1px solid #000; padding: 10px; }
        
        @media print { .btn-print { display: none; } }
        .btn-print { background-color: #0056b3; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; display: inline-block; font-family: sans-serif; margin-bottom: 20px;}
    </style>
</head>
<body>

    @if(!request()->has('export'))
        <a href="{{ route('kaprodi.transkrip', ['nim' => $mahasiswa->nim, 'export' => 'pdf']) }}" class="btn-print">Unduh PDF Resmi</a>
    @endif

    <div class="header">
        <h2>DAFTAR NILAI PPI EKSTERNAL (SEMESTER GENAP)</h2>
        <p>Politeknik Manufaktur Bandung - Program Studi {{ $mahasiswa->programStudi->nama_prodi }}</p>
    </div>

    <table class="student-info">
        <tr>
            <td class="label">NIM</td><td>: {{ $mahasiswa->nim }}</td>
            <td class="label">Kelas</td><td>: {{ $mahasiswa->kelas }}</td>
        </tr>
        <tr>
            <td class="label">Nama Lengkap</td><td>: {{ $mahasiswa->nama_mhs }}</td>
            <td class="label">Tahun Ajaran</td><td>: {{ date('Y')-1 }}/{{ date('Y') }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Kode MK</th>
                <th rowspan="2" class="text-left">Mata Kuliah</th>
                <th colspan="2">P & M (10%)</th>
                <th colspan="2">P & S (30%)</th>
                <th colspan="2">Logbook MK (60%)</th>
                <th rowspan="2">Nilai Akhir<br>(NA)</th>
                <th rowspan="2">Predikat</th>
            </tr>
            <tr>
                <th>Nilai</th><th>Konversi</th>
                <th>Nilai</th><th>Konversi</th>
                <th>Rata-rata</th><th>Jml Jam</th>
            </tr>
        </thead>
        <tbody>
            @foreach($courses as $index => $c)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $c['matkul']->kd_mat }}</td>
                <td class="text-left">{{ $c['matkul']->nama_komp }}</td>
                <td>{{ number_format($pm, 2) }}</td>
                <td>{{ number_format($pm * 0.1, 2) }}</td>
                <td>{{ number_format($ps, 2) }}</td>
                <td>{{ number_format($ps * 0.3, 2) }}</td>
                <td>{{ number_format($c['raw'], 2) }}</td>
                <td>{{ number_format($c['jam'], 1) }}</td>
                <td style="font-weight: bold;">{{ number_format($c['akhir'], 2) }}</td>
                <td style="font-weight: bold;">{{ $c['grade'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="notes">
        <strong>Formula Perhitungan (Sesuai Standar):</strong><br>
        * Presentasi & Makalah (P&M) = (40% x N_Presentasi) + (60% x N_Makalah)<br>
        * Prestasi & Supervisi (P&S) = (50% x N_Prestasi) + (50% x N_Supervisi)<br>
        * Nilai Akhir (NA) = (10% x P&M) + (30% x P&S) + (60% x Rata-rata Mingguan MK)
    </div>

    <table class="footer">
        <tr>
            <td></td>
            <td></td>
            <td>
                Bandung, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                Kepala Program Studi<br><br><br><br><br>
                <strong>___________________________</strong>
            </td>
        </tr>
    </table>

</body>
</html>