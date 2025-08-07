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
use Illuminate\Database\Eloquent\Collection;

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
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01)
                            ->minValue(0)
                            ->required()
                            ->rules(['numeric', 'min:0'])
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $costPrice = $get('cost_price');
                                if ($state && $costPrice && $state <= $costPrice) {
                                    $set('sale_price', null);
                                    \Filament\Notifications\Notification::make()
                                        ->title('Errore Prezzo')
                                        ->body('Il prezzo di vendita deve essere maggiore del prezzo di acquisto')
                                        ->danger()
                                        ->send();
                                }
                            }),

                        TextInput::make('cost_price')
                            ->label('Prezzo di acquisto')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01)
                            ->minValue(0)
                            ->required()
                            ->rules(['numeric', 'min:0'])
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $salePrice = $get('sale_price');
                                if ($state && $salePrice && $salePrice <= $state) {
                                    $set('cost_price', null);
                                    \Filament\Notifications\Notification::make()
                                        ->title('Errore Prezzo')
                                        ->body('Il prezzo di acquisto deve essere minore del prezzo di vendita')
                                        ->danger()
                                        ->send();
                                }
                            }),

                        TextInput::make('stock_quantity')
                            ->label('Quantità prodotto')
                            ->numeric()
                            ->step(0.001)
                            ->minValue(0)
                            ->default(0)
                            ->required()
                            ->rules(['numeric', 'min:0']),
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
                    ->numeric(decimalPlaces: 2)
                    ->suffix(fn (Product $record): string => ' ' . $record->unit)
                    ->sortable()
                    ->color(fn ($state): string => $state <= 5 ? 'danger' : ($state <= 10 ? 'warning' : 'success')),

                TextColumn::make('margin')
                    ->label('Margine %')
                    ->numeric(decimalPlaces: 1)
                    ->suffix('%')
                    ->color(fn ($state): string => $state > 30 ? 'success' : ($state > 15 ? 'warning' : 'danger')),

                TextColumn::make('unit_profit')
                    ->label('Profitto/Unità')
                    ->money('EUR', true)
                    ->color(fn ($state): string => $state > 0 ? 'success' : 'danger'),

                TextColumn::make('in_stock')
                    ->label('Disponibile')
                    ->badge()
                    ->color(fn ($state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn ($state): string => $state ? 'Sì' : 'No'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->label('Categoria')
                    ->preload(),

                Tables\Filters\Filter::make('low_stock')
                    ->label('Stock Basso')
                    ->query(fn (Builder $query): Builder => $query->lowStock())
                    ->toggle(),

                Tables\Filters\Filter::make('in_stock')
                    ->label('In Stock')
                    ->query(fn (Builder $query): Builder => $query->inStock())
                    ->toggle(),

                Tables\Filters\Filter::make('high_margin')
                    ->label('Margine Alto (>30%)')
                    ->query(fn (Builder $query): Builder => $query->whereRaw('((sale_price - cost_price) / sale_price * 100) > 30'))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('adjust_stock')
                    ->label('Regola Stock')
                    ->icon('heroicon-m-adjustments-horizontal')
                    ->color('warning')
                    ->form([
                        Forms\Components\TextInput::make('adjustment')
                            ->label('Regolazione Stock')
                            ->numeric()
                            ->required()
                            ->helperText('Inserisci un valore positivo per aggiungere, negativo per sottrarre'),
                    ])
                    ->action(function (Product $record, array $data): void {
                        $newQuantity = $record->stock_quantity + $data['adjustment'];
                        $record->update(['stock_quantity' => max(0, $newQuantity)]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('updatePrices')
                        ->label('Aggiorna Prezzi')
                        ->icon('heroicon-m-currency-euro')
                        ->color('warning')
                        ->form([
                            Forms\Components\TextInput::make('price_percentage')
                                ->label('Percentuale di Variazione')
                                ->numeric()
                                ->suffix('%')
                                ->helperText('Inserisci una percentuale positiva per aumentare, negativa per diminuire'),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $percentage = $data['price_percentage'] / 100;
                            
                            $records->each(function (Product $product) use ($percentage) {
                                $newSalePrice = $product->sale_price * (1 + $percentage);
                                $product->update(['sale_price' => round($newSalePrice, 2)]);
                            });
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
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