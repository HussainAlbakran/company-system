<?php

namespace App\Filament\Resources;

use App\Models\Employee;
use App\Models\Factory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use App\Filament\Resources\EmployeeResource\Pages;
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
                    Forms\Components\TextInput::make('employee_number')
                        ->label('Employee Number'),
                    Forms\Components\TextInput::make('job_title')
                        ->label('Job Title'),
                    Forms\Components\TextInput::make('phone')
                        ->label('Phone'),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email(),
                    Forms\Components\Textarea::make('address')
                        ->label('Address')
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('salary')
                        ->label('Salary')
                        ->numeric()
                        ->prefix('SAR'),
                    Forms\Components\DatePicker::make('hire_date')
                        ->label('Hiring Date'),
                    Forms\Components\DatePicker::make('residency_expiry_date')
                        ->label('Residency Expiry Date'),
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'active' => 'Active',
                            'inactive' => 'Inactive',
                        ])
                        ->default('active')
                        ->required(),
                    Forms\Components\Select::make('department_id')
                        ->label('Department')
                        ->relationship('department', 'name')
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('factory_id')
                        ->label('Factory')
                        ->options(fn () => Factory::query()->pluck('name', 'id')->all())
                        ->searchable()
                        ->preload(),
                ])->columns(2),
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
                Tables\Columns\TextColumn::make('employee_number')
                    ->label('Employee Number'),
                Tables\Columns\TextColumn::make('department.name')
                    ->label('Department'),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('salary')
                    ->label('Salary')
                    ->money('SAR'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}