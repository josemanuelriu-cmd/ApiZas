<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\BoardgameFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Casts\Attribute as CastsAttribute;


class Boardgame extends Model
{
    protected $table = 'boardgames';    
    
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name', 
        'slug', 
        'min_players', 
        'max_players', 
        'min_age', 
        'duration', 
        'description',
        'owner_user_id'
     ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'name' => 'string',
            'min_players' => 'integer',
            'max_players' => 'integer',
            'min_age' => 'integer',
            'duration' => 'integer',
            'description' => 'string',
            'owner_user_id' => 'integer'
        ];
    }

    protected function name(): CastsAttribute
    {
        return CastsAttribute::make(
            get: fn ($value) => ucwords($value), 
            set: fn ($value) => strtolower($value) 
        );
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($boardgame) {
            $boardgame->slug = Str::slug($boardgame->name);
        });
    }
    public function types()
    {
        return $this->belongsToMany(
            Type::class, 
            'boardgame_type', 
            'boardgame_id', 
            'type_id'
        )->withTimestamps();
    }
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }
}
