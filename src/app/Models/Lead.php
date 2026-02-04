<?php
// app/Models/Lead.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = ['plan','name','phone','email','notes','message','lead_token','extras'];
    protected $casts = ['extras' => 'array'];
}
