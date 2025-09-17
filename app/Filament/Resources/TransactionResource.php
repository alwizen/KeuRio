<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Enums\FontWeight;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'Transaksi';

    protected static ?string $modelLabel = 'Transaksi';

    protected static ?string $pluralModelLabel = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Judul')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->maxLength(65535)
                    ->columnSpanFull(),


                Forms\Components\TextInput::make('amount')
                    ->label('Jumlah')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->step(0.01)
                    ->minValue(0),

                Forms\Components\Select::make('type')
                    ->label('Jenis')
                    ->required()
                    ->options([
                        'income' => 'Pemasukan',
                        'expense' => 'Pengeluaran',
                    ])
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(
                        fn($state, Forms\Set $set) =>
                        $set('category', null)
                    ),

                Forms\Components\TextInput::make('category')
                    ->label('Kategori')
                    ->datalist([
                        'lainnya',
                        'Penjualan'
                    ]),
                // Forms\Components\Select::make('category')
                //     ->label('Kategori')
                //     ->options(function (Forms\Get $get) {
                //         $type = $get('type');

                //         if ($type === 'income') {
                //             return [
                //                 'gaji' => 'Gaji',
                //                 'bonus' => 'Bonus',
                //                 'freelance' => 'Freelance',
                //                 'investasi' => 'Investasi',
                //                 'penjualan' => 'Penjualan',
                //                 'hadiah' => 'Hadiah',
                //                 'lainnya' => 'Lainnya',
                //             ];
                //         } elseif ($type === 'expense') {
                //             return [
                //                 'makanan' => 'Makanan & Minuman',
                //                 'transportasi' => 'Transportasi',
                //                 'belanja' => 'Belanja',
                //                 'tagihan' => 'Tagihan',
                //                 'kesehatan' => 'Kesehatan',
                //                 'pendidikan' => 'Pendidikan',
                //                 'hiburan' => 'Hiburan',
                //                 'pakaian' => 'Pakaian',
                //                 'rumah_tangga' => 'Rumah Tangga',
                //                 'lainnya' => 'Lainnya',
                //             ];
                //         }

                //         return [];
                //     })
                //     ->native(false)
                //     ->searchable(),

                Forms\Components\DatePicker::make('transaction_date')
                    ->label('Tanggal Transaksi')
                    ->required()
                    ->default(now())
                    ->native(false),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->weight(FontWeight::Medium),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('Jenis')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'income' => 'Pemasukan',
                        'expense' => 'Pengeluaran',
                    })
                    ->colors([
                        'success' => 'income',
                        'danger' => 'expense',
                    ])
                    ->icons([
                        'heroicon-o-arrow-trending-up' => 'income',
                        'heroicon-o-arrow-trending-down' => 'expense',
                    ]),

                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(function (?string $state): string {
                        if (!$state)
                            return '-';

                        $categories = [
                            // Income categories
                            'gaji' => 'Gaji',
                            'bonus' => 'Bonus',
                            'freelance' => 'Freelance',
                            'investasi' => 'Investasi',
                            'penjualan' => 'Penjualan',
                            'hadiah' => 'Hadiah',
                            // Expense categories
                            'makanan' => 'Makanan & Minuman',
                            'transportasi' => 'Transportasi',
                            'belanja' => 'Belanja',
                            'tagihan' => 'Tagihan',
                            'kesehatan' => 'Kesehatan',
                            'pendidikan' => 'Pendidikan',
                            'hiburan' => 'Hiburan',
                            'pakaian' => 'Pakaian',
                            'rumah_tangga' => 'Rumah Tangga',
                            'lainnya' => 'Lainnya',
                        ];

                        return $categories[$state] ?? $state;
                    }),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable()
                    ->color(
                        fn(Transaction $record): string =>
                        $record->type === 'income' ? 'success' : 'danger'
                    )
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    }),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Jenis')
                    ->options([
                        'income' => 'Pemasukan',
                        'expense' => 'Pengeluaran',
                    ]),

                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options([
                        'gaji' => 'Gaji',
                        'bonus' => 'Bonus',
                        'freelance' => 'Freelance',
                        'makanan' => 'Makanan & Minuman',
                        'transportasi' => 'Transportasi',
                        'belanja' => 'Belanja',
                        'tagihan' => 'Tagihan',
                        'hiburan' => 'Hiburan',
                    ]),

                Filter::make('transaction_date')
                    ->form([
                        DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('transaction_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('transaction_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('transaction_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTransactions::route('/'),
        ];
    }
}
