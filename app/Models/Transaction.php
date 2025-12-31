<?php

namespace App\Models;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\Traits\Filterable;
use App\Models\Traits\FilterRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
  use HasFactory, SoftDeletes, Filterable, FilterRole;

  protected $guarded = ['id'];

  protected $casts = [
    'type' => TransactionType::class,
    'date' => 'date',
    'items' => 'array',
    'amount' => 'integer',
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function category()
  {
    return $this->belongsTo(Category::class);
  }
}
