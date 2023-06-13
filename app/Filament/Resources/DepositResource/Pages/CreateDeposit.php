<?php

namespace App\Filament\Resources\DepositResource\Pages;

use App\Filament\Resources\DepositResource;
use App\Models\User;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateDeposit extends CreateRecord
{
    protected static string $resource = DepositResource::class;

    /***
     * Save data from $form
     */
    protected function handleRecordCreation(array $data): Model
    {
        // Retrieve the record of selected user to update
        $user = User::findOrFail($data['user_id']);

        // Update the record with the new data
        $user->money = $user->money + $data['amount'];

        // Save the changes to the database
        $user->save();

        return static::getModel()::create($data);
    } // End of save data customization

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}
