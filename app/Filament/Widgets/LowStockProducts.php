<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Product;

class LowStockProducts extends BaseWidget
{
    protected static ?string $heading = 'Prodotti con Stock Basso';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::lowStock()->with('category')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Prodotto')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoria')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Stock Rimanente')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(fn (Product $record): string => ' ' . $record->unit)
                    ->color('warning')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('sale_price')
                    ->label('Prezzo Vendita')
                    ->money('EUR', true),

                Tables\Columns\TextColumn::make('margin')
                    ->label('Margine')
                    ->numeric(decimalPlaces: 1)
                    ->suffix('%')
                    ->color(fn ($state): string => $state > 30 ? 'success' : ($state > 15 ? 'warning' : 'danger')),
            ])
            ->actions([
                Tables\Actions\Action::make('restock')
                    ->label('Riordina')
                    ->icon('heroicon-m-plus')
                    ->color('success')
                    ->url(fn (Product $record): string => route('filament.admin.resources.products.edit', $record)),
            ])
            ->emptyStateHeading('Nessun prodotto con stock basso')
            ->emptyStateDescription('Tutti i prodotti hanno stock sufficiente.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}