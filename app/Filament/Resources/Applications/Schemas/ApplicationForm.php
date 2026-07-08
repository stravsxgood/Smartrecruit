<?php

namespace App\Filament\Resources\Applications\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('applicant_profile_id')
                    ->relationship('applicantProfile', 'id')
                    ->required(),
                Select::make('job_position_id')
                    ->relationship('jobPosition', 'title')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('applied'),
                TextInput::make('custom_answers'),
                Textarea::make('hrd_notes')
                    ->columnSpanFull(),
            ]);
    }
}
