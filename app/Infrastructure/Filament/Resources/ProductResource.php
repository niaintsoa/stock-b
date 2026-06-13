<?php

namespace App\Infrastructure\Filament\Resources;

use App\Domain\Entity\Product;
use App\Infrastructure\Filament\Resources\ProductResource\Pages;
use App\Infrastructure\Filament\Resources\ProductResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    public static function getModelLabel(): string
    {
        return 'Produit';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Produits';
    }

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Gestion des Produits';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations du produit')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom du produit')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('allow_price_change')
                            ->label('Modifier le prix')
                            ->dehydrated(false)
                            ->reactive()
                            ->hiddenOn('create'),
                        Forms\Components\TextInput::make('price')
                            ->label('Prix')
                            ->required()
                            ->numeric()
                            ->prefix('Ar')
                            ->disabled(fn (callable $get, string $operation): bool => $operation === 'edit' && !$get('allow_price_change')),
                        Forms\Components\TextInput::make('price_change_reason')
                            ->label('Motif du changement de prix')
                            ->required(fn (callable $get): bool => (bool) $get('allow_price_change'))
                            ->visible(fn (callable $get, string $operation): bool => $operation === 'edit' && $get('allow_price_change')),
                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'active' => 'Actif',
                                'inactive' => 'Inactif',
                            ])
                            ->default('active')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Prix')
                    ->money('MGA')
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_stock')
                    ->label('Stock actuel')
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        (int) $state <= 0 => 'danger',
                        (int) $state <= 10 => 'warning',
                        default => 'success',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('Créé par')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'active' => 'Actif',
                        'inactive' => 'Inactif',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('add_stock')
                    ->label('Mouvement stock')
                    ->icon('heroicon-o-arrows-up-down')
                    ->color('warning')
                    ->form([
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
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->rule(function (Product $record, callable $get) {
                                return function (string $attribute, $value, \Closure $fail) use ($record, $get) {
                                    if ($get('type') === 'exit' && $value > $record->current_stock) {
                                        $fail("Stock insuffisant. Stock actuel : {$record->current_stock}");
                                    }
                                };
                            }),
                        Forms\Components\DatePicker::make('expiry_date')
                            ->label('Date d\'expiration (Optionnel)')
                            ->visible(fn (callable $get) => $get('type') === 'entry'),
                        Forms\Components\TextInput::make('reason')
                            ->label('Motif'),
                    ])
                    ->action(function (Product $record, array $data) {
                        if ($data['type'] === 'exit') {
                            $record->depleteStock((int) $data['quantity'], $data['reason'] ?? null, auth()->id());
                        } else {
                            $record->stockMovements()->create([
                                'type' => 'entry',
                                'quantity' => $data['quantity'],
                                'reason' => $data['reason'] ?? null,
                                'expiry_date' => $data['expiry_date'] ?? null,
                                'status' => 'completed',
                                'created_by' => auth()->id(),
                                'updated_by' => auth()->id(),
                            ]);
                        }
                    })
                    ->successNotificationTitle('Mouvement de stock enregistré'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            RelationManagers\StockMovementsRelationManager::class,
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
