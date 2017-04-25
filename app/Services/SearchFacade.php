<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class SearchFacade
{
	protected $suppliers = [
		"supplier1" => 'https://api.myjson.com/bins/2tlb8',
		"supplier2" => 'https://api.myjson.com/bins/42lok',
		"supplier3" => 'https://api.myjson.com/bins/15ktg'
	];

    protected $result= [];
    protected $_destination, $_checkin, $_checkout, $_guests, $_suppliers;
    public function __construct($_destination, $_checkin, $_checkout, $_guests) {
        $this->_destination= $_destination;
        $this->_checkout= $_checkout;
        $this->_checkin= $_checkin;
        $this->_guests= $_guests;
    }

    public function getSupplierUrl($supplier) {
        return $this->suppliers[$supplier];
    }
    public function fetchSupplier($supplier,$params) {
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $this->getSupplierUrl($supplier)); 
        //return the transfer as a string 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        // $output contains the output string 
        $output = curl_exec($ch); 
        curl_close($ch); 

        return json_decode($output);
    }

    public function fetchMulipleSuppliers($suppliers = null, $params = null) {
        //Fetch from all suppliers if the users didn't submit the requested suppliers
        if (empty($suppliers)) {
            $suppliers= array_keys($this->suppliers);
        }
        //Check if we already have the results in cache
        if (Cache::has($this->_destination.$this->_checkin.$this->_checkout.$this->_guests)) {
            return Cache::get($this->_destination.$this->_checkin.$this->_checkout.$this->_guests);
        } 
        //otherwise iterate through the suppliers and retrieve one by one
        foreach ($suppliers as $key => $supplier) {
            $output= $this->fetchSupplier($supplier,$params);
            $this->appendResults($output,$supplier);
        }

        //cleanup the output by removing the keys
        $this->result= array_values($this->result);

        //save the result in cache
        Cache::add($this->_destination.$this->_checkin.$this->_checkout.$this->_guests, json_encode($this->result), 5);

        return $this->result;
    }

    public function appendResults($output,$supplier) {
        //Iterate through the supplier results
        foreach ($output as $key => $value) {
            //if we have a new item or another offer with a better price, we save the item
            if ((!array_has($this->result,$key)) || $this->result[$key]["price"]>$value) {
                $this->result[$key]=["id" => $key, "price" => $value, "supplier" => $supplier ];
            }
        }
    }
}
