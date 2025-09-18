<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Models\Category;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ManageCategories extends ManageRecords
{
    protected static string $resource = CategoryResource::class;

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
                ->badge(Category::count()),
                
            'aktif' => Tab::make('Aktif')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', true))
                ->badge(Category::where('is_active', true)->count())
                ->badgeColor('success'),
                
            'pemasukan' => Tab::make('Pemasukan')
                ->modifyQueryUsing(fn (Builder $query) => $query->forIncome())
                ->badge(Category::forIncome()->count())
                ->badgeColor('success'),
                
            'pengeluaran' => Tab::make('Pengeluaran')
                ->modifyQueryUsing(fn (Builder $query) => $query->forExpense())
                ->badge(Category::forExpense()->count())
                ->badgeColor('danger'),
        ];
    }
}
