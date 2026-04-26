<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\TypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Type extends Model
{
    protected $table = 'types';    
    
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'type', 
        'description',
     ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => 'string',
            'description' => 'string',
        ];
    }    
    public function boardgames()
    {
        return $this->belongsToMany(
            Boardgame::class, 
            'boardgame_type', 
            'type_id',
            'boardgame_id'
        )->withTimestamps();
    }    
}