<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use App\Services\Billing\BillingService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    // Счета создаются программно при оформлении подписки учреждением
    // (SubscriptionController -> BillingService::subscribe), не вручную
    // в админке.
    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Ожидает оплаты',
                        'paid' => 'Оплачен',
                        'cancelled' => 'Отменён',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('institution.name')
                    ->label('Учреждение')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subscription.plan.name')
                    ->label('Тариф'),
                Tables\Columns\TextColumn::make('amount')
                    ->formatStateUsing(fn (Invoice $record) => "{$record->amount} {$record->currency}"),
                Tables\Columns\TextColumn::make('provider')
                    ->label('Провайдер'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'cancelled' => 'danger',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Выставлен')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Оплачен')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Ожидает оплаты',
                        'paid' => 'Оплачен',
                        'cancelled' => 'Отменён',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('markPaid')
                    ->label('Отметить оплаченным')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Invoice $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (Invoice $record) {
                        app(BillingService::class)->markInvoicePaid($record, auth()->user());

                        Notification::make()
                            ->title('Счёт отмечен оплаченным, подписка активирована')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
