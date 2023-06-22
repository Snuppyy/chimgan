<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use MoonShine\Models\MoonshineUser;

class Order extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'client_id',
    'user_id',
    'payment_id',
    'date',
    'price',
    'given_bottle',
    'taken_bottle',
    'description',
    'status',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'date' => 'date:d-m-Y',
    'status' => 'bool',
  ];

  public function client(): BelongsTo
  {
    return $this->BelongsTo(Client::class);
  }

  public function payment(): BelongsTo
  {
    return $this->BelongsTo(Payment::class);
  }

  public function user(): BelongsTo
  {
    return $this->BelongsTo(MoonshineUser::class);
  }
}
