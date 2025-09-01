<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visibility extends Model
{
    public $timestamps = false;
    protected $fillable = ['code'];
}
