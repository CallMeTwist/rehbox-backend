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

    /**
     * Standard clinical ROM ranges (degrees) — used as helper reference in the form.
     * Mirrors ROM_STANDARDS in motion.ts.
     */
    private const ROM_REFERENCE = [
        'neck_flexion' => ['label' => 'Neck Flexion',          'max' => 45,  'landmarks' => '7, 11, 23  (ear–shoulder–hip)'],
        'neck_extension' => ['label' => 'Neck Extension',        'max' => 45,  'landmarks' => '7, 11, 23  (ear–shoulder–hip)'],
        'neck_lateral' => ['label' => 'Neck Lateral Tilt',     'max' => 45,  'landmarks' => '7, 11, 23  (ear–shoulder–hip)'],
        'neck_rotation' => ['label' => 'Neck Rotation',         'max' => 60,  'landmarks' => '7, 11, 23  (ear–shoulder–hip)'],
        'shoulder_flexion' => ['label' => 'Shoulder Flexion',      'max' => 180, 'landmarks' => '23, 11, 13 (hip–shoulder–elbow)'],
        'shoulder_extension' => ['label' => 'Shoulder Extension',    'max' => 60,  'landmarks' => '23, 11, 13 (hip–shoulder–elbow)'],
        'shoulder_abduction' => ['label' => 'Shoulder Abduction',    'max' => 180, 'landmarks' => '23, 11, 13 (hip–shoulder–elbow)'],
        'shoulder_adduction' => ['label' => 'Shoulder Adduction',    'max' => 30,  'landmarks' => '23, 11, 13 (hip–shoulder–elbow)'],
        'shoulder_ir' => ['label' => 'Shoulder Int. Rotation', 'max' => 70,  'landmarks' => '23, 11, 13 (hip–shoulder–elbow)'],
        'shoulder_er' => ['label' => 'Shoulder Ext. Rotation', 'max' => 90,  'landmarks' => '23, 11, 13 (hip–shoulder–elbow)'],
        'elbow_flexion' => ['label' => 'Elbow Flexion',         'max' => 160, 'landmarks' => '11, 13, 15 (shoulder–elbow–wrist)'],
        'elbow_extension' => ['label' => 'Elbow Extension',       'max' => 10,  'landmarks' => '11, 13, 15 (shoulder–elbow–wrist)'],
        'hip_flexion' => ['label' => 'Hip Flexion',           'max' => 125, 'landmarks' => '11, 23, 25 (shoulder–hip–knee)'],
        'hip_extension' => ['label' => 'Hip Extension',         'max' => 15,  'landmarks' => '11, 23, 25 (shoulder–hip–knee)'],
        'hip_abduction' => ['label' => 'Hip Abduction',         'max' => 45,  'landmarks' => '11, 23, 25 (shoulder–hip–knee)'],
        'hip_adduction' => ['label' => 'Hip Adduction',         'max' => 30,  'landmarks' => '11, 23, 25 (shoulder–hip–knee)'],
        'knee_flexion' => ['label' => 'Knee Flexion',          'max' => 140, 'landmarks' => '23, 25, 27 (hip–knee–ankle)'],
        'knee_extension' => ['label' => 'Knee Extension',        'max' => 140, 'landmarks' => '23, 25, 27 (hip–knee–ankle)'],
    ];

    /**
     * Mirrors the folder tree: which exercise types are valid for each body region.
     *
     * @var array<string, array<int, string>>
     */
    private const REGION_TYPES = [
        'back' => ['rom', 'strengthening', 'stretching', 'functional'],
        'chest' => ['chest_wall_mobilization', 'lung_expansion', 'airways_clearance', 'strengthening'],
        'elbow_forearm_wrist' => ['rom', 'strengthening', 'stretching', 'functional'],
        'general' => ['chest_abs', 'cool_down', 'core_stability', 'legs', 'strengthening_arm', 'stretching'],
        'head_neck' => ['rom', 'strengthening', 'stretching', 'functional'],
        'lower_limbs' => ['rom', 'strengthening', 'stretching', 'functional'],
        'pelvic' => ['strengthening', 'stretching', 'functional'],
        'upper_limbs' => ['rom', 'strengthening', 'stretching', 'endurance', 'functional'],
    ];

    private const AREA_LABELS = [
        'back' => 'Back', 'chest' => 'Chest', 'elbow_forearm_wrist' => 'Elbow, Forearm & Wrist',
        'general' => 'General Exercises (Free tier)', 'head_neck' => 'Head & Neck',
        'lower_limbs' => 'Lower Limbs', 'pelvic' => 'Pelvic', 'upper_limbs' => 'Upper Limbs',
    ];

    private const CATEGORY_LABELS = [
        'strengthening' => 'Strengthening', 'stretching' => 'Stretching', 'rom' => 'ROM (Range of Motion)',
        'functional' => 'Functional', 'endurance' => 'Endurance', 'lung_expansion' => 'Lung Expansion',
        'chest_wall_mobilization' => 'Chest Wall Mobilization', 'airways_clearance' => 'Airways Clearance',
        'chest_abs' => 'Chest & Abs', 'cool_down' => 'Cool Down', 'core_stability' => 'Core Stability',
        'legs' => 'Legs', 'strengthening_arm' => 'Strengthening (Arm)',
    ];

    public static function form(Form $form): Form
    {
        $romOptions = collect(self::ROM_REFERENCE)
            ->mapWithKeys(fn ($v, $k) => [$k => $v['label']." (0–{$v['max']}°)"])
            ->all();

        return $form->schema([
            Forms\Components\TextInput::make('title')->required(),

            Forms\Components\Section::make('Folder mapping')
                ->description('These map to the upload folder structure.')
                ->schema([
                    Forms\Components\Select::make('area')
                        ->label('Body Region')
                        ->options(self::AREA_LABELS)
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, Forms\Set $set) {
                            $set('category', null);
                            $set('access_tier', $state === 'general' ? 'free' : 'paid');
                            $set('video_source', 'upload');
                        }),

                    Forms\Components\Select::make('category')
                        ->label('Exercise Type')
                        ->options(function (Forms\Get $get) {
                            $area = $get('area');
                            if (! $area || ! isset(self::REGION_TYPES[$area])) {
                                return self::CATEGORY_LABELS;
                            }

                            return collect(self::REGION_TYPES[$area])
                                ->mapWithKeys(fn ($cat) => [$cat => self::CATEGORY_LABELS[$cat] ?? $cat])
                                ->all();
                        })
                        ->required()
                        ->live(),

                    Forms\Components\Select::make('access_tier')
                        ->options(['free' => 'Free', 'paid' => 'Paid'])
                        ->required()
                        ->live()
                        ->disabled(fn (Forms\Get $get) => $get('area') === 'general')
                        ->dehydrated()
                        ->afterStateUpdated(function ($state, Forms\Set $set) {
                            $set('video_source', 'upload');
                        }),
                ])->columns(3),

            Forms\Components\Section::make('Video source')
                ->schema([
                    Forms\Components\Select::make('video_source')
                        ->options(['upload' => 'Upload (.mp4)', 'youtube' => 'YouTube URL'])
                        ->required()
                        ->live()
                        ->disabled(fn (Forms\Get $get) => in_array($get('access_tier'), ['free', 'paid'], true))
                        ->dehydrated(),

                    Forms\Components\FileUpload::make('video_path')
                        ->label('Video file')
                        ->disk(config('rehbox.exercise_video_disk'))
                        ->directory(fn (Forms\Get $get) => sprintf(
                            'exercises/videos/%s/%s',
                            $get('area') ?: 'misc',
                            $get('category') ?: 'misc',
                        ))
                        ->acceptedFileTypes(['video/mp4', 'video/quicktime'])
                        ->maxSize(30 * 1024)
                        ->visible(fn (Forms\Get $get) => $get('video_source') === 'upload')
                        ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                            if ($state && ! $get('title')) {
                                $base = pathinfo(is_string($state) ? $state : $state->getClientOriginalName(), PATHINFO_FILENAME);
                                $set('title', \Illuminate\Support\Str::headline($base));
                            }
                        })
                        ->helperText('Max 30 MB. Larger files: re-encode to H.264 720p ~2 Mbps.'),

                    Forms\Components\TextInput::make('youtube_url')
                        ->label('YouTube URL')
                        ->placeholder('https://www.youtube.com/watch?v=...')
                        ->url()
                        ->rule('regex:/^https?:\/\/(www\.)?(youtube\.com|youtu\.be)\//i')
                        ->visible(fn (Forms\Get $get) => $get('video_source') === 'youtube'),
                ]),

            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('default_sets')->numeric()->default(3),
                Forms\Components\TextInput::make('default_reps')->numeric()->default(10),
            ]),

            Forms\Components\Section::make('Thumbnail override (optional)')
                ->description('Leave blank to auto-generate (FFmpeg first-frame for uploads, YouTube thumbnail for free).')
                ->schema([
                    Forms\Components\FileUpload::make('illustration_url')
                        ->image()
                        ->disk(config('rehbox.exercise_video_disk'))
                        ->directory('exercises/illustrations'),
                ])->collapsed(),

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

            // ── Goniometric Form Analysis ────────────────────────────────
            Forms\Components\Section::make('Goniometric Form Analysis (MediaPipe)')
                ->description(
                    'Define joint angle rules for real-time form scoring and ROM tracking. '.
                    'Leave empty to use visibility-based scoring only.',
                )
                ->collapsed()
                ->schema([
                    Forms\Components\Placeholder::make('landmark_reference')
                        ->label('MediaPipe Landmark Indices')
                        ->content(
                            'NECK: 0=Nose · 7=L.Ear · 8=R.Ear | '.
                            'SHOULDER: 11=L.Shoulder · 12=R.Shoulder | '.
                            'ELBOW: 13=L.Elbow · 14=R.Elbow | '.
                            'WRIST: 15=L.Wrist · 16=R.Wrist | '.
                            'HIP: 23=L.Hip · 24=R.Hip | '.
                            'KNEE: 25=L.Knee · 26=R.Knee | '.
                            'ANKLE: 27=L.Ankle · 28=R.Ankle',
                        ),

                    Forms\Components\Repeater::make('correct_angles')
                        ->label('Joint Rules')
                        ->schema([

                            // ── Movement preset selector ─────────────────
                            Forms\Components\Select::make('movement')
                                ->label('Movement Type (Clinical Standard)')
                                ->options($romOptions)
                                ->placeholder('Select to auto-fill standard ROM...')
                                ->helperText(
                                    'Selecting a preset fills in the standard clinical ROM range and '.
                                    'landmark reference. You can override values below.',
                                )
                                ->live()
                                ->columnSpanFull(),

                            // ── Display the standard ROM when a movement is selected ──
                            Forms\Components\Placeholder::make('rom_reference')
                                ->label('Standard Clinical ROM')
                                ->content(function (Forms\Get $get) {
                                    $movement = $get('movement');
                                    if (! $movement || ! isset(self::ROM_REFERENCE[$movement])) {
                                        return '— select a movement type above —';
                                    }
                                    $ref = self::ROM_REFERENCE[$movement];

                                    return "Normal range: 0–{$ref['max']}° | Suggested landmarks: {$ref['landmarks']}";
                                })
                                ->columnSpanFull(),

                            // ── Joint identity ───────────────────────────
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('joint')
                                    ->label('Joint Rule ID')
                                    ->placeholder('e.g. left_knee_flexion')
                                    ->helperText('Unique slug for this rule within the exercise.')
                                    ->required(),

                                Forms\Components\Select::make('side')
                                    ->label('Body Side')
                                    ->options([
                                        'bilateral' => 'Bilateral (both sides)',
                                        'left' => 'Left only (e.g. post-left-knee surgery)',
                                        'right' => 'Right only (e.g. post-right-shoulder surgery)',
                                    ])
                                    ->default('bilateral')
                                    ->helperText('Bilateral averages left + right. Single-side ignores the other limb entirely.')
                                    ->required(),
                            ]),

                            // ── Landmark triplet ─────────────────────────
                            Forms\Components\Grid::make(3)->schema([
                                Forms\Components\TextInput::make('landmarks.0')
                                    ->label('Proximal Landmark')
                                    ->numeric()
                                    ->placeholder('23')
                                    ->helperText('e.g. Hip for knee')
                                    ->required(),
                                Forms\Components\TextInput::make('landmarks.1')
                                    ->label('Vertex (Joint Centre)')
                                    ->numeric()
                                    ->placeholder('25')
                                    ->helperText('The joint being measured')
                                    ->required(),
                                Forms\Components\TextInput::make('landmarks.2')
                                    ->label('Distal Landmark')
                                    ->numeric()
                                    ->placeholder('27')
                                    ->helperText('e.g. Ankle for knee')
                                    ->required(),
                            ]),

                            // ── Form scoring bounds ──────────────────────
                            Forms\Components\Grid::make(3)->schema([
                                Forms\Components\TextInput::make('min')
                                    ->label('Min Acceptable Angle (°)')
                                    ->numeric()
                                    ->placeholder('0')
                                    ->helperText('Below this = form too restricted')
                                    ->required(),
                                Forms\Components\TextInput::make('max')
                                    ->label('Max Acceptable Angle (°)')
                                    ->numeric()
                                    ->placeholder('140')
                                    ->helperText('Use the clinical normal max from the preset above')
                                    ->required(),
                                Forms\Components\TextInput::make('weight')
                                    ->label('Scoring Weight (0–1)')
                                    ->numeric()
                                    ->placeholder('1.0')
                                    ->default(1.0),
                            ]),

                            // ── Feedback messages ────────────────────────
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('feedback_low')
                                    ->label('Feedback: Angle Too High (> max)')
                                    ->placeholder('Straighten your knee more')
                                    ->required(),
                                Forms\Components\TextInput::make('feedback_high')
                                    ->label('Feedback: Angle Too Low (< min)')
                                    ->placeholder('Bend your knee further')
                                    ->required(),
                            ]),

                            // ── Rep counting ─────────────────────────────
                            Forms\Components\Fieldset::make('Rep Counting & ROM Tracking')
                                ->schema([
                                    Forms\Components\Toggle::make('rep_joint')
                                        ->label('Primary rep-counting joint')
                                        ->helperText(
                                            'Enable on exactly ONE joint per exercise. This joint drives the rep counter '.
                                            'and the live ROM gauge. Wrong-exercise detection is based on this joint.',
                                        )
                                        ->default(false),
                                    Forms\Components\Grid::make(2)->schema([
                                        Forms\Components\TextInput::make('up_threshold')
                                            ->label('Up Threshold (°)')
                                            ->numeric()
                                            ->placeholder('110')
                                            ->helperText('Angle marking the top of movement (e.g. 110° = knee well-bent in a squat).'),
                                        Forms\Components\TextInput::make('down_threshold')
                                            ->label('Down Threshold (°)')
                                            ->numeric()
                                            ->placeholder('30')
                                            ->helperText('Angle marking the return position (e.g. 30° = knee almost straight).'),
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
            Tables\Columns\BadgeColumn::make('area')->label('Area'),
            Tables\Columns\BadgeColumn::make('category')->label('Type'),
            Tables\Columns\BadgeColumn::make('difficulty'),
            Tables\Columns\BadgeColumn::make('access_tier')
                ->colors(['success' => 'free', 'primary' => 'paid']),
            Tables\Columns\IconColumn::make('video_source')
                ->icons([
                    'heroicon-o-cloud-arrow-up' => 'upload',
                    'heroicon-o-play-circle' => 'youtube',
                ]),
            Tables\Columns\IconColumn::make('is_active')->boolean(),
            Tables\Columns\IconColumn::make('correct_angles')
                ->label('ROM Tracking')
                ->boolean()
                ->getStateUsing(fn (Exercise $record): bool => ! empty($record->correct_angles))
                ->trueIcon('heroicon-o-signal')
                ->falseIcon('heroicon-o-no-symbol')
                ->trueColor('success')
                ->falseColor('gray'),
        ])
            ->filters([
                SelectFilter::make('area')->label('Body Region')->options(self::AREA_LABELS),
                SelectFilter::make('category')->label('Exercise Type')->options(self::CATEGORY_LABELS),
                SelectFilter::make('access_tier')->options(['free' => 'Free', 'paid' => 'Paid']),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
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
