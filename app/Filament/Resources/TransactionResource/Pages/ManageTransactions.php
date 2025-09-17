<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Transaction;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ManageTransactions extends ManageRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua')
                ->badge(Transaction::count()),
                
            'pemasukan' => Tab::make('Pemasukan')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'income'))
                ->badge(Transaction::where('type', 'income')->count())
                ->badgeColor('success'),
                
            'pengeluaran' => Tab::make('Pengeluaran')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'expense'))
                ->badge(Transaction::where('type', 'expense')->count())
                ->badgeColor('danger'),
                
            'bulan_ini' => Tab::make('Bulan Ini')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->whereMonth('transaction_date', now()->month)
                          ->whereYear('transaction_date', now()->year)
                )
                ->badge(Transaction::whereMonth('transaction_date', now()->month)
                       ->whereYear('transaction_date', now()->year)->count()),
        ];
    }
}
