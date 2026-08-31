<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan_Kemajuan_Siswa_Empat_Pilar_{{ date('Ymd') }}</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 40px;
            font-size: 12px;
            line-height: 1.5;
        }

        /* Kop Surat */
        .kop-surat {
            text-align: center;
            border-bottom: 3px double #1e293b;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .kop-surat h1 {
            font-size: 18px;
            margin: 0 0 6px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 800;
        }
        .kop-surat h2 {
            font-size: 14px;
            margin: 0 0 6px 0;
            font-weight: 600;
        }
        .kop-surat p {
            margin: 0;
            color: #64748b;
            font-size: 11px;
        }

        .report-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 24px;
            font-size: 11px;
        }
        .meta-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .meta-item span {
            font-weight: 600;
        }

        /* Table styling for print */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 10px 12px;
            text-align: left;
        }
        th {
            background-color: #f1f5f9 !important;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
            color: #334155;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }

        /* Signatures block */
        .signature-block {
            display: flex;
            justify-content: flex-end;
            margin-top: 60px;
            page-break-inside: avoid;
        }
        .signature {
            text-align: center;
            width: 200px;
        }
        .signature .date {
            margin-bottom: 60px;
        }
        .signature .name {
            font-weight: 700;
            text-decoration: underline;
        }
        .signature .role {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }

        /* Action bar shown only on screen */
        .action-bar {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid transparent;
            transition: all 0.2s;
        }
        .btn-primary {
            background-color: #ef4444;
            color: white;
        }
        .btn-primary:hover {
            background-color: #dc2626;
        }
        .btn-secondary {
            background-color: white;
            border-color: #cbd5e1;
            color: #334155;
        }
        .btn-secondary:hover {
            background-color: #f1f5f9;
        }

        @media print {
            .action-bar {
                display: none !important;
            }
            body {
                padding: 0;
            }
            @page {
                size: A4;
                margin: 2cm;
            }
        }
    </style>
</head>
<body>

    <!-- Action Bar for Screen view -->
    <div class="action-bar">
        <div>
            <span style="font-weight: 600;">Pratinjau Cetak Laporan</span>
            <span style="color: #64748b; margin-left: 8px; font-size: 11px;">Gunakan tombol cetak atau simpan sebagai PDF dari menu browser.</span>
        </div>
        <div style="display: flex; gap: 12px;">
            <button onclick="window.close()" class="btn btn-secondary">Tutup Halaman</button>
            <button onclick="window.print()" class="btn btn-primary">🖨️ Cetak / Simpan PDF</button>
        </div>
    </div>

    <!-- Print Content -->
    <div style="max-width: 800px; margin: 0 auto;">
        
        <!-- Kop Surat -->
        <div class="kop-surat">
            <h1>Laporan Kemajuan Belajar Siswa</h1>
            <h2>Platform Pendidikan Empat Pilar Kebangsaan</h2>
            <p>Mata Pelajaran: Pendidikan Pancasila dan Kewarganegaraan (PPKN)</p>
            <p style="margin-top: 4px;">SMA / SMK / MA Tingkat Nasional</p>
        </div>

        <!-- Meta info -->
        <div class="report-meta">
            <div class="meta-group">
                <div class="meta-item">Tanggal Cetak: <span>{{ date('d F Y, H:i') }}</span></div>
                <div class="meta-item">Dicetak Oleh: <span>{{ Auth::user()->name }} (Admin)</span></div>
            </div>
            <div class="meta-group" style="text-align: right;">
                <div class="meta-item">Total Siswa Terdaftar: <span>{{ count($students) }} Siswa</span></div>
                <div class="meta-item">Total Modul Pembelajaran: <span>{{ $totalMaterialsCount }} Modul</span></div>
            </div>
        </div>

        <!-- Table Data -->
        <table>
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">No</th>
                    <th>Nama Siswa</th>
                    <th>Sekolah</th>
                    <th style="width: 130px;">Dapil</th>
                    <th style="text-align: center; width: 100px;">Materi Selesai</th>
                    <th style="text-align: center; width: 90px;">Kuis Diikuti</th>
                    <th style="text-align: center; width: 90px;">Rerata Nilai</th>
                    <th style="text-align: center; width: 70px;">Poin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $index => $student)
                    @php
                        $progressPercent = $totalMaterialsCount > 0 
                            ? round(($student->completed_progress_count / $totalMaterialsCount) * 100) 
                            : 0;
                    @endphp
                    <tr>
                        <td style="text-align: center; font-weight: 600;">{{ $index + 1 }}</td>
                        <td style="font-weight: 600;">{{ $student->name }}</td>
                        <td>{{ $student->school_name }}</td>
                        <td>{{ $student->dapil ?? '-' }}</td>
                        <td style="text-align: center;">
                            {{ $student->completed_progress_count }} / {{ $totalMaterialsCount }} ({{ $progressPercent }}%)
                        </td>
                        <td style="text-align: center;">{{ $student->quizzes_count }} Kali</td>
                        <td style="text-align: center; font-weight: 700; color: {{ $student->average_score >= 70 ? '#10b981' : ($student->average_score > 0 ? '#ef4444' : '#64748b') }}">
                            {{ $student->average_score > 0 ? $student->average_score . '%' : '-' }}
                        </td>
                        <td style="text-align: center; font-weight: 700;">{{ $student->points }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Signatures -->
        <div class="signature-block">
            <div class="signature">
                <div class="date">Jakarta, {{ date('d F Y') }}</div>
                <div class="name">{{ Auth::user()->name }}</div>
                <div class="role">Guru / Administrator PPKN</div>
            </div>
        </div>

    </div>

    <!-- Automatically trigger browser print dialog -->
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 800);
        }
    </script>
</body>
</html>
