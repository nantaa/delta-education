<?php

namespace App\Filament\Resources\DiscountCodes;

use App\Filament\Resources\DiscountCodes\Pages\ManageDiscountCodes;
use App\Models\DiscountCode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class DiscountCodeResource extends Resource
{
    protected static ?string $model = DiscountCode::class;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-ticket';
    }
    protected static ?string $navigationLabel = 'Kode Diskon';
    protected static ?string $modelLabel = 'Kode Diskon';
    protected static ?string $pluralModelLabel = 'Daftar Kode Diskon';
    protected static ?string $navigationGroup = 'Manajemen Event';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label('Kode Diskon')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->suffixAction(
                        Forms\Components\Actions\Action::make('generate')
                            ->icon('heroicon-m-arrow-path')
                            ->action(function (Forms\Set $set) {
                                $set('code', strtoupper(Str::random(8)));
                            })
                    ),
                Forms\Components\Select::make('discount_type')
                    ->label('Tipe Diskon')
                    ->options([
                        'fixed' => 'Nominal (Rp)',
                        'percent' => 'Persentase (%)',
                    ])
                    ->required()
                    ->default('fixed')
                    ->live(),
                Forms\Components\TextInput::make('discount_value')
                    ->label('Nilai Diskon')
                    ->required()
                    ->numeric()
                    ->prefix(fn(Forms\Get $get) => $get('discount_type') === 'percent' ? '%' : 'Rp')
                    ->default(0),
                Forms\Components\TextInput::make('usage_limit')
                    ->label('Batas Penggunaan')
                    ->numeric()
                    ->helperText('Kosongkan jika tanpa batas (unlimited)')
                    ->placeholder('Contoh: 100'),
                Forms\Components\DateTimePicker::make('valid_until')
                    ->label('Berlaku Hingga')
                    ->placeholder('Tanpa batas waktu'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('discount_type')
                    ->label('Potongan')
                    ->formatStateUsing(function ($record) {
                        if ($record->discount_type === 'percent') {
                            return (int) $record->discount_value . '%';
                        }
                        return 'Rp ' . number_format($record->discount_value, 0, ',', '.');
                    }),
                Tables\Columns\TextColumn::make('used_count')
                    ->label('Terpakai')
                    ->formatStateUsing(function ($record) {
                        $limit = $record->usage_limit ? " / {$record->usage_limit}" : " (Unlimited)";
                        return $record->used_count . $limit;
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('valid_until')
                    ->label('Berlaku Hingga')
                    ->dateTime('d M Y H:i')
                    ->placeholder('Selamanya'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->boolean()
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageDiscountCodes::route('/'),
        ];
    }
}