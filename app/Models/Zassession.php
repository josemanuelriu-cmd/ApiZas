<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\ZassessionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute as CastsAttribute;


class Zassession extends Model
{
    protected $table = 'zassessions';    
    
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'event_name',
        'date',
        'start_time',
        'end_time',
        'max_users',
        'direction',
        'latitude',
        'longitude',
    ];

    protected function direction(): CastsAttribute
    {
        return CastsAttribute::make(
            get: fn ($value) => ucwords($value), 
            set: fn ($value) => strtolower($value) 
        );
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'name' => 'string',
            'event_name' => 'string',
            'date' => 'date:Y-m-d',
            'start_time' => 'datetime:H:i:s',
            'end_time' => 'datetime:H:i:s',
            'max_users' => 'integer',
            'direction' => 'string',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }
    public function users()
    {
        return $this->belongsToMany(
            User::class, 
            'user_zassession',             
            'zassession_id',
            'user_id', 
        );
    }

    /*
    public function games()
    {
        return $this->hasMany(
            Game::class, 
            'zassession_id'
        );
    }
    */
}
