<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterStoreRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FormController extends Controller
{
  /**
   * @param RegisterStoreRequest $request 
   * @return \Illuminate\Http\Response
   */
  public function store(RegisterStoreRequest $request)
  { 
    // create a slug out of: 
    // - main_tenant_lastname, main_tenant_firstname, main_tenant_street_number and main_tenant_postal_code_city
    $slug = Str::slug($request->main_tenant_lastname . ' ' . $request->main_tenant_firstname . ' ' . $request->main_tenant_postal_code_city, '-');

    // create an array from the request
    $data = $request->all();

    // save data in a json file /storage/app/registration-{$slug}-{date('d.m.Y, time())}.json
    \Storage::disk('local')->put('registration-' . $slug . '-' . date('d-m-Y-H-i-s') . '.json', json_encode($data));
    
    return response()->json(200);
  }

}
