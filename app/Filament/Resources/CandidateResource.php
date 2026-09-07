<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CandidateResource\Pages;
use App\Models\Candidate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CandidateResource extends Resource
{
    protected static ?string $model = Candidate::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\TextInput::make('subject')
                    ->maxLength(255),
                Forms\Components\TextInput::make('education_levels'),
                Forms\Components\TextInput::make('diploma_document_path')
                    ->maxLength(255),
                Forms\Components\TextInput::make('teaching_category')
                    ->maxLength(255),
                Forms\Components\TextInput::make('category_document_path')
                    ->maxLength(255),
                Forms\Components\Toggle::make('relocation_ready')
                    ->required(),
                Forms\Components\Textarea::make('relocation_conditions')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('employment_type')
                    ->required()
                    ->maxLength(255)
                    ->default('either'),
                Forms\Components\TextInput::make('desired_stake_fraction')
                    ->numeric(),
                Forms\Components\Textarea::make('bio')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject')
                    ->searchable(),
                Tables\Columns\TextColumn::make('diploma_document_path')
                    ->searchable(),
                Tables\Columns\TextColumn::make('teaching_category')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category_document_path')
                    ->searchable(),
                Tables\Columns\IconColumn::make('relocation_ready')
                    ->boolean(),
                Tables\Columns\TextColumn::make('employment_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('desired_stake_fraction')
                    ->numeric()
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
            'index' => Pages\ListCandidates::route('/'),
            'create' => Pages\CreateCandidate::route('/create'),
            'edit' => Pages\EditCandidate::route('/{record}/edit'),
        ];
    }
}
