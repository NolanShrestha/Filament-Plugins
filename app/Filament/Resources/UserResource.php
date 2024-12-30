<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action; 
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Hash;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use App\Filament\Pages\ManageFooter;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Collection;  
use App\Jobs\UsersCsvExportJob;
use Filament\Notifications\Notification;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('copyright')
                    ->label('Copyright notice')
                    ->required(),
                Repeater::make('links')
                    ->schema([
                        TextInput::make('label')->required(),
                        TextInput::make('url')
                            ->url()
                            ->required(),
                    ]),
                TableRepeater::make('users')
                    ->showLabels()
                    ->emptyLabel('There are no registered users!')
                    ->headers([
                        Header::make('name')
                            ->markAsRequired()
                            ->align(Alignment::Center)
                            ->width('150px'),
                        Header::make('email'),
                    ])
                    ->renderHeader(false)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required(),
                    ])
                    ->columnSpan('full'),
                Forms\Components\Section::make('User Details')
                    ->schema([
                        Forms\Components\TextInput::make('name.en') 
                            ->label('Name (English)')
                            ->required(),
                        Forms\Components\TextInput::make('name.es') 
                            ->label('Name (Spanish)')
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->required(fn(string $operation): bool => $operation === 'create')
                            ->dehydrateStateUsing(fn(string $state): string => Hash::make($state))
                            ->maxLength(255),
                    ])->columnSpan('full'),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            return $state['en'] ?? '-'; 
                        }
                        return $state;
                    })
                    ->label('Name (English)')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            return $state['es'] ?? '-'; 
                        }
                        return $state;
                    })
                    ->label('Name (Spanish)')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('name_translations')
                    ->label('View Names In')
                    ->options([
                        'en' => 'English',
                        'es' => 'Spanish',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!$data['value']) {
                            return $query;
                        }
              
                        return $query->whereNotNull("name->{$data['value']}");
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('activities')
                    ->label('View Activities')
                    ->url(fn($record) => UserResource::getUrl('activities', ['record' => $record])),
                Action::make('export')
                    ->label('Export Users')
                    ->action(function () {
                        $users = User::all();
                        UsersCsvExportJob::dispatch($users);

                        Notification::make()
                            ->title('Export Started')
                            ->body('The user export job is now running.')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkAction::make('export-jobs')
                    ->label('Background Export')
                    ->icon('heroicon-o-cog')
                    ->action(function (Collection $records) {
                        UsersCsvExportJob::dispatch($records, 'users.csv');

                        Notification::make()
                            ->title('Export Started')
                            ->body('The user export job is now running.')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Define any relations if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'activities' => Pages\ListUserActivities::route('/{record}/activities'),
            // 'manage-footer' => ManageFooter::route('/manage-footer'),
        ];
    }
}