<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class term extends Model
{
    use HasFactory;
    public $table = 'terms';
    protected $fillable = ["id", "teacher_id", "start_date","end_date"];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "teacher_id");
    }
    public function classes(): hasOne
    {
        return $this->hasOne(classes::class, "terms_id");
    }
}
