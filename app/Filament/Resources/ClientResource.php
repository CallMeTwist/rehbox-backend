<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Clients';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Subscription')
                    ->schema([
                        Forms\Components\Select::make('subscription_status')
                            ->options([
                                'inactive' => 'Inactive',
                                'active' => 'Active',
                                'expired' => 'Expired',
                            ])
                            ->required(),
                        Forms\Components\DateTimePicker::make('subscription_expires_at')
                            ->label('Subscription Expires At')
                            ->nullable()
                            ->helperText('Leave blank for no expiry.'),
                    ]),
            ]);
    }

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
                Tables\Columns\TextColumn::make('physiotherapist.user.name')
                    ->label('Physiotherapist')
                    ->default('—'),
                Tables\Columns\BadgeColumn::make('subscription_status')
                    ->label('Subscription')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                        'warning' => 'expired',
                    ]),
                Tables\Columns\TextColumn::make('subscription_expires_at')
                    ->label('Expires At')
                    ->dateTime()
                    ->placeholder('No expiry'),
                Tables\Columns\TextColumn::make('coin_balance')
                    ->label('Coins')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('subscription_status')
                    ->options([
                        'inactive' => 'Inactive',
                        'active' => 'Active',
                        'expired' => 'Expired',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('activate_subscription')
                    ->label('Activate Subscription')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Client $record): void {
                        $record->update([
                            'subscription_status' => 'active',
                            'subscription_expires_at' => null,
                        ]);

                        Notification::make()
                            ->title('Subscription activated')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Client $record): bool => ! $record->isSubscribed()),
                Tables\Actions\Action::make('deactivate_subscription')
                    ->label('Deactivate')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Client $record): void {
                        $record->update([
                            'subscription_status' => 'inactive',
                        ]);

                        Notification::make()
                            ->title('Subscription deactivated')
                            ->danger()
                            ->send();
                    })
                    ->visible(fn (Client $record): bool => $record->isSubscribed()),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'view' => Pages\ViewClient::route('/{record}'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
