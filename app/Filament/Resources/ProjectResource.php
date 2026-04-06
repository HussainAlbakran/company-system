ublic static function form(Form $form): Form {
    return $form->schema([
        Forms\Components\Section::make('Project Details')
          public static function canViewAny(): bool
{
    // يسمح للمدير والمهندس (Engineer) فقط بدخول قسم المشاريع
    return in_array(auth()->user()->role, ['admin', 'engineer']);
} 
        ->schema([
                Forms\Components\TextInput::make('project_name')
                    ->label('Project Name')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->columnSpanFull(),
            ]),
    ]);
}

public static function table(Table $table): Table {
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('project_name')
                ->label('Project Name')
                ->searchable()
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