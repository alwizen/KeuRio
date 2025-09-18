<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Category;
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
use Illuminate\Support\Str;


class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    
    protected static ?string $navigationLabel = 'Transaksi';
    
    protected static ?string $modelLabel = 'Transaksi';
    
    protected static ?string $pluralModelLabel = 'Transaksi';

    protected static ?string $navigationGroup = 'KAS';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                         Forms\Components\DatePicker::make('transaction_date')
                            ->label('Tanggal Transaksi')
                            ->required()
                            ->default(now())
                            ->native(false),
                            
                        Forms\Components\Select::make('type')
                            ->label('Jenis')
                            ->required()
                            ->options([
                                'income' => 'Pemasukan',
                                'expense' => 'Pengeluaran',
                            ])
                            ->default('expense')
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => 
                                $set('category', null)
                            ),
                            
                        Forms\Components\Select::make('category_id')
                            ->label('Kategori')
                            ->relationship(
                                name: 'category',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query, Forms\Get $get) => 
                                    $query->active()
                                          ->forTransactionType($get('type') ?? 'expense')
                                          ->ordered()
                            )
                            ->getOptionLabelFromRecordUsing(fn (Category $record) => 
                                ($record->icon ? $record->icon . ' ' : '') . $record->name
                            )
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Kategori')
                                    ->required(),
                                Forms\Components\TextInput::make('icon')
                                    ->label('Icon (Emoji)')
                                    ->placeholder('🍽️'),
                                Forms\Components\ColorPicker::make('color')
                                    ->label('Warna')
                                    ->default('#6b7280'),
                                Forms\Components\Select::make('type')
                                    ->label('Jenis')
                                    ->options([
                                        'income' => 'Pemasukan',
                                        'expense' => 'Pengeluaran', 
                                        'both' => 'Keduanya',
                                    ])
                                    ->default(fn (Forms\Get $get) => $get('../../type') ?? 'expense')
                                    ->required(),
                            ])
                            ->createOptionUsing(function (array $data, Forms\Get $get): int {
                                $data['slug'] = Str::slug($data['name']);
                                $data['is_active'] = true;
                                $data['sort_order'] = 0;
                                
                                return Category::create($data)->getKey();
                            }),
                            

                        Forms\Components\TextInput::make('amount')
                            ->label('Jumlah')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->step(0.01)
                            ->minValue(0),
                       

                         Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->maxLength(65535)
                            ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Jenis Transaksi')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
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

                Tables\Columns\TextColumn::make('transaction_date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->weight(FontWeight::Medium),
                    
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(function (?string $state, Transaction $record): string {
                        if (!$record->category) return '-';
                        
                        return ($record->category->icon ? $record->category->icon . ' ' : '') . $record->category->name;
                    })
                    ->color('primary'),
                    
                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('Rp. ')
                    ->sortable()
                    ->color(fn (Transaction $record): string => 
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
                    
                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->preload(),
                    
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
                                fn (Builder $query, $date): Builder => $query->whereDate('transaction_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('transaction_date', '<=', $date),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTransactions::route('/'),
        ];
    }
}
