<?php

namespace App\Domain\Entity;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use ApiPlatform\Metadata\ApiResource;

use Filament\Models\Contracts\FilamentUser;

use ApiPlatform\Metadata\QueryParameter;
use ApiPlatform\Laravel\Eloquent\Filter\PartialSearchFilter;
use ApiPlatform\Laravel\Eloquent\Filter\OrderFilter;

#[ApiResource(
    parameters: [
        'name' => new QueryParameter(filter: PartialSearchFilter::class),
        'email' => new QueryParameter(filter: PartialSearchFilter::class),
        'sort[:property]' => new QueryParameter(filter: OrderFilter::class),
    ]
)]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected static function newFactory()
    {
        return \Database\Factories\UserFactory::new();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_id',
        'profile_type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profile()
    {
        return $this->morphTo();
    }

    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return $this->profile_type === Admin::class;
    }
}
