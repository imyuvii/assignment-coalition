<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Hash;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property array<string> $dates
 */
class User extends Authenticatable
{
    use SoftDeletes;
    use Notifiable;
    use HasFactory;

    public $table = 'users';

    protected $hidden = [
        'remember_token',
        'password',
    ];

    /** @var array<int, string> */
    protected array $dates = [
        'email_verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getIsAdminAttribute(): bool
    {
        return $this->roles()->where('id', 1)->exists();
    }


    public function getEmailVerifiedAtAttribute(?string $value): ?string
    {
        if ($value) {
            $carbonDate = Carbon::createFromFormat('Y-m-d H:i:s', $value);
            return $carbonDate !== false ? $carbonDate->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
        }
        return null;
    }

    public function setEmailVerifiedAtAttribute(?string $value): void
    {
        if ($value) {
            $carbonDate = Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value);
            $this->attributes['email_verified_at'] = $carbonDate !== false ? $carbonDate->format('Y-m-d H:i:s') : null;
        } else {
            $this->attributes['email_verified_at'] = null;
        }
    }

    public function setPasswordAttribute(string $input): void
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * @return BelongsToMany<Role>
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }
}
