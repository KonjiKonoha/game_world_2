<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepositResource\Pages;
use App\Filament\Resources\DepositResource\RelationManagers;
use App\Models\Deposit;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\TemporaryUploadedFile;
use stdClass;

class DepositResource extends Resource
{
    protected static ?string $model = Deposit::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Select::make('user_id')
                            ->required()
                            ->searchable()
                            ->relationship('user', 'name'),
                        Select::make('payment_method')
                            ->required()
                            ->options([
                                'kpay' => 'KPay',
                                'wave' => 'WaveMoney',
                                'kbz_bank_transfer' => 'KBZ Bank Transfer',
                                'aya_bank_transfer' => 'Aya Bank Transfer',
                                'yoma_bank_transfer' => 'Yoma Bank Transfer',
                                'a_bank_transfer' => 'A Bank Transfer',
                            ]),
                        FileUpload::make('proof')
                            ->required()
                            ->image()
                            ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                                return (string) str($file->getClientOriginalName())->prepend('deposit-');
                            }),
                        TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->mask(fn (TextInput\Mask $mask) => $mask->money(prefix: 'K ', thousandsSeparator: ',', decimalPlaces: 2)),
                        TextInput::make('remarks')
                            ->required()
                            ->columnSpan(2),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('#')->getStateUsing(
                    static function (stdClass $rowLoop, HasTable $livewire): string {
                        return (string) ($rowLoop->iteration +
                            ($livewire->tableRecordsPerPage * ($livewire->page - 1
                            ))
                        );
                    }
                )->sortable(),
                TextColumn::make('user.name')
                    ->sortable(),
                TextColumn::make('amount')
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->sortable(),
                ImageColumn::make('proof')
                    ->size(180)
                    ->square(),
                TextColumn::make('created_at')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('user')->relationship('user', 'name'),
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until')
                            ->default(now()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //Tables\Actions\DeleteBulkAction::make(),
            ]);
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
            'index' => Pages\ListDeposits::route('/'),
            'create' => Pages\CreateDeposit::route('/create'),
            //'edit' => Pages\EditDeposit::route('/{record}/edit'),
        ];
    }
}
