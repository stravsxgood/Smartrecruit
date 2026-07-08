<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'HRD Management';

    protected static ?int $navigationSort = 1;

    protected static string|\UnitEnum|null $navigationGroup = 'User Management';

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();
        return $user && $user->isSuperAdmin();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('role', '!=', 'super_admin');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi User')
                    ->components([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->maxLength(255)
                            ->dehydrateStateUsing(fn (?string $state): ?string => $state ? Hash::make($state) : null),

                        Select::make('role')
                            ->label('Role')
                            ->options([
                                'super_admin' => '👑 Super Admin',
                                'hrd' => '💼 HRD',
                                'candidate' => '👤 Candidate',
                            ])
                            ->default('hrd')
                            ->required(),
                    ])->columns(1),

                Section::make('Status Akun')
                    ->components([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => '✅ Active',
                                'suspended' => '⏸️ Suspended',
                                'inactive' => '❌ Inactive',
                            ])
                            ->default('active')
                            ->required()
                            ->live(),

                        Textarea::make('suspend_reason')
                            ->label('Alasan Suspend')
                            ->rows(3)
                            ->visible(fn (callable $get): bool => $get('status') === 'suspended'),

                        DateTimePicker::make('suspended_at')
                            ->label('Tanggal Suspend')
                            ->visible(fn (callable $get): bool => $get('status') === 'suspended'),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'hrd' => 'warning',
                        'candidate' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'suspended' => 'warning',
                        'inactive' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('job_positions_count')
                    ->label('Job Created')
                    ->counts('jobPositions')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'super_admin' => 'Super Admin',
                        'hrd' => 'HRD',
                        'candidate' => 'Candidate',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                        'inactive' => 'Inactive',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),

                Action::make('suspend')
                    ->label('Suspend')
                    ->icon(Heroicon::OutlinedPause)
                    ->color('warning')
                    ->visible(fn (User $record): bool =>
                        $record->role === 'hrd' && $record->status === 'active'
                    )
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('suspend_reason')
                            ->label('Alasan Suspend')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (User $record, array $data): void {
                        $record->suspend($data['suspend_reason']);
                    })
                    ->successNotificationTitle('HRD berhasil di-suspend'),

                Action::make('activate')
                    ->label('Activate')
                    ->icon(Heroicon::OutlinedPlay)
                    ->color('success')
                    ->visible(fn (User $record): bool =>
                        $record->role === 'hrd' && $record->status === 'suspended'
                    )
                    ->requiresConfirmation()
                    ->action(function (User $record): void {
                        $record->activate();
                    })
                    ->successNotificationTitle('HRD berhasil di-activate'),

                Action::make('deleteHrd')
                    ->label('Hapus')
                    ->icon(Heroicon::OutlinedTrash)
                    ->color('danger')
                    ->visible(fn (User $record): bool =>
                        $record->role === 'hrd' && $record->status !== 'active'
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Akun HRD')
                    ->modalDescription('Apakah Anda yakin ingin menghapus akun HRD ini?')
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->action(function (User $record): void {
                        $record->deactivate();
                    })
                    ->successNotificationTitle('Akun HRD berhasil dihapus'),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),

                BulkAction::make('bulkSuspend')
                    ->label('Suspend Selected')
                    ->icon(Heroicon::OutlinedPause)
                    ->color('warning')
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('suspend_reason')
                            ->label('Alasan Suspend')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (array $data, Collection $records): void {
                        foreach ($records as $record) {
                            if ($record->role === 'hrd') {
                                $record->suspend($data['suspend_reason']);
                            }
                        }
                    })
                    ->deselectRecordsAfterCompletion()
                    ->successNotificationTitle('HRD terpilih berhasil di-suspend'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
