<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Demande extends Model
{
    use HasFactory;
     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [

        'date',
        'état',
        'type_demande_id',
        'detail',
        'user_id',
        'image_path',
        'estactif'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function type_demande()
    {
        return $this->belongsTo(Type_demande::class);
    }

}
