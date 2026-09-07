<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StaffRequestResource\Pages;
use App\Models\StaffRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StaffRequestResource extends Resource
{
    protected static ?string $model = StaffRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('institution_id')
                    ->relationship('institution', 'name')
                    ->required(),
                Forms\Components\TextInput::make('created_by')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('hiring_campaign_id')
                    ->relationship('hiringCampaign', 'title'),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('subject')
                    ->maxLength(255),
                Forms\Components\TextInput::make('education_level')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('required_category')
                    ->maxLength(255),
                Forms\Components\TextInput::make('employment_type')
                    ->required()
                    ->maxLength(255)
                    ->default('full_time'),
                Forms\Components\TextInput::make('stake_fraction')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\Toggle::make('is_next_school_year')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('salary_from')
                    ->numeric(),
                Forms\Components\TextInput::make('salary_to')
                    ->numeric(),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255)
                    ->default('draft'),
                Forms\Components\DateTimePicker::make('published_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('institution.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hiringCampaign.title')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject')
                    ->searchable(),
                Tables\Columns\TextColumn::make('education_level')
                    ->searchable(),
                Tables\Columns\TextColumn::make('required_category')
                    ->searchable(),
                Tables\Columns\TextColumn::make('employment_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stake_fraction')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_next_school_year')
                    ->boolean(),
                Tables\Columns\TextColumn::make('salary_from')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('salary_to')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListStaffRequests::route('/'),
            'create' => Pages\CreateStaffRequest::route('/create'),
            'edit' => Pages\EditStaffRequest::route('/{record}/edit'),
        ];
    }
}
