<?php

namespace App\Filament\Resources\Webinars;

use App\Filament\Resources\Webinars\Pages\ManageWebinars;
use App\Models\Webinar;
use App\Models\Participant;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class WebinarResource extends Resource
{
    protected static ?string $model = Webinar::class;
    protected static ?string $navigationLabel = 'Webinar';
    protected static ?string $pluralLabel = 'Webinar';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return Heroicon::CalendarDays;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([

            TextInput::make('title')
                ->label('Judul Webinar')
                ->required()
                ->maxLength(191)
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, callable $set) =>
                    $set('slug', Str::slug($state))
                )
                ->columnSpanFull(),

            TextInput::make('slug')
                ->label('Slug URL')
                ->required()
                ->unique(Webinar::class, 'slug', ignoreRecord: true)
                ->helperText('Dibuat otomatis dari judul. Contoh: webinar-data-science-2025'),

            Select::make('status')
                ->label('Status')
                ->options([
                    'draft'     => 'Draft',
                    'published' => 'Published',
                    'closed'    => 'Closed',
                ])
                ->default('draft')
                ->required(),

            Textarea::make('description')
                ->label('Deskripsi')
                ->rows(5)
                ->columnSpanFull(),

            TextInput::make('price')
                ->label('Harga (Rp)')
                ->numeric()
                ->default(0)
                ->prefix('Rp')
                ->helperText('Isi 0 untuk webinar gratis'),

            TextInput::make('capacity')
                ->label('Kapasitas Peserta')
                ->numeric()
                ->default(100)
                ->required(),

            DateTimePicker::make('scheduled_at')
                ->label('Waktu Pelaksanaan')
                ->required()
                ->timezone('Asia/Jakarta')
                ->displayFormat('d M Y H:i'),

            TextInput::make('zoom_link')
                ->label('Link Zoom / Meeting')
                ->url()
                ->placeholder('https://zoom.us/j/...')
                ->columnSpanFull(),

            FileUpload::make('poster')
                ->label('Poster / Thumbnail Webinar')
                ->image()
                ->disk('public')
                ->directory('webinar-posters')
                ->imagePreviewHeight('200')
                ->maxSize(2048)
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->helperText('Format: JPG/PNG/WebP. Maks 2MB. Rasio ideal 16:9.')
                ->columnSpanFull(),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('title')->label('Judul'),
            TextEntry::make('slug')->label('Slug'),
            TextEntry::make('status')->badge()->label('Status'),
            TextEntry::make('price')->money('IDR')->label('Harga'),
            TextEntry::make('scheduled_at')->dateTime('d M Y H:i')->label('Jadwal'),
            TextEntry::make('capacity')->numeric()->label('Kapasitas'),
            TextEntry::make('zoom_link')->placeholder('-')->label('Link Meeting')->columnSpanFull(),
            TextEntry::make('description')->placeholder('-')->columnSpanFull()->label('Deskripsi'),
            ImageEntry::make('poster')
                ->label('Poster')
                ->disk('public')
                ->height(180)
                ->columnSpanFull()
                ->placeholder('Belum ada poster'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Webinar')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'published' => 'success',
                        'draft'     => 'warning',
                        'closed'    => 'danger',
                        default     => 'gray',
                    }),

                TextColumn::make('price')
                    ->label('Harga')
                    ->formatStateUsing(fn ($state) => $state > 0 ? 'Rp ' . number_format($state, 0, ',', '.') : 'Gratis')
                    ->sortable(),

                TextColumn::make('scheduled_at')
                    ->label('Jadwal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('participants_count')
                    ->label('Peserta')
                    ->counts('participants')
                    ->sortable()
                    ->suffix(fn ($record) => ' / ' . ($record?->capacity ?? 0)),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft'     => 'Draft',
                        'published' => 'Published',
                        'closed'    => 'Closed',
                    ]),
            ])
            ->defaultSort('scheduled_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageWebinars::route('/'),
        ];
    }
}
