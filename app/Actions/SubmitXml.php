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
    $url = env('XML_SUBMIT_URL');
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

      // Send info mail to the admin
      \Mail::raw('The application has been submitted successfully.', function($message) {
        $message->subject('Application submitted');
        $message->to('m@marceli.to');
      });

      // Send info mail to website owner
      $xml_user_data = [
        'date' => date('d.m.Y', time()),
        'last_name' => $this->getXmlValue($xml_data, 'LAST_NAME'),
        'first_name' => $this->getXmlValue($xml_data, 'FIRST_NAME'),
        'email' => $this->getXmlValue($xml_data, 'EMAIL'),
      ];

      // Mailtext template
      $mailtext = "Bestätigung\n\nAuf der Website der À Porta-Stiftung wurde am %date% eine neue Wohnungsbewerbung von %first_name% %last_name% (%email%) ausgefüllt.\n\nBitte bearbeiten Sie die Wohnungsbewerbung so bald wie möglich.\n\nDanke!";

      // Replace the placeholders with the data from the xml
      foreach ($xml_user_data as $key => $value)
      {
        $mailtext = str_replace("%$key%", $value, $mailtext);
      }

      \Mail::raw($mailtext, function($message) {
        $message->subject('Neue Wohnungsbewerbung von der À Porta-Website');
        $message->to('wohnung@aporta-stiftung.ch');
      });
    }
    else
    {
      \Storage::move($xml, 'xml/failed/' . basename($xml));

      // Send error mail to the admin
      \Mail::raw('The application has been submitted unsuccessfully.', function($message) {
        $message->subject('Application submission failed');
        $message->to('m@marceli.to');
      });
      \Log::error($response->body());
    }
  }

  public function getXmlValue($xml_data, $tag)
  {
    $start_tag = "<$tag>";
    $end_tag = "</$tag>";
    $start_pos = strpos($xml_data, $start_tag);
    $end_pos = strpos($xml_data, $end_tag);
    if ($start_pos === false || $end_pos === false)
    {
      return null;
    }
    $start_pos += strlen($start_tag);
    $value = substr($xml_data, $start_pos, $end_pos - $start_pos);
    return html_entity_decode($value, ENT_QUOTES, 'UTF-8');
  }
}