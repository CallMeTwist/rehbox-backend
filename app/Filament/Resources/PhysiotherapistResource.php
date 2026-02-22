<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PhysiotherapistResource\Pages;
use App\Filament\Resources\PhysiotherapistResource\RelationManagers;
use App\Models\Physiotherapist;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class PhysiotherapistResource extends Resource
{
    protected static ?string $model = Physiotherapist::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Physiotherapists';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('license_number')
                    ->label('License No.')
                    ->searchable(),

                Tables\Columns\TextColumn::make('hospital_or_clinic')
                    ->label('Hospital / Clinic')
                    ->limit(30),

                Tables\Columns\BadgeColumn::make('vetting_status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Applied')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('vetting_status')
                    ->options([
                        'pending'  => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                // View credential document
                Action::make('view_document')
                    ->label('View Credentials')
                    ->icon('heroicon-o-document-text')
                    ->url(fn (Physiotherapist $record) =>
                    route('credentials.view', $record->id)
                    )
                    ->openUrlInNewTab()
                    ->visible(fn (Physiotherapist $record) =>
                        $record->credential_document_path !== null
                    ),

                // Approve action
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Physiotherapist')
                    ->modalDescription('This will grant full access and send an approval email.')
                    ->visible(fn (Physiotherapist $record) =>
                        $record->vetting_status === 'pending'
                    )
                    ->action(function (Physiotherapist $record) {
                        $code = $record->generateActivationCode();

                        $record->update([
                            'vetting_status' => 'approved',
                            'vetted_at'      => now(),
                        ]);

                        // Send approval email
                        $record->user->notify(new \App\Notifications\PTApproved($code));

                        Notification::make()
                            ->title('Physiotherapist approved!')
                            ->body("Activation code: {$code}")
                            ->success()
                            ->send();
                    }),

                // Reject action with reason
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Reason for rejection')
                            ->required()
                            ->rows(3),
                    ])
                    ->visible(fn (Physiotherapist $record) =>
                        $record->vetting_status === 'pending'
                    )
                    ->action(function (Physiotherapist $record, array $data) {
                        $record->update([
                            'vetting_status'   => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                        ]);

                        $record->user->notify(
                            new \App\Notifications\PTRejected($data['rejection_reason'])
                        );

                        Notification::make()
                            ->title('Physiotherapist rejected.')
                            ->warning()
                            ->send();
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPhysiotherapists::route('/'),
        ];
    }
}
