<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Forms;

class OrderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $recordTitleAttribute = 'product_id';

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')->label('Prodotto'),
                Tables\Columns\TextColumn::make('quantity')->label('Quantità'),
                Tables\Columns\TextColumn::make('unit_price')->label('Prezzo Unitario')->money('eur'),
                Tables\Columns\TextColumn::make('subtotal')->label('Subtotale')->money('eur'),
            ])
            ->filters([
                //
            ]);
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->label('Prodotto')
                    ->required(),
                Forms\Components\TextInput::make('quantity')
                    ->label('Quantità')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('unit_price')
                    ->label('Prezzo Unitario')
                    ->numeric()
                    ->required(),
            ]);
    }
}
