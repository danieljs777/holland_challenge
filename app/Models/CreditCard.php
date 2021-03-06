<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditCard extends Model
{
   	protected $table = 'ccs';

    public $timestamps = false;

   	protected $fillable = [
		'name',
		'type',
		'number',
		'exp_date',
		'client_id'
	   ];


}
