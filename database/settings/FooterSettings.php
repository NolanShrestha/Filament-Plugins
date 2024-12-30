<?php

namespace Database\Settings;

use Spatie\LaravelSettings\Settings;

class FooterSettings extends Settings
{
    public string $footer_text;
    public string $contact_email;

    public static function group(): string
    {
        return 'footer';
    }
}
