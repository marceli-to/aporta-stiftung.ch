<template>
<section class="content w-full py-48 !pt-0">
  <div class="max-w-page mx-auto px-15 md:px-45 xl:px-60">

  <!-- Loading state while lookups are fetched. -->
  <template v-if="lookups.loading && !lookups.ready">
    <h2>Formular wird geladen…</h2>
  </template>

  <!-- Hard error: lookups fetch failed. Never render the form with empty dropdowns. -->
  <template v-else-if="lookups.error">
    <h2>Das Anmeldeformular ist momentan nicht verfügbar.</h2>
    <p class="text-lg mt-15">Bitte versuchen Sie es in ein paar Minuten erneut.</p>
  </template>

  <template v-else-if="lookups.ready">

  <template v-if="isAuthenticated">
    <template v-if="isSent">
      <feedback />
    </template>
    <template v-else>
      <div ref="validationErrors">
        <validation-errors :validationErrors="validationErrors" v-if="validationErrors.length > 0" />
      </div>
      <form>

        <h2>Personendaten</h2>
        <form-grid>
          <div class="sm:col-span-12 sm:grid sm:grid-cols-12 sm:gap-30">
            <form-group :error="hasError('main_applicant.salutation')">
              <form-label :error="hasError('main_applicant.salutation')">Anrede</form-label>
              <form-select
                v-model="form.main_applicant.salutation"
                :options="optionsFor('salutations')"
                :error="hasError('main_applicant.salutation')"
                @focus="removeError('main_applicant.salutation')">
              </form-select>
            </form-group>
          </div>
          <form-group :error="hasError('main_applicant.last_name')">
            <form-label :error="hasError('main_applicant.last_name')">Familienname</form-label>
            <form-input type="text" v-model="form.main_applicant.last_name"
              :error="hasError('main_applicant.last_name')"
              @focus="removeError('main_applicant.last_name')">
            </form-input>
          </form-group>
          <form-group :error="hasError('main_applicant.first_name')">
            <form-label :error="hasError('main_applicant.first_name')">Vorname</form-label>
            <form-input type="text" v-model="form.main_applicant.first_name"
              :error="hasError('main_applicant.first_name')"
              @focus="removeError('main_applicant.first_name')">
            </form-input>
          </form-group>
          <form-group :error="hasError('main_applicant.street')">
            <form-label :error="hasError('main_applicant.street')">Strasse</form-label>
            <form-input type="text" v-model="form.main_applicant.street"
              :error="hasError('main_applicant.street')"
              @focus="removeError('main_applicant.street')">
            </form-input>
          </form-group>
          <form-group :error="hasError('main_applicant.street_number')">
            <form-label :error="hasError('main_applicant.street_number')">Hausnummer</form-label>
            <form-input type="text" v-model="form.main_applicant.street_number"
              :error="hasError('main_applicant.street_number')"
              @focus="removeError('main_applicant.street_number')">
            </form-input>
          </form-group>
          <form-group :error="hasError('main_applicant.postal_code')">
            <form-label :error="hasError('main_applicant.postal_code')">PLZ</form-label>
            <form-input type="text" v-model="form.main_applicant.postal_code"
              :error="hasError('main_applicant.postal_code')"
              @focus="removeError('main_applicant.postal_code')">
            </form-input>
          </form-group>
          <form-group :error="hasError('main_applicant.city')">
            <form-label :error="hasError('main_applicant.city')">Ort</form-label>
            <form-input type="text" v-model="form.main_applicant.city"
              :error="hasError('main_applicant.city')"
              @focus="removeError('main_applicant.city')">
            </form-input>
          </form-group>
          <form-group :error="hasError('main_applicant.birth_date')">
            <form-label :error="hasError('main_applicant.birth_date')">Geburtsdatum</form-label>
            <form-input type="date" v-model="form.main_applicant.birth_date" placeholder="TT.MM.JJJJ"
              :error="hasError('main_applicant.birth_date')"
              @focus="removeError('main_applicant.birth_date')">
            </form-input>
          </form-group>
          <form-group :error="hasError('main_applicant.marital_status')">
            <form-label :error="hasError('main_applicant.marital_status')">Familienstand</form-label>
            <form-select v-model="form.main_applicant.marital_status"
              :options="optionsFor('marital_statuses')"
              :error="hasError('main_applicant.marital_status')"
              @focus="removeError('main_applicant.marital_status')">
            </form-select>
          </form-group>
          <form-group :error="hasError('main_applicant.nationality')">
            <form-label :error="hasError('main_applicant.nationality')">Nationalität</form-label>
            <form-select v-model="form.main_applicant.nationality"
              :options="optionsFor('nationalities')"
              :error="hasError('main_applicant.nationality')"
              @focus="removeError('main_applicant.nationality')">
            </form-select>
          </form-group>
          <form-group :error="hasError('main_applicant.place_of_origin')">
            <template v-if="form.main_applicant.nationality == 'CH'">
              <form-label :error="hasError('main_applicant.place_of_origin')">Heimatort</form-label>
              <form-input type="text" v-model="form.main_applicant.place_of_origin"
                :error="hasError('main_applicant.place_of_origin')"
                @focus="removeError('main_applicant.place_of_origin')">
              </form-input>
            </template>
          </form-group>
          <template v-if="form.main_applicant.nationality && form.main_applicant.nationality !== 'CH'">
            <form-group :error="hasError('main_applicant.residence_permit')">
              <form-label :error="hasError('main_applicant.residence_permit')">Niederlassungsbewilligung</form-label>
              <form-select v-model="form.main_applicant.residence_permit"
                :options="optionsFor('residence_permits')"
                :error="hasError('main_applicant.residence_permit')"
                @focus="removeError('main_applicant.residence_permit')">
              </form-select>
            </form-group>
            <form-group :error="hasError('main_applicant.swiss_residence_since')">
              <form-label :error="hasError('main_applicant.swiss_residence_since')">In der Schweiz wohnhaft seit</form-label>
              <form-input type="date" v-model="form.main_applicant.swiss_residence_since" placeholder="TT.MM.JJJJ"
                :error="hasError('main_applicant.swiss_residence_since')"
                @focus="removeError('main_applicant.swiss_residence_since')">
              </form-input>
            </form-group>
          </template>
          <form-group :error="hasError('main_applicant.mobile_phone')">
            <form-label :error="hasError('main_applicant.mobile_phone')">Mobiltelefon</form-label>
            <form-input type="text" v-model="form.main_applicant.mobile_phone"
              :error="hasError('main_applicant.mobile_phone')"
              @focus="removeError('main_applicant.mobile_phone')">
            </form-input>
          </form-group>
          <form-group>
            <form-label :required="false">Telefon (Festnetz)</form-label>
            <form-input type="text" v-model="form.main_applicant.landline_phone"></form-input>
          </form-group>
          <form-group :error="hasError('main_applicant.email')">
            <form-label :error="hasError('main_applicant.email')">E-Mail-Adresse</form-label>
            <form-input type="email" v-model="form.main_applicant.email"
              :error="hasError('main_applicant.email')"
              @focus="removeError('main_applicant.email')">
            </form-input>
          </form-group>
          <form-group :error="hasError('main_applicant.occupation')">
            <form-label :error="hasError('main_applicant.occupation')">Beruf</form-label>
            <form-input type="text" v-model="form.main_applicant.occupation"
              :error="hasError('main_applicant.occupation')"
              @focus="removeError('main_applicant.occupation')">
            </form-input>
          </form-group>
          <form-group :error="hasError('main_applicant.employment_status')">
            <form-label :error="hasError('main_applicant.employment_status')">Erwerbssituation</form-label>
            <form-select v-model="form.main_applicant.employment_status"
              :options="optionsFor('employment_statuses')"
              :error="hasError('main_applicant.employment_status')"
              @focus="removeError('main_applicant.employment_status')">
            </form-select>
          </form-group>
          <form-group :error="hasError('main_applicant.debt_enforcement_last_2y')">
            <form-label :error="hasError('main_applicant.debt_enforcement_last_2y')">Betreibungen/Verlustscheine (letzte 2 Jahre)</form-label>
            <form-select v-model="form.main_applicant.debt_enforcement_last_2y"
              :options="boolOptionsDebt"
              :error="hasError('main_applicant.debt_enforcement_last_2y')"
              @focus="removeError('main_applicant.debt_enforcement_last_2y')">
            </form-select>
          </form-group>
          <template v-if="form.main_applicant.employment_status === 'employed'">
            <div class="!col-span-12 !mt-15 md:!mt-30">
              <h2>Aktueller Arbeitgeber</h2>
              <div class="sm:grid sm:grid-cols-12 gap-30 mb-30">
                <form-group :error="hasError('main_applicant.employer.name')">
                  <form-label :error="hasError('main_applicant.employer.name')">Aktuelle*r Arbeitgeber*in</form-label>
                  <form-input type="text" v-model="form.main_applicant.employer.name"
                    :error="hasError('main_applicant.employer.name')"
                    @focus="removeError('main_applicant.employer.name')">
                  </form-input>
                </form-group>
              </div>
              <div class="sm:grid sm:grid-cols-12 gap-30">
                <form-group :error="hasError('main_applicant.employer.workload_percent')">
                  <form-label :error="hasError('main_applicant.employer.workload_percent')">Arbeitspensum (in Prozent)</form-label>
                  <form-input type="number" v-model="form.main_applicant.employer.workload_percent"
                    :error="hasError('main_applicant.employer.workload_percent')"
                    @focus="removeError('main_applicant.employer.workload_percent')">
                  </form-input>
                </form-group>
                <form-group :error="hasError('main_applicant.employer.annual_income_bracket')">
                  <form-label :error="hasError('main_applicant.employer.annual_income_bracket')">Jahreseinkommen (in CHF)</form-label>
                  <form-select v-model="form.main_applicant.employer.annual_income_bracket"
                    :options="optionsFor('income_brackets')"
                    :error="hasError('main_applicant.employer.annual_income_bracket')"
                    @focus="removeError('main_applicant.employer.annual_income_bracket')">
                  </form-select>
                </form-group>
              </div>
            </div>
          </template>
        </form-grid>

        <h2 class="!mt-35 md:!mt-70">Aktuelle Wohnsituation</h2>
        <form-grid>
          <form-group :error="hasError('main_applicant.current_housing.tenant_role')">
            <form-label :error="hasError('main_applicant.current_housing.tenant_role')">Aktuelle Wohnsituation</form-label>
            <form-select v-model="form.main_applicant.current_housing.tenant_role"
              :options="optionsFor('tenant_roles')"
              :error="hasError('main_applicant.current_housing.tenant_role')"
              @focus="removeError('main_applicant.current_housing.tenant_role')">
            </form-select>
          </form-group>
          <form-group :error="hasError('main_applicant.current_housing.terminated_by_landlord')">
            <form-label :error="hasError('main_applicant.current_housing.terminated_by_landlord')">Wurde das aktuelle Mietverhältnis durch den Vermieter*in gekündigt?</form-label>
            <form-select v-model="form.main_applicant.current_housing.terminated_by_landlord"
              :options="boolOptions"
              :error="hasError('main_applicant.current_housing.terminated_by_landlord')"
              @focus="removeError('main_applicant.current_housing.terminated_by_landlord')">
            </form-select>
          </form-group>
          <template v-if="form.main_applicant.current_housing.terminated_by_landlord === true">
            <form-group class="!col-span-12" :error="hasError('main_applicant.current_housing.termination_reason')">
              <form-label :error="hasError('main_applicant.current_housing.termination_reason')">Geben Sie bitte den Grund an, weshalb Ihnen Ihr*e Vermieter*in gekündigt hat:</form-label>
              <form-textarea v-model="form.main_applicant.current_housing.termination_reason"
                :error="hasError('main_applicant.current_housing.termination_reason')"
                @focus="removeError('main_applicant.current_housing.termination_reason')">
              </form-textarea>
            </form-group>
          </template>
          <form-group :error="hasError('main_applicant.current_housing.landlord_name')">
            <form-label :error="hasError('main_applicant.current_housing.landlord_name')">Aktuelle*r Vermieter*in</form-label>
            <form-input type="text" v-model="form.main_applicant.current_housing.landlord_name"
              :error="hasError('main_applicant.current_housing.landlord_name')"
              @focus="removeError('main_applicant.current_housing.landlord_name')">
            </form-input>
          </form-group>
          <form-group>
            <form-label :required="false">Vermieter*in Kontaktperson</form-label>
            <form-input type="text" v-model="form.main_applicant.current_housing.landlord_contact_person"></form-input>
          </form-group>
          <form-group>
            <form-label :required="false">Vermieter*in Telefon</form-label>
            <form-input type="text" v-model="form.main_applicant.current_housing.landlord_phone"></form-input>
          </form-group>
          <form-group :error="hasError('main_applicant.current_housing.rent_duration')">
            <form-label :error="hasError('main_applicant.current_housing.rent_duration')">Wie lange leben Sie in der aktuellen Wohnung?</form-label>
            <form-select v-model="form.main_applicant.current_housing.rent_duration"
              :options="optionsFor('rent_durations')"
              :error="hasError('main_applicant.current_housing.rent_duration')"
              @focus="removeError('main_applicant.current_housing.rent_duration')">
            </form-select>
          </form-group>
          <template v-if="form.main_applicant.current_housing.rent_duration === 'less_than_1_year'">
            <form-group class="!col-span-12" :error="hasError('main_applicant.current_housing.previous_landlord')">
              <form-label :error="hasError('main_applicant.current_housing.previous_landlord')">Geben Sie bitte Name und Telefonnummer des/der früheren Vermieters/Vermieterin an.</form-label>
              <form-input type="text" v-model="form.main_applicant.current_housing.previous_landlord"
                :error="hasError('main_applicant.current_housing.previous_landlord')"
                @focus="removeError('main_applicant.current_housing.previous_landlord')">
              </form-input>
            </form-group>
          </template>
        </form-grid>

        <h2 class="!mt-35 md:!mt-70">Weitere Person</h2>
        <form-grid>
          <form-group :error="hasError('shares_apartment')">
            <form-label :error="hasError('shares_apartment')">Werden Sie die Wohnung teilen?</form-label>
            <form-select v-model="form.shares_apartment"
              :options="boolOptions"
              :error="hasError('shares_apartment')"
              @focus="removeError('shares_apartment')">
            </form-select>
          </form-group>
          <template v-if="form.shares_apartment === true">
            <form-grid class="col-span-12 !mb-0">
              <form-group class="!col-span-12">
                <form-label class="mb-12 xl:mb-16">Mit wem werden Sie die Wohnung teilen?</form-label>
                <div class="grid grid-cols-12 gap-30">
                  <form-group class="!col-span-12 md:!col-span-6 xl:!col-span-4" v-for="(type, index) in sharedWithOptions" :key="type.value">
                    <form-checkbox
                      :id="`shared_with_${index}`"
                      v-model="sharedWith"
                      :value="type.value"
                      @update="updateSharedWith($event)">
                      <template v-slot:label>{{ type.label }}</template>
                    </form-checkbox>
                  </form-group>
                </div>
              </form-group>
            </form-grid>
          </template>
        </form-grid>

        <template v-if="form.shares_apartment === true && hasChildren === true">
          <h2 class="!mt-35 md:!mt-70">Aufteilung Erwachsene/Kinder</h2>
          <form-grid>
            <form-group :error="hasError('household_info.adults_count')">
              <form-label :error="hasError('household_info.adults_count')">Anzahl Erwachsene</form-label>
              <form-input type="number" v-model="form.household_info.adults_count"
                :error="hasError('household_info.adults_count')"
                @focus="removeError('household_info.adults_count')">
              </form-input>
            </form-group>
            <form-group :error="hasError('household_info.children_count')">
              <form-label :error="hasError('household_info.children_count')">Anzahl Kinder</form-label>
              <form-input type="number" v-model="form.household_info.children_count"
                :error="hasError('household_info.children_count')"
                @focus="removeError('household_info.children_count')">
              </form-input>
            </form-group>
            <form-group :error="hasError('household_info.all_children_live_constantly')" v-if="parseInt(form.household_info.children_count) > 0">
              <form-label :error="hasError('household_info.all_children_live_constantly')">Leben alle Kinder ständig mit Ihnen zusammen?</form-label>
              <form-select v-model="form.household_info.all_children_live_constantly"
                :options="boolOptions"
                :error="hasError('household_info.all_children_live_constantly')"
                @focus="removeError('household_info.all_children_live_constantly')">
              </form-select>
            </form-group>
            <template v-for="(child, idx) in form.children" :key="idx">
              <form-group :error="hasError(`children.${idx}.birth_year`)">
                <form-label :error="hasError(`children.${idx}.birth_year`)">Jahrgang Kind {{ child.position }}</form-label>
                <form-input type="number" v-model="child.birth_year"
                  :error="hasError(`children.${idx}.birth_year`)"
                  @focus="removeError(`children.${idx}.birth_year`)">
                </form-input>
              </form-group>
            </template>
          </form-grid>
        </template>

        <template v-if="form.shares_apartment === true && hasCoApplicant === true">
          <h2 class="!mt-35 md:!mt-70">Angaben zur weiteren erwachsenen Person</h2>
          <form-grid>
            <div class="sm:col-span-12 sm:grid sm:grid-cols-12 sm:gap-30">
              <form-group :error="hasError('co_applicant.salutation')">
                <form-label :error="hasError('co_applicant.salutation')">Anrede</form-label>
                <form-select v-model="form.co_applicant.salutation"
                  :options="optionsFor('salutations')"
                  :error="hasError('co_applicant.salutation')"
                  @focus="removeError('co_applicant.salutation')">
                </form-select>
              </form-group>
            </div>
            <form-group :error="hasError('co_applicant.last_name')">
              <form-label :error="hasError('co_applicant.last_name')">Familienname</form-label>
              <form-input type="text" v-model="form.co_applicant.last_name"
                :error="hasError('co_applicant.last_name')"
                @focus="removeError('co_applicant.last_name')">
              </form-input>
            </form-group>
            <form-group :error="hasError('co_applicant.first_name')">
              <form-label :error="hasError('co_applicant.first_name')">Vorname</form-label>
              <form-input type="text" v-model="form.co_applicant.first_name"
                :error="hasError('co_applicant.first_name')"
                @focus="removeError('co_applicant.first_name')">
              </form-input>
            </form-group>
            <div class="sm:col-span-12 sm:grid sm:grid-cols-12 sm:gap-30">
              <form-group :error="hasError('co_applicant.same_address_as_main')">
                <form-label :error="hasError('co_applicant.same_address_as_main')">Aktuell gleiche Adresse wie Hauptperson?</form-label>
                <form-select v-model="form.co_applicant.same_address_as_main"
                  :options="boolOptions"
                  :error="hasError('co_applicant.same_address_as_main')"
                  @focus="removeError('co_applicant.same_address_as_main')">
                </form-select>
              </form-group>
            </div>
            <template v-if="form.co_applicant.same_address_as_main === false">
              <form-group :error="hasError('co_applicant.street')">
                <form-label :error="hasError('co_applicant.street')">Strasse</form-label>
                <form-input type="text" v-model="form.co_applicant.street"
                  :error="hasError('co_applicant.street')"
                  @focus="removeError('co_applicant.street')">
                </form-input>
              </form-group>
              <form-group :error="hasError('co_applicant.street_number')">
                <form-label :error="hasError('co_applicant.street_number')">Hausnummer</form-label>
                <form-input type="text" v-model="form.co_applicant.street_number"
                  :error="hasError('co_applicant.street_number')"
                  @focus="removeError('co_applicant.street_number')">
                </form-input>
              </form-group>
              <form-group :error="hasError('co_applicant.postal_code')">
                <form-label :error="hasError('co_applicant.postal_code')">PLZ</form-label>
                <form-input type="text" v-model="form.co_applicant.postal_code"
                  :error="hasError('co_applicant.postal_code')"
                  @focus="removeError('co_applicant.postal_code')">
                </form-input>
              </form-group>
              <form-group :error="hasError('co_applicant.city')">
                <form-label :error="hasError('co_applicant.city')">Ort</form-label>
                <form-input type="text" v-model="form.co_applicant.city"
                  :error="hasError('co_applicant.city')"
                  @focus="removeError('co_applicant.city')">
                </form-input>
              </form-group>
            </template>
            <form-group :error="hasError('co_applicant.birth_date')">
              <form-label :error="hasError('co_applicant.birth_date')">Geburtsdatum</form-label>
              <form-input type="date" v-model="form.co_applicant.birth_date" placeholder="TT.MM.JJJJ"
                :error="hasError('co_applicant.birth_date')"
                @focus="removeError('co_applicant.birth_date')">
              </form-input>
            </form-group>
            <form-group :error="hasError('co_applicant.marital_status')">
              <form-label :error="hasError('co_applicant.marital_status')">Familienstand</form-label>
              <form-select v-model="form.co_applicant.marital_status"
                :options="optionsFor('marital_statuses')"
                :error="hasError('co_applicant.marital_status')"
                @focus="removeError('co_applicant.marital_status')">
              </form-select>
            </form-group>
            <form-group :error="hasError('co_applicant.nationality')">
              <form-label :error="hasError('co_applicant.nationality')">Nationalität</form-label>
              <form-select v-model="form.co_applicant.nationality"
                :options="optionsFor('nationalities')"
                :error="hasError('co_applicant.nationality')"
                @focus="removeError('co_applicant.nationality')">
              </form-select>
            </form-group>
            <form-group :error="hasError('co_applicant.place_of_origin')">
              <template v-if="form.co_applicant.nationality === 'CH'">
                <form-label :error="hasError('co_applicant.place_of_origin')">Heimatort</form-label>
                <form-input type="text" v-model="form.co_applicant.place_of_origin"
                  :error="hasError('co_applicant.place_of_origin')"
                  @focus="removeError('co_applicant.place_of_origin')">
                </form-input>
              </template>
            </form-group>
            <template v-if="form.co_applicant.nationality && form.co_applicant.nationality !== 'CH'">
              <form-group :error="hasError('co_applicant.residence_permit')">
                <form-label :error="hasError('co_applicant.residence_permit')">Niederlassungsbewilligung</form-label>
                <form-select v-model="form.co_applicant.residence_permit"
                  :options="optionsFor('residence_permits')"
                  :error="hasError('co_applicant.residence_permit')"
                  @focus="removeError('co_applicant.residence_permit')">
                </form-select>
              </form-group>
              <form-group :error="hasError('co_applicant.swiss_residence_since')">
                <form-label :error="hasError('co_applicant.swiss_residence_since')">In der Schweiz wohnhaft seit</form-label>
                <form-input type="date" v-model="form.co_applicant.swiss_residence_since" placeholder="TT.MM.JJJJ"
                  :error="hasError('co_applicant.swiss_residence_since')"
                  @focus="removeError('co_applicant.swiss_residence_since')">
                </form-input>
              </form-group>
            </template>
            <form-group :error="hasError('co_applicant.mobile_phone')">
              <form-label :error="hasError('co_applicant.mobile_phone')">Mobiltelefon</form-label>
              <form-input type="text" v-model="form.co_applicant.mobile_phone"
                :error="hasError('co_applicant.mobile_phone')"
                @focus="removeError('co_applicant.mobile_phone')">
              </form-input>
            </form-group>
            <form-group>
              <form-label :required="false">Telefon (Festnetz)</form-label>
              <form-input type="text" v-model="form.co_applicant.landline_phone"></form-input>
            </form-group>
            <form-group :error="hasError('co_applicant.email')">
              <form-label :error="hasError('co_applicant.email')">E-Mail-Adresse</form-label>
              <form-input type="email" v-model="form.co_applicant.email"
                :error="hasError('co_applicant.email')"
                @focus="removeError('co_applicant.email')">
              </form-input>
            </form-group>
            <form-group :error="hasError('co_applicant.occupation')">
              <form-label :error="hasError('co_applicant.occupation')">Beruf</form-label>
              <form-input type="text" v-model="form.co_applicant.occupation"
                :error="hasError('co_applicant.occupation')"
                @focus="removeError('co_applicant.occupation')">
              </form-input>
            </form-group>
            <form-group :error="hasError('co_applicant.employment_status')">
              <form-label :error="hasError('co_applicant.employment_status')">Erwerbssituation</form-label>
              <form-select v-model="form.co_applicant.employment_status"
                :options="optionsFor('employment_statuses')"
                :error="hasError('co_applicant.employment_status')"
                @focus="removeError('co_applicant.employment_status')">
              </form-select>
            </form-group>
            <form-group :error="hasError('co_applicant.debt_enforcement_last_2y')">
              <form-label :error="hasError('co_applicant.debt_enforcement_last_2y')">Betreibungen/Verlustscheine (letzte 2 Jahre)</form-label>
              <form-select v-model="form.co_applicant.debt_enforcement_last_2y"
                :options="boolOptionsDebt"
                :error="hasError('co_applicant.debt_enforcement_last_2y')"
                @focus="removeError('co_applicant.debt_enforcement_last_2y')">
              </form-select>
            </form-group>

            <template v-if="form.co_applicant.employment_status === 'employed'">
              <div class="!col-span-12 !mt-15 md:!mt-30">
                <h2>Aktueller Arbeitgeber der weiteren Person</h2>
                <div class="sm:grid sm:grid-cols-12 gap-30 mb-30">
                  <form-group :error="hasError('co_applicant.employer.name')">
                    <form-label :error="hasError('co_applicant.employer.name')">Aktuelle*r Arbeitgeber*in</form-label>
                    <form-input type="text" v-model="form.co_applicant.employer.name"
                      :error="hasError('co_applicant.employer.name')"
                      @focus="removeError('co_applicant.employer.name')">
                    </form-input>
                  </form-group>
                </div>
                <div class="sm:grid sm:grid-cols-12 gap-30">
                  <form-group :error="hasError('co_applicant.employer.workload_percent')">
                    <form-label :error="hasError('co_applicant.employer.workload_percent')">Arbeitspensum (in Prozent)</form-label>
                    <form-input type="number" v-model="form.co_applicant.employer.workload_percent"
                      :error="hasError('co_applicant.employer.workload_percent')"
                      @focus="removeError('co_applicant.employer.workload_percent')">
                    </form-input>
                  </form-group>
                  <form-group :error="hasError('co_applicant.employer.annual_income_bracket')">
                    <form-label :error="hasError('co_applicant.employer.annual_income_bracket')">Jahreseinkommen (in CHF)</form-label>
                    <form-select v-model="form.co_applicant.employer.annual_income_bracket"
                      :options="optionsFor('income_brackets')"
                      :error="hasError('co_applicant.employer.annual_income_bracket')"
                      @focus="removeError('co_applicant.employer.annual_income_bracket')">
                    </form-select>
                  </form-group>
                </div>
              </div>
            </template>
          </form-grid>

          <h2 class="!mt-35 md:!mt-70">Aktuelle Wohnsituation der weiteren Person</h2>
          <form-grid>
            <form-group :error="hasError('co_applicant.current_housing.tenant_role')">
              <form-label :error="hasError('co_applicant.current_housing.tenant_role')">Aktuelle Wohnsituation</form-label>
              <form-select v-model="form.co_applicant.current_housing.tenant_role"
                :options="optionsFor('tenant_roles')"
                :error="hasError('co_applicant.current_housing.tenant_role')"
                @focus="removeError('co_applicant.current_housing.tenant_role')">
              </form-select>
            </form-group>
            <form-group :error="hasError('co_applicant.current_housing.terminated_by_landlord')">
              <form-label :error="hasError('co_applicant.current_housing.terminated_by_landlord')">Wurde das aktuelle Mietverhältnis durch den Vermieter*in gekündigt?</form-label>
              <form-select v-model="form.co_applicant.current_housing.terminated_by_landlord"
                :options="boolOptions"
                :error="hasError('co_applicant.current_housing.terminated_by_landlord')"
                @focus="removeError('co_applicant.current_housing.terminated_by_landlord')">
              </form-select>
            </form-group>
            <template v-if="form.co_applicant.current_housing.terminated_by_landlord === true">
              <form-group class="!col-span-12" :error="hasError('co_applicant.current_housing.termination_reason')">
                <form-label :error="hasError('co_applicant.current_housing.termination_reason')">Geben Sie bitte den Grund an, weshalb Ihnen Ihr*e Vermieter*in gekündigt hat:</form-label>
                <form-textarea v-model="form.co_applicant.current_housing.termination_reason"
                  :error="hasError('co_applicant.current_housing.termination_reason')"
                  @focus="removeError('co_applicant.current_housing.termination_reason')">
                </form-textarea>
              </form-group>
            </template>
            <form-group :error="hasError('co_applicant.current_housing.landlord_name')">
              <form-label :error="hasError('co_applicant.current_housing.landlord_name')">Aktuelle*r Vermieter*in</form-label>
              <form-input type="text" v-model="form.co_applicant.current_housing.landlord_name"
                :error="hasError('co_applicant.current_housing.landlord_name')"
                @focus="removeError('co_applicant.current_housing.landlord_name')">
              </form-input>
            </form-group>
            <form-group>
              <form-label :required="false">Vermieter*in Kontaktperson</form-label>
              <form-input type="text" v-model="form.co_applicant.current_housing.landlord_contact_person"></form-input>
            </form-group>
            <form-group>
              <form-label :required="false">Vermieter*in Telefon</form-label>
              <form-input type="text" v-model="form.co_applicant.current_housing.landlord_phone"></form-input>
            </form-group>
            <form-group :error="hasError('co_applicant.current_housing.rent_duration')">
              <form-label :error="hasError('co_applicant.current_housing.rent_duration')">Wie lange leben Sie in der aktuellen Wohnung?</form-label>
              <form-select v-model="form.co_applicant.current_housing.rent_duration"
                :options="optionsFor('rent_durations')"
                :error="hasError('co_applicant.current_housing.rent_duration')"
                @focus="removeError('co_applicant.current_housing.rent_duration')">
              </form-select>
            </form-group>
            <template v-if="form.co_applicant.current_housing.rent_duration === 'less_than_1_year'">
              <form-group class="!col-span-12" :error="hasError('co_applicant.current_housing.previous_landlord')">
                <form-label :error="hasError('co_applicant.current_housing.previous_landlord')">Geben Sie bitte Name und Telefonnummer des/der früheren Vermieters/Vermieterin an.</form-label>
                <form-input type="text" v-model="form.co_applicant.current_housing.previous_landlord"
                  :error="hasError('co_applicant.current_housing.previous_landlord')"
                  @focus="removeError('co_applicant.current_housing.previous_landlord')">
                </form-input>
              </form-group>
            </template>
          </form-grid>
        </template>

        <h2 class="!mt-35 md:!mt-70">Präferenzen</h2>
        <form-grid class="col-span-12 !mb-0">
          <form-group class="!col-span-10 mb-15" :error="hasError('housing_wish.districts')">
            <form-label class="mb-12 xl:mb-16" :error="hasError('housing_wish.districts')">In welchen Stadtkreisen möchten Sie wohnen?</form-label>
            <div class="grid grid-cols-12 gap-30">
              <form-group class="!col-span-6 md:!col-span-3" v-for="(district, index) in optionsFor('districts')" :key="district.value">
                <form-checkbox
                  :id="`housing_wish_districts_${index}`"
                  v-model="form.housing_wish.districts"
                  :value="district.value"
                  @update="updateMultiInput($event, form.housing_wish, 'districts')">
                  <template v-slot:label>{{ district.label }}</template>
                </form-checkbox>
              </form-group>
            </div>
          </form-group>
          <form-group class="!col-span-10 mb-15" :error="hasError('housing_wish.floors')">
            <form-label class="mb-12 xl:mb-16" :error="hasError('housing_wish.floors')">In welchem Stockwerk möchten Sie wohnen?</form-label>
            <div class="grid grid-cols-12 gap-30">
              <form-group class="!col-span-6 md:!col-span-3" v-for="(floor, index) in optionsFor('floors')" :key="floor.value">
                <form-checkbox
                  :id="`housing_wish_floors_${index}`"
                  v-model="form.housing_wish.floors"
                  :value="floor.value"
                  @update="updateMultiInput($event, form.housing_wish, 'floors')">
                  <template v-slot:label>{{ floor.label }}</template>
                </form-checkbox>
              </form-group>
            </div>
          </form-group>
          <form-group class="!col-span-10" :error="hasError('housing_wish.rooms')">
            <form-label class="mb-12 xl:mb-16" :error="hasError('housing_wish.rooms')">Wie viele Zimmer brauchen Sie?</form-label>
            <div class="grid grid-cols-12 gap-30">
              <form-group class="!col-span-6 md:!col-span-3" v-for="(room, index) in optionsFor('rooms')" :key="room.value">
                <form-checkbox
                  :id="`housing_wish_rooms_${index}`"
                  v-model="form.housing_wish.rooms"
                  :value="room.value"
                  @update="updateMultiInput($event, form.housing_wish, 'rooms')">
                  <template v-slot:label>{{ room.label }}</template>
                </form-checkbox>
              </form-group>
            </div>
          </form-group>
          <form-group :error="hasError('housing_wish.wants_balcony')">
            <form-label :error="hasError('housing_wish.wants_balcony')">Wünschen Sie einen Balkon?</form-label>
            <form-select v-model="form.housing_wish.wants_balcony"
              :options="boolOptions"
              :error="hasError('housing_wish.wants_balcony')"
              @focus="removeError('housing_wish.wants_balcony')">
            </form-select>
          </form-group>
          <form-group :error="hasError('housing_wish.wants_elevator')">
            <form-label :error="hasError('housing_wish.wants_elevator')">Wünschen Sie einen Lift im Haus?</form-label>
            <form-select v-model="form.housing_wish.wants_elevator"
              :options="boolOptions"
              :error="hasError('housing_wish.wants_elevator')"
              @focus="removeError('housing_wish.wants_elevator')">
            </form-select>
          </form-group>
          <form-group :error="hasError('housing_wish.max_gross_rent')">
            <form-label :error="hasError('housing_wish.max_gross_rent')">max. Mietzins (inkl. Nebenkosten, min. 1200)</form-label>
            <form-input type="number" v-model="form.housing_wish.max_gross_rent"
              :error="hasError('housing_wish.max_gross_rent')"
              @focus="removeError('housing_wish.max_gross_rent')">
            </form-input>
          </form-group>
          <form-group :error="hasError('housing_wish.earliest_move_in')">
            <form-label :error="hasError('housing_wish.earliest_move_in')">Bezugstermin frühestens</form-label>
            <form-input type="date" v-model="form.housing_wish.earliest_move_in" placeholder="TT.MM.JJJJ"
              :error="hasError('housing_wish.earliest_move_in')"
              @focus="removeError('housing_wish.earliest_move_in')">
            </form-input>
          </form-group>
        </form-grid>

        <h2 class="!mt-35 md:!mt-70">Weitere Angaben</h2>
        <form-grid>
          <form-group :error="hasError('household_info.plays_music')">
            <form-label :error="hasError('household_info.plays_music')">Spielen Sie oder ein*e Mitbewohner*in ein Musikinstrument?</form-label>
            <form-select v-model="form.household_info.plays_music"
              :options="boolOptions"
              :error="hasError('household_info.plays_music')"
              @focus="removeError('household_info.plays_music')">
            </form-select>
          </form-group>
          <form-group v-if="form.household_info.plays_music === true" :error="hasError('household_info.musical_instruments')">
            <form-label :error="hasError('household_info.musical_instruments')">Welches Musikinstrument</form-label>
            <form-input type="text" v-model="form.household_info.musical_instruments"
              :error="hasError('household_info.musical_instruments')"
              @focus="removeError('household_info.musical_instruments')">
            </form-input>
          </form-group>
          <form-group :error="hasError('household_info.has_pets')">
            <form-label :error="hasError('household_info.has_pets')">Halten Sie Haustiere?</form-label>
            <form-select v-model="form.household_info.has_pets"
              :options="boolOptions"
              :error="hasError('household_info.has_pets')"
              @focus="removeError('household_info.has_pets')">
            </form-select>
          </form-group>
          <form-group v-if="form.household_info.has_pets === true" :error="hasError('household_info.pets_description')">
            <form-label :error="hasError('household_info.pets_description')">Welche Haustiere halten Sie? (Hundehaltung ist verboten)</form-label>
            <form-input type="text" v-model="form.household_info.pets_description"
              :error="hasError('household_info.pets_description')"
              @focus="removeError('household_info.pets_description')">
            </form-input>
          </form-group>
          <form-group class="!col-span-12">
            <form-label :required="false">Bemerkungen</form-label>
            <form-textarea v-model="form.household_info.remarks"></form-textarea>
          </form-group>
        </form-grid>

        <form-grid>
          <div class="col-span-full border-2 border-aqua bg-sky p-10 lg:p-20">
            Bitte beachten Sie, dass Ihre Anmeldung nach 6 Monaten bei uns automatisch gelöscht wird. Sollten Sie dann immer noch eine Wohnung suchen, bitten wir Sie um eine E-Mail an wohnung@aporta-stiftung.ch, um Ihr Profil zu erneuern.
          </div>
          <form-group>
            <button
              :class="[!isLoading ? 'bg-black text-white hover:bg-aqua transition-colors' : 'opacity-50 pointer-events-none select-none', 'font-bold text-lg text-white py-15 px-20 leading-none inline-flex items-center w-auto text-left']"
              type="button"
              @click.prevent="submit()">
              Senden
            </button>
          </form-group>
        </form-grid>

      </form>
    </template>
  </template>
  <template v-else>
    <template v-if="hasAuthenticationError">
      <authentication-error :message="'Das eingegebene Passwort ist ungültig!'" />
    </template>
    <h2>Bitte geben Sie das Passwort ein, um das Formular auszufüllen.</h2>
    <form>
      <form-grid>
        <form-group class="!col-span-12" :error="errors.password">
          <form-input
            type="password"
            v-model="auth.password"
            :error="errors.password"
            @focus="hasAuthenticationError = false; errors.password = null;">
          </form-input>
        </form-group>
        <form-group>
          <button
            :class="[!isLoading ? 'bg-black text-white hover:bg-aqua transition-colors' : 'opacity-50 pointer-events-none select-none', 'font-bold text-lg text-white py-15 px-20 leading-none inline-flex items-center w-auto text-left']"
            type="button"
            @click.prevent="authenticate()">
            Anmelden
          </button>
        </form-group>
      </form-grid>
    </form>
  </template>

  </template>
  </div>
</section>
</template>

<script>
import NProgress from 'nprogress';
import FormGrid from '@/form/components/form/Grid.vue';
import FormGroup from '@/form/components/form/Group.vue';
import FormLabel from '@/form/components/form/Label.vue';
import FormInput from '@/form/components/form/Input.vue';
import FormSelect from '@/form/components/form/Select.vue';
import FormCheckbox from '@/form/components/form/Checkbox.vue';
import FormTextarea from '@/form/components/form/Textarea.vue';
import ValidationErrors from '@/form/components/form/ValidationErrors.vue';
import AuthenticationError from '@/form/components/form/AuthenticationError.vue';
import Feedback from '@/form/components/form/Feedback.vue';
import { reactive } from 'vue';
import { useLookups } from '@/form/composables/useLookups.js';

const yesNoOptions = [
  { label: 'Bitte wählen', value: null },
  { label: 'Ja', value: true },
  { label: 'Nein', value: false },
];

const debtOptions = [
  { label: 'Bitte wählen', value: null },
  { label: 'Ja, ich hatte Betreibungen/Verlustscheine', value: true },
  { label: 'Nein, ich hatte keine Betreibungen/Verlustscheine', value: false },
];

function emptyApplicant() {
  return {
    salutation: null,
    first_name: null,
    last_name: null,
    street: null,
    street_number: null,
    postal_code: null,
    city: null,
    birth_date: null,
    marital_status: null,
    nationality: null,
    place_of_origin: null,
    residence_permit: null,
    swiss_residence_since: null,
    mobile_phone: null,
    landline_phone: null,
    email: null,
    occupation: null,
    employment_status: null,
    debt_enforcement_last_2y: null,
    employer: {
      name: null,
      workload_percent: null,
      annual_income_bracket: null,
    },
    current_housing: {
      tenant_role: null,
      terminated_by_landlord: null,
      termination_reason: null,
      landlord_name: null,
      landlord_contact_person: null,
      landlord_phone: null,
      rent_duration: null,
      previous_landlord: null,
    },
  };
}

function emptyCoApplicant() {
  return {
    ...emptyApplicant(),
    relationship_to_main: null,
    same_address_as_main: null,
  };
}

export default {

  components: {
    FormGrid,
    FormGroup,
    FormLabel,
    FormInput,
    FormSelect,
    FormTextarea,
    FormCheckbox,
    ValidationErrors,
    AuthenticationError,
    Feedback,
  },

  setup() {
    // Wrap in reactive() so nested refs auto-unwrap when accessed in template
    // (a plain object containing refs does not unwrap).
    const lookups = reactive(useLookups());
    return { lookups };
  },

  data() {
    return {
      auth: { password: null },

      form: {
        shares_apartment: null,
        main_applicant: emptyApplicant(),
        co_applicant: null,
        housing_wish: {
          earliest_move_in: null,
          max_gross_rent: null,
          wants_balcony: null,
          wants_elevator: null,
          districts: [],
          floors: [],
          rooms: [],
        },
        household_info: {
          total_persons: 1,
          adults_count: 1,
          children_count: 0,
          all_children_live_constantly: null,
          plays_music: null,
          musical_instruments: null,
          has_pets: null,
          pets_description: null,
          remarks: null,
        },
        children: [],
      },

      // Multi-checkbox state for "Mit wem werden Sie die Wohnung teilen?":
      // holds relationship slugs from the lookups + the sentinel '__children'
      // when "Kinder" is checked. Derived state (hasCoApplicant, hasChildren,
      // co_applicant.relationship_to_main) is kept in sync via a watcher.
      sharedWith: [],
      hasCoApplicant: false,
      hasChildren: false,

      errors: {
        password: null,
      },
      serverErrors: {},
      validationErrors: [],

      boolOptions: yesNoOptions,
      boolOptionsDebt: debtOptions,

      routes: {
        authenticate: '/api/form/register/authenticate',
        store: '/api/form/register',
      },

      isSent: false,
      isLoading: false,
      isAuthenticated: false,
      hasAuthenticationError: false,
      authToken: null,
    };
  },

  computed: {
    sharedWithOptions() {
      return [
        ...this.lookups.activeOnly('relationships').map((entry) => ({
          label: entry.label,
          value: entry.slug,
        })),
        { label: 'Kinder', value: '__children' },
      ];
    },
  },

  mounted() {
    this.lookups.load();
  },

  methods: {

    optionsFor(key) {
      const placeholder = [{ label: 'Bitte wählen', value: null }];
      const items = this.lookups.activeOnly(key).map((entry) => ({
        label: entry.label,
        value: entry.slug,
      }));
      return [...placeholder, ...items];
    },

    hasError(path) {
      return !!this.serverErrors[path];
    },

    removeError(path) {
      if (this.serverErrors[path]) {
        const next = { ...this.serverErrors };
        delete next[path];
        this.serverErrors = next;
      }
    },

    updateMultiInput(value, target, key) {
      const list = target[key];
      const idx = list.indexOf(value);
      if (idx === -1) {
        list.push(value);
      } else {
        list.splice(idx, 1);
      }
      this.removeError(`housing_wish.${key}`);
    },

    updateSharedWith(value) {
      const isChildren = value === '__children';
      const idx = this.sharedWith.indexOf(value);

      if (idx !== -1) {
        // Already checked → uncheck.
        this.sharedWith.splice(idx, 1);
        return;
      }

      // Newly checked. For relationship slugs, enforce single-select among
      // the four — only one co-applicant is allowed in v1. "Kinder" stacks.
      if (isChildren) {
        this.sharedWith.push(value);
      } else {
        this.sharedWith = [
          ...this.sharedWith.filter((v) => v === '__children'),
          value,
        ];
      }
    },

    authenticate() {
      this.isLoading = true;
      this.validationErrors = [];
      NProgress.start();
      this.axios.post(this.routes.authenticate, this.auth).then((response) => {
        NProgress.done();
        this.auth.password = null;
        this.authToken = response.data?.token ?? null;
        this.hasAuthenticationError = false;
        this.isAuthenticated = true;
        this.isLoading = false;
      }).catch(() => {
        NProgress.done();
        this.errors.password = true;
        this.isAuthenticated = false;
        this.authToken = null;
        this.isLoading = false;
        this.hasAuthenticationError = true;
      });
    },

    buildPayload() {
      // Numeric coercion for fields the contract types as integer.
      const coerceInt = (v) => (v === null || v === '' ? null : parseInt(v, 10));

      const payload = JSON.parse(JSON.stringify(this.form));

      // adults_count is kept in sync with the relationship state by the
      // sharedWith watcher, and the visible input (when hasChildren) can
      // raise it further. Just sanity-floor at 1 here.
      payload.household_info.adults_count = Math.max(
        1,
        coerceInt(payload.household_info.adults_count) || 1
      );
      payload.household_info.children_count = coerceInt(payload.household_info.children_count) ?? 0;
      payload.household_info.total_persons =
        (payload.household_info.adults_count || 0) + (payload.household_info.children_count || 0);

      payload.children = (payload.children || []).map((c, idx) => ({
        position: idx + 1,
        birth_year: coerceInt(c.birth_year),
      }));

      if (payload.main_applicant.employer && payload.main_applicant.employer.workload_percent != null) {
        payload.main_applicant.employer.workload_percent = coerceInt(payload.main_applicant.employer.workload_percent);
      }
      if (payload.main_applicant.employment_status !== 'employed') {
        payload.main_applicant.employer = null;
      }

      if (payload.co_applicant) {
        if (payload.co_applicant.employer && payload.co_applicant.employer.workload_percent != null) {
          payload.co_applicant.employer.workload_percent = coerceInt(payload.co_applicant.employer.workload_percent);
        }
        if (payload.co_applicant.employment_status !== 'employed') {
          payload.co_applicant.employer = null;
        }
      }

      // max_gross_rent must be a decimal string per contract.
      if (payload.housing_wish.max_gross_rent != null && payload.housing_wish.max_gross_rent !== '') {
        const n = Number(payload.housing_wish.max_gross_rent);
        if (!Number.isNaN(n)) {
          payload.housing_wish.max_gross_rent = n.toFixed(2);
        }
      }

      return payload;
    },

    submit() {
      this.isSent = false;
      this.isLoading = true;
      this.validationErrors = [];
      this.serverErrors = {};
      NProgress.start();

      const payload = this.buildPayload();

      this.axios.post(this.routes.store, payload, {
        headers: { 'X-Form-Token': this.authToken ?? '' },
      }).then(() => {
        NProgress.done();
        this.isSent = true;
        this.isLoading = false;
      }).catch((error) => {
        NProgress.done();
        this.isLoading = false;
        if (error.response?.status === 401) {
          // Session expired or token invalidated — push the visitor back to the gate.
          this.isAuthenticated = false;
          this.hasAuthenticationError = true;
          this.authToken = null;
          return;
        }
        this.handleValidationErrors(error.response?.data);
      });
    },

    handleValidationErrors(data) {
      if (!data || !data.errors) return;
      const flat = {};
      const summary = [];
      for (const key in data.errors) {
        const msgs = data.errors[key];
        flat[key] = true;
        if (Array.isArray(msgs) && msgs.length) {
          summary.push(msgs[0]);
        }
      }
      this.serverErrors = flat;
      this.validationErrors = summary;

      this.$nextTick(() => {
        if (this.$refs.validationErrors) {
          this.$refs.validationErrors.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });
    },
  },

  watch: {

    'form.main_applicant.employment_status'(value) {
      if (value !== 'employed') {
        this.form.main_applicant.employer = { name: null, workload_percent: null, annual_income_bracket: null };
      }
    },

    'form.main_applicant.nationality'(value) {
      if (value === 'CH') {
        this.form.main_applicant.residence_permit = null;
        this.form.main_applicant.swiss_residence_since = null;
      } else {
        this.form.main_applicant.place_of_origin = null;
      }
    },

    'form.main_applicant.current_housing.terminated_by_landlord'(value) {
      if (value !== true) {
        this.form.main_applicant.current_housing.termination_reason = null;
      }
    },

    'form.main_applicant.current_housing.rent_duration'(value) {
      if (value !== 'less_than_1_year') {
        this.form.main_applicant.current_housing.previous_landlord = null;
      }
    },

    'form.shares_apartment'(value) {
      if (value !== true) {
        this.sharedWith = [];
      }
    },

    sharedWith: {
      handler(list) {
        const hasChildren = list.includes('__children');
        const relationshipSlug = list.find((v) => v !== '__children') || null;
        const hasCoApplicant = relationshipSlug !== null;

        this.hasCoApplicant = hasCoApplicant;
        this.hasChildren = hasChildren;

        if (hasCoApplicant) {
          if (!this.form.co_applicant) {
            this.form.co_applicant = emptyCoApplicant();
          }
          this.form.co_applicant.relationship_to_main = relationshipSlug;
        } else {
          this.form.co_applicant = null;
        }

        if (hasChildren) {
          if (!parseInt(this.form.household_info.children_count, 10)) {
            this.form.household_info.children_count = 1;
          }
        } else {
          this.form.household_info.children_count = 0;
          this.form.household_info.all_children_live_constantly = null;
          this.form.children = [];
        }

        // Keep adults_count in sync with the relationship state. When the
        // input is hidden (no children section) this is the only way it gets
        // set; when the input is visible the user can still override.
        this.form.household_info.adults_count = 1 + (hasCoApplicant ? 1 : 0);
      },
      deep: true,
    },

    'form.household_info.children_count'(value) {
      const count = Math.max(0, parseInt(value, 10) || 0);
      const current = this.form.children.length;
      if (count > current) {
        for (let i = current; i < count; i += 1) {
          this.form.children.push({ position: i + 1, birth_year: null });
        }
      } else if (count < current) {
        this.form.children.splice(count);
      }
      if (count === 0) {
        this.form.household_info.all_children_live_constantly = null;
      }
    },

    'form.co_applicant.employment_status'(value) {
      if (this.form.co_applicant && value !== 'employed') {
        this.form.co_applicant.employer = { name: null, workload_percent: null, annual_income_bracket: null };
      }
    },

    'form.co_applicant.nationality'(value) {
      if (!this.form.co_applicant) return;
      if (value === 'CH') {
        this.form.co_applicant.residence_permit = null;
        this.form.co_applicant.swiss_residence_since = null;
      } else {
        this.form.co_applicant.place_of_origin = null;
      }
    },

    'form.co_applicant.same_address_as_main'(value) {
      if (!this.form.co_applicant) return;
      if (value === true) {
        this.form.co_applicant.street = null;
        this.form.co_applicant.street_number = null;
        this.form.co_applicant.postal_code = null;
        this.form.co_applicant.city = null;
      }
    },

    'form.co_applicant.current_housing.terminated_by_landlord'(value) {
      if (this.form.co_applicant && value !== true) {
        this.form.co_applicant.current_housing.termination_reason = null;
      }
    },

    'form.co_applicant.current_housing.rent_duration'(value) {
      if (this.form.co_applicant && value !== 'less_than_1_year') {
        this.form.co_applicant.current_housing.previous_landlord = null;
      }
    },

    'form.household_info.plays_music'(value) {
      if (value !== true) {
        this.form.household_info.musical_instruments = null;
      }
    },

    'form.household_info.has_pets'(value) {
      if (value !== true) {
        this.form.household_info.pets_description = null;
      }
    },
  },
};
</script>
