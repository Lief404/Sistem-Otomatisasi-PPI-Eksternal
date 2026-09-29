<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Daftar Nilai PPI Eksternal</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 8pt; color: #000; margin: 0; }
        .header { text-align: center; font-weight: bold; font-size: 14pt; text-decoration: underline; margin-bottom: 5px; text-transform: uppercase; }
        .sub-header { text-align: center; font-weight: bold; font-size: 10pt; margin-bottom: 20px; }
        
        table.data-table { width: 100%; border-collapse: collapse; text-align: center; }
        table.data-table th, table.data-table td { border: 1.5px solid #000; padding: 4px; }
        
        /* Pewarnaan Header ala Excel */
        table.data-table thead th { background-color: #d9d9d9; font-weight: bold; }
        
        .text-left { text-align: left !important; }
        .font-bold { font-weight: bold; }
        
        @media print { .btn-print { display: none; } }
        .btn-print { background-color: #0056b3; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; display: inline-block; margin-bottom: 20px; font-family: sans-serif;}
    </style>
</head>
<body>

    @if(!request()->has('export'))
        <!-- Tombol ini bisa diberi parameter '?kelas=3AEC2' dari view sebelumnya -->
        <a href="{{ route('kaprodi.rekap_all', ['export' => 'pdf', 'kelas' => $kelas]) }}" class="btn-print">Cetak PDF Batch</a>
    @endif

    <div class="header">
        DAFTAR NILAI PPI EKSTERNAL {{ $kelas ?? 'SELURUH KELAS' }}
    </div>
    <div class="sub-header">
        Semester : Genap &nbsp;&nbsp;|&nbsp;&nbsp; Tahun : {{ date('Y')-1 }}/{{ date('Y') }}
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 2%;">NO</th>
                <th rowspan="2" style="width: 6%;">NIM</th>
                <th rowspan="2" style="width: 12%;">NAMA</th>
                
                <th colspan="3">Presentasi & Makalah (10%)</th>
                <th colspan="3">Prestasi & Supervisi (30%)</th>
                
                @foreach($matkuls as $mk)
                    <th colspan="4">{{ $mk->kd_mat }} (60%)</th>
                @endforeach
                
                <th rowspan="2">Keterangan</th>
            </tr>
            <tr>
                <!-- P & M -->
                <th>Presentasi<br>40%</th>
                <th>Makalah<br>60%</th>
                <th>Nilai<br>P & M</th>
                
                <!-- P & S -->
                <th>Prestasi<br>50%</th>
                <th>Supervisi<br>50%</th>
                <th>Nilai<br>P & S</th>

                <!-- Loop MK -->
                @foreach($matkuls as $mk)
                    <th>Rata-rata<br>Mingguan</th>
                    <th>Jumlah<br>Jam</th>
                    <th>Nilai<br>{{ $mk->kd_mat }}</th>
                    <th>NA<br>{{ $mk->kd_mat }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rekapData as $index => $data)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $data['nim'] }}</td>
                <td class="text-left font-bold">{{ $data['nama'] }}</td>
                
                <td>{{ number_format($data['n_pres'], 2) }}</td>
                <td>{{ number_format($data['n_mak'], 2) }}</td>
                <td class="font-bold">{{ number_format($data['pm'], 2) }}</td>
                
                <td>{{ number_format($data['n_pres_ind'], 2) }}</td>
                <td>{{ number_format($data['n_sup'], 2) }}</td>
                <td class="font-bold">{{ number_format($data['ps'], 2) }}</td>
                
                @foreach($matkuls as $mk)
                    @php $mkData = $data['courses'][$mk->kd_mat]; @endphp
                    <td>{{ number_format($mkData['rata'], 2) }}</td>
                    <td>{{ number_format($mkData['jam'], 1) }}</td>
                    <td class="font-bold">{{ number_format($mkData['akhir'], 4) }}</td>
                    <td class="font-bold">{{ $mkData['grade'] }}</td>
                @endforeach
                
                <td>Diserahkan</td>
            </tr>
            @empty
            <tr>
                <td colspan="100%">Data mahasiswa tidak ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <br>
    <table style="width: 100%; font-size: 8pt;">
        <tr>
            <td style="width: 70%;">
                <b>Catatan Rumus:</b><br>
                @foreach($matkuls as $mk)
                    NA_{{ $mk->kd_mat }} = (10% * N_P&M) + (30% * N_P&S) + (60% * Rata-rata Mingguan {{ $mk->kd_mat }})<br>
                @endforeach
            </td>
            <td style="width: 30%; text-align: center;">
                Mengetahui,<br>
                Kepala Program Studi<br>
                <br><br><br><br>
                <b>(Siti Aminah, S.T., M.T.)</b>
            </td>
        </tr>
    </table>

</body>
</html>