<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $table = 'batches';

    public $timestamps = false;

	protected $fillable = [
	    'filename',
	    'total',
	    'processed',
	    'finished'
	];

    public function process_client(&$object, $i)    
    {
        $client = Client::create(
        [
            'name' => $object->name,
            'checked' => $object->checked,
            'description' => $object->description,
            'interest' => $object->interest,
            'date_of_birth' => $object->date_of_birth->toDateString(),
            'email' => $object->email,
            'account' => $object->account
        ]);
        
        echo "....... Saved : " . $client->id;
    
        $credit_card = CreditCard::create(
        [
            'name' => $object->credit_card->name,
            'type' => $object->credit_card->type,
            'number' => $object->credit_card->number,
            'exp_date' => $object->credit_card->expirationDate,
            'client_id' => $client->id
        ]);

        $address_split = \VIISON\AddressSplitter\AddressSplitter::splitAddress($object->address);

        $address = Address::create(
        [
            'address' => @$address_split['streetName'],
            'number' => @$address_split['houseNumber'],
            'complement' => '',
            'neighborhood' => '',
            'zip_number' => '',
            'state' => '',
            'city' => @$address_split['additionToAddress1'],
            'country' => @$address_split['additionToAddress2'],
            'reference' => '',
            'client_id' => $client->id,
            'type' => 'B' // Billing Address
        ]);               

        $this->update(['processed' => $i]);


        return $client; 
    }


}
