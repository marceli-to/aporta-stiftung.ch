<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterAuthRequest;
use App\Http\Requests\RegisterStoreRequest;
use App\Actions\CreateXml;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FormController extends Controller
{

  /**
   * @param RegisterAuthRequest $request 
   * @return \Illuminate\Http\Response
   */

  public function authenticate(RegisterAuthRequest $request)
  {
    $password = $request->input('password');
    $key = json_decode(\Storage::disk('local')->get('key.json'), true);
    foreach ($key as $k => $v)
    {
      if ($v['password'] == $password && $v['used'] == false)
      {
        $key[$k]['used'] = true;
        \Storage::disk('local')->put('key.json', json_encode($key));
        return response()->json(['token' => $v['token']]);
      }
    }
    return response()->json(['error' => 'Unauthorized'], 401);

  }

  /**
   * @param RegisterStoreRequest $request 
   * @return \Illuminate\Http\Response
   */
  public function store(RegisterStoreRequest $request)
  { 

    $this->validateToken($request->token);

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

  public function validateToken($token = NULL)
  {
    if (!$token) {
      return response()->json(['error' => 'Unauthorized'], 401);
    }
    // Validate the request->token with the key.json file
    $key = json_decode(\Storage::disk('local')->get('key.json'), true);
    foreach ($key as $k => $v)
    {
      if ($v['token'] == $token)
      {
        return true;
      }
    }
    response()->json(['error' => 'Unauthorized'], 401);
  }

}
