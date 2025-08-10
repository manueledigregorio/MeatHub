<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome prodotto')
                            ->required(),

                        Select::make('unit')
                            ->label('Tipo')
                            ->required()
                            ->options([
                                'kg' => 'kg',
                                'pz' => 'pz',
                            ]),
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->required(),

                        TextInput::make('sale_price')
                            ->label('Prezzo di vendita')
                            ->numeric(),

                        TextInput::make('cost_price')
                            ->label('Prezzo di acquisto')
                            ->numeric(),

                        TextInput::make('stock_quantity')
                            ->label('Quantità prodotto')
                            ->numeric(),
                    ])
                    ->columns(3)


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('unit')
                    ->label('Unità')
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Categoria')
                    ->sortable(),
                TextColumn::make('sale_price')
                    ->label('Prezzo di vendita')
                    ->money('EUR', true)
                    ->sortable(),
                TextColumn::make('cost_price')
                    ->label('Prezzo di acquisto')
                    ->money('EUR', true)
                    ->sortable(),
                TextColumn::make('stock_quantity')
                    ->label('Quantità')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PurchaseRelationManager::class,
            RelationManagers\StockMovementRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}