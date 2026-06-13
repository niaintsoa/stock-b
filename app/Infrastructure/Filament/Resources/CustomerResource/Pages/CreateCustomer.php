<?php

namespace App\Infrastructure\Filament\Resources\CustomerResource\Pages;

use App\Infrastructure\Filament\Resources\CustomerResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::id();

        return $data;
    }

    protected function afterCreate(): void
    {
        $customer = $this->record;
        $email = $this->data['email'];

        $user = \App\Domain\Entity\User::create([
            'name' => $customer->first_name . ' ' . $customer->last_name,
            'email' => $email,
            'password' => Hash::make(Str::random(16)),
            'profile_id' => $customer->id,
            'profile_type' => get_class($customer),
        ]);

        $token = Password::broker()->createToken($user);
        $user->notify(new \App\Notifications\WelcomeCustomerNotification($token));
    }
}
