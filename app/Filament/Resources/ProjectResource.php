<?php

namespace App\Filament\Resources;

use App\Models\Project;
use App\Filament\Resources\ProjectResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Projects';

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()->role, ['admin', 'engineer', 'manager'], true);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Project Details')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Project Name')
                        ->required(),
                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->columnSpanFull(),
                    Forms\Components\Select::make('department_id')
                        ->label('Department')
                        ->relationship('department', 'name')
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('responsible_employee_id')
                        ->label('Responsible Employee')
                        ->relationship('responsibleEmployee', 'name')
                        ->searchable()
                        ->preload(),
                    Forms\Components\DatePicker::make('start_date')
                        ->label('Start Date'),
                    Forms\Components\DatePicker::make('end_date')
                        ->label('End Date'),
                    Forms\Components\Select::make('status')
                        ->options([
                            'ongoing' => 'Ongoing',
                            'completed' => 'Completed',
                            'paused' => 'Paused',
                        ])
                        ->default('ongoing'),
                    Forms\Components\TextInput::make('progress_percentage')
                        ->label('Progress %')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->default(0),
                    Forms\Components\TextInput::make('project_value')
                        ->label('Project Value')
                        ->numeric()
                        ->default(0),
                    Forms\Components\TextInput::make('expenses')
                        ->label('Expenses')
                        ->numeric()
                        ->default(0),
                    Forms\Components\FileUpload::make('project_pdf')
                        ->label('Project PDF')
                        ->acceptedFileTypes(['application/pdf'])
                        ->directory('project_pdfs')
                        ->disk('public'),
                    Forms\Components\Textarea::make('notes')
                        ->label('Notes')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Project Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('progress_percentage')
                    ->label('Progress')
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Added Date')
                    ->date()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}