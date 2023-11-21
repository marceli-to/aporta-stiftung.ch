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
    // create a slug
    $slug = Str::slug($request->main_tenant_lastname . ' ' . $request->main_tenant_firstname . ' ' . $request->main_tenant_postal_code_city, '-');

    // create an array from the request
    $data = $request->all();

    // save data in a json file
    \Storage::disk('local')->put('registration-' . $slug . '-' . date('d-m-Y-H-i-s') . '.json', json_encode($data));

    return response()->json(200);
  }

}
