<?php

namespace App\Filament\Resources\Applications;

use App\Filament\Resources\Applications\Pages\CreateApplication;
use App\Filament\Resources\Applications\Pages\EditApplication;
use App\Filament\Resources\Applications\Pages\ListApplications;
use App\Models\Application;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('applicantProfile.user.name')
                    ->label('Nama Pelamar')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jobPosition.title')
                    ->label('Posisi yang Dilamar')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('phone')
                    ->label('No. Telepon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        'reviewed' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Melamar')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Kandidat')
                    ->schema([
                        TextInput::make('applicant_name_display')
                            ->label('Nama Pelamar')
                            ->formatStateUsing(fn ($record) => $record?->applicantProfile?->user?->name ?? '-')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('applicant_email_display')
                            ->label('Email')
                            ->formatStateUsing(fn ($record) => $record?->applicantProfile?->user?->email ?? '-')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('phone')
                            ->label('No. Telepon')
                            ->disabled(),

                        Textarea::make('cover_letter')
                            ->label('Surat Lamaran')
                            ->rows(4)
                            ->disabled()
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Data CV (Terekstrak AI)')
                    ->schema([
                        TagsInput::make('skills')
                            ->label('Skills')
                            ->disabled()
                            ->columnSpanFull(),

                        Textarea::make('experience')
                            ->label('Pengalaman Kerja')
                            ->disabled()
                            ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $state)
                            ->rows(4)
                            ->columnSpanFull(),

                        Textarea::make('education')
                            ->label('Pendidikan')
                            ->disabled()
                            ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $state)
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('Keputusan HRD')
                    ->schema([
                        Select::make('status')
                            ->label('Ubah Status Pelamar')
                            ->options([
                                'pending' => '⏳ Pending (Menunggu)',
                                'reviewed' => '👁️ Reviewed (Sedang Ditinjau)',
                                'accepted' => '✅ Accepted (Diterima)',
                                'rejected' => '❌ Rejected (Ditolak)',
                            ])
                            ->required(),

                        Textarea::make('hrd_notes')
                            ->label('Catatan Internal HRD')
                            ->rows(3)
                            ->helperText('Catatan ini hanya bisa dilihat oleh HRD, tidak untuk pelamar.')
                            ->columnSpanFull(),
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
            'index' => ListApplications::route('/'),
            'create' => CreateApplication::route('/create'),
            'edit' => EditApplication::route('/{record}/edit'),
        ];
    }
}
