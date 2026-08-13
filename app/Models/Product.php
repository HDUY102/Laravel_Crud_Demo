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

    protected $appends = ['image_url']; // Append the imgage_url attribute to the model's array and JSON representations

    public function newUniqueId(): string
    {
        return (string) Str::uuid7();
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        // Nếu chuỗi đã là URL (http...) thì giữ nguyên, ngược lại chuyển thành URL từ storage
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        return asset('storage/' . ltrim($this->image, '/'));
    }
}
