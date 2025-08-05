<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockMovementResource\Pages;
use App\Filament\Resources\StockMovementResource\RelationManagers;
use App\Models\StockMovement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Conditional;
use Filament\Tables\Columns\TextColumn;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    Select::make('product_id')
                        ->label('Prodotto')
                        ->relationship('product', 'name') // Assicurati che esista la relazione
                        ->searchable()
                        ->required(),

                    Select::make('type')
                        ->label('Tipo')
                        ->options([
                            'in' => 'Entrata',
                            'out' => 'Uscita',
                        ])
                        ->required(),

                    TextInput::make('quantity')
                        ->label('Quantità')
                        ->numeric()
                        ->required(),

                    TextInput::make('cost_price')
                        ->label('Prezzo di acquisto')
                        ->numeric()
                        ->visible(fn(callable $get) => $get('type') === 'in'),

                    Textarea::make('note')
                        ->label('Note')
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Prodotto')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->formatStateUsing(fn($state) => $state === 'in' ? 'Entrata' : 'Uscita')
                    ->sortable(),
                TextColumn::make('quantity')
                    ->label('Quantità')
                    ->sortable(),
                TextColumn::make('cost_price')
                    ->label('Prezzo di acquisto')
                    ->money('EUR', true),
                TextColumn::make('note')
                    ->label('Note')
                    ->limit(30),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockMovements::route('/'),
            'create' => Pages\CreateStockMovement::route('/create'),
            'edit' => Pages\EditStockMovement::route('/{record}/edit'),
        ];
    }
}
