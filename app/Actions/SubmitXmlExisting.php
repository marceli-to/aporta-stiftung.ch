<?php
namespace App\Actions;
use Illuminate\Support\Facades\Http;

class SubmitXmlExisting
{
  public function execute()
  {
    // XMLs are stored in /storage/app/xml/existing
    // Get all xml files from storage. Only get files with .xml extension
    $xmls = \Storage::files('xml/existing');

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
      \Mail::raw('The data for an existing tenant has been submitted successfully.', function($message) {
        $message->subject('Data for existing tenant submitted');
        $message->to(env('MAIL_TO_LOG'));
      });

      // Send info mail to website owner
      $xml_user_data = [
        'date' => date('d.m.Y', time()),
        'last_name' => $this->getXmlValue($xml_data, 'LAST_NAME'),
        'first_name' => $this->getXmlValue($xml_data, 'FIRST_NAME'),
        'email' => $this->getXmlValue($xml_data, 'EMAIL'),
      ];

      // Mailtext template for mail to the website owner
      $mailtext = "Bestätigung\n\nAuf der Website der à Porta-Stiftung wurde am %date% das Formular für bestehende Mietende von %first_name% %last_name% (%email%) ausgefüllt.";

      // Replace the placeholders with the data from the xml
      foreach ($xml_user_data as $key => $value)
      {
        $mailtext = str_replace("%$key%", $value, $mailtext);
      }

      \Mail::raw($mailtext, function($message) {
        $message->subject('Es wurden Daten bestehender Mieter eingereicht');
        $message->to(env('MAIL_TO_OWNER'));
      });

      // Mailtext template for mail to the user
      // $mailtext_user = "Vielen Dank für Ihre Angaben.\n\nMit dem Einreichen des Formulars bekunden Sie Ihr Interesse für eine Wohnung und wir nehmen Sie in unsere Datenbank auf. Sollten wir ein Wohnungsangebot für Sie haben, melden wir uns bei Ihnen. Haben Sie bitte Verständnis, dass wir nicht allen Interessierten eine Wohnung anbieten können.\n\nBitte beachten Sie, dass die Anmeldung nur 6 Monate gültig ist und danach automatisch gelöscht wird. Sollten Sie nach Ablauf dieser Frist weiterhin eine Wohnung suchen, bitte wir Sie, die Anmeldung rechtzeitig zu verlängern. Bitte schreiben Sie uns dazu eine E-Mail an: wohnung@aporta-stiftung.ch. Wir werden Ihre Anmeldung dann um weitere 6 Monate verlängern.\n\nFreundliche Grüsse\n\nDr. Stephan à Porta-Stiftung";

      // \Mail::raw($mailtext_user, function($message) use ($xml_user_data) {
      //   $message->subject('Vielen Dank für Ihre Anfrage');
      //   $message->to($xml_user_data['email']);
      // });
    }
    else
    {
      \Storage::move($xml, 'xml/failed/' . basename($xml));

      // Send error mail to the admin
      \Mail::raw('The application has been submitted unsuccessfully.', function($message) {
        $message->subject('Application submission failed');
        $message->to(env('MAIL_TO_LOG'));
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