<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'semester', 'year', 'credits', 'type'];

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function getNotesCountAttribute()
    {
        return $this->notes()->where('is_active', true)->count();
    }
}