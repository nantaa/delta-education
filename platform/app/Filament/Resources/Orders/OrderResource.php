<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Resources\Orders\Pages\ManageOrders;
use App\Models\Order;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationLabel = 'Transaksi';
    protected static ?string $pluralLabel = 'Transaksi';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'customer_name';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return Heroicon::Banknotes;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('customer_name')->label('Nama')->required(),
            TextInput::make('customer_email')->label('Email')->email()->required(),
            TextInput::make('customer_phone')->label('WhatsApp')->tel(),
            TextInput::make('total_amount')
                ->label('Total (Rp)')
                ->numeric()
                ->prefix('Rp')
                ->required(),
            Select::make('status')
                ->label('Status Pembayaran')
                ->options([
                    'pending'  => 'Pending',
                    'paid'     => 'Paid',
                    'failed'   => 'Failed',
                    'refunded' => 'Refunded',
                ])
                ->default('pending')
                ->required(),
            TextInput::make('payment_method')->label('Metode Pembayaran'),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('customer_name')->label('Nama'),
            TextEntry::make('customer_email')->label('Email'),
            TextEntry::make('customer_phone')->label('WhatsApp')->placeholder('-'),
            TextEntry::make('total_amount')
                ->label('Total')
                ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
            TextEntry::make('status')->badge()->label('Status')
                ->color(fn (string $state): string => match($state) {
                    'paid'     => 'success',
                    'pending'  => 'warning',
                    'failed'   => 'danger',
                    'refunded' => 'gray',
                    default    => 'gray',
                }),
            TextEntry::make('payment_method')->label('Metode')->placeholder('-'),
            TextEntry::make('created_at')->dateTime('d M Y H:i')->label('Waktu Transaksi'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer_name')
                    ->label('Pembeli')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('customer_email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('customer_phone')
                    ->label('WhatsApp')
                    ->searchable(),

                TextColumn::make('total_amount')
                    ->label('Total')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'paid'     => 'success',
                        'pending'  => 'warning',
                        'failed'   => 'danger',
                        'refunded' => 'gray',
                        default    => 'gray',
                    }),

                TextColumn::make('payment_method')
                    ->label('Metode'),

                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending'  => 'Pending',
                        'paid'     => 'Paid',
                        'failed'   => 'Failed',
                        'refunded' => 'Refunded',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
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
            'index' => ManageOrders::route('/'),
        ];
    }
}
