<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use App\Models\Product;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Prodotti Venduti';

    protected static ?string $label = 'Prodotto';

    protected static ?string $pluralLabel = 'Prodotti';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Dettagli Prodotto')
                    ->schema([
                        Select::make('product_id')
                            ->label('Prodotto')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $product = Product::find($state);
                                    if ($product) {
                                        $set('unit_price', $product->sale_price);
                                    }
                                }
                            }),

                        TextInput::make('quantity')
                            ->label('Quantità')
                            ->numeric()
                            ->default(1)
                            ->minValue(0.001)
                            ->step(0.001)
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $unitPrice = $get('unit_price');
                                if ($state && $unitPrice) {
                                    $set('subtotal', round($state * $unitPrice, 2));
                                }
                            }),

                        TextInput::make('unit_price')
                            ->label('Prezzo Unitario')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01)
                            ->minValue(0)
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $quantity = $get('quantity');
                                if ($state && $quantity) {
                                    $set('subtotal', round($state * $quantity, 2));
                                }
                            }),

                        TextInput::make('subtotal')
                            ->label('Subtotale')
                            ->numeric()
                            ->prefix('€')
                            ->disabled()
                            ->dehydrated(false), // Non salvato nel DB, calcolato automaticamente
                    ])
                    ->columns(2)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product.name')
            ->columns([
                TextColumn::make('product.name')
                    ->label('Prodotto')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('product.unit')
                    ->label('Unità')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'kg' => 'success',
                        'pz' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('quantity')
                    ->label('Quantità')
                    ->numeric(decimalPlaces: 3)
                    ->sortable(),

                TextColumn::make('unit_price')
                    ->label('Prezzo Unitario')
                    ->money('EUR', true)
                    ->sortable(),

                TextColumn::make('subtotal')
                    ->label('Subtotale')
                    ->money('EUR', true)
                    ->sortable(),

                TextColumn::make('margin')
                    ->label('Margine %')
                    ->numeric(decimalPlaces: 1)
                    ->suffix('%')
                    ->color(fn ($state): string => $state > 30 ? 'success' : ($state > 15 ? 'warning' : 'danger')),

                TextColumn::make('total_profit')
                    ->label('Profitto')
                    ->money('EUR', true)
                    ->color(fn ($state): string => $state > 0 ? 'success' : 'danger'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Aggiungi Prodotto'),
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
