<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasUuids; // Automatically generate UUIDs 

    protected $fillable = [ // Define fillable attributes
        'nameProduct',
        'price',
        'status',
        'image'
    ];

    public $incrementing = false; // Declare ID isn't auto-incrementing

    protected $keyType = 'string'; // Declare the data type of the ID as string

    public function newUniqueId(): string
    {
        return (string) Str::uuid7();
    }
}
