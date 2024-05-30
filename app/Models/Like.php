<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $appended = ['love'];

    public function getLoveAttribute(){
        return $this->count('uuid') > 0;
    }
}
