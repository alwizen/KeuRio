<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - RBJ Corp</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: white;
        }

        .slip {
            height: 148.5mm;
            padding: 15mm;
            border-bottom: 1px dashed #ccc;
            page-break-inside: avoid;
        }

        .slip:last-child {
            border-bottom: none;
        }

        :root {
            --logo-size: 64px;
        }

        /* ubah sesuai ukuran logo kamu */

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #2c3e50;
            padding-bottom: 8px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .logo {
            width: var(--logo-size);
            height: var(--logo-size);
            object-fit: contain;
            display: block;
        }

        /* KUNCI: blok teks setinggi logo + posisi presisi */
        .brand-block {
            position: relative;
            height: var(--logo-size);
            min-width: 220px;
            /* opsional */
        }

        /* RBJ CORP sedikit di atas center logo */
        .brand-title {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-60%);
            /* naikkan dikit biar subjudul pas di bawah */
            margin: 0;
            line-height: 1;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: .5px;
        }

        /* SLIP GAJI tepat di bawahnya */
        .brand-sub {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(10%);
            /* sedikit di bawah center */
            margin: 0;
            font-size: 14px;
            letter-spacing: .5px;
        }

        .slip-number {
            text-align: right;
            font-size: 11px;
            color: #666;
        }

        .employee-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 12px 15px;
            border-radius: 5px;
        }

        .info-left,
        .info-right {
            width: 48%;
        }

        .info-row {
            margin-bottom: 6px;
            display: flex;
        }

        .info-label {
            width: 100px;
            font-weight: 600;
            color: #2c3e50;
        }

        .info-value {
            flex: 1;
            color: #34495e;
        }

        .salary-details {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }

        .earnings,
        .deductions {
            flex: 1;
        }

        .section-title {
            background-color: #34495e;
            color: white;
            padding: 8px 12px;
            font-weight: 600;
            margin-bottom: 10px;
            border-radius: 3px;
            font-size: 13px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 12px;
            border-bottom: 1px solid #ecf0f1;
            margin-bottom: 2px;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #2c3e50;
        }

        .detail-amount {
            font-weight: 600;
            color: #27ae60;
        }

        .deductions .detail-amount {
            color: #e74c3c;
        }

        .total-section {
            background-color: #2c3e50;
            color: white;
            padding: 12px 15px;
            margin-top: 10px;
            border-radius: 5px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .total-row:last-child {
            margin-bottom: 0;
            font-size: 16px;
            font-weight: bold;
            border-top: 1px solid #34495e;
            padding-top: 8px;
            margin-top: 8px;
        }

        .note {
            margin-top: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-left: 4px solid #3498db;
            border-radius: 0 3px 3px 0;
        }

        .note-title {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .note-text {
            color: #7f8c8d;
            font-size: 11px;
            line-height: 1.5;
        }

        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 10px;
            color: #95a5a6;
            border-top: 1px solid #ecf0f1;
            padding-top: 10px;
        }

        @media print {
            .page {
                margin: 0;
                width: 210mm;
                height: 297mm;
            }

            .slip {
                height: 148.5mm;
            }
        }

        .amount {
            font-family: 'Courier New', monospace;
        }
    </style>
</head>

<body>
    <div class="page">
        @foreach ($payrolls as $index => $payroll)
            <div class="slip">
                <!-- Header -->
                <div class="header">
                    <div class="header-left">
                        <img src="{{ public_path('img/hero2.png') }}" alt="RBJ Corp Logo" class="logo">
                        <div class="brand-block">
                            <h1 class="brand-title">RBJ CORP</h1>
                            <div class="brand-sub">SLIP GAJI</div>
                        </div>
                    </div>

                    <div class="slip-number">
                        <div>No: {{ str_pad($payroll->id, 6, '0', STR_PAD_LEFT) }}</div>
                        <div>{{ date('d/m/Y') }}</div>
                    </div>
                </div>

                <!-- Employee Information -->
                <div class="employee-info">
                    <div class="info-left">
                        <div class="info-row">
                            <div class="info-label">Nama</div>
                            <div class="info-value">: {{ $payroll->employee->name ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="info-right">
                        <div class="info-row">
                            <div class="info-label">Periode</div>
                            <div class="info-value">: {{ $payroll->month }}/{{ $payroll->year }}</div>
                        </div>
                    </div>
                </div>

                <!-- Salary Details -->
                <div class="salary-details">
                    <!-- Earnings -->
                    <div class="earnings">
                        <div class="section-title">PENGHASILAN</div>
                        <div class="detail-row">
                            <span class="detail-label">Gaji Pokok</span>
                            <span class="detail-amount amount">Rp
                                {{ number_format($payroll->base_salary, 0, ',', '.') }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Bonus</span>
                            <span class="detail-amount amount">Rp
                                {{ number_format($payroll->bonus, 0, ',', '.') }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Tunj. Kesehatan</span>
                            <span class="detail-amount amount">Rp
                                {{ number_format($payroll->health_incentive, 0, ',', '.') }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Insentif Kerja</span>
                            <span class="detail-amount amount">Rp
                                {{ number_format($payroll->work_incentive, 0, ',', '.') }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Lain-lain</span>
                            <span class="detail-amount amount">Rp
                                {{ number_format($payroll->other, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Deductions -->
                    <div class="deductions">
                        <div class="section-title">POTONGAN</div>
                        <div class="detail-row">
                            <span class="detail-label">Kasbon</span>
                            <span class="detail-amount amount">Rp
                                {{ number_format($payroll->cash_advance, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Total Section -->
                <div class="total-section">
                    <div class="total-row">
                        <span>Total Penghasilan:</span>
                        <span class="amount">Rp
                            {{ number_format($payroll->base_salary + $payroll->bonus + $payroll->health_incentive + $payroll->work_incentive + $payroll->other, 0, ',', '.') }}</span>
                    </div>
                    <div class="total-row">
                        <span>Total Potongan:</span>
                        <span class="amount">Rp {{ number_format($payroll->cash_advance, 0, ',', '.') }}</span>
                    </div>
                    <div class="total-row">
                        <span>TAKE HOME PAY (THP):</span>
                        <span class="amount">Rp {{ number_format($payroll->total_thp, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Note -->
                @if ($payroll->note)
                    <div class="note">
                        <div class="note-title">Catatan:</div>
                        <div class="note-text">{{ $payroll->note }}</div>
                    </div>
                @endif

                <!-- Footer -->
                <div class="footer">
                    <div>RBJ Corp - Human Resources Department</div>
                    <div>Dokumen ini dihasilkan secara otomatis dan tidak memerlukan tanda tangan</div>
                </div>
            </div>
        @endforeach
    </div>
</body>

</html>
