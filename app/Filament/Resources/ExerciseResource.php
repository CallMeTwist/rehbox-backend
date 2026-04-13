<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExerciseResource\Pages;
use App\Models\Exercise;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ExerciseResource extends Resource
{
    protected static ?string $model = Exercise::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // app/Filament/Resources/ExerciseResource.php
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required(),

            Forms\Components\Select::make('area')
                ->label('Body Area')
                ->options([
                    'neck' => 'Neck',
                    'shoulder' => 'Shoulder',
                    'elbow_forearm_wrist' => 'Elbow, Forearm & Wrist',
                    'back' => 'Back',
                    'lower_limb' => 'Lower Limb',
                ])->required(),

            Forms\Components\Select::make('category')
                ->label('Exercise Type')
                ->options([
                    'strengthening' => 'Strengthening',
                    'stretching' => 'Stretching',
                    'rom' => 'Range of Motion (ROM)',
                    'functional' => 'Functional',
                    'endurance' => 'Endurance',
                ])->required(),

            Forms\Components\Select::make('difficulty')
                ->options([
                    'beginner' => 'Beginner',
                    'intermediate' => 'Intermediate',
                    'advanced' => 'Advanced',
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

            Forms\Components\Section::make('Form Analysis (MediaPipe)')
                ->description('Define joint angle rules for real-time form scoring. Leave empty to use visibility-based scoring.')
                ->collapsed()
                ->schema([
                    Forms\Components\Placeholder::make('landmark_reference')
                        ->label('Common Landmark Indices')
                        ->content('11=L.Shoulder · 12=R.Shoulder · 13=L.Elbow · 14=R.Elbow · 15=L.Wrist · 16=R.Wrist · 23=L.Hip · 24=R.Hip · 25=L.Knee · 26=R.Knee · 27=L.Ankle · 28=R.Ankle'),

                    Forms\Components\Repeater::make('correct_angles')
                        ->label('Joint Rules')
                        ->schema([
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('joint')
                                    ->label('Joint Name')
                                    ->placeholder('e.g. left_knee')
                                    ->helperText('Unique identifier for this joint rule.')
                                    ->required(),
                                Forms\Components\Grid::make(3)->schema([
                                    Forms\Components\TextInput::make('landmarks.0')
                                        ->label('Proximal Index')
                                        ->numeric()
                                        ->placeholder('23')
                                        ->required(),
                                    Forms\Components\TextInput::make('landmarks.1')
                                        ->label('Vertex Index')
                                        ->numeric()
                                        ->placeholder('25')
                                        ->required(),
                                    Forms\Components\TextInput::make('landmarks.2')
                                        ->label('Distal Index')
                                        ->numeric()
                                        ->placeholder('27')
                                        ->required(),
                                ]),
                            ]),
                            Forms\Components\Grid::make(3)->schema([
                                Forms\Components\TextInput::make('min')
                                    ->label('Min Angle (°)')
                                    ->numeric()
                                    ->placeholder('70')
                                    ->required(),
                                Forms\Components\TextInput::make('max')
                                    ->label('Max Angle (°)')
                                    ->numeric()
                                    ->placeholder('110')
                                    ->required(),
                                Forms\Components\TextInput::make('weight')
                                    ->label('Weight (0–1)')
                                    ->numeric()
                                    ->placeholder('1.0')
                                    ->default(1.0),
                            ]),
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('feedback_low')
                                    ->label('Feedback: Angle Too High')
                                    ->placeholder('Straighten your left knee')
                                    ->helperText('Shown when angle exceeds max.')
                                    ->required(),
                                Forms\Components\TextInput::make('feedback_high')
                                    ->label('Feedback: Angle Too Low')
                                    ->placeholder('Bend your left knee more')
                                    ->helperText('Shown when angle falls below min.')
                                    ->required(),
                            ]),

                            Forms\Components\Fieldset::make('Rep Counting (optional)')
                                ->schema([
                                    Forms\Components\Toggle::make('rep_joint')
                                        ->label('Primary rep-counting joint')
                                        ->helperText('Enable on exactly one joint per exercise to drive the rep counter.')
                                        ->default(false),
                                    Forms\Components\Grid::make(2)->schema([
                                        Forms\Components\TextInput::make('up_threshold')
                                            ->label('Up threshold (°)')
                                            ->numeric()
                                            ->placeholder('150')
                                            ->helperText('Angle that marks the top of movement — e.g. 150 for shoulder raised overhead.'),
                                        Forms\Components\TextInput::make('down_threshold')
                                            ->label('Down threshold (°)')
                                            ->numeric()
                                            ->placeholder('30')
                                            ->helperText('Angle that marks the bottom of movement — e.g. 30 for arm at side.'),
                                    ]),
                                ]),
                        ])
                        ->addActionLabel('Add Joint Rule')
                        ->collapsible()
                        ->defaultItems(0)
                        ->reorderable(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('title')->searchable(),
            Tables\Columns\BadgeColumn::make('area')->label('Area')->colors([
                'primary' => 'neck',
                'success' => 'shoulder',
                'warning' => 'elbow_forearm_wrist',
                'danger' => 'back',
                'secondary' => 'lower_limb',
            ]),
            Tables\Columns\BadgeColumn::make('category')->label('Type')->colors([
                'primary' => 'strengthening',
                'success' => 'stretching',
                'warning' => 'rom',
                'danger' => 'functional',
                'secondary' => 'endurance',
            ]),
            Tables\Columns\BadgeColumn::make('difficulty'),
            Tables\Columns\IconColumn::make('is_active')->boolean(),
        ])
            ->filters([
                SelectFilter::make('area')->label('Body Area')->options([
                    'neck' => 'Neck',
                    'shoulder' => 'Shoulder',
                    'elbow_forearm_wrist' => 'Elbow, Forearm & Wrist',
                    'back' => 'Back',
                    'lower_limb' => 'Lower Limb',
                ]),
                SelectFilter::make('category')->label('Exercise Type')->options([
                    'strengthening' => 'Strengthening',
                    'stretching' => 'Stretching',
                    'rom' => 'Range of Motion (ROM)',
                    'functional' => 'Functional',
                    'endurance' => 'Endurance',
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
