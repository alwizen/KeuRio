<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayrollResource\Pages;
use App\Models\Payroll;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class PayrollResource extends Resource
{
    protected static ?string $model = Payroll::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'HR 👥';
    protected static ?string $navigationLabel = 'Penggajian';
    protected static ?string $label = 'Penggajian';

    /** Helper bulan */
    protected static array $months = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('employee_id')
                ->relationship('employee', 'name')
                ->label('Nama Karyawan')
                ->required(),

            Forms\Components\Select::make('month')
                ->label('Bulan')
                ->options(self::$months)
                ->required(),

            Forms\Components\TextInput::make('year')
                ->numeric()
                ->label('Tahun')
                ->minValue(2000)
                ->maxValue(3100)
                ->default(2025)
                ->required(),

            // ===== Fields yang memengaruhi THP (live onBlur) =====
            Forms\Components\TextInput::make('base_salary')
                ->label('Gaji')
                ->numeric()->prefix('Rp')->default(0)
                ->live(onBlur: true)
                ->afterStateUpdated(fn(Set $set, Get $get) => self::recalcThp($set, $get)),

            Forms\Components\TextInput::make('bonus')
                ->numeric()->prefix('Rp')->default(0)
                ->live(onBlur: true)
                ->afterStateUpdated(fn(Set $set, Get $get) => self::recalcThp($set, $get)),

            Forms\Components\TextInput::make('health_incentive')
                ->label('Tunj. Kesehatan')
                ->numeric()->prefix('Rp')->default(0)
                ->live(onBlur: true)
                ->afterStateUpdated(fn(Set $set, Get $get) => self::recalcThp($set, $get)),

            Forms\Components\TextInput::make('work_incentive')
                ->numeric()->prefix('Rp')->default(0)
                ->live(onBlur: true)
                ->label('Insentif')
                ->afterStateUpdated(fn(Set $set, Get $get) => self::recalcThp($set, $get)),

            Forms\Components\TextInput::make('other')
                ->label('Lain-lain')
                ->numeric()->prefix('Rp')->default(0)
                ->live(onBlur: true)
                ->afterStateUpdated(fn(Set $set, Get $get) => self::recalcThp($set, $get)),

            Forms\Components\TextInput::make('cash_advance')
                ->label('Cashbon')
                ->numeric()->prefix('Rp')->default(0)
                ->live(onBlur: true)
                ->afterStateUpdated(fn(Set $set, Get $get) => self::recalcThp($set, $get)),

            Forms\Components\Textarea::make('note'),

            // Tetap kirim ke server walau disabled
            Forms\Components\TextInput::make('total_thp')
                ->numeric()->prefix('Rp')
                ->disabled()
                ->dehydrated()
                ->helperText('Auto-calculated (live on blur).')
                // Saat form di-hydrate (mode edit), pastikan terisi sesuai komponen lain
                ->afterStateHydrated(fn(Set $set, Get $get) => self::recalcThp($set, $get)),
        ]);
    }

    /** Hitung ulang THP dari state form */
    protected static function recalcThp(Set $set, Get $get): void
    {
        $toNum = fn($v) => (float) str_replace([',', ' '], '', (string) ($v ?? 0));
        $thp = (
            $toNum($get('base_salary')) +
            $toNum($get('bonus')) +
            $toNum($get('health_incentive')) +
            $toNum($get('work_incentive')) +
            $toNum($get('other'))
        ) - $toNum($get('cash_advance'));

        $set('total_thp', (string) max($thp, 0));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')
                    ->label('Employee')
                    ->label('Nama Karyawan')
                    ->searchable(),

                // Tampilkan nama bulan
                Tables\Columns\TextColumn::make('month')
                    ->label('Month')
                    ->label('Bulan')
                    ->formatStateUsing(fn($state) => self::$months[(int) $state] ?? $state)
                    ->sortable(),

                Tables\Columns\TextColumn::make('year')
                    ->label('Year')
                    ->label('Tahun')
                    ->sortable(),
                //      $toNum($get('base_salary')) +
                //     $toNum($get('bonus')) +
                //     $toNum($get('health_incentive')) +
                //     $toNum($get('work_incentive')) +
                //     $toNum($get('other'))
                // ) - $toNum($get('cash_advance'));

                Tables\Columns\TextColumn::make('base_salary')
                    ->label('Gaji /Bulan ')
                    ->numeric()
                    ->prefix('Rp '),
                Tables\Columns\TextColumn::make('bonus')
                    ->label('Bonus')
                    ->numeric()
                    ->prefix('Rp '),
                Tables\Columns\TextColumn::make('health_incentive')
                    ->label('Tunj. Kesehatan')
                    ->numeric()
                    ->toggleable()
                    ->prefix('Rp '),
                Tables\Columns\TextColumn::make('work_incentive')
                    ->label('Insentif')
                    ->toggleable()
                    ->numeric()
                    ->prefix('Rp '),
                Tables\Columns\TextColumn::make('cash_advance')
                    ->label('CashBon')
                    ->numeric()
                    ->prefix('Rp '),

                Tables\Columns\TextColumn::make('total_thp')
                    ->label('Total THP')
                    ->numeric()
                    ->prefix('Rp ')
                    ->summarize(
                        Sum::make()
                            ->label('Total')
                            ->numeric()
                            ->prefix('Rp.')
                    ),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('employee_id')
                    ->label('Employee')
                    ->relationship('employee', 'name'),

                Tables\Filters\SelectFilter::make('month')
                    ->label('Month')
                    ->options(self::$months),

                Tables\Filters\SelectFilter::make('year')
                    ->label('Year')
                    ->options(
                        fn() => Payroll::query()
                            ->select('year')->distinct()
                            ->pluck('year', 'year')->toArray()
                    ),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('From'),
                        Forms\Components\DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'] ?? null, fn($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'] ?? null, fn($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\Action::make('print')
                        ->label('Cetak')
                        ->icon('heroicon-o-printer')
                        ->color('gray')
                        ->action(function (Payroll $record) {
                            $pdf = Pdf::loadView('payroll.slip', [
                                'payrolls' => [$record],
                            ])->setPaper('A4', 'portrait');

                            return response()->streamDownload(
                                fn() => print($pdf->output()),
                                'slip-' . $record->id . '.pdf'
                            );
                        }),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                    ->button()
                    ->icon('heroicon-o-paper-clip')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make(),
                    Tables\Actions\BulkAction::make('print_slips')
                        ->label('Print Slips (PDF)')
                        ->icon('heroicon-o-printer')
                        ->color('gray')
                        ->action(function (Collection $records) {
                            // render semua slip dalam 1 PDF (tiap slip page-break)
                            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('payroll.slip', [
                                'payrolls' => $records->values(), // pastikan indexed
                            ])->setPaper('A4', 'portrait');

                            return response()->streamDownload(
                                fn() => print($pdf->output()),
                                'payroll-slips.pdf'
                            );
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePayrolls::route('/'),
        ];
    }
}
