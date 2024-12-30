<?php
namespace App\Filament\Resources;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Hash;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
class UserResource extends Resource
{
protected static ?string $model = User::class;
protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
public static function form(Form $form): Form
 {
return $form
 ->schema([
TableRepeater::make('users')
 ->showLabels()
 ->emptyLabel('There are no registered users!')
 ->headers([
Header::make('name')
 ->markAsRequired()
 ->align(Alignment::Center)
 ->width('150px'),
Header::make('email')
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
// Handling the translation fields for 'name'
 Forms\Components\TextInput::make('name.en') // English translation
 ->label('Name (English)')
 ->required(),
 Forms\Components\TextInput::make('name.es') // Spanish translation
 ->label('Name (Spanish)')
 ->required(),
 Forms\Components\TextInput::make('email')
 ->email()
 ->required()
 ->maxLength(255),
 Forms\Components\TextInput::make('password')
 ->password()
 ->required(fn (string $operation): bool => $operation === 'create')
 ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
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
SelectFilter::make('name_language')
 ->label('View Names In')
 ->options([
'en' => 'English',
'es' => 'Spanish',
 ])
 ->query(function (Builder $query, array $data) {
if (! $data['value']) {
return $query;
 }
return $query->whereNotNull("name->{$data['value']}");
 })
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
// Define any relations if needed
 ];
 }
public static function getPages(): array
 {
return [
'index' => Pages\ListUsers::route('/'),
'create' => Pages\CreateUser::route('/create'),
'edit' => Pages\EditUser::route('/{record}/edit'),
 ];
 }
}