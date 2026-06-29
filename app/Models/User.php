<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password', 'dni', 'institutional_code', 
        'faculty', 'career', 'entry_year', 'max_books', 
        'loan_days', 'total_debt', 'is_active'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;
    use HasRoles;

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

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    public function loans() {
        return $this->hasMany(Loan::class);
    }

    public function fines() {
        return $this->hasMany(Fine::class);
    }

    public function reservations() {
        return $this->hasMany(Reservation::class);
    }
    // Suma dinámicamente las multas pendientes de la tabla fines
    public function getRealDebtAttribute(): float
    {
        // Deuda ya registrada
        $registeredDebt = $this->total_debt;

        // Multa acumulada de préstamos activos y vencidos aún no devueltos
        $pendingFines = $this->loans()
            ->where('status', 'active')
            ->where('return_date', '<', now())
            ->get()
            ->sum(function ($loan) {
                $dueDate = \Carbon\Carbon::parse($loan->return_date)->startOfDay();
                $today = \Carbon\Carbon::now('America/Lima')->startOfDay();
                return $dueDate->diffInDays($today) * 0.50;
            });

        return $registeredDebt + $pendingFines;
    }
}
