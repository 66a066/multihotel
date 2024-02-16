<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorInfo extends Model
{
    use HasFactory;
    protected $fillable = [
        'language_id',
        'vendor_id',
        'name',
        'country',
        'city',
        'state',
        'zip_code',
        'address',
        'details'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
