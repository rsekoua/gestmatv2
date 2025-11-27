<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations de l\'utilisateur')
                    ->description('Informations de base de l\'utilisateur')
                    ->icon(Heroicon::User)
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nom complet')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('email')
                            ->label('Adresse email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->columnSpanFull(),

                        DateTimePicker::make('email_verified_at')
                            ->label('Email vérifié le')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->columnSpanFull(),
                    ]),

                Section::make('Rôles et Permissions')
                    ->description('Gérer les rôles de l\'utilisateur')
                    ->icon(Heroicon::ShieldCheck)
                    ->schema([
                        Select::make('roles')
                            ->label('Rôles')
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->options(Role::all()->pluck('name', 'id'))
                            ->preload()
                            ->searchable()
                            ->helperText('Sélectionnez un ou plusieurs rôles pour cet utilisateur')
                            ->columnSpanFull(),
                    ]),

                Section::make('Sécurité')
                    ->description('Mot de passe de l\'utilisateur')
                    ->icon(Heroicon::LockClosed)
                    ->columns(2)
                    ->schema([
                        TextInput::make('password')
                            ->label('Mot de passe')
                            ->password()
                            ->required(fn ($context) => $context === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->minLength(8)
                            ->same('password_confirmation')
                            ->revealable()
                            ->columnSpan(1),

                        TextInput::make('password_confirmation')
                            ->label('Confirmer le mot de passe')
                            ->password()
                            ->required(fn ($context) => $context === 'create')
                            ->dehydrated(false)
                            ->revealable()
                            ->columnSpan(1),
                    ]),
            ]);
    }
}
