<?php

namespace App\Filament\Pages;

use App\Models\AppSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class AppSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Settings';

    protected static ?string $navigationGroup = 'System';

    protected static ?int $navigationSort = 99;

    protected static string $view = 'filament.pages.app-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'standard_plan_price' => (int) (AppSetting::getValue('standard_plan_price_kobo', 200000) / 100),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Subscription Plan Pricing')
                    ->description('Prices are in Nigerian Naira (₦). Changes take effect immediately for new subscriptions.')
                    ->schema([
                        TextInput::make('standard_plan_price')
                            ->label('Standard Plan Price (₦/month)')
                            ->numeric()
                            ->prefix('₦')
                            ->minValue(100)
                            ->maxValue(1000000)
                            ->required()
                            ->helperText('Current value is what clients pay when subscribing to Standard tier.'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $priceKobo = (int) ($data['standard_plan_price'] * 100);

        AppSetting::setValue('standard_plan_price_kobo', $priceKobo);

        Notification::make()
            ->title('Settings saved')
            ->body('Standard plan price updated to ₦'.number_format($data['standard_plan_price']).'/month.')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Changes')
                ->submit('save'),
        ];
    }
}
