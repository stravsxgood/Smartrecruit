<?php

namespace App\Filament\Resources\News;

use App\Filament\Resources\News\Pages;
use App\Models\News;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static ?string $navigationLabel = 'News';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('News Content')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul Berita')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (callable $set, $state) {
                                $set('slug', Str::slug($state));
                            }),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Textarea::make('excerpt')
                            ->label('Excerpt/Ringkasan')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),

                        RichEditor::make('content')
                            ->label('Konten Berita')
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ]),
                    ])->columns(1),

                Section::make('Media & Publishing')
                    ->schema([
                        // ✅ PERBAIKAN 1: Tambahkan disk('public') dan visibility('public')
                        FileUpload::make('image')
                            ->label('Gambar Utama')
                            ->image()
                            ->disk('public')              // ✅ WAJIB: Simpan di disk public
                            ->visibility('public')         // ✅ WAJIB: File bisa diakses publik
                            ->directory('news')            // Folder: storage/app/public/news/
                            ->maxSize(5120)
                            ->imageResizeTargetWidth('1200')
                            ->imageResizeTargetHeight('630')
                            ->preserveFilenames(false)     // Generate nama file unik
                            ->getUploadedFileNameForStorageUsing(
                                fn($file) => now()->format('YmdHis') . '_' . $file->getClientOriginalName()
                            ),

                        Select::make('category')
                            ->label('Kategori')
                            ->options([
                                'News' => 'News',
                                'Business' => 'Business',
                                'Technology' => 'Technology',
                                'Lifestyle' => 'Lifestyle',
                                'Sport' => 'Sport',
                                'Entertainment' => 'Entertainment',
                            ])
                            ->default('News')
                            ->required(),

                        // ✅ PERBAIKAN 2: Ganti author_id dengan author_name (string)
                        TextInput::make('author_name')
                            ->label('Nama Penulis')
                            ->default(fn() => Auth::user()?->name ?? 'Redaksi')
                            ->maxLength(255),

                        DateTimePicker::make('published_at')
                            ->label('Tanggal Publikasi')
                            ->default(now())
                            ->required(),

                        // ✅ PERBAIKAN 3: Default true + auto set published_at
                        Toggle::make('is_published')
                            ->label('Publikasikan')
                            ->default(true) // ✅ Default true agar langsung muncul
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $set('published_at', now());
                                }
                            }),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Gambar')
                    ->disk('public')  // ✅ WAJIB: Ambil dari disk public
                    ->square(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge(),

                Tables\Columns\TextColumn::make('author_name')
                    ->label('Penulis'),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Tanggal Publikasi')
                    ->dateTime('d M Y')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('Status')
                    ->boolean(),

                Tables\Columns\TextColumn::make('views')
                    ->label('Views')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'News' => 'News',
                        'Business' => 'Business',
                        'Technology' => 'Technology',
                        'Lifestyle' => 'Lifestyle',
                        'Sport' => 'Sport',
                        'Entertainment' => 'Entertainment',
                    ]),
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Status Publikasi'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('published_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}
