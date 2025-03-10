<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class subject extends Model
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    //

    protected $fillable = [
        'id',
        'name',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    public function lesson(): hasMany
    {
        return $this->hasMany(lesson::class);
    }
}
