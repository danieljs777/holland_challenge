<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
   	protected $table = 'addresses';

   	protected $fillable = [
		'address',
		'number',
		'complement',
		'neighborhood',
		'zip_number',
		'state',
		'city',
		'country',
		'reference',
		'type',
		'client_id'
	   ];
}
