<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['title', 'slug', 'content', 'type_id', 'is_published'];

    public function getFormatedDate($column, $format = 'd-m-Y')
    {
        return Carbon::create($this->$column)->format($format);
    }

    public function printImage()
    {
        return asset('storage/' . $this->image);
    }

    // Definisco la relazione molti ad uno
    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    // Definisco la relazione many to many con Technologies
    public function technologies()
    {
        return $this->belongsToMany(Technology::class);
    }

    // Creo l'Accessor per modificare l'url delle immagini
    //  (!) Questo verrà eseguito SEMPRE quindi può essere dispendioso
    // public function image()
    // {
    //     return Attribute::make(fn ($value) => $value && app('request')->is('api/*') ? url('storage/' . $value) : $value);
    // }
}
