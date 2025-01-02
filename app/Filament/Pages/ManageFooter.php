<?php

namespace App\Filament\Pages;

use App\Database\Settings\FooterSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageFooter extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = FooterSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('footer_text')
                    ->label('Footer Text')
                    ->required(),

                Forms\Components\TextInput::make('contact_email')
                    ->label('Contact Email')
                    ->email()
                    ->required(),
            ]);
    }
}
