<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SearchController extends BaseController
{
	protected $suppliers = [
		"supplier1" => 'https://api.myjson.com/bins/2tlb8',
		"supplier2" => 'https://api.myjson.com/bins/42lok',
		"supplier3" => 'https://api.myjson.com/bins/15ktg'
	];
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function search(Request $request) {
    	//if value exist in cache return directly from cache
    	$cache_key= $request->get('destination').$request->get('checkin').$request->get('checkout').$request->get('guests');
    	if (Cache::has($cache_key)) {
    		return Cache::get($cache_key);
		}
    	// Put requested suppliers into an array
    	if (!empty($request->get('suppliers'))) {
    		$suppliers= explode(',',$request->get('suppliers'));
    	} else {
    		$suppliers= ['supplier1','supplier2'];
    	}
    	$result= [];
    	foreach ($suppliers as $i => $supplier) {
    		//get the endpoint of supplier i
    		//fetch data from supplier i
    		$ch = curl_init(); 
	        curl_setopt($ch, CURLOPT_URL, $this->suppliers[$supplier]); 
	        //return the transfer as a string 
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	        // $output contains the output string 
	        $output = json_decode(curl_exec($ch)); 
	        curl_close($ch); 
	        //append to result and check if already exist
    		
	        foreach ($output as $key => $value) {
	        	if ((!array_has($result,$key)) || $result[$key]["price"]>$value) {
	        		$result[$key]=["id" => $key, "price" => $value, "supplier" => $supplier ];
	        	}
	        }
    		
    	}
    	//cache
    	$result= array_values($result);
    	Cache::add($cache_key, json_encode($result), 5);
    	return $result;
    }
}
