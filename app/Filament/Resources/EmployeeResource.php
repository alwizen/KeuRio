<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'HR 👥';
    protected static ?string $navigationLabel = 'Karyawan';
    protected static ?string $label = 'Karyawan';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nip_preview')
                ->label('NIP')
                ->default(fn($record) => $record?->nip ?? 'RBJ-' . str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT)),

            Forms\Components\TextInput::make('name')
                ->required()
                ->label('Nama Karyawan')
                ->maxLength(255),

            Forms\Components\TextInput::make('phone')
                ->label('Telp')
                ->tel()
                ->default(6285),

            Forms\Components\Textarea::make('address')
                ->rows(2)
                ->label('Alamat')
                ->default('Tegal'),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nip')->searchable()->label('No Induk'),
                Tables\Columns\TextColumn::make('name')->searchable()->label('Nama Karyawan'),
                Tables\Columns\TextColumn::make('phone')->label('Telp')->placeholder('Tidak punya no telp'),
                Tables\Columns\TextColumn::make('address')->label('Alamat')
                    ->placeholder('Tidak Punya Tempat tinggal'),
                // Tables\Columns\TextColumn::make('created_at')->date(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageEmployees::route('/'),
        ];
    }
}
