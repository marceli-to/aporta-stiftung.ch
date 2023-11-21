<?php
namespace App\Actions;
use Illuminate\Support\Str;

class CreateXml
{
  public function execute($json)
  {
    // Decode json
    $json = json_decode($json);

    // Create xml file name
    $xml_filename = \Str::slug($json->main_tenant_lastname . ' ' . $json->main_tenant_firstname . ' ' . $json->main_tenant_postal_code_city, '-');

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
    $uuid = \Str::uuid()->toString();
    $guid = $xml->createElement('_GUID', str_replace('-', '', $uuid));
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
  
      $salutation = $xml->createElement('SALUTATION', $json->main_tenant_salutation);
      $mainTenant->appendChild($salutation);
  
      $lastName = $xml->createElement('LAST_NAME', $json->main_tenant_lastname);
      $mainTenant->appendChild($lastName);
  
      $firstName = $xml->createElement('FIRST_NAME', $json->main_tenant_firstname);
      $mainTenant->appendChild($firstName);
  
      $address = $xml->createElement('ADDRESS');
      $mainTenant->appendChild($address);
  
      $street = $xml->createElement('STREET', $json->main_tenant_street_number);
      $address->appendChild($street);
  
      $postalCodeCity = $xml->createElement('POSTAL_CODE_CITY', $json->main_tenant_postal_code_city);
      $address->appendChild($postalCodeCity);
  
      $birthdate = $xml->createElement('BIRTHDATE', $json->main_tenant_birthdate);
      $mainTenant->appendChild($birthdate);
  
      $maritalStatus = $xml->createElement('MARITAL_STATUS', $json->main_tenant_marital_status);
      $mainTenant->appendChild($maritalStatus);
  
      $nationality = $xml->createElement('NATIONALITY', $json->main_tenant_nationality);
      $mainTenant->appendChild($maritalStatus);
  
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
  
      $workPhone = $xml->createElement('WORK_PHONE', $json->main_tenant_work_phone);
      $mainTenant->appendChild($workPhone);
  
      $employmentSituation = $xml->createElement('EMPLOYMENT_SITUATION', $json->main_tenant_employment_status);
      $mainTenant->appendChild($employmentSituation);
  
      $debtEnforcementYn = $xml->createElement('DEBT_ENFORCEMENT_YN', $json->main_tenant_debt_enforcement_yn);
      $mainTenant->appendChild($debtEnforcementYn);
  
      $currentEmployer = $xml->createElement('CURRENT_EMPLOYER');
      $mainTenant->appendChild($currentEmployer);
  
      $name = $xml->createElement('NAME', $json->main_tenant_current_employer_name);
      $currentEmployer->appendChild($name);
  
      $workload = $xml->createElement('WORKLOAD', $json->main_tenant_workload);
      $mainTenant->appendChild($workload);
  
      $annualIncome = $xml->createElement('ANNUAL_INCOME', $json->main_tenant_annual_income);
      $mainTenant->appendChild($annualIncome);
  
      $currentRent = $xml->createElement('CURRENT_RENT');
      $mainTenant->appendChild($currentRent);
  
      $tenantRole = $xml->createElement('TENANT_ROLE', $json->main_tenant_current_rent_tenant_role);
      $currentRent->appendChild($tenantRole);
  
      $rentTermination = $xml->createElement('RENT_TERMINATION');
      $currentRent->appendChild($rentTermination);
  
      $terminator = $xml->createElement('TERMINATOR', $json->main_tenant_current_rent_terminator);
      $rentTermination->appendChild($terminator);
  
      $reason = $xml->createElement('REASON', $json->main_tenant_current_rent_terminator_reason);
      $rentTermination->appendChild($reason);
  
      $currentRenter = $xml->createElement('CURRENT_RENTER');
      $currentRent->appendChild($currentRenter);
  
      $name = $xml->createElement('NAME', $json->main_tenant_current_renter_name);
      $currentRenter->appendChild($name);
  
      $phone = $xml->createElement('PHONE', $json->main_tenant_current_renter_phone);
      $currentRenter->appendChild($phone);
  
      $rentDuration = $xml->createElement('RENT_DURATION', $json->main_tenant_current_renter_rent_duration);
      $currentRent->appendChild($rentDuration);
  
      $previousRenter = $xml->createElement('PREVIOUS_RENTER', $json->main_tenant_current_renter_previous_renter);
      $currentRent->appendChild($previousRenter);
    }

    if ($json->has_sub_tenant)
    {
      $subTenant = $xml->createElement('SUB_TENANT');
      $interestRequest->appendChild($subTenant);

      $salutation = $xml->createElement('SALUTATION', $json->sub_tenant_salutation);
      $subTenant->appendChild($salutation);

      $lastName = $xml->createElement('LAST_NAME', $json->sub_tenant_lastname);
      $subTenant->appendChild($lastName);

      $firstName = $xml->createElement('FIRST_NAME', $json->sub_tenant_firstname);
      $subTenant->appendChild($firstName);

      $subTenantSameAdressYn = $xml->createElement('SUB_TENANT_SAME_ADRESS_YN', $json->sub_tenant_same_adress);
      $subTenant->appendChild($subTenantSameAdressYn);

      $address = $xml->createElement('ADDRESS');
      $subTenant->appendChild($address);

      $street = $xml->createElement('STREET', $json->sub_tenant_street);
      $address->appendChild($street);

      $postalCodeCity = $xml->createElement('POSTAL_CODE_CITY', $json->sub_tenant_postal_code_city);
      $address->appendChild($postalCodeCity);

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

      $workPhone = $xml->createElement('WORK_PHONE', $json->sub_tenant_work_phone);
      $subTenant->appendChild($workPhone);

      $employmentSituation = $xml->createElement('EMPLOYMENT_SITUATION', $json->sub_tenant_employment_status);
      $subTenant->appendChild($employmentSituation);

      $debtEnforcementYn = $xml->createElement('DEBT_ENFORCEMENT_YN', $json->sub_tenant_debt_enforcement_yn);
      $subTenant->appendChild($debtEnforcementYn);

      $currentEmployer = $xml->createElement('CURRENT_EMPLOYER');
      $subTenant->appendChild($currentEmployer);

      $name = $xml->createElement('NAME', $json->sub_tenant_current_employer_name);
      $currentEmployer->appendChild($name);

      $workload = $xml->createElement('WORKLOAD', $json->sub_tenant_workload);
      $subTenant->appendChild($workload);

      $annualIncome = $xml->createElement('ANNUAL_INCOME', $json->sub_tenant_annual_income);
      $subTenant->appendChild($annualIncome);

      $currentRent = $xml->createElement('CURRENT_RENT');
      $subTenant->appendChild($currentRent);

      $tenantRole = $xml->createElement('TENANT_ROLE', $json->sub_tenant_current_rent_tenant_role);
      $currentRent->appendChild($tenantRole);

      $rentTermination = $xml->createElement('RENT_TERMINATION');
      $currentRent->appendChild($rentTermination);

      $terminator = $xml->createElement('TERMINATOR', $json->sub_tenant_current_rent_terminator);
      $rentTermination->appendChild($terminator);

      $reason = $xml->createElement('REASON', $json->sub_tenant_current_rent_terminator_reason);
      $rentTermination->appendChild($reason);

      $currentRenter = $xml->createElement('CURRENT_RENTER');
      $currentRent->appendChild($currentRenter);

      $name = $xml->createElement('NAME', $json->sub_tenant_current_renter_name);
      $currentRenter->appendChild($name);

      $phone = $xml->createElement('PHONE', $json->sub_tenant_current_renter_phone);
      $currentRenter->appendChild($phone);

      $rentDuration = $xml->createElement('RENT_DURATION', $json->sub_tenant_current_renter_rent_duration);
      $currentRent->appendChild($rentDuration);

      $previousRenter = $xml->createElement('PREVIOUS_RENTER', $json->sub_tenant_current_renter_previous_renter);
      $currentRent->appendChild($previousRenter);

      $relationship = $xml->createElement('RELATIONSHIP');
      $subTenant->appendChild($relationship);
    }

    // RENT_PREFERENCES
    $rentPreferences = $xml->createElement('RENT_PREFERENCES');
    $interestRequest->appendChild($rentPreferences);

    $districtId = $xml->createElement('DISTRICT_ID', implode(', ', $json->rent_pref_district_id));
    $rentPreferences->appendChild($districtId);

    $floorId = $xml->createElement('FLOOR_ID', implode(', ', $json->rent_pref_floor_id));
    $rentPreferences->appendChild($floorId);

    $json->rent_pref_rooms_qty = str_replace('&frac12;', '.5', $json->rent_pref_rooms_qty);
    $roomsQty = $xml->createElement('ROOMS_QTY', implode(', ', $json->rent_pref_rooms_qty));
    $rentPreferences->appendChild($roomsQty);

    $noBalconyYn = $xml->createElement('NO_BALCONY_YN', $json->rent_pref_nobalcony_yn);
    $rentPreferences->appendChild($noBalconyYn);

    $noElevatorYn = $xml->createElement('NO_ELEVATOR_YN', $json->rent_pref_noelevator);
    $rentPreferences->appendChild($noElevatorYn);

    $maxRent = $xml->createElement('MAX_RENT', $json->rent_pref_max_rent);
    $rentPreferences->appendChild($maxRent);

    $minStartDate = $xml->createElement('MIN_START_DATE', $json->rent_pref_min_start_date);
    $rentPreferences->appendChild($minStartDate);

    // ACCOMMODATION
    $accommodation = $xml->createElement('ACCOMMODATION');
    $interestRequest->appendChild($accommodation);

    $totalPersons = $xml->createElement('TOTAL_PERSONS', $json->accomodation_total_persons);
    $accommodation->appendChild($totalPersons);

    $adultsQty = $xml->createElement('ADULTS_QTY', $json->accomodation_adults_qty);
    $accommodation->appendChild($adultsQty);

    $childrenQty = $xml->createElement('CHILDREN_QTY', $json->accomodation_children_qty);
    $accommodation->appendChild($childrenQty);

    $childrenLivingConstantlyQty = $xml->createElement('CHILDREN_LIVING_CONSTANTLY_QTY', $json->accomodation_children_living_constantly);
    $accommodation->appendChild($childrenLivingConstantlyQty);

    // remove newlines, replace with a comma and an empty space
    $childrenAgeGroup = $xml->createElement('CHILDREN_AGE_GROUP', str_replace("\n", ', ', $json->accomodation_children_age_group));
    $accommodation->appendChild($childrenAgeGroup);

    $playMusicYn = $xml->createElement('PLAY_MUSIC_YN', $json->accomodation_play_music_yn);
    $accommodation->appendChild($playMusicYn);

    $musicalInstruments = $xml->createElement('MUSICAL_INSTRUMENTS', $json->accomodation_musical_instruments);
    $accommodation->appendChild($musicalInstruments);

    $petsYn = $xml->createElement('PETS_YN', $json->accomodation_pets_yn);
    $accommodation->appendChild($petsYn);

    $pets = $xml->createElement('PETS', $json->accomodation_pets);
    $accommodation->appendChild($pets);

    $remarks = $xml->createElement('REMARKS', $json->accomodation_remarks);
    $accommodation->appendChild($remarks);

    // save the file to /storage/app/xml, create the folder if it does not exist
    if (!file_exists(storage_path('app/xml')))
    {
      mkdir(storage_path('app/xml'), 0777, true);
    }

    $xml->save(storage_path('app/xml/bewerbung-'.$xml_filename.'.xml'));
  }
}