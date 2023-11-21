<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterStoreRequest;
use App\Actions\CreateXml;
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
    // Create a slug
    $slug = Str::slug($request->main_tenant_lastname . ' ' . $request->main_tenant_firstname . ' ' . $request->main_tenant_postal_code_city, '-');

    // Create an array from the request
    $data = $request->all();

    // As backup service, save data in a json file in /storage/app/json, create the folder if it does not exist
    if (!file_exists(storage_path('app/json')))
    {
      mkdir(storage_path('app/json'), 0777, true);
    }
    \Storage::disk('local')->put('json/registration-' . $slug . '-' . date('d-m-Y-H-i-s') . '.json', json_encode($data));

    // Create the xml
    (new CreateXml())->execute(json_encode($data));

    return response()->json(200);
  }

}
