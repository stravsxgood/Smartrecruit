<?php

namespace App\Filament\Resources\JobPositions;

use App\Filament\Resources\JobPositions\Pages;
use App\Models\JobPosition;
use App\Models\User;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\FileUpload;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Support\Facades\Auth;

class JobPositionsResource extends Resource
{
    protected static ?string $model = JobPosition::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $navigationLabel = 'Job Positions';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        // ✅ Type hint agar Intelephense mengenali method User
        /** @var User|null $user */
        $user = Auth::user();

        if ($user && $user->isSuperAdmin()) {
            return $query;
        }

        return $query->where('creator_id', $user?->id);
    }

    public static function form(Schema $schema): Schema
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $schema
            ->schema([
                Section::make('Informasi Lowongan')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul Posisi')
                            ->required()
                            ->maxLength(255),

                        Select::make('creator_id')
                            ->label('HRD Pembuat')
                            ->relationship('creator', 'name')
                            ->default($user?->id)
                            ->required()
                            ->visible($user?->isSuperAdmin() ?? false),

                        Textarea::make('description')
                            ->label('Deskripsi Pekerjaan')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),

                        TagsInput::make('requirements')
                            ->label('Requirements')
                            ->placeholder('Tambah requirement')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Media & Status')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Gambar Lowongan')
                            ->image()
                            ->disk('public')
                            ->directory('job-positions')
                            ->maxSize(5120),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => '✅ Active',
                                'inactive' => '❌ Inactive',
                                'draft' => '📝 Draft',
                            ])
                            ->default('active')
                            ->required(),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('HRD')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'draft' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('applications_count')
                    ->label('Total Lamaran')
                    ->counts('applications')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'draft' => 'Draft',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!isset($data['creator_id']) && $user) {
            $data['creator_id'] = $user->id;
        }
        return $data;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJobPositions::route('/'),
            'create' => Pages\CreateJobPosition::route('/create'),
            'edit' => Pages\EditJobPosition::route('/{record}/edit'),
        ];
    }
}
