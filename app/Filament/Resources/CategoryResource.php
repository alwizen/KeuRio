<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;


class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Kategori';

    protected static ?string $navigationGroup = 'KAS 💸';

    protected static ?string $modelLabel = 'Kategori';

    protected static ?string $pluralModelLabel = 'Kategori';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kategori')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Kategori')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn($state, Forms\Set $set) =>
                                $set('slug', Str::slug($state))
                            ),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->alphaDash()
                            ->helperText('URL-friendly version of name. Will be auto-generated if left empty.'),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Pengaturan Tampilan')
                    ->schema([
                        Forms\Components\TextInput::make('icon')
                            ->label('Icon')
                            ->placeholder('🍽️')
                            ->helperText('Gunakan emoji atau icon class (contoh: 🍽️, 🚗, 💼)'),

                        Forms\Components\ColorPicker::make('color')
                            ->label('Warna')
                            ->default('#6b7280')
                            ->hex()
                            ->rgba(),

                        Forms\Components\Select::make('type')
                            ->label('Jenis Transaksi')
                            ->required()
                            ->options([
                                'income' => 'Pemasukan saja',
                                'expense' => 'Pengeluaran saja',
                                'both' => 'Keduanya (Pemasukan & Pengeluaran)',
                            ])
                            ->default('expense')
                            ->native(false),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0)
                            ->helperText('Angka lebih kecil akan ditampilkan lebih dulu'),
                    ])->columns(2),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->helperText('Kategori yang tidak aktif tidak akan muncul di pilihan transaksi'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->width(60),

                Tables\Columns\TextColumn::make('icon')
                    ->label('Icon')
                    ->default('-')
                    ->width(60),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->weight(FontWeight::Medium)
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('Jenis')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'income' => 'Pemasukan',
                        'expense' => 'Pengeluaran',
                        'both' => 'Keduanya',
                    })
                    ->colors([
                        'success' => 'income',
                        'danger' => 'expense',
                        'primary' => 'both',
                    ]),

                Tables\Columns\ColorColumn::make('color')
                    ->label('Warna'),

                Tables\Columns\TextColumn::make('transactions_count')
                    ->label('Total Transaksi')
                    ->counts('transactions')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Jenis Transaksi')
                    ->options([
                        'income' => 'Pemasukan',
                        'expense' => 'Pengeluaran',
                        'both' => 'Keduanya',
                    ]),

                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        1 => 'Aktif',
                        0 => 'Tidak Aktif',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalDescription('Kategori yang memiliki transaksi tidak dapat dihapus. Pastikan untuk memindahkan atau menghapus transaksi terlebih dahulu.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktifkan')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(
                            fn(\Illuminate\Database\Eloquent\Collection $records) =>
                            $records->each->update(['is_active' => true])
                        )
                        ->requiresConfirmation()
                        ->modalHeading('Aktifkan Kategori')
                        ->modalDescription('Apakah Anda yakin ingin mengaktifkan kategori yang dipilih?'),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Nonaktifkan')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(
                            fn(\Illuminate\Database\Eloquent\Collection $records) =>
                            $records->each->update(['is_active' => false])
                        )
                        ->requiresConfirmation()
                        ->modalHeading('Nonaktifkan Kategori')
                        ->modalDescription('Apakah Anda yakin ingin menonaktifkan kategori yang dipilih?'),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCategories::route('/'),
        ];
    }
}
