<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\SearchRequest;
use App\Services\SearchFacade;

class SearchController extends BaseController
{
	protected $suppliers = [
		"supplier1" => 'https://api.myjson.com/bins/2tlb8',
		"supplier2" => 'https://api.myjson.com/bins/42lok',
		"supplier3" => 'https://api.myjson.com/bins/15ktg'
	];
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function search(SearchRequest $request) {
    	$service= new SearchFacade($request->get('destination'),$request->get('checkin'),$request->get('checkout'),$request->get('guests'));
    	// Put requested suppliers into an array
    	if (!empty($request->get('suppliers'))) {
    		$suppliers= explode(',',$request->get('suppliers'));
            $result= $service->fetchMulipleSuppliers($suppliers);
    	} else {
            $result= $service->fetchMulipleSuppliers();
        }
    	
    	return $result;
    }
}
