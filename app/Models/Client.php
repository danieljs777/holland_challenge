<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';

    public $timestamps = false;
    
    // Campos preenchiveis
	protected $fillable = [
	    'name',
	    'checked',
	    'description',
	    'interest',
	    'date_of_birth',
	    'email',
	    'account'
	];



}
