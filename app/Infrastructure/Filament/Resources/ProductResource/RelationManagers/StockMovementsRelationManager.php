<?php

namespace App\Infrastructure\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class StockMovementsRelationManager extends RelationManager
{
    protected static string $relationship = 'stockMovements';

    protected static ?string $recordTitleAttribute = 'id';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return 'Mouvements de stock';
    }

    public static function getModelLabel(): string
    {
        return 'Mouvement';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Mouvements';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label('Type de mouvement')
                    ->options([
                        'entry' => 'Entrée',
                        'exit' => 'Sortie',
                    ])
                    ->reactive()
                    ->required(),
                Forms\Components\TextInput::make('quantity')
                    ->label('Quantité')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->rule(function (callable $get) {
                        return function (string $attribute, $value, \Closure $fail) use ($get) {
                            $product = $this->getOwnerRecord();
                            if ($get('type') === 'exit' && $value > $product->current_stock) {
                                $fail("Stock insuffisant. Stock actuel : {$product->current_stock}");
                            }
                        };
                    }),
                Forms\Components\DatePicker::make('expiry_date')
                    ->label('Date d\'expiration')
                    ->visible(fn (callable $get) => $get('type') === 'entry'),
                Forms\Components\TextInput::make('reason')
                    ->label('Motif')
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'entry' => 'success',
                        'exit' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantité'),
                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('Expiration')
                    ->date('d/m/Y')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('reason')
                    ->label('Motif'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->using(function (array $data, string $model): Model {
                        $product = $this->getOwnerRecord();
                        if ($data['type'] === 'exit') {
                            $product->depleteStock((int) $data['quantity'], $data['reason'] ?? null, auth()->id());
                            return $product->stockMovements()->latest()->first() ?? new \App\Domain\Entity\StockMovement();
                        }
                        return $product->stockMovements()->create([
                            'type' => 'entry',
                            'quantity' => $data['quantity'],
                            'reason' => $data['reason'] ?? null,
                            'expiry_date' => $data['expiry_date'] ?? null,
                            'status' => 'completed',
                            'created_by' => auth()->id(),
                            'updated_by' => auth()->id(),
                        ]);
                    }),
            ])
            ->actions([
                // Pas d'édition de mouvement de stock
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
