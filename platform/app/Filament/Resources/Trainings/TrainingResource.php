<?php

namespace App\Filament\Resources\Trainings;

use App\Filament\Resources\Trainings\Pages\ManageTrainings;
use App\Models\Training;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class TrainingResource extends Resource
{
    protected static ?string $model = Training::class;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-briefcase';
    }

    protected static ?string $navigationLabel = 'Pelatihan K3';
    protected static ?string $modelLabel = 'Pelatihan K3';
    protected static ?string $pluralModelLabel = 'Daftar Pelatihan K3';
    protected static string|\UnitEnum|null $navigationGroup = 'Manajemen Event';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('title')
                ->label('Judul Pelatihan')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, callable $set) =>
                    $set('slug', Str::slug($state))
                )
                ->columnSpanFull(),

            Forms\Components\TextInput::make('slug')
                ->label('Slug URL')
                ->required()
                ->unique(ignoreRecord: true)
                ->helperText('Dibuat otomatis dari judul.'),

            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'draft'     => 'Draft',
                    'published' => 'Published',
                    'closed'    => 'Closed',
                ])
                ->default('draft')
                ->required(),

            Forms\Components\TextInput::make('price')
                ->label('Harga Dasar (Rp)')
                ->numeric()
                ->default(0)
                ->prefix('Rp'),

            Forms\Components\TextInput::make('capacity')
                ->label('Kapasitas Peserta')
                ->numeric()
                ->default(100)
                ->required(),

            Forms\Components\DateTimePicker::make('scheduled_at')
                ->label('Waktu Pelaksanaan')
                ->required()
                ->timezone('Asia/Jakarta'),

            Forms\Components\TextInput::make('location')
                ->label('Lokasi / Tipe')
                ->placeholder('Contoh: Online via Zoom / Hotel XYZ Jakarta')
                ->columnSpanFull(),

            Forms\Components\TextInput::make('link_forwarder')
                ->label('Link Form Eksternal (Link Forwarder)')
                ->url()
                ->helperText('Peserta akan diarahkan ke link formulir ini setelah sukses mendaftar/bayar.')
                ->columnSpanFull(),

            Forms\Components\Textarea::make('description')
                ->label('Deskripsi Lengkap')
                ->rows(4)
                ->columnSpanFull(),

            Forms\Components\FileUpload::make('poster')
                ->label('Poster / Thumbnail')
                ->image()
                ->disk('public')
                ->directory('training-posters')
                ->imagePreviewHeight('200')
                ->maxSize(2048)
                ->helperText('Format: JPG/PNG/WebP. Maks 2MB.')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft'     => 'warning',
                        'published' => 'success',
                        'closed'    => 'danger',
                        default     => 'gray',
                    }),

                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->formatStateUsing(fn ($state) => $state > 0 ? 'Rp ' . number_format($state, 0, ',', '.') : 'Gratis')
                    ->sortable(),

                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Jadwal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('participants_count')
                    ->label('Peserta')
                    ->counts('participants')
                    ->suffix(fn ($record) => ' / ' . ($record?->capacity ?? 0)),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft'     => 'Draft',
                        'published' => 'Published',
                        'closed'    => 'Closed',
                    ]),
            ])
            ->recordActions([
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
            'index' => ManageTrainings::route('/'),
        ];
    }
}
