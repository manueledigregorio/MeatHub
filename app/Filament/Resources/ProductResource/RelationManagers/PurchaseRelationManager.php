<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;

class PurchaseRelationManager extends RelationManager
{
    protected static string $relationship = 'purchases';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('quantity')
                    ->label('Quantità acquistata')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('unit_cost')
                    ->label('Prezzo d’acquisto')
                    ->numeric() //numero si arrotonda da 7,50 a 8
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('supplier.name')
                    ->label('Fornitore')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label('Prodotto')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->label('Quantità acquistata')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('unit_cost')
                    ->label('Prezzo d’acquisto')
                    ->money('EUR', true),
                TextColumn::make('total')
                    ->label('Totale')
                    ->getStateUsing(fn($record) => number_format($record->quantity * $record->unit_cost, 2) . ' €')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}