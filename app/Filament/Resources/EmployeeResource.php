<?php

namespace App\Filament\Resources;

use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'HR Management';

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()->role, ['admin', 'hr']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Employee Information')
                ->description('Main personnel data')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Full Name')
                        ->required(),
                    Forms\Components\TextInput::make('id_number')
                        ->label('ID / IQAMA Number')
                        ->required(),
                    Forms\Components\TextInput::make('salary')
                        ->label('Salary')
                        ->numeric()
                        ->prefix('SAR'),
                    Forms\Components\DatePicker::make('hire_date')
                        ->label('Hiring Date'),
                    Forms\Components\Select::make('department')
                        ->label('Department')
                        ->options([
                            'Administration' => 'Administration',
                            'Production' => 'Production',
                            'Maintenance' => 'Maintenance',
                            'Transport' => 'Transport',
                        ]),
                ])->columns(2),

            Forms\Components\Section::make('Official Documents')
                ->description('Upload PDF files here')
                ->schema([
                    Forms\Components\FileUpload::make('attachments')
                        ->label('PDF Documents (Contract, ID, Passport)')
                        ->multiple()
                        ->acceptedFileTypes(['application/pdf'])
                        ->directory('employee-docs')
                        ->openable()
                        ->downloadable()
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('id_number')
                    ->label('ID Number'),
                Tables\Columns\TextColumn::make('department')
                    ->label('Department'),
                Tables\Columns\TextColumn::make('salary')
                    ->label('Salary')
                    ->money('SAR'),
            ]);
    }
}