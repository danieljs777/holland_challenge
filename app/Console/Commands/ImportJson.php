<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use DB;
use App\Models\Batch;

class ImportJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:json {file?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import JSON from challenge';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $start = microtime(true);

        $json_file = ($this->argument('file') != '') ? $this->argument('file') : "challenge.json";

        $json_data = file_get_contents($json_file);
        $json_objects = json_decode($json_data);

        $total = count($json_objects);

        $fields = [
            'filename' => $json_file,
            'total' => $total,
            'processed' => 0,
            'finished' => 0
        ];

        $batch = Batch::where(['filename' => $json_file, 'finished' => 0])->first();

        if($batch)
        {
            $i = $batch->processed;
        }
        else
        {
            $batch = Batch::create($fields);
            $i = 0; 
        }

        do
        {

            $object = $json_objects[$i];

            try
            {
                // Age Requirement Checking
                if(strpos($object->date_of_birth, "/") !== FALSE)
                {
                    $object->date_of_birth = \Carbon\Carbon::createFromFormat('d/m/Y', $object->date_of_birth);
                }
                else
                {
                    $object->date_of_birth = \Carbon\Carbon::parse($object->date_of_birth);
                }

                $age = $object->date_of_birth->diffInYears(\Carbon\Carbon::now());

                $age_allowed = ($age >= 18 && $age < 65) ? "OK" : "NOK";

                echo "\n #$i " . $object->name . " - " . $object->date_of_birth->toDateString() . " - " . $age . " - " . $object->credit_card->number;

                // Credit Card Requirement Checking
                $cc_allowed = "NOK";

                $repeating = array();
                preg_match_all('/(\d)(\1+)/', $object->credit_card->number, $repeating);

                if (count($repeating[0]) > 0)
                {
                    foreach ($repeating[0] as $repeat)
                    {
                        if(strlen($repeat) == 3)
                        {
                            $cc_allowed = "OK";
                            break;
                        }
                    }
                }

                if($age_allowed == "NOK" || $cc_allowed == "NOK")
                {
                    $i++;
                    continue;
                }

                DB::beginTransaction();

                $client = $batch->process_client($object, $i);

            }
            catch(\Exception $ex)
            {
                DB::rollback();
                dd($ex);                
            }

            if($client->id)
            {
                $i++;
                DB::commit();
            }
            else
            {
                break;
            }

        }
        while($i < $total);
        
        $batch->update(['finished' => 1]);

        $checkpoint = time();

        echo "########################################### \n" . count($json_objects) . " items processed in " . (time() - $start) . " seconds";

        return;
    }


}
