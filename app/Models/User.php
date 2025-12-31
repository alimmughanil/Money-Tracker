<?php

namespace App\Models;

use App\Models\Traits\Filterable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use App\Models\Traits\SelectOptions;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
  use HasApiTokens, HasFactory, Notifiable, SoftDeletes, Filterable, SelectOptions;

  protected $fillable = [
    'name',
    'username',
    'email',
    'phone',
    'password',
    'role',
    'picture',
    'status'
  ];
  protected $hidden = [
    'password',
    'remember_token',
  ];

  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
    ];
  }

  public function balance()
  {
    $select = [
      'wallets.user_id',
      DB::raw('SUM(wallets.debit) as debit_total'),
      DB::raw('SUM(wallets.credit) as credit_total'),
    ];

    return $this->hasMany(\App\Models\Wallet::class, 'user_id', 'id')->select($select);
  }

  public function profile()
  {
    return $this->hasOne(\App\Models\Profile::class);
  }

  public function wallet()
  {
    return $this->hasMany(\App\Models\Wallet::class, 'user_id', 'id');
  }

  public function scopePartner($query)
  {
    return $query->where('role', 'partner');
  }

  public function scopeAdminPartner($query)
  {
    return $query->whereIn('role', ['admin', 'partner']);
  }
}
