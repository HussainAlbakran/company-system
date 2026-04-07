<?php

namespace App\Filament\Resources;

use App\Models\Factory;
use App\Filament\Resources\FactoryResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FactoryResource extends Resource
{
    protected static ?string $model = Factory::class;
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationLabel = 'Production Tracking';

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()->role, ['admin', 'factory_manager']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Production & Supply Details')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Factory Name')
                        ->required(),
                    Forms\Components\TextInput::make('location')
                        ->label('Location'),
                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('quantity')
                        ->label('Total Quantity')
                        ->numeric()
                        ->required(),
                    Forms\Components\TextInput::make('supplied_quantity')
                        ->label('Supplied Quantity')
                        ->numeric()
                        ->default(0),
                    Forms\Components\DatePicker::make('start_date')
                        ->label('Start Date'),
                    Forms\Components\DatePicker::make('end_date')
                        ->label('Production End'),
                    Forms\Components\DatePicker::make('delivery_date')
                        ->label('Delivery Date'),
                    Forms\Components\TextInput::make('receiver_name')
                        ->label('Received By'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Factory')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Total Qty'),
                Tables\Columns\TextColumn::make('supplied_quantity')
                    ->label('Supplied'),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->label('Start'),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->label('End'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFactories::route('/'),
            'create' => Pages\CreateFactory::route('/create'),
            'edit' => Pages\EditFactory::route('/{record}/edit'),
        ];
    }
}