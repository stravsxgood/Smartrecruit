<?php

namespace App\Filament\Resources\JobPositions\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class JobPositionsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul Lowongan')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Textarea::make('description')
                    ->label('Deskripsi Pekerjaan')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull(),

                // ✅ FileUpload HANYA di sini (FORM)
                FileUpload::make('image')
                    ->label('Gambar Banner Lowongan')
                    ->image()
                    ->disk('public')
                    ->directory('jobs')
                    ->maxSize(5120)
                    ->columnSpanFull(),

                Select::make('status')
                    ->label('Status Lowongan')
                    ->options([
                        'draft' => '📝 Draft',
                        'active' => '✅ Aktif',
                        'closed' => '🚫 Ditutup',
                    ])
                    ->default('active')
                    ->required(),

                TagsInput::make('requirements')
                    ->label('Skills / Requirements')
                    ->placeholder('Ketik skill lalu tekan Enter')
                    ->columnSpanFull(),

                TagsInput::make('benefits')
                    ->label('Benefit / Fasilitas')
                    ->placeholder('Ketik benefit lalu tekan Enter')
                    ->columnSpanFull(),

                Hidden::make('created_by')
                    ->default(fn() => Auth::id()),
            ]);
    }
}
