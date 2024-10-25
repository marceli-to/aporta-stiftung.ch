<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterAuthRequest;
use App\Http\Requests\RegisterStoreRequest;
use App\Http\Requests\RegisterExistingStoreRequest;
use App\Actions\CreateXml;
use App\Actions\CreateExistingXml;
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
        return response()->json(['token' => $v['token']]);
      }
    }
    return response()->json(['error' => 'Unauthorized'], 401);

  }

  /**
   * @param RegisterAuthRequest $request 
   * @return \Illuminate\Http\Response
   */

   public function authenticateExisting(RegisterAuthRequest $request)
   {
     $password = $request->input('password');
     $key = json_decode(\Storage::disk('local')->get('key-existing.json'), true);
     foreach ($key as $k => $v)
     {
       if ($v['password'] == $password && $v['used'] == false)
       {
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
    $slug = Str::slug($request->main_tenant_lastname . ' ' . $request->main_tenant_firstname . ' ' . $request->main_tenant_postal_code . ' ' . $request->main_tenant_city, '-');

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

  /**
   * @param RegisterExistingStoreRequest $request 
   * @return \Illuminate\Http\Response
   */
  public function storeExisting(RegisterExistingStoreRequest $request)
  { 
    $this->validateToken($request->token, true);

    // Create a slug
    $slug = Str::slug($request->main_tenant_lastname . ' ' . $request->main_tenant_firstname . ' ' . $request->main_tenant_email, '-');

    // Create an array from the request
    $data = $request->all();

    // As backup service, save data in a json file in /storage/app/json, create the folder if it does not exist
    if (!file_exists(storage_path('app/json/existing')))
    {
      mkdir(storage_path('app/json/existing'), 0777, true);
    }

    \Storage::disk('local')->put('json/existing/anmeldung-bestehende-mietende-' . $slug . '-' . date('d-m-Y-H-i-s') . '.json', json_encode($data));

    // Create the xml
    (new CreateExistingXml())->execute(json_encode($data));

    return response()->json(200);
  }

  public function validateToken($token = NULL, $existing = false)
  {
    if (!$token) {
      return response()->json(['error' => 'Unauthorized'], 401);
    }
    // Validate the request->token with the key.json file
    if ($existing)
    {
      $key = json_decode(\Storage::disk('local')->get('key-existing.json'), true);
    }
    else
    {
      $key = json_decode(\Storage::disk('local')->get('key.json'), true);
    }

    foreach ($key as $k => $v)
    {
      if ($v['token'] == $token)
      {
        $key[$k]['used'] = true;
        \Storage::disk('local')->put($existing ? 'key-existing.json' : 'key.json', json_encode($key));
        return true;
      }
    }
    response()->json(['error' => 'Unauthorized'], 401);
  }

}
