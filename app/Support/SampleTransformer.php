<?php

namespace App\Support;

use Faker\Factory as FakerFactory;
use Illuminate\Support\Facades\Http;

/**
 * Transforms legacy registration JSON (main_tenant_*, sub_tenant_*, …) into the
 * new nested SubmitApplicationRequest contract, and optionally anonymises PII.
 *
 * Shared by the dev autofill endpoint (DevSampleController, one random sample
 * per form load) and the batch seed exporter (applications:export, every file
 * in .sample-data/). Keep the transform logic here so both stay identical.
 */
class SampleTransformer
{
    /**
     * Fetch the live lookup tables used to resolve legacy ids/labels to the
     * new slug enums. Returns null on any failure so callers can bail.
     *
     * @return array<string, array<int, array{slug?: string, label?: string}>>|null
     */
    public function fetchLookups(): ?array
    {
        try {
            $res = Http::acceptJson()->timeout(10)->get(config('aporta.lookups_url'));
            if (! $res->ok()) {
                return null;
            }

            return $res->json();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Replace identifying applicant fields (names, email, phones) with faker
     * data so test/seed data never carries real personal data. Landlord fields
     * and free-text remarks are left as-is.
     */
    public function anonymise(array &$payload): void
    {
        $faker = FakerFactory::create('de_CH');

        $fakeOne = function (array &$applicant) use ($faker) {
            $first = $faker->firstName();
            $last = $faker->lastName();
            $applicant['first_name'] = $first;
            $applicant['last_name'] = $last;
            $applicant['email'] = mb_strtolower(
                preg_replace('/[^a-z]/i', '', $first).'.'.preg_replace('/[^a-z]/i', '', $last).'@example.test'
            );
            // E.164 Swiss numbers with fixed, libphonenumber-valid prefixes.
            // Mobile: 079 (Swisscom). Landline: 044 (Zurich). Randomizing
            // only the area code leads to occasional invalid prefixes
            // (e.g. +4142..., +4170...) that the backend rejects.
            $applicant['mobile_phone'] = '+4179'.$faker->numerify('#######');
            if (! empty($applicant['landline_phone'])) {
                $applicant['landline_phone'] = '+4144'.$faker->numerify('#######');
            }
        };

        if (isset($payload['main_applicant']) && is_array($payload['main_applicant'])) {
            $fakeOne($payload['main_applicant']);
        }
        if (isset($payload['co_applicant']) && is_array($payload['co_applicant'])) {
            $fakeOne($payload['co_applicant']);
        }
    }

    public function transform(array $l, array $lookups): array
    {
        // --- Legacy ID/label → new-slug resolvers ------------------------------
        // Resolve a slug by matching its lookup label against a target label.
        $byLabel = function (string $key, ?string $label) use ($lookups): ?string {
            if ($label === null) {
                return null;
            }
            $target = $this->normLabel($label);
            foreach ($lookups[$key] ?? [] as $entry) {
                if ($this->normLabel($entry['label'] ?? '') === $target) {
                    return $entry['slug'] ?? null;
                }
            }

            return null;
        };

        // Legacy enum tables (rescued from pre-rewrite Register.vue).
        $maritalById = [
            '1' => 'ledig', '2' => 'verheiratet', '3' => 'geschieden',
            '4' => 'verwitwet', '5' => 'aufgelöste Partnerschaft',
            '6' => 'eingetragene Partnerschaft',
        ];
        $employmentById = [
            '1' => 'Angestellt', '2' => 'Student*in', '3' => 'Selbständig',
            '4' => 'Arbeitslos', '5' => 'Im Ruhestand (pensioniert)',
            '6' => 'Familienmanager*in',
        ];
        // NB: the live lookup labels use an EN DASH (U+2013), not a hyphen.
        $incomeById = [
            '1' => "Weniger als 20'000", '2' => "20'000–30'000",
            '3' => "30'000–40'000", '4' => "40'000–50'000",
            '5' => "50'000–60'000", '6' => "60'000–70'000",
            '7' => "70'000–80'000", '8' => "80'000–90'000",
            '9' => "90'000–100'000", '10' => "100'000–120'000",
            '11' => "120'000–140'000", '12' => "Mehr als 140'000",
        ];
        $tenantRoleById = [
            '1' => 'Hauptmieter*in',
            '2' => 'Untermieter*in',
        ];
        $districtById = [
            '4' => 'Kreis 4', '5' => 'Kreis 5', '6' => 'Kreis 6',
            '7' => 'Kreis 7', '8' => 'Kreis 8', '10' => 'Kreis 10',
        ];
        // Backend collapsed floors to two buckets: ground (EG/Hochparterre)
        // and everything above (Obergeschoss). Legacy id 0 = Hochparterre,
        // 1..6 = upper floors → map them onto the two new labels. Duplicates
        // (multiple upper floors → one slug) are deduped below.
        $floorById = [
            '0' => 'EG/Hochparterre', '1' => 'Obergeschoss', '2' => 'Obergeschoss',
            '3' => 'Obergeschoss', '4' => 'Obergeschoss', '5' => 'Obergeschoss',
            '6' => 'Obergeschoss',
        ];
        $relationshipById = [
            '1' => 'Ehepartner*in',
            '2' => 'Lebenspartner*in mit eingetragener Partnerschaft',
            '3' => 'Lebenspartner*in',
            '4' => 'Mitbewohner*in',
        ];

        $idToSlug = function (string $key, array $idLabelMap, $legacyId) use ($byLabel): ?string {
            if ($legacyId === null || $legacyId === '') {
                return null;
            }
            $label = $idLabelMap[(string) $legacyId] ?? null;

            return $byLabel($key, $label);
        };

        // Nationality: legacy values are a mix of 2-letter ISO codes ("CH",
        // "DE") and German labels ("Italien", "Bosnien und Herzegowina").
        // Try the value as a slug first, fall back to label-match.
        $resolveNationality = function ($legacy) use ($lookups, $byLabel): ?string {
            if (! is_string($legacy) || $legacy === '') {
                return null;
            }
            foreach ($lookups['nationalities'] ?? [] as $entry) {
                if (($entry['slug'] ?? null) === $legacy) {
                    return $entry['slug'];
                }
            }

            return $byLabel('nationalities', $legacy);
        };

        $bool01 = function ($v): ?bool {
            if ($v === null || $v === '') {
                return null;
            }

            return in_array((string) $v, ['1', 'true'], true);
        };

        // --- Build applicant blocks --------------------------------------------
        $buildApplicant = function (string $prefix, bool $isMain) use ($l, $byLabel, $idToSlug, $resolveNationality, $maritalById, $employmentById, $incomeById, $tenantRoleById, $bool01) {
            $a = [
                'salutation' => $byLabel('salutations', $l["{$prefix}_salutation"] ?? null),
                'first_name' => $l["{$prefix}_firstname"] ?? null,
                'last_name' => $l["{$prefix}_lastname"] ?? null,
                'birth_date' => $l["{$prefix}_birthdate"] ?? null,
                'marital_status' => $idToSlug('marital_statuses', $maritalById, $l["{$prefix}_marital_status"] ?? null),
                'nationality' => $resolveNationality($l["{$prefix}_nationality"] ?? null),
                'place_of_origin' => $l["{$prefix}_home_town"] ?? null,
                'residence_permit' => $l["{$prefix}_residence_permit"] ?? null,
                'swiss_residence_since' => $l["{$prefix}_swiss_residence_since"] ?? null,
                'mobile_phone' => $l["{$prefix}_private_phone"] ?? null,
                'landline_phone' => $l["{$prefix}_work_phone"] ?? null,
                'email' => $l["{$prefix}_email"] ?? null,
                'occupation' => $l["{$prefix}_occupation"] ?? null,
                'employment_status' => $idToSlug('employment_statuses', $employmentById, $l["{$prefix}_employment_status"] ?? null),
                'debt_enforcement_last_2y' => $bool01($l["{$prefix}_debt_enforcement_yn"] ?? null),
                'employer' => [
                    'name' => $l["{$prefix}_current_employer_name"] ?? null,
                    'workload_percent' => isset($l["{$prefix}_workload"]) && $l["{$prefix}_workload"] !== null && $l["{$prefix}_workload"] !== ''
                        ? (int) $l["{$prefix}_workload"] : null,
                    'annual_income_bracket' => $idToSlug('income_brackets', $incomeById, $l["{$prefix}_annual_income"] ?? null),
                ],
                'current_housing' => [
                    'tenant_role' => $idToSlug('tenant_roles', $tenantRoleById, $l["{$prefix}_current_rent_tenant_role"] ?? null),
                    'terminated_by_landlord' => isset($l["{$prefix}_current_rent_terminator"])
                        ? ((string) $l["{$prefix}_current_rent_terminator"] === '1')
                        : null,
                    'termination_reason' => $l["{$prefix}_current_rent_terminator_reason"] ?? null,
                    'landlord_name' => $l["{$prefix}_current_renter_name"] ?? null,
                    'landlord_contact_person' => $l["{$prefix}_current_renter_contact_person"] ?? null,
                    'landlord_phone' => $l["{$prefix}_current_renter_phone"] ?? null,
                ],
            ];

            // Strip the employer block when not employed — backend rule wants
            // employer absent unless employment_status == 'employed'.
            if ($a['employment_status'] !== 'employed') {
                $a['employer'] = null;
            }

            if ($isMain) {
                $a['street'] = $l['main_tenant_street'] ?? null;
                $a['street_number'] = $l['main_tenant_street_number'] ?? null;
                $a['postal_code'] = $l['main_tenant_postal_code'] ?? null;
                $a['city'] = $l['main_tenant_city'] ?? null;
            }

            return $a;
        };

        $sharesApartment = $bool01($l['sub_tenant_yn'] ?? null) ?? false;

        $subTypes = (array) ($l['sub_tenant_type'] ?? []);
        $subTypes = array_map('strval', $subTypes);
        $hasChildren = in_array('5', $subTypes, true);
        $coRelLegacy = null;
        foreach ($subTypes as $t) {
            if ($t !== '5') {
                $coRelLegacy = $t;
                break;
            }
        }
        $hasCoApplicant = $coRelLegacy !== null;

        $coApplicant = null;
        if ($sharesApartment && $hasCoApplicant) {
            $co = $buildApplicant('sub_tenant', isMain: false);
            $co['relationship_to_main'] = $idToSlug('relationships', $relationshipById, $coRelLegacy);
            $co['same_address_as_main'] = $bool01($l['sub_tenant_same_adress'] ?? null);
            // Address only required when same_address_as_main === false.
            $co['street'] = $l['sub_tenant_street'] ?? null;
            $co['street_number'] = $l['sub_tenant_street_number'] ?? null;
            $co['postal_code'] = $l['sub_tenant_postal_code'] ?? null;
            $co['city'] = $l['sub_tenant_city'] ?? null;
            $coApplicant = $co;
        }

        // --- Housing wish -----------------------------------------------------
        $districts = array_values(array_filter(array_map(
            fn ($id) => $idToSlug('districts', $districtById, $id),
            (array) ($l['rent_pref_district_id'] ?? [])
        )));
        $floors = array_values(array_unique(array_filter(array_map(
            fn ($id) => $idToSlug('floors', $floorById, $id),
            (array) ($l['rent_pref_floor_id'] ?? [])
        ))));
        $noElevator = $bool01($l['rent_pref_noelevator'] ?? null);

        // Earliest move-in: backend requires >= today. Legacy dates are
        // historical, so floor at tomorrow to keep the sample submittable.
        $moveIn = $l['rent_pref_min_start_date'] ?? null;
        if (! $moveIn || strtotime($moveIn) < strtotime('today')) {
            $moveIn = date('Y-m-d', strtotime('+1 day'));
        }

        // Household counts. Legacy fields are sometimes null; reconstruct.
        $adults = (int) ($l['accomodation_adults_qty'] ?? 0);
        if ($adults < 1) {
            $adults = 1 + ($hasCoApplicant ? 1 : 0);
        }
        $childrenCount = (int) ($l['accomodation_children_qty'] ?? 0);
        if ($childrenCount === 0 && $hasChildren) {
            // hasChildren implies at least one but the legacy field may be
            // empty — fall back to children_year_of_birth length.
            $childrenCount = max(1, count((array) ($l['children_year_of_birth'] ?? [])));
        }

        $children = [];
        $years = array_values(array_filter((array) ($l['children_year_of_birth'] ?? [])));
        for ($i = 0; $i < $childrenCount; $i++) {
            $children[] = [
                'position' => $i + 1,
                'birth_year' => isset($years[$i]) ? (int) $years[$i] : (int) date('Y') - 5,
            ];
        }

        return [
            'shares_apartment' => $sharesApartment,
            'main_applicant' => $buildApplicant('main_tenant', isMain: true),
            'co_applicant' => $coApplicant,
            'housing_wish' => [
                'earliest_move_in' => $moveIn,
                'max_gross_rent' => isset($l['rent_pref_max_rent']) && $l['rent_pref_max_rent'] !== ''
                    ? number_format((float) $l['rent_pref_max_rent'], 2, '.', '')
                    : null,
                'districts' => $districts,
                'floors' => $floors,
                'wants_elevator' => $noElevator === null ? null : ! $noElevator,
            ],
            'household_info' => [
                'total_persons' => $adults + $childrenCount,
                'adults_count' => $adults,
                'children_count' => $childrenCount,
                'all_children_live_constantly' => $bool01($l['accomodation_children_living_constantly'] ?? null),
                'has_pets' => $bool01($l['accomodation_pets_yn'] ?? null) ?? false,
                'pets_description' => $l['accomodation_pets'] ?? null,
                'remarks' => $l['accomodation_remarks'] ?? null,
            ],
            'children' => $children,
        ];
    }

    private function normLabel(string $s): string
    {
        $s = html_entity_decode($s, ENT_HTML5 | ENT_QUOTES, 'UTF-8');
        $s = trim(preg_replace('/\s+/', ' ', $s));

        return mb_strtolower($s);
    }
}
