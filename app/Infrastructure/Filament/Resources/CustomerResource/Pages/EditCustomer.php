<?php

namespace App\Infrastructure\Filament\Resources\CustomerResource\Pages;

use App\Infrastructure\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    protected function afterSave(): void
    {
        $customer = $this->record;
        if (isset($this->data['email'])) {
            $user = $customer->user;
            if ($user) {
                $user->update([
                    'email' => $this->data['email'],
                    'name' => $customer->first_name . ' ' . $customer->last_name,
                ]);
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = Auth::id();

        return $data;
    }
}
