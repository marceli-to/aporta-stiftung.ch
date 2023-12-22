<?php
namespace App\Actions;
use Illuminate\Support\Facades\Http;

class SubmitXml
{
  public function execute()
  {
    // XMLs are stored in /storage/app/xml
    // Get all xml files from storage. Only get files with .xml extension
    $xmls = \Storage::files('xml');

    // Remove non-xml files from the array
    $xmls = array_filter($xmls, function($file) {
      return strpos($file, '.xml') !== false;
    });

    // If there are no xml files, return
    if (count($xmls) == 0)
    {
      return;
    }

    // Get the first file from the array
    $xml = array_shift($xmls);

    // Get the contents of the file
    $xml_data = \Storage::get($xml);

    // Send it to the external server
    $url = 'http://46.14.13.206:443/ID_INTEREST_REQUEST_WEB/awws/ID_Interest_Request.awws';
    $response = Http::withHeaders([
      'Content-Type' => 'text/xml; charset=utf-8',
      'SOAPAction' => 'urn:ID_Interest_Request/SendRequest',
    ])
    ->withBody($xml_data, 'text/xml')
    ->post($url);

    // Get http_code from the response
    $http_code = $response->status();
    
    // If the response is 200, move the file to /storage/app/xml/processed
    if ($http_code == 200)
    {
      \Storage::move($xml, 'xml/processed/' . basename($xml));
      \Mail::raw('The application has been submitted successfully.', function($message) {
        $message->subject('Application submitted');
        $message->to('m@marceli.to');
      });
    }
    else
    {
      \Storage::move($xml, 'xml/failed/' . basename($xml));
      \Mail::raw('The application has been submitted unsuccessfully.', function($message) {
        $message->subject('Application submission failed');
        $message->to('m@marceli.to');
      });
      \Log::error($response->body());
    }
  }
}