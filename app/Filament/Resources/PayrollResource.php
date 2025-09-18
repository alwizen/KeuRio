<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayrollResource\Pages;
use App\Filament\Resources\PayrollResource\RelationManagers;
use App\Models\Payroll;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PayrollResource extends Resource
{
    protected static ?string $model = Payroll::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'HR';
    protected static ?string $navigationLabel = 'Penggajian';

    protected static ?string $label = 'Penggajian';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('employee_id')
                ->relationship('employee', 'name')
                ->label('Nama Karyawan')
                ->required(),

            Forms\Components\Select::make('month')
            ->options([
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
                ]),

            Forms\Components\TextInput::make('year')
                ->numeric()
                ->minValue(2000)
                ->maxValue(3100)
                ->default(2025)
                ->required(),

            Forms\Components\TextInput::make('base_salary')
                ->numeric()->prefix('Rp')->default(0),

            Forms\Components\TextInput::make('bonus')
                ->numeric()->prefix('Rp')->default(0),

            Forms\Components\TextInput::make('health_incentive')
                ->numeric()->prefix('Rp')->default(0),

            Forms\Components\TextInput::make('work_incentive')
                ->numeric()->prefix('Rp')->default(0),

            Forms\Components\TextInput::make('other')
                ->numeric()->prefix('Rp')->default(0),

            Forms\Components\TextInput::make('cash_advance')
                ->numeric()->prefix('Rp')->default(0),

            Forms\Components\Textarea::make('note'),

            Forms\Components\TextInput::make('total_thp')
                ->numeric()->prefix('Rp')->disabled()
                ->helperText('Auto-calculated before save.'),
        ]);
    }

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('employee.name')
                ->label('Employee')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('month')
                ->label('Month')
                ->sortable(),

            Tables\Columns\TextColumn::make('year')
                ->label('Year')
                ->sortable(),

            Tables\Columns\TextColumn::make('total_thp')
                ->money('Rp. ')
                ->summarize(Sum::make()
            ->label('Total')
        ->money('Rp.')),
        ])
        ->filters([
            // filter berdasarkan karyawan
            Tables\Filters\SelectFilter::make('employee_id')
                ->label('Employee')
                ->relationship('employee', 'name'),

            // filter bulan
            Tables\Filters\SelectFilter::make('month')
                ->label('Month')
                ->options([
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
                ]),

            // filter tahun (otomatis ambil dari data di DB)
            Tables\Filters\SelectFilter::make('year')
                ->label('Year')
                ->options(fn () => Payroll::query()
                    ->select('year')
                    ->distinct()
                    ->pluck('year', 'year')
                    ->toArray()
                ),

            // filter range tanggal created_at
            Tables\Filters\Filter::make('created_at')
                ->form([
                    Forms\Components\DatePicker::make('from')->label('From'),
                    Forms\Components\DatePicker::make('until')->label('Until'),
                ])
                ->query(function ($query, array $data) {
                    return $query
                        ->when($data['from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                        ->when($data['until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                }),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]);
}

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePayrolls::route('/'),
        ];
    }
}
