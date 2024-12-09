<?php

namespace App\Modules\Listing\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListingController extends Controller
{

    /**
     * Display the module welcome screen
     *
     * @return \Illuminate\Http\Response
     */
    public function welcome()
    {
        return view("Listing::welcome");
    }

    public function PilgrimListing(Request $request){
        $data = array();
        //$public_html = strval(view("Listing::application-form", $data));
        //return response()->json(['responseCode' => 1, 'html' => $public_html]);

        return view("Listing::application-form", $data);
    }
}
