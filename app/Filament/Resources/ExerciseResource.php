<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExerciseResource\Pages;
use App\Filament\Resources\ExerciseResource\RelationManagers;
use App\Models\Exercise;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExerciseResource extends Resource
{
    protected static ?string $model = Exercise::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // app/Filament/Resources/ExerciseResource.php
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required(),

            Forms\Components\Select::make('category')
                ->options([
                    'head_neck'  => 'Head & Neck',
                    'upper_limb' => 'Upper Limb',
                    'back'       => 'Back',
                    'lower_limb' => 'Lower Limb',
                ])->required(),

            Forms\Components\Select::make('difficulty')
                ->options([
                    'beginner'     => 'Beginner',
                    'intermediate' => 'Intermediate',
                    'advanced'     => 'Advanced',
                ])->default('beginner'),

            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('default_sets')->numeric()->default(3),
                Forms\Components\TextInput::make('default_reps')->numeric()->default(10),
            ]),

            Forms\Components\FileUpload::make('illustration_url')
                ->image()->directory('exercises/illustrations'),

            Forms\Components\TextInput::make('video_url')
                ->url()->placeholder('https://...'),

            Forms\Components\Tabs::make('Instructions')->tabs([
                Forms\Components\Tabs\Tab::make('English')->schema([
                    Forms\Components\Textarea::make('instructions_en')->rows(3),
                ]),
                Forms\Components\Tabs\Tab::make('Pidgin')->schema([
                    Forms\Components\Textarea::make('instructions_pcm')->rows(3),
                ]),
                Forms\Components\Tabs\Tab::make('Yoruba')->schema([
                    Forms\Components\Textarea::make('instructions_yo')->rows(3),
                ]),
                Forms\Components\Tabs\Tab::make('Igbo')->schema([
                    Forms\Components\Textarea::make('instructions_ig')->rows(3),
                ]),
                Forms\Components\Tabs\Tab::make('Hausa')->schema([
                    Forms\Components\Textarea::make('instructions_ha')->rows(3),
                ]),
            ]),

            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('title')->searchable(),
            Tables\Columns\BadgeColumn::make('category')->colors([
                'primary' => 'head_neck',
                'success' => 'upper_limb',
                'warning' => 'back',
                'danger'  => 'lower_limb',
            ]),
            Tables\Columns\BadgeColumn::make('difficulty'),
            Tables\Columns\IconColumn::make('is_active')->boolean(),
        ])
            ->filters([
                SelectFilter::make('category')->options([
                    'head_neck'  => 'Head & Neck',
                    'upper_limb' => 'Upper Limb',
                    'back'       => 'Back',
                    'lower_limb' => 'Lower Limb',
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
            'index' => Pages\ListExercises::route('/'),
            'create' => Pages\CreateExercise::route('/create'),
            'edit' => Pages\EditExercise::route('/{record}/edit'),
        ];
    }
}
