<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class classes extends Model
{
    use HasFactory;
    public $table = 'classes';
    protected $fillable = [
        "id",
        "terms_id",
        "student_id",
        "lesson_id",
        "confirmed"
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(lesson::class);
    }
    public function terms(): BelongsTo
    {
        return $this->belongsTo(term::class);
    }

}
