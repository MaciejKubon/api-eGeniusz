<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class lesson extends Model
{
    use HasFactory;
    public $table = 'lessons';
    protected $fillable = ["id", "teacher_id","subject_id","subject_level_id","price"];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    public function subject(): BelongsTo
    {
        return $this->belongsTo(subject::class, "subject_id");
    }
    public function subjectLevel(): BelongsTo
    {
        return $this->belongsTo(subjectLevel::class,'subject_level_id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "teacher_id");
    }
    public function classes(): HasMany
    {
        return  $this->hasMany(Classes::class, "lesson_id");
    }
}
