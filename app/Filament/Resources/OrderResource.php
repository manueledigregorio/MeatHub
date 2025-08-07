<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\OrderResource\RelationManagers\ItemsRelationManager;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Ordini';

    protected static ?string $label = 'Ordine';

    protected static ?string $pluralLabel = 'Ordini';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informazioni Ordine')
                    ->schema([
                        Forms\Components\TextInput::make('total')
                            ->label('Totale')
                            ->numeric()
                            ->prefix('€')
                            ->disabled()
                            ->dehydrated(false), // Il totale viene calcolato automaticamente

                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Data Ordine')
                            ->default(now())
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
                    ->label('Totale')
                    ->money('EUR', true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_profit')
                    ->label('Profitto Totale')
                    ->money('EUR', true)
                    ->color(fn ($state): string => $state > 0 ? 'success' : 'danger'),

                Tables\Columns\TextColumn::make('average_margin')
                    ->label('Margine Medio')
                    ->numeric(decimalPlaces: 1)
                    ->suffix('%')
                    ->color(fn ($state): string => $state > 30 ? 'success' : ($state > 15 ? 'warning' : 'danger')),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('Prodotti')
                    ->counts('items')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
