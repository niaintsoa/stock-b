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
                Forms\Components\TextInput::make('quantity')
                    ->label('Quantité')
                    ->required()
                    ->numeric()
                    ->minValue(1),
                Forms\Components\Select::make('type')
                    ->label('Type')
                    ->options([
                        'entry' => 'Entrée',
                        'exit' => 'Sortie',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('reason')
                    ->label('Motif')
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantité')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'entry' => 'success',
                        'exit' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'entry' => 'Entrée',
                        'exit' => 'Sortie',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('reason')
                    ->label('Motif'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('Par'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['created_by'] = Auth::id();

                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
