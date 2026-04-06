<?php

namespace App\Filament\Resources\Trainings;

use App\Filament\Resources\Trainings\Pages\ManageTrainings;
use App\Models\Training;
use Filament\Forms;
use Filament\Forms\Form;
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
    protected static ?string $navigationGroup = 'Manajemen Event';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'draft',
                        'success' => 'published',
                        'danger'  => 'closed',
                    ]),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR', locale: 'id'),
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Jadwal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('participants_count')
                    ->label('Peserta')
                    ->counts('participants')
                    ->formatStateUsing(fn ($state, $record) => $state . ' / ' . $record->capacity),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft'     => 'Draft',
                        'published' => 'Published',
                        'closed'    => 'Closed',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTrainings::route('/'),
        ];
    }
}
