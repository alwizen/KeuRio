{{-- resources/views/payroll/slip.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }

        .slip {
            width: 100%;
            height: 14.5cm; /* setengah A4 */
            border: 1px solid #000;
            padding: 10px;
            box-sizing: border-box;
            page-break-inside: avoid;
            margin-bottom: 5px;
        }

        h2 {
            margin: 0;
            font-size: 14px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 4px;
            text-align: left;
            font-size: 11px;
        }

        th {
            border-bottom: 1px solid #000;
        }

        .amount {
            text-align: right;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 11px;
        }
    </style>
</head>
<body>
    @foreach ($payrolls as $payroll)
        <div class="slip">
            <h2>SLIP GAJI</h2>
            <p><strong>Periode:</strong> {{ $payroll->month_name }} {{ $payroll->year }}</p>
            <p><strong>Nama:</strong> {{ $payroll->employee->name }}</p>
            <p><strong>NIP:</strong> {{ $payroll->employee->nip }}</p>
            <p><strong>Dibuat:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>

            <table>
                <thead>
                    <tr>
                        <th>Komponen</th>
                        <th class="amount">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Base Salary</td>
                        <td class="amount">Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Bonus</td>
                        <td class="amount">Rp {{ number_format($payroll->bonus, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Health Incentive</td>
                        <td class="amount">Rp {{ number_format($payroll->health_incentive, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Work Incentive</td>
                        <td class="amount">Rp {{ number_format($payroll->work_incentive, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Other</td>
                        <td class="amount">Rp {{ number_format($payroll->other, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Cash Advance (Potongan)</td>
                        <td class="amount">- Rp {{ number_format($payroll->cash_advance, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Total Take Home Pay (THP)</th>
                        <th class="amount">Rp {{ number_format($payroll->total_thp, 0, ',', '.') }}</th>
                    </tr>
                </tbody>
            </table>

            <div class="footer">
                Mengetahui,<br><br><br>
                _______________________
            </div>
        </div>
    @endforeach
</body>
</html>
