<?php

namespace App\Filament\Resources\Participants;

use App\Filament\Resources\Participants\Pages\ManageParticipants;
use App\Models\Participant;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\MorphToSelect;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ParticipantResource extends Resource
{
    protected static ?string $model = Participant::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-users';
    }

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return 'Participants';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'People';
    }

    // ─── Option Loaders (delegate to model) ──────────────────────────────────

    public static function jobOptions(): array
    {
        return Participant::jobOptions();
    }

    public static function sourceOptions(): array
    {
        return Participant::sourceOptions();
    }

    // ─── Form ─────────────────────────────────────────────────────────────────

    public static function form(Schema $schema): Schema
    {
        return $schema->components([

            // ── Section 1: Event & Registration ───────────────────────────
            \Filament\Forms\Components\Section::make('Informasi Kegiatan')
                ->schema([
                    \Filament\Forms\Components\MorphToSelect::make('participatable')
                        ->label('Terdaftar Pada')
                        ->types([
                            \Filament\Forms\Components\MorphToSelect\Type::make(\App\Models\Webinar::class)
                                ->titleAttribute('title'),
                            // More types can be added here easily!
                        ])
                        ->required()
                        ->searchable()
                        ->preload(),
                ]),

            // ── Section 2: Personal Info ──────────────────────────────────
            TextInput::make('name')
                ->label('Nama Lengkap')
                ->required(),

            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required(),

            TextInput::make('whatsapp_number')
                ->label('Nomor WhatsApp')
                ->tel(),

            TextInput::make('age')
                ->label('Usia')
                ->numeric()
                ->minValue(10)
                ->maxValue(99),

            Select::make('gender')
                ->label('Jenis Kelamin')
                ->options(['Laki-laki' => 'Laki‑laki', 'Perempuan' => 'Perempuan', 'Lainnya' => 'Lainnya']),

            TextInput::make('domicile')
                ->label('Domisili'),

            Textarea::make('background')
                ->label('Latar Belakang')
                ->columnSpanFull(),

            // ── Section 3: Education ──────────────────────────────────────
            TextInput::make('last_education')
                ->label('Pendidikan Terakhir'),

            Select::make('education_status')
                ->label('Status Pendidikan')
                ->options([
                    'Tidak Sedang Menempuh' => 'Tidak Sedang Menempuh',
                    'Sedang Menempuh'       => 'Sedang Menempuh',
                ])
                ->default('Tidak Sedang Menempuh')
                ->live(),

            TextInput::make('institution_name')
                ->label('Asal Perguruan Tinggi / Sekolah')
                ->visible(fn ($get) => $get('education_status') === 'Sedang Menempuh'),

            // ── Section 4: Employment ─────────────────────────────────────
            Select::make('employment_status')
                ->label('Status Pekerjaan')
                ->options([
                    'Tidak Sedang Bekerja' => 'Tidak Sedang Bekerja',
                    'Sedang Bekerja'       => 'Sedang Bekerja',
                ])
                ->default('Tidak Sedang Bekerja')
                ->live(),

            Select::make('current_job')
                ->label('Pekerjaan Saat Ini')
                ->options(fn () => self::jobOptions())
                ->searchable()
                ->visible(fn ($get) => $get('employment_status') === 'Sedang Bekerja'),

            TextInput::make('company')
                ->label('Institusi / Perusahaan')
                ->visible(fn ($get) => $get('employment_status') === 'Sedang Bekerja'),

            // ── Section 5: Source & Consent ───────────────────────────────
            Select::make('event_source')
                ->label('Darimana Tahu Event Ini')
                ->options(fn () => self::sourceOptions())
                ->searchable(),

            Toggle::make('privacy_consent')
                ->label('Persetujuan Kebijakan Privasi')
                ->required(),

            // ── Section 6: Payment ────────────────────────────────────────
            Select::make('payment_method')
                ->label('Metode Pembayaran')
                ->options([
                    'none'          => 'Belum Bayar',
                    'midtrans'      => 'Midtrans',
                    'bank_transfer' => 'Transfer Bank',
                    'paypal'        => 'PayPal',
                ])
                ->default('none'),

            TextInput::make('transaction_id')
                ->label('ID Transaksi'),

            Select::make('payment_status')
                ->label('Status Pembayaran')
                ->options([
                    'pending'  => 'Pending',
                    'settlement'=> 'Settled/Paid',
                    'paid'     => 'Paid (Manual)',
                    'failed'   => 'Failed',
                    'refunded' => 'Refunded',
                ])
                ->default('pending'),

            TextInput::make('amount_paid')
                ->label('Nominal Dibayar')
                ->numeric()
                ->prefix('Rp'),
        ]);
    }

    // ─── Infolist (read-only view) ────────────────────────────────────────────

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('participatable.title')->label('Terdaftar Pada')->badge()->color('info'),
            TextEntry::make('name')->label('Nama'),
            TextEntry::make('email'),
            TextEntry::make('whatsapp_number')->label('WhatsApp'),
            TextEntry::make('age')->label('Usia')->numeric(),
            TextEntry::make('gender')->label('Jenis Kelamin'),
            TextEntry::make('domicile')->label('Domisili'),
            TextEntry::make('background')->label('Latar Belakang')->columnSpanFull()->placeholder('-'),
            TextEntry::make('last_education')->label('Pendidikan Terakhir'),
            TextEntry::make('education_status')->label('Status Pendidikan'),
            TextEntry::make('institution_name')->label('Asal PT/Sekolah')->placeholder('-'),
            TextEntry::make('employment_status')->label('Status Pekerjaan'),
            TextEntry::make('current_job')->label('Pekerjaan')->placeholder('-'),
            TextEntry::make('company')->label('Perusahaan')->placeholder('-'),
            TextEntry::make('event_source')->label('Sumber Info'),
            IconEntry::make('privacy_consent')->label('Kebijakan Privasi')->boolean(),
            TextEntry::make('payment_method')->label('Metode Bayar')->badge(),
            TextEntry::make('transaction_id')->label('ID Transaksi')->placeholder('-'),
            TextEntry::make('payment_status')->label('Status Bayar')->badge(),
            TextEntry::make('amount_paid')->label('Nominal')->money('IDR'),
            TextEntry::make('created_at')->label('Didaftarkan')->dateTime()->placeholder('-'),
        ]);
    }

    // ─── Table ────────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('participatable.title')->label('Event/Webinar')->badge()->color('info')->sortable(),
                TextColumn::make('whatsapp_number')->label('WhatsApp')->searchable(),
                TextColumn::make('gender')->label('Kelamin'),
                TextColumn::make('education_status')->label('Pendidikan')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('employment_status')->label('Pekerjaan')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('event_source')->label('Sumber')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('payment_status')->label('Bayar')->badge()
                    ->color(fn (string $state) => match ($state) {
                        'settlement', 'paid' => 'success',
                        'pending'            => 'warning',
                        'failed'             => 'danger',
                        'refunded'           => 'info',
                        default              => 'gray',
                    }),
                TextColumn::make('created_at')->label('Daftar')->dateTime()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('participatable_type')
                    ->label('Tipe Event')
                    ->options([
                        \App\Models\Webinar::class => 'Webinar',
                    ]),
                SelectFilter::make('payment_status')
                    ->label('Status Bayar')
                    ->options(['pending' => 'Pending', 'settlement' => 'Settled', 'paid' => 'Paid (Manual)', 'failed' => 'Failed', 'refunded' => 'Refunded']),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageParticipants::route('/'),
        ];
    }
}
