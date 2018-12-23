<?php

use Illuminate\Database\Seeder;

use App\Http\Models\Loan;
use App\Http\Models\Cs;

use Faker\Factory as Faker;


class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {	
    	$faker = Faker::create();
    	$loan_data = array();
    	for($i=1;$i<=145;$i++) {
			$define_term	= array(3,6,12);
			$term 			= $define_term[rand(0,2)];
			$status 		= "paid";
			$collection_note= "-";

			for($j=1;$j<=$term;$j++) {
				
				if($status == 'paid') {
					$conditional_installment = rand(1,35);
					if($conditional_installment > 30) {
						$status 			= 'late';

						$reason=array(
							"blacklisted-fraud",
							"financial-problem",
							"next year","next month","run away"
						);

						$collection_note 	= $reason[rand(0,4)];
					}
				}
				

				$data = array(
    			'loan_id'			=>$i,
    			'term'				=>$j,
    			'status'			=>$status,
    			'collection_note'	=>$collection_note
    			);
    			array_push($loan_data,$data);
			}
    		
    	}
    	Loan::insert($loan_data);


    	$cs_data = array();
    	for($i=1;$i<=300;$i++) {

    		$cust_id 	= rand(1,500);
    		$define_cs_id 		= rand(1,50);
    		$status 	= "-";
    		$timestamp  = date('Y-m-d H:i:s', strtotime('-'.rand(1,30).' day'));
    		$iteration  = rand(3,10);

    		for($j=1;$j<=$iteration;$j++) {
    			$timestamp  = date('Y-m-d H:i:s', strtotime('+'.rand(3600,7200).' second',strtotime($timestamp)));
    			if($status == "closed") {
    				$status = "on process";
    				$define_cs_id  = rand(1,50);
    			} else if($j == 1) {
    				$status = "received";
    			} else if ($j == 2){
    				$status = "on process";
    			} 
    			else if($j == $iteration) {
    				$conditional_closed = rand(0,5);

    				if($conditional_closed < 4) {
    					$status = "closed";
    				} else {
						$list_status = array(
						"escalate",
						"waiting for third party",
						);
						$status 	= $list_status[rand(0,1)];
    				}
    			} 
    			else {

    				if($iteration > 4 && $j+3 < $iteration) {
    					$conditional_closed = rand(0,1);

    					if($conditional_closed == 1) {
    						$status = "closed";
    					} else {
    						$list_status = array(
							"escalate",
							"waiting for third party",
							);
    						$status 	= $list_status[rand(0,1)];
    					}
    				} else {
    					$list_status = array(
							"escalate",
							"waiting for third party",
						);
    					$status 	= $list_status[rand(0,1)];
    				}
    			}

    			if($status == "escalate" || $status == "received") {
    				$cs_id = null;
    			} else {
    				$cs_id = $define_cs_id;
    			}
    			$data = array(
    			'ticket_id'		=> $i,
    			'cust_id'		=> $cust_id,
    			'cs_id'			=> $cs_id,
    			'status'		=> $status,
    			//'timestamp'		=> date('Y-m-d H:i:s'),
    			'timestamp'		=> $timestamp
    			);
    			array_push($cs_data,$data);

    			if($status == "escalate") {
    				$define_cs_id  = rand(1,50);
    			}	
    		}
    		
    	}
    	Cs::insert($cs_data);
        // $this->call(UsersTableSeeder::class);
    }
}
