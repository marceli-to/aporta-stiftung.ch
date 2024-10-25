<?php
namespace App\Actions;
use Illuminate\Support\Str;

class CreateExistingXml
{
  public function execute($json)
  {
    // Decode json
    $json = json_decode($json);

    // Loop over the json and replace special characters (like &, <, >, ", ') with their respective html entities
    foreach ($json as $key => $value)
    {
      // if value is an array, loop over it and replace special characters
      if (is_array($value))
      {
        foreach ($value as $k => $v)
        {
          $json->$key[$k] = htmlspecialchars($v);
        }
      }
      // if value is not an array, replace special characters
      else {
        $json->$key = htmlspecialchars($value);
      }
    }

    // Create xml file name
    $xml_filename = \Str::slug($json->main_tenant_lastname . ' ' . $json->main_tenant_firstname . ' ' . $json->main_tenant_email) . '-' . time();

    $xml = new \DOMDocument();
    $xml->preserveWhiteSpace = false;
    $xml->formatOutput = true;
    
    $envelope = $xml->createElementNS('http://schemas.xmlsoap.org/soap/envelope/', 'soap:Envelope');
    $envelope->setAttribute('xmlns:xsi', 'http://www.w3.org/1999/XMLSchema-instance');
    $envelope->setAttribute('xmlns:xsd', 'http://www.w3.org/1999/XMLSchema');
    $xml->appendChild($envelope);

    $body = $xml->createElement('soap:Body');
    $envelope->appendChild($body);

    // create guid
    $guid = $xml->createElement('_GUID', 'd230611c14e74a8db5ad03ec98199550');
    $guid->setAttribute('xmlns', 'urn:ID_Interest_Request');
    $guid->setAttribute('xsd:type', 'xsd:string');
    $body->appendChild($guid);

    $interestRequest = $xml->createElement('_InterestRequest');
    $interestRequest->setAttribute('xmlns', 'urn:ID_Interest_Request');
    $interestRequest->setAttribute('xsd:type', 's0:INTEREST_REQUEST');
    $body->appendChild($interestRequest);

    if ($json->main_tenant_lastname)
    {
      $mainTenant = $xml->createElement('MAIN_TENANT');
      $interestRequest->appendChild($mainTenant);
      $mainTenant->appendChild($xml->createElement('SALUTATION', $json->main_tenant_salutation));
      $mainTenant->appendChild($xml->createElement('LAST_NAME', $json->main_tenant_lastname));
      $mainTenant->appendChild($xml->createElement('FIRST_NAME', $json->main_tenant_firstname));

      $birthdate = $xml->createElement('BIRTHDATE', $json->main_tenant_birthdate);
      $mainTenant->appendChild($birthdate);

      $maritalStatus = $xml->createElement('MARITAL_STATUS', $json->main_tenant_marital_status);
      $mainTenant->appendChild($maritalStatus);

      $nationality = $xml->createElement('NATIONALITY', $json->main_tenant_nationality);
      $mainTenant->appendChild($nationality);

      $homeTown = $xml->createElement('HOME_TOWN', $json->main_tenant_home_town);
      $mainTenant->appendChild($homeTown);

      $residencePermit = $xml->createElement('RESIDENCE_PERMIT', $json->main_tenant_residence_permit);
      $mainTenant->appendChild($residencePermit);

      $swissResidenceSince = $xml->createElement('SWISS_RESIDENCE_SINCE', $json->main_tenant_swiss_residence_since);
      $mainTenant->appendChild($swissResidenceSince);

      $email = $xml->createElement('EMAIL', $json->main_tenant_email);
      $mainTenant->appendChild($email);

      $privatePhone = $xml->createElement('PRIVATE_PHONE', $json->main_tenant_private_phone);
      $mainTenant->appendChild($privatePhone);

      $occupation = $xml->createElement('OCCUPATION', $json->main_tenant_occupation);
      $mainTenant->appendChild($occupation);

      $employmentSituation = $xml->createElement('EMPLOYMENT_SITUATION', $json->main_tenant_employment_status);
      $mainTenant->appendChild($employmentSituation);

      $currentEmployer = $xml->createElement('CURRENT_EMPLOYER');
      $mainTenant->appendChild($currentEmployer);

      $name = $xml->createElement('NAME', $json->main_tenant_current_employer_name);
      $currentEmployer->appendChild($name);
    }

    $subTenantYN = $xml->createElement('SUB_TENANT_YN', $json->sub_tenant_yn);
    $interestRequest->appendChild($subTenantYN);

    if ($json->has_sub_tenant)
    {
      $subTenantType = $xml->createElement('SUB_TENANT_TYPE', implode(',', $json->sub_tenant_type));
      $interestRequest->appendChild($subTenantType);

      $subTenant = $xml->createElement('SUB_TENANT');
      $interestRequest->appendChild($subTenant);

      $subTenantTypes = [
        1 => 'Ehepartner*in',
        2 => 'Lebenspartner*in mit eingetragener Partnerschaft',
        3 => 'Lebenspartner*in',
        4 => 'Mitbewohner*in',
        5 => 'Kinder',
      ];
      
      // convert sub_tenant_type to string (i.e. 1,2,3 to Ehepartner*in, Lebenspartner*in mit eingetragener Partnerschaft, Lebenspartner*in)
      $relationshipType = '';
      foreach ($json->sub_tenant_type as $type)
      {
        $relationshipType .= $subTenantTypes[$type] . ', ';
      }

      $relationship = $xml->createElement('RELATIONSHIP', $relationshipType);
      $subTenant->appendChild($relationship);

      $salutation = $xml->createElement('SALUTATION', $json->sub_tenant_salutation);
      $subTenant->appendChild($salutation);

      $lastName = $xml->createElement('LAST_NAME', $json->sub_tenant_lastname);
      $subTenant->appendChild($lastName);

      $firstName = $xml->createElement('FIRST_NAME', $json->sub_tenant_firstname);
      $subTenant->appendChild($firstName);

      $birthdate = $xml->createElement('BIRTHDATE', $json->sub_tenant_birthdate);
      $subTenant->appendChild($birthdate);

      $maritalStatus = $xml->createElement('MARITAL_STATUS', $json->sub_tenant_marital_status);
      $subTenant->appendChild($maritalStatus);

      $nationality = $xml->createElement('NATIONALITY', $json->sub_tenant_nationality);
      $subTenant->appendChild($nationality);

      $homeTown = $xml->createElement('HOME_TOWN', $json->sub_tenant_home_town);
      $subTenant->appendChild($homeTown);

      $residencePermit = $xml->createElement('RESIDENCE_PERMIT', $json->sub_tenant_residence_permit);
      $subTenant->appendChild($residencePermit);

      $swissResidenceSince = $xml->createElement('SWISS_RESIDENCE_SINCE', $json->sub_tenant_swiss_residence_since);
      $subTenant->appendChild($swissResidenceSince);

      $email = $xml->createElement('EMAIL', $json->sub_tenant_email);
      $subTenant->appendChild($email);

      $privatePhone = $xml->createElement('PRIVATE_PHONE', $json->sub_tenant_private_phone);
      $subTenant->appendChild($privatePhone);

      $occupation = $xml->createElement('OCCUPATION', $json->sub_tenant_occupation);
      $subTenant->appendChild($occupation);

      $employmentSituation = $xml->createElement('EMPLOYMENT_SITUATION', $json->sub_tenant_employment_status);
      $subTenant->appendChild($employmentSituation);

      $currentEmployer = $xml->createElement('CURRENT_EMPLOYER');
      $subTenant->appendChild($currentEmployer);

      $name = $xml->createElement('NAME', $json->sub_tenant_current_employer_name);
      $currentEmployer->appendChild($name);
    }

    // ACCOMMODATION
    $accommodation = $xml->createElement('ACCOMMODATION');
    $interestRequest->appendChild($accommodation);
    $accommodation->appendChild($xml->createElement('TOTAL_PERSONS', $json->accomodation_total_persons));
    $accommodation->appendChild($xml->createElement('ADULTS_QTY', $json->accomodation_adults_qty));
    $accommodation->appendChild($xml->createElement('CHILDREN_QTY', $json->accomodation_children_qty));
    $accommodation->appendChild($xml->createElement('CHILDREN_AGE_GROUP', str_replace("\n", ', ', $json->accomodation_children_age_group)));
    $accommodation->appendChild($xml->createElement('PETS_YN', $json->accomodation_pets_yn));
    $accommodation->appendChild($xml->createElement('PETS', $json->accomodation_pets));

    $emergencyContact = $json->emergency_contact_firstname . ' ' . $json->emergency_contact_lastname . "\n" . $json->emergency_contact_phone . "\n" . $json->emergency_contact_email;

    $accommodation->appendChild($xml->createElement('REMARKS', $emergencyContact . "\n\n" . $json->remarks));

    // save the file to /storage/app/xml, create the folder if it does not exist
    if (!file_exists(storage_path('app/xml/existing')))
    {
      mkdir(storage_path('app/xml/existing'), 0777, true);
    }

    $xml->save(storage_path('app/xml/existing/anmeldung-bestehende-mietende-' . $xml_filename . '.xml'));
  }
}