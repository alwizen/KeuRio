<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\TransactionResource;
use App\Models\Transaction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Support\Enums\FontWeight;

class RecentTransactionsWidget extends BaseWidget
{
    protected static ?string $heading = 'Transaksi Terbaru';

    protected static bool $isLazy = false;

    protected static ?int $sort = 3;

    protected static ?string $maxHeight = '200px';


    protected int | string | array $columnSpan = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Transaction::query()->latest('transaction_date')->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('transaction_date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    // ->searchable()
                    ->weight(FontWeight::Medium)
                    ->limit(20),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('Jenis')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'income' => 'Masuk',
                        'expense' => 'Keluar',
                    })
                    ->colors([
                        'success' => 'income',
                        'danger' => 'expense',
                    ])
                    ->size('xs'),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    // ->searchable()
                    ->weight(FontWeight::Medium)
                    ->limit(20),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->color(
                        fn(Transaction $record): string =>
                        $record->type === 'income' ? 'success' : 'danger'
                    )
                    ->weight(FontWeight::Bold),
            ])
            // ->actions([
            //     Tables\Actions\Action::make('view')
            //         ->label('Lihat')
            //         ->icon('heroicon-m-eye')
            //         // ->url(fn (Transaction $record): string => TransactionResource::getUrl('edit', ['record' => $record]))
            //         ->size('sm'),
            // ])
            ->defaultPaginationPageOption(10);
    }
}
