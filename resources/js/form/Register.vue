<template>
<section class="content w-full py-48 !pt-0">
  <div class="max-w-page mx-auto px-15 md:px-45 xl:px-60">
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
            <form-group :error="errors.main_tenant_salutation">
              <form-label :error="errors.main_tenant_salutation">Anrede</form-label>
              <form-select 
                v-model="form.main_tenant_salutation" 
                :options="salutations"
                :error="errors.main_tenant_salutation"
                @blur="validateField('main_tenant_salutation')"
                @focus="removeError('main_tenant_salutation')">
              </form-select>
            </form-group>
          </div>
          <form-group :error="errors.main_tenant_lastname">
            <form-label :error="errors.main_tenant_lastname">Familienname</form-label>
            <form-input 
              type="text" 
              v-model="form.main_tenant_lastname" 
              :error="errors.main_tenant_lastname"
              @blur="validateField('main_tenant_lastname')"
              @focus="removeError('main_tenant_lastname')">
            </form-input>
          </form-group>
          <form-group :error="errors.main_tenant_firstname">
            <form-label :error="errors.main_tenant_firstname">Vorname</form-label>
            <form-input 
              type="text" 
              v-model="form.main_tenant_firstname" 
              :error="errors.main_tenant_firstname"
              @blur="validateField('main_tenant_firstname')"
              @focus="removeError('main_tenant_firstname')">
            </form-input>
          </form-group>
          <form-group :error="errors.main_tenant_street_number">
            <form-label :error="errors.main_tenant_street_number">Strasse und Hausnummer</form-label>
            <form-input 
              type="text" 
              v-model="form.main_tenant_street_number" 
              :error="errors.main_tenant_street_number"
              @blur="validateField('main_tenant_street_number')"
              @focus="removeError('main_tenant_street_number')">
            </form-input>
          </form-group>
          <form-group :error="errors.main_tenant_postal_code_city">
            <form-label :error="errors.main_tenant_postal_code_city">PLZ und Ort</form-label>
            <form-input 
              type="text" 
              v-model="form.main_tenant_postal_code_city" 
              :error="errors.main_tenant_postal_code_city"
              @blur="validateField('main_tenant_postal_code_city')"
              @focus="removeError('main_tenant_postal_code_city')">
            </form-input>
          </form-group>
          <form-group :error="errors.main_tenant_birthdate">
            <form-label :error="errors.main_tenant_birthdate">Geburtsdatum</form-label>
            <form-input 
              type="date" 
              v-model="form.main_tenant_birthdate" 
              placeholder="TT.MM.JJJJ"
              :error="errors.main_tenant_birthdate"
              @blur="validateField('main_tenant_birthdate')"
              @focus="removeError('main_tenant_birthdate')">
            </form-input>
          </form-group>
          <form-group :error="errors.main_tenant_marital_status">
            <form-label :error="errors.main_tenant_marital_status">Familienstand</form-label>
            <form-select
                v-model="form.main_tenant_marital_status"
                :options="marital_status"
                :error="errors.main_tenant_marital_status"
                @blur="validateField('main_tenant_marital_status')"
                @focus="removeError('main_tenant_marital_status')">
            </form-select>
          </form-group>
          <form-group :error="errors.main_tenant_nationality">
            <form-label :error="errors.main_tenant_nationality">Nationalität</form-label>
            <form-select v-model="form.main_tenant_nationality"
              :options="nationality"
              :error="errors.main_tenant_nationality"
              @blur="validateField('main_tenant_nationality')"
              @focus="removeError('main_tenant_nationality')">
            </form-select>
          </form-group>
          <form-group :error="errors.main_tenant_home_town">
            <template v-if="form.main_tenant_nationality == 'CH'">
              <form-label :error="errors.main_tenant_home_town">Heimatort</form-label>
              <form-input 
                type="text" 
                v-model="form.main_tenant_home_town" 
                :error="errors.main_tenant_home_town"
                @blur="validateField('main_tenant_home_town')"
                @focus="removeError('main_tenant_home_town')">
              </form-input>
            </template>
          </form-group>
          <template v-if="form.main_tenant_nationality == 'Andere'">
            <form-group :error="errors.main_tenant_residence_permit">
              <form-label :error="errors.main_tenant_residence_permit">Niederlassungsbewilligung</form-label>
              <form-select v-model="form.main_tenant_residence_permit"
                :options="residence_permits"
                :error="errors.main_tenant_residence_permit"
                @blur="validateField('main_tenant_residence_permit')"
                @focus="removeError('main_tenant_residence_permit')">
              </form-select>
            </form-group>
            <form-group :error="errors.main_tenant_swiss_residence_since">
              <form-label :error="errors.main_tenant_swiss_residence_since">In der Schweiz wohnhaft seit</form-label>
              <form-input 
                type="date" 
                v-model="form.main_tenant_swiss_residence_since" 
                placeholder="TT.MM.JJJJ"
                :error="errors.main_tenant_swiss_residence_since"
                @blur="validateField('main_tenant_swiss_residence_since')"
                @focus="removeError('main_tenant_swiss_residence_since')">
              </form-input>
            </form-group>
          </template>
          <form-group :error="errors.main_tenant_private_phone">
            <form-label :error="errors.main_tenant_private_phone">Telefon (privat)</form-label>
            <form-input 
              type="text" 
              v-model="form.main_tenant_private_phone" 
              :error="errors.main_tenant_private_phone"
              @blur="validateField('main_tenant_private_phone')"
              @focus="removeError('main_tenant_private_phone')">
            </form-input>
          </form-group>
          <form-group>
            <form-label :required="false">Telefon (geschäftlich)</form-label>
            <form-input 
              type="text" 
              v-model="form.main_tenant_work_phone" 
              :error="errors.main_tenant_work_phone">
            </form-input>
          </form-group>
          <form-group :error="errors.main_tenant_email">
            <form-label :error="errors.main_tenant_email">E-Mail-Adresse</form-label>
            <form-input 
              type="email" 
              v-model="form.main_tenant_email" 
              :error="errors.main_tenant_email"
              @blur="validateEmail('main_tenant_email')"
              @focus="removeError('main_tenant_email')">
            </form-input>
          </form-group>
          <form-group :error="errors.main_tenant_occupation">
            <form-label :error="errors.main_tenant_occupation">Beruf</form-label>
            <form-input 
              type="text" 
              v-model="form.main_tenant_occupation" 
              :error="errors.main_tenant_occupation"
              @blur="validateField('main_tenant_occupation')"
              @focus="removeError('main_tenant_occupation')">
            </form-input>
          </form-group>
          <form-group :error="errors.main_tenant_employment_status">
            <form-label :error="errors.main_tenant_employment_status">Erwerbssituation</form-label>
            <form-select v-model="form.main_tenant_employment_status"
              :options="employment_status"
              :error="errors.main_tenant_employment_status"
              @blur="validateField('main_tenant_employment_status')"
              @focus="removeError('main_tenant_employment_status')">
            </form-select>
          </form-group>
          <form-group :error="errors.main_tenant_debt_enforcement_yn">
            <form-label :error="errors.main_tenant_debt_enforcement_yn">Betreibungen</form-label>
            <form-select v-model="form.main_tenant_debt_enforcement_yn"
              :options="debt_enforcement"
              :error="errors.main_tenant_debt_enforcement_yn"
              @blur="validateField('main_tenant_debt_enforcement_yn')"
              @focus="removeError('main_tenant_debt_enforcement_yn')">
            </form-select>
          </form-group>
          <template v-if="form.main_tenant_employment_status == 'Angestellt'">
            <div class="!col-span-12 !mt-15 md:!mt-30">
              <h2>Aktueller Arbeitgeber</h2>
              <div class="sm:grid sm:grid-cols-12 gap-30 mb-30">
                <form-group :error="errors.main_tenant_current_employer_name">
                  <form-label :error="errors.main_tenant_current_employer_name">Aktuelle*r Arbeitgeber*in</form-label>
                  <form-input 
                    type="text" 
                    v-model="form.main_tenant_current_employer_name" 
                    :error="errors.main_tenant_current_employer_name"
                    @blur="validateField('main_tenant_current_employer_name')"
                    @focus="removeError('main_tenant_current_employer_name')">
                  </form-input>
                </form-group>
              </div>
              <div class="sm:grid sm:grid-cols-12 gap-30">
                <form-group :error="errors.main_tenant_workload">
                  <form-label :error="errors.main_tenant_workload">Arbeitspensum (in Prozent)</form-label>
                  <form-input 
                    type="text" 
                    v-model="form.main_tenant_workload" 
                    :error="errors.main_tenant_workload"
                    @blur="validateField('main_tenant_workload')"
                    @focus="removeError('main_tenant_workload')">
                  </form-input>
                </form-group>
                <form-group :error="errors.main_tenant_annual_income">
                  <form-label :error="errors.main_tenant_annual_income">Jahreseinkommen (in CHF)</form-label>
                  <form-select v-model="form.main_tenant_annual_income"
                    :options="annual_incomes"
                    :error="errors.main_tenant_annual_income"
                    @blur="validateField('main_tenant_annual_income')"
                    @focus="removeError('main_tenant_annual_income')">
                  </form-select>
                </form-group>
              </div>
            </div>
          
          </template>
        </form-grid>
        
        <h2 class="!mt-35 md:!mt-70">Aktuelle Wohnsituation</h2>
        <form-grid>
          <form-group :error="errors.main_tenant_current_rent_tenant_role">
            <form-label :error="errors.main_tenant_current_rent_tenant_role">Aktuelle Wohnsituation</form-label>
            <form-select v-model="form.main_tenant_current_rent_tenant_role"
              :options="tenant_roles"
              :error="errors.main_tenant_current_rent_tenant_role"
              @blur="validateField('main_tenant_current_rent_tenant_role')"
              @focus="removeError('main_tenant_current_rent_tenant_role')">
            </form-select>
          </form-group>
          <form-group :error="errors.main_tenant_current_rent_terminator">
            <form-label :error="errors.main_tenant_current_rent_terminator">Wurde das aktuelle Mietverhältnis durch den Vermieter*in gekündigt?</form-label>
            <form-select v-model="form.main_tenant_current_rent_terminator"
              :options="yes_no"
              :error="errors.main_tenant_current_rent_terminator"
              @blur="validateField('main_tenant_current_rent_terminator')"
              @focus="removeError('main_tenant_current_rent_terminator')">
            </form-select>
          </form-group>
          <template v-if="form.main_tenant_current_rent_terminator == 'Ja'">
            <form-group class="!col-span-12" :error="errors.main_tenant_current_rent_terminator_reason">
              <form-label :error="errors.main_tenant_current_rent_terminator_reason">Geben Sie bitte den Grund an, weshalb Ihnen Ihr*e Vermieter*in gekündigt hat:</form-label>
              <form-textarea :error="errors.main_tenant_current_rent_terminator_reason" v-model="form.main_tenant_current_rent_terminator_reason"
                @blur="validateField('main_tenant_current_rent_terminator_reason')"
                @focus="removeError('main_tenant_current_rent_terminator_reason')">
              </form-textarea>
            </form-group>
          </template>
          <form-group :error="errors.main_tenant_current_renter_name">
            <form-label :error="errors.main_tenant_current_renter_name">Aktuelle*r Vermieter*in*</form-label>
            <form-input 
              type="text" 
              v-model="form.main_tenant_current_renter_name" 
              :error="errors.main_tenant_current_renter_name"
              @blur="validateField('main_tenant_current_renter_name')"
              @focus="removeError('main_tenant_current_renter_name')">
            </form-input>
          </form-group>
          <form-group :error="errors.main_tenant_current_renter_phone">
            <form-label :error="errors.main_tenant_current_renter_phone">Vermieter*in Telefon</form-label>
            <form-input 
              type="text" 
              v-model="form.main_tenant_current_renter_phone" 
              :error="errors.main_tenant_current_renter_phone"
              @blur="validateField('main_tenant_current_renter_phone')"
              @focus="removeError('main_tenant_current_renter_phone')">
            </form-input>
          </form-group>
          <form-group :error="errors.main_tenant_current_renter_rent_duration">
            <form-label :error="errors.main_tenant_current_renter_rent_duration">Wie lange leben Sie in der aktuellen Wohnung?</form-label>
            <form-select v-model="form.main_tenant_current_renter_rent_duration"
              :options="rent_duration"
              :error="errors.main_tenant_current_renter_rent_duration"
              @blur="validateField('main_tenant_current_renter_rent_duration')"
              @focus="removeError('main_tenant_current_renter_rent_duration')">
            </form-select>
          </form-group>
          <template v-if="form.main_tenant_current_renter_rent_duration == 'Weniger als 1 Jahr'">
            <form-group class="!col-span-12" :error="errors.main_tenant_current_renter_previous_renter">
              <form-label :error="errors.main_tenant_current_renter_previous_renter">Geben Sie bitte Name und Telefonnummer des/der früheren Vermieters/Vermieterin an.</form-label>
              <form-input 
                type="text" 
                v-model="form.main_tenant_current_renter_previous_renter" 
                :error="errors.main_tenant_current_renter_previous_renter"
                @blur="validateField('main_tenant_current_renter_previous_renter')"
                @focus="removeError('main_tenant_current_renter_previous_renter')">
              </form-input>
            </form-group>
          </template>
        </form-grid>
        
        <h2 class="!mt-35 md:!mt-70">Weitere Person</h2>
        <form-grid>
          <form-group :error="errors.sub_tenant_yn">
            <form-label :error="errors.sub_tenant_yn">Werden Sie die Wohnung teilen?</form-label>
            <form-select
              v-model="form.sub_tenant_yn"
              :options="yes_no"
              :error="errors.sub_tenant_yn"
              @blur="validateField('sub_tenant_yn')"
              @focus="removeError('sub_tenant_yn')">
            </form-select>
          </form-group>
          <template v-if="form.sub_tenant_yn == 'Ja'">
            <form-grid class="col-span-12 !mb-0">
              <form-group class="!col-span-12" :error="errors.sub_tenant_type">
                <form-label :error="errors.sub_tenant_type" class="mb-12 xl:mb-16">Mit wem werden Sie die Wohnung teilen?</form-label>
                <div class="grid grid-cols-12 gap-30">
                  <form-group class="!col-span-12 md:!col-span-6 xl:!col-span-4" v-for="(type, index) in tenant_types" :key="index">
                    <form-checkbox
                      :id="`sub_tenant_${index}`" 
                      v-model="form.sub_tenant_type"
                      :value="type"
                      @update="updateCheckboxInput($event, 'sub_tenant_type')">
                      <template v-slot:label>{{ type }}</template>
                    </form-checkbox>
                  </form-group>
                </div>
              </form-group>
            </form-grid>
          </template>
        </form-grid>
        
        <template v-if="form.sub_tenant_type.includes('Kinder')">
          <div class="text-md my-30">Angaben zum Kind/zu den Kindern folgen weiter unten.</div>
        </template>

        <template v-if="form.sub_tenant_type.some(item => item !== 'Kinder')">
          <form-grid>
            <div class="sm:col-span-12 sm:grid sm:grid-cols-12 sm:gap-30">
              <form-group :error="errors.sub_tenant_salutation">
                <form-label :error="errors.sub_tenant_salutation">Anrede</form-label>
                <form-select 
                  v-model="form.sub_tenant_salutation" 
                  :options="salutations"
                  :error="errors.sub_tenant_salutation"
                  @blur="validateField('sub_tenant_salutation')"
                  @focus="removeError('sub_tenant_salutation')">
                </form-select>
              </form-group>
            </div>
            <form-group :error="errors.sub_tenant_lastname">
              <form-label :error="errors.sub_tenant_lastname">Familienname</form-label>
              <form-input 
                type="text" 
                v-model="form.sub_tenant_lastname" 
                :error="errors.sub_tenant_lastname"
                @blur="validateField('sub_tenant_lastname')"
                @focus="removeError('sub_tenant_lastname')">
              </form-input>
            </form-group>
            <form-group :error="errors.sub_tenant_firstname">
              <form-label :error="errors.sub_tenant_firstname">Vorname</form-label>
              <form-input 
                type="text" 
                v-model="form.sub_tenant_firstname" 
                :error="errors.sub_tenant_firstname"
                @blur="validateField('sub_tenant_firstname')"
                @focus="removeError('sub_tenant_firstname')">
              </form-input>
            </form-group>
            <div class="sm:col-span-12 sm:grid sm:grid-cols-12 sm:gap-30">
              <form-group :error="errors.sub_tenant_same_adress">
                <form-label :error="errors.sub_tenant_same_adress">Aktuell gleiche Adresse wie Hauptmieter?</form-label>
                <form-select
                  v-model="form.sub_tenant_same_adress"
                  :options="yes_no"
                  :error="errors.sub_tenant_same_adress"
                  @blur="validateField('sub_tenant_same_adress')"
                  @focus="removeError('sub_tenant_same_adress')">
                </form-select>
              </form-group>
            </div>
            <template v-if="form.sub_tenant_same_adress == 'Nein'">
              <form-group :error="errors.sub_tenant_street">
                <form-label :error="errors.sub_tenant_street">Strasse und Hausnummer</form-label>
                <form-input 
                  type="text" 
                  v-model="form.sub_tenant_street" 
                  :error="errors.sub_tenant_street"
                  @blur="validateField('sub_tenant_street')"
                  @focus="removeError('sub_tenant_street')">
                </form-input>
              </form-group>
              <form-group :error="errors.sub_tenant_postal_code_city">
                <form-label :error="errors.sub_tenant_postal_code_city">PLZ und Ort</form-label>
                <form-input 
                  type="text" 
                  v-model="form.sub_tenant_postal_code_city" 
                  :error="errors.sub_tenant_postal_code_city"
                  @blur="validateField('sub_tenant_postal_code_city')"
                  @focus="removeError('sub_tenant_postal_code_city')">
                </form-input>
              </form-group>
            </template>
            <form-group :error="errors.sub_tenant_birthdate">
              <form-label :error="errors.sub_tenant_birthdate">Geburtsdatum</form-label>
              <form-input 
                type="date" 
                v-model="form.sub_tenant_birthdate" 
                placeholder="TT.MM.JJJJ"
                :error="errors.sub_tenant_birthdate"
                @blur="validateField('sub_tenant_birthdate')"
                @focus="removeError('sub_tenant_birthdate')">
              </form-input>
            </form-group>
            <form-group :error="errors.sub_tenant_marital_status">
              <form-label :error="errors.main_tenant_marital_status">Familienstand</form-label>
              <form-select
                  v-model="form.main_tenant_marital_status"
                  :options="marital_status"
                  :error="errors.main_tenant_marital_status"
                  @blur="validateField('main_tenant_marital_status')"
                  @focus="removeError('main_tenant_marital_status')">
              </form-select>
            </form-group>
            <form-group :error="errors.sub_tenant_nationality">
              <form-label :error="errors.sub_tenant_nationality">Nationalität</form-label>
              <form-select v-model="form.sub_tenant_nationality"
                :options="nationality"
                :error="errors.sub_tenant_nationality"
                @blur="validateField('sub_tenant_nationality')"
                @focus="removeError('sub_tenant_nationality')">
              </form-select>
            </form-group>
            <form-group :error="errors.sub_tenant_home_town">
              <template v-if="form.sub_tenant_nationality == 'CH'">
                <form-label :error="errors.sub_tenant_home_town">Heimatort</form-label>
                <form-input 
                  type="text" 
                  v-model="form.sub_tenant_home_town" 
                  :error="errors.sub_tenant_home_town"
                  @blur="validateField('sub_tenant_home_town')"
                  @focus="removeError('sub_tenant_home_town')">
                </form-input>
              </template>
            </form-group>
            <template v-if="form.sub_tenant_nationality == 'Andere'">
            <form-group :error="errors.sub_tenant_residence_permit">
              <form-label :error="errors.sub_tenant_residence_permit">Niederlassungsbewilligung</form-label>
              <form-select v-model="form.sub_tenant_residence_permit"
                :options="residence_permits"
                :error="errors.sub_tenant_residence_permit"
                @blur="validateField('sub_tenant_residence_permit')"
                @focus="removeError('sub_tenant_residence_permit')">
              </form-select>
            </form-group>
            <form-group :error="errors.sub_tenant_swiss_residence_since">
              <form-label :error="errors.sub_tenant_swiss_residence_since">In der Schweiz wohnhaft seit</form-label>
              <form-input 
                type="date" 
                v-model="form.sub_tenant_swiss_residence_since" 
                placeholder="TT.MM.JJJJ"
                :error="errors.sub_tenant_swiss_residence_since"
                @blur="validateField('sub_tenant_swiss_residence_since')"
                @focus="removeError('sub_tenant_swiss_residence_since')">
              </form-input>
            </form-group>
          </template>
            <form-group :error="errors.sub_tenant_private_phone">
              <form-label :error="errors.sub_tenant_private_phone">Telefon (privat)</form-label>
              <form-input 
                type="text" 
                v-model="form.sub_tenant_private_phone" 
                :error="errors.sub_tenant_private_phone"
                @blur="validateField('sub_tenant_private_phone')"
                @focus="removeError('sub_tenant_private_phone')">
              </form-input>
            </form-group>
            <form-group>
              <form-label :required="false">Telefon (geschäftlich)</form-label>
              <form-input 
                type="text" 
                v-model="form.sub_tenant_work_phone" 
                :error="errors.sub_tenant_work_phone">
              </form-input>
            </form-group>
            <form-group :error="errors.sub_tenant_email">
              <form-label :error="errors.sub_tenant_email">E-Mail-Adresse</form-label>
              <form-input 
                type="email" 
                v-model="form.sub_tenant_email" 
                :error="errors.sub_tenant_email"
                @blur="validateEmail('sub_tenant_email')"
                @focus="removeError('sub_tenant_email')">
              </form-input>
            </form-group>
            <form-group :error="errors.sub_tenant_occupation">
              <form-label :error="errors.sub_tenant_occupation">Beruf</form-label>
              <form-input 
                type="text" 
                v-model="form.sub_tenant_occupation" 
                :error="errors.sub_tenant_occupation"
                @blur="validateField('sub_tenant_occupation')"
                @focus="removeError('sub_tenant_occupation')">
              </form-input>
            </form-group>
            <form-group :error="errors.sub_tenant_employment_status">
              <form-label :error="errors.sub_tenant_employment_status">Erwerbssituation</form-label>
              <form-select v-model="form.sub_tenant_employment_status"
                :options="employment_status"
                :error="errors.sub_tenant_employment_status"
                @blur="validateField('sub_tenant_employment_status')"
                @focus="removeError('sub_tenant_employment_status')">
              </form-select>
            </form-group>
            <form-group :error="errors.sub_tenant_debt_enforcement_yn">
              <form-label :error="errors.main_tenant_debt_enforcement_yn">Betreibungen</form-label>
              <form-select v-model="form.main_tenant_debt_enforcement_yn"
                :options="debt_enforcement"
                :error="errors.main_tenant_debt_enforcement_yn"
                @blur="validateField('main_tenant_debt_enforcement_yn')"
                @focus="removeError('main_tenant_debt_enforcement_yn')">
              </form-select>
            </form-group>

            <template v-if="form.sub_tenant_employment_status == 'Angestellt'">
              <div class="!col-span-12 !mt-15 md:!mt-30">
                <h2>Aktueller Arbeitgeber der weiteren Person</h2>
                <div class="sm:grid sm:grid-cols-12 gap-30 mb-30">
                  <form-group :error="errors.sub_tenant_current_employer_name">
                    <form-label :error="errors.sub_tenant_current_employer_name">Aktuelle*r Arbeitgeber*in</form-label>
                    <form-input 
                      type="text" 
                      v-model="form.sub_tenant_current_employer_name" 
                      :error="errors.sub_tenant_current_employer_name"
                      @blur="validateField('sub_tenant_current_employer_name')"
                      @focus="removeError('sub_tenant_current_employer_name')">
                    </form-input>
                  </form-group>
                </div>
                <div class="sm:grid sm:grid-cols-12 gap-30">
                  <form-group :error="errors.sub_tenant_workload">
                    <form-label :error="errors.sub_tenant_workload">Arbeitspensum (in Prozent)</form-label>
                    <form-input 
                      type="text" 
                      v-model="form.sub_tenant_workload" 
                      :error="errors.sub_tenant_workload"
                      @blur="validateField('sub_tenant_workload')"
                      @focus="removeError('sub_tenant_workload')">
                    </form-input>
                  </form-group>
                  <form-group :error="errors.sub_tenant_annual_income">
                    <form-label :error="errors.sub_tenant_annual_income">Jahreseinkommen (in CHF)</form-label>
                    <form-select v-model="form.sub_tenant_annual_income"
                      :options="annual_incomes"
                      :error="errors.sub_tenant_annual_income"
                      @blur="validateField('sub_tenant_annual_income')"
                      @focus="removeError('sub_tenant_annual_income')">
                    </form-select>
                  </form-group>
                </div>
              </div>
            </template>

          </form-grid>
          <h2 class="!mt-35 md:!mt-70">Aktuelle Wohnsituation der weiteren Person</h2>
          <form-grid>
            <form-group :error="errors.sub_tenant_current_rent_tenant_role">
              <form-label :error="errors.sub_tenant_current_rent_tenant_role">Aktuelle Wohnsituation</form-label>
              <form-select v-model="form.sub_tenant_current_rent_tenant_role"
                :options="tenant_roles"
                :error="errors.sub_tenant_current_rent_tenant_role"
                @blur="validateField('sub_tenant_current_rent_tenant_role')"
                @focus="removeError('sub_tenant_current_rent_tenant_role')">
              </form-select>
            </form-group>
            <form-group :error="errors.sub_tenant_current_rent_terminator">
              <form-label :error="errors.sub_tenant_current_rent_terminator">Wurde das aktuelle Mietverhältnis durch den Vermieter*in gekündigt?</form-label>
              <form-select v-model="form.sub_tenant_current_rent_terminator"
                :options="yes_no"
                :error="errors.sub_tenant_current_rent_terminator"
                @blur="validateField('sub_tenant_current_rent_terminator')"
                @focus="removeError('sub_tenant_current_rent_terminator')">
              </form-select>
            </form-group>
            <template v-if="form.sub_tenant_current_rent_terminator == 'Ja'">
              <form-group class="!col-span-12" :error="errors.sub_tenant_current_rent_terminator_reason">
                <form-label :error="errors.sub_tenant_current_rent_terminator_reason">Geben Sie bitte den Grund an, weshalb Ihnen Ihr*e Vermieter*in gekündigt hat:</form-label>
                <form-textarea :error="errors.sub_tenant_current_rent_terminator_reason" v-model="form.sub_tenant_current_rent_terminator_reason"
                  @blur="validateField('sub_tenant_current_rent_terminator_reason')"
                  @focus="removeError('sub_tenant_current_rent_terminator_reason')">
                </form-textarea>
              </form-group>
            </template>
            <form-group :error="errors.sub_tenant_current_renter_name">
              <form-label :error="errors.sub_tenant_current_renter_name">Aktuelle*r Vermieter*in*</form-label>
              <form-input 
                type="text" 
                v-model="form.sub_tenant_current_renter_name" 
                :error="errors.sub_tenant_current_renter_name"
                @blur="validateField('sub_tenant_current_renter_name')"
                @focus="removeError('sub_tenant_current_renter_name')">
              </form-input>
            </form-group>
            <form-group :error="errors.sub_tenant_current_renter_phone">
              <form-label :error="errors.sub_tenant_current_renter_phone">Vermieter*in Telefon</form-label>
              <form-input 
                type="text" 
                v-model="form.sub_tenant_current_renter_phone" 
                :error="errors.sub_tenant_current_renter_phone"
                @blur="validateField('sub_tenant_current_renter_phone')"
                @focus="removeError('sub_tenant_current_renter_phone')">
              </form-input>
            </form-group>
            <form-group :error="errors.sub_tenant_current_renter_rent_duration">
              <form-label :error="errors.sub_tenant_current_renter_rent_duration">Wie lange leben Sie in der aktuellen Wohnung?</form-label>
              <form-select v-model="form.sub_tenant_current_renter_rent_duration"
                :options="rent_duration"
                :error="errors.sub_tenant_current_renter_rent_duration"
                @blur="validateField('sub_tenant_current_renter_rent_duration')"
                @focus="removeError('sub_tenant_current_renter_rent_duration')">
              </form-select>
            </form-group>
            <template v-if="form.sub_tenant_current_renter_rent_duration == 'Weniger als 1 Jahr'">
              <form-group class="!col-span-12" :error="errors.sub_tenant_current_renter_previous_renter">
                <form-label :error="errors.sub_tenant_current_renter_previous_renter">Geben Sie bitte Name und Telefonnummer des/der früheren Vermieters/Vermieterin an.</form-label>
                <form-input 
                  type="text" 
                  v-model="form.sub_tenant_current_renter_previous_renter" 
                  :error="errors.sub_tenant_current_renter_previous_renter"
                  @blur="validateField('sub_tenant_current_renter_previous_renter')"
                  @focus="removeError('sub_tenant_current_renter_previous_renter')">
                </form-input>
              </form-group>
            </template>
          </form-grid>
        </template>

        <h2 class="!mt-35 md:!mt-70">Präferenzen</h2>
        <form-grid class="col-span-12 !mb-0">
          <form-group class="!col-span-10 mb-15" :error="errors.rent_pref_district_id">
            <form-label class="mb-12 xl:mb-16" :error="errors.rent_pref_district_id">In welchen Stadtkreisen möchten Sie wohnen?</form-label>
            <div class="grid grid-cols-12 gap-30">
              <form-group class="!col-span-6 md:!col-span-3" v-for="(district, index) in districts" :key="index">
                <form-checkbox
                  :id="`rent_pref_district_${index}`" 
                  v-model="form.rent_pref_district_id"
                  :value="district"
                  @update="updateCheckboxInput($event, 'rent_pref_district_id')">
                  <template v-slot:label>{{ district }}</template>
                </form-checkbox>
              </form-group>
            </div>
          </form-group>
          <form-group class="!col-span-10 mb-15" :error="errors.rent_pref_floor_id">
            <form-label class="mb-12 xl:mb-16" :error="errors.rent_pref_floor_id">In welchem Stockwerk möchten Sie wohnen?</form-label>
            <div class="grid grid-cols-12 gap-30">
              <form-group class="!col-span-6 md:!col-span-3" v-for="(floor, index) in floors" :key="index">
                <form-checkbox
                  :id="`rent_pref_floor_${index}`" 
                  v-model="form.rent_pref_floor_id"
                  :value="floor"
                  @update="updateCheckboxInput($event, 'rent_pref_floor_id')">
                  <template v-slot:label>{{ floor }}</template>
                </form-checkbox>
              </form-group>
            </div>
          </form-group>
          <form-group class="!col-span-10" :error="errors.rent_pref_rooms_qty">
            <form-label class="mb-12 xl:mb-16" :error="errors.rent_pref_rooms_qty">Wie viele Zimmer brauchen Sie?</form-label>
            <div class="grid grid-cols-12 gap-30">
              <form-group class="!col-span-6 md:!col-span-3" v-for="(room, index) in rooms" :key="index">
                <form-checkbox
                  :id="`rent_pref_room_${index}`" 
                  v-model="form.rent_pref_rooms_qty"
                  :value="room"
                  @update="updateCheckboxInput($event, 'rent_pref_rooms_qty')">
                  <template v-slot:label><span v-html="room"></span></template>
                </form-checkbox>
              </form-group>
            </div>
          </form-group>
          <form-group :error="errors.rent_pref_nobalcony_yn">
            <form-label :error="errors.rent_pref_nobalcony_yn">Wünschen Sie einen Balkon?</form-label>
            <form-select
              v-model="form.rent_pref_nobalcony_yn"
              :options="yes_no"
              :error="errors.rent_pref_nobalcony_yn"
              @blur="validateField('rent_pref_nobalcony_yn')"
              @focus="removeError('rent_pref_nobalcony_yn')">
            </form-select>
          </form-group>
          <form-group :error="errors.rent_pref_noelevator">
            <form-label :error="errors.rent_pref_noelevator">Wünschen Sie einen Lift im Haus?</form-label>
            <form-select
              v-model="form.rent_pref_noelevator"
              :options="yes_no"
              :error="errors.rent_pref_noelevator"
              @blur="validateField('rent_pref_noelevator')"
              @focus="removeError('rent_pref_noelevator')">
            </form-select>
          </form-group>
          <form-group :error="errors.rent_pref_max_rent">
            <form-label :error="errors.rent_pref_max_rent">Mietzins (inkl. Nebenkosten) bis max. CHF</form-label>
            <form-input 
              type="text" 
              v-model="form.rent_pref_max_rent" 
              :error="errors.rent_pref_max_rent"
              @blur="validateField('rent_pref_max_rent')"
              @focus="removeError('rent_pref_max_rent')">
            </form-input>
          </form-group>
          <form-group :error="errors.rent_pref_min_start_date">
            <form-label :error="errors.rent_pref_min_start_date">Bezugstermin frühestens</form-label>
            <form-input 
              type="date" 
              v-model="form.rent_pref_min_start_date" 
              placeholder="TT.MM.JJJJ"
              :error="errors.rent_pref_min_start_date"
              @blur="validateField('rent_pref_min_start_date')"
              @focus="removeError('rent_pref_min_start_date')">
            </form-input>
          </form-group>
        </form-grid>

        <h2 class="!mt-35 md:!mt-70">Weitere Angaben</h2>
        <form-grid>
          <form-group :error="errors.accomodation_play_music_yn">
            <form-label :error="errors.accomodation_play_music_yn">Spielen Sie oder ein*e Mitbewohner*in ein Musikinstrument?</form-label>
            <form-select
              v-model="form.accomodation_play_music_yn"
              :options="yes_no"
              :error="errors.accomodation_play_music_yn"
              @blur="validateField('accomodation_play_music_yn')"
              @focus="removeError('accomodation_play_music_yn')">
            </form-select>
          </form-group>
          <form-group :error="errors.accomodation_musical_instruments">
            <template v-if="form.accomodation_play_music_yn == 'Ja'">
              <form-label :error="errors.accomodation_musical_instruments">Welches Musikinstrument</form-label>
              <form-input 
                type="text" 
                v-model="form.accomodation_musical_instruments" 
                :error="errors.accomodation_musical_instruments"
                @blur="validateField('accomodation_musical_instruments')"
                @focus="removeError('accomodation_musical_instruments')">
              </form-input>
            </template>
          </form-group>
          <form-group :error="errors.accomodation_pets_yn">
            <form-label :error="errors.accomodation_pets_yn">Halten Sie Haustiere?</form-label>
            <form-select
              v-model="form.accomodation_pets_yn"
              :options="yes_no"
              :error="errors.accomodation_pets_yn"
              @blur="validateField('accomodation_pets_yn')"
              @focus="removeError('accomodation_pets_yn')">
            </form-select>
          </form-group>
          <form-group :error="errors.accomodation_pets">
            <template v-if="form.accomodation_pets_yn == 'Ja'">
              <form-label :error="errors.accomodation_pets">Welche Haustiere halten Sie? (Hundehaltung ist verboten)</form-label>
              <form-input 
                type="text" 
                v-model="form.accomodation_pets" 
                :error="errors.accomodation_pets"
                @blur="validateField('accomodation_pets')"
                @focus="removeError('accomodation_pets')">
              </form-input>
            </template>
          </form-group>

          <form-group class="!col-span-12">
            <form-label :required="false">Bemerkungen</form-label>
            <form-textarea v-model="form.accomodation_remarks">
            </form-textarea>
          </form-group>

        </form-grid>

        <form-grid>
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
import Feedback from '@/form/components/form/Feedback.vue';

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
    Feedback,
  },

  data() {
    return {
      form: {
        main_tenant_salutation: 'Frau',
        main_tenant_lastname: null,
        main_tenant_firstname: null,
        main_tenant_street_number: null,
        main_tenant_postal_code_city: null,
        main_tenant_birthdate: null,
        main_tenant_marital_status: 'ledig',
        main_tenant_nationality: null,
        main_tenant_residence_permit: null,
        main_tenant_swiss_residence_since: null,
        main_tenant_home_town: null,
        main_tenant_email: null,
        main_tenant_private_phone: null,
        main_tenant_work_phone: null,
        main_tenant_occupation: null,
        main_tenant_employment_status: null,
        main_tenant_current_employer_name: null,
        main_tenant_workload: null,
        main_tenant_annual_income: null,
        main_tenant_debt_enforcement_yn: 'Nein, ich hatte keine Betreibungen in den letzten zwei Jahren',
        main_tenant_current_rent_tenant_role: 'Ich bin Hauptmieter*in',
        main_tenant_current_rent_terminator: 'Nein',
        main_tenant_current_rent_terminator_reason: null,
        main_tenant_current_renter_name: null,
        main_tenant_current_renter_phone: null,
        main_tenant_current_renter_rent_duration: 'Mehr als 2 Jahre',
        main_tenant_current_renter_previous_renter: null,
        sub_tenant_yn: 'Nein',
        sub_tenant_type: [],
        sub_tenant_salutation: 'Frau',
        sub_tenant_lastname: null,
        sub_tenant_same_adress: 'Ja',
        sub_tenant_street: null,
        sub_tenant_postal_code_city: null,
        sub_tenant_birthdate: null,
        sub_tenant_marital_status: 'ledig',
        sub_tenant_nationality: null,
        sub_tenant_residence_permit: null,
        sub_tenant_swiss_residence_since: null,
        sub_tenant_home_town: null,
        sub_tenant_private_phone: null,
        sub_tenant_work_phone: null,
        sub_tenant_email: null,
        sub_tenant_occupation: null,
        sub_tenant_employment_status: null,
        sub_tenant_debt_enforcement_yn: 'Nein, ich hatte keine Betreibungen in den letzten zwei Jahren',
        sub_tenant_current_rent_tenant_role: 'Ich bin Hauptmieter*in',
        sub_tenant_current_rent_terminator: 'Nein',
        sub_tenant_current_renter_name: null,
        sub_tenant_current_renter_phone: null,
        sub_tenant_current_renter_rent_duration: 'Mehr als 2 Jahre',
        sub_tenant_current_renter_previous_renter: null,
        sub_tenant_current_rent_terminator_reason: null,
        rent_pref_district_id: [],
        rent_pref_floor_id: [],
        rent_pref_rooms_qty: [],
        rent_pref_nobalcony_yn: 'Nein',
        rent_pref_noelevator: 'Nein',
        rent_pref_max_rent: null,
        rent_pref_min_start_date: null,
        accomodation_play_music_yn: 'Nein',
        accomodation_musical_instruments: null,
        accomodation_pets_yn: 'Nein',
        accomodation_pets: null,
        accomodation_remarks: null,

        has_sub_tenant: false,
      },

      errors: {
        main_tenant_salutation: null,
        main_tenant_lastname: null,
        main_tenant_firstname: null,
        main_tenant_street_number: null,
        main_tenant_postal_code_city: null,
        main_tenant_birthdate: null,
        main_tenant_marital_status: null,
        main_tenant_nationality: null,
        main_tenant_residence_permit: null,
        main_tenant_swiss_residence_since: null,
        main_tenant_home_town: null,
        main_tenant_email: null,
        main_tenant_private_phone: null,
        main_tenant_work_phone: null,
        main_tenant_occupation: null,
        main_tenant_employment_status: null,
        main_tenant_current_employer_name: null,
        main_tenant_workload: null,
        main_tenant_annual_income: null,
        main_tenant_debt_enforcement_yn: null,
        main_tenant_current_rent_tenant_role: null,
        main_tenant_current_rent_terminator: null,
        main_tenant_current_rent_terminator_reason: null,
        main_tenant_current_renter_name: null,
        main_tenant_current_renter_phone: null,
        main_tenant_current_renter_rent_duration: null,
        main_tenant_current_renter_previous_renter: null,
        sub_tenant_yn: null,
        sub_tenant_type: null,
        sub_tenant_salutation: null,
        sub_tenant_same_adress: null,
        sub_tenant_street: null,
        sub_tenant_postal_code_city: null,
        sub_tenant_birthdate: null,
        sub_tenant_marital_status: null,
        sub_tenant_nationality: null,
        sub_tenant_residence_permit: null,
        sub_tenant_swiss_residence_since: null,
        sub_tenant_home_town: null,
        sub_tenant_private_phone: null,
        sub_tenant_work_phone: null,
        sub_tenant_email: null,
        sub_tenant_occupation: null,
        sub_tenant_employment_status: null,
        sub_tenant_debt_enforcement_yn: null,
        sub_tenant_current_rent_tenant_role: null,
        sub_tenant_current_rent_terminator: null,
        sub_tenant_current_renter_name: null,
        sub_tenant_current_renter_phone: null,
        sub_tenant_current_renter_rent_duration: null,
        sub_tenant_current_renter_previous_renter: null,
        sub_tenant_current_rent_terminator_reason: null,
        rent_pref_district_id: null,
        rent_pref_floor_id: null,
        rent_pref_rooms_qty: null,
        rent_pref_nobalcony_yn: null,
        rent_pref_noelevator: null,
        rent_pref_max_rent: null,
        rent_pref_min_start_date: null,
        accomodation_play_music_yn: null,
        accomodation_musical_instruments: null,
        accomodation_pets_yn: null,
        accomodation_pets: null,
        accomodation_remarks: null,
      },

      validateSubTenant: false,

      salutations: [
        { label: 'Frau', value: 'Frau' },
        { label: 'Herr', value: 'Herr' },
      ],

      marital_status: [
        { label: 'ledig', value: 'ledig' },
        { label: 'verheiratet', value: 'verheiratet' },
        { label: 'geschieden', value: 'geschieden' },
        { label: 'eingetragene Partnerschaft', value: 'eingetragene Partnerschaft' },
        { label: 'aufgelöste Partnerschaft', value: 'aufgelöste Partnerschaft' },
        { label: 'verwitwet', value: 'verwitwet' },
      ],

      employment_status: [
        { label: null, value: null},
        { label: 'Angestellt', value: 'Angestellt'},
        { label: 'Student*in', value: 'Student*in' },
        { label: 'Arbeitslos', value: 'Arbeitslos' },
        { label: 'IV', value: 'IV' },
        { label: 'Im Ruhestand (pensioniert)', value: 'Im Ruhestand (pensioniert)' },
        { label: 'Familienmanager*in', value: 'Familienmanager*in' },
        { label: 'IV-Renter*in', value: 'IV-Renter*in' },
      ],

      nationality: [
        { label: null, value: null},
        { label: 'CH', value: 'CH' },
        { label: 'Andere', value: 'Andere' },
      ],

      residence_permits: [
        { label: 'B', value: 'B'},
        { label: 'C', value: 'C'},
        { label: 'Ci', value: 'Ci'},
        { label: 'G', value: 'G'},
        { label: 'L', value: 'L'},
        { label: 'F', value: 'F'},
        { label: 'N', value: 'N'},
        { label: 'S', value: 'S'},
      ],

      annual_incomes: [
        { label: null, value: null},
        { label: "Weniger als 20'000", value: "Weniger als 20'000" },
        { label: "20'000-30'000", value: "20'000-30'000" },
        { label: "30'000-40'000", value: "30'000-40'000" },
        { label: "40'000-50'000", value: "40'000-50'000" },
        { label: "50'000-60'000", value: "50'000-60'000" },
        { label: "60'000-70'000", value: "60'000-70'000" },
        { label: "70'000-80'000", value: "70'000-80'000" },
        { label: "80'000-90'000", value: "80'000-90'000" },
        { label: "90'000-100'000", value: "90'000-100'000" },
        { label: "100'000-120'000", value: "100'000-120'000" },
        { label: "120'000-140'000", value: "120'000-140'000" },
        { label: "Mehr als 140'000", value: "Mehr als 140'000" }
      ],

      rent_duration: [
        { label: 'Weniger als 1 Jahr', value: 'Weniger als 1 Jahr' },
        { label: '1 bis 2 Jahre', value: '1 bis 2 Jahre' },
        { label: 'Mehr als 2 Jahre', value: 'Mehr als 2 Jahre' },
      ],

      tenant_types: [
        'Ehepartner*in',
        'Lebenspartner*in mit eingetragener Partnerschaft',
        'Lebenspartner*in',
        'Mitbewohner*in',
        'Kinder'
      ],

      tenant_roles: [
        { label: 'Ich bin Hauptmieter*in', value: 'Ich bin Hauptmieter*in' },
        { label: 'Ich bin Untermieter*in', value: 'Ich bin Untermieter*in' },
      ],

      yes_no: [
        { label: 'Ja', value: 'Ja' },
        { label: 'Nein', value: 'Nein' },
      ],

      debt_enforcement: [
        { label: 'Ja, ich hatte Betreibungen in den letzten zwei Jahren', value: 'Ja, ich hatte Betreibungen in den letzten zwei Jahren' },
        { label: 'Nein, ich hatte keine Betreibungen in den letzten zwei Jahren', value: 'Nein, ich hatte keine Betreibungen in den letzten zwei Jahren' },
      ],

      districts: [
       'Kreis 4',
       'Kreis 5',
       'Kreis 6',
       'Kreis 7',
       'Kreis 8',
       'Kreis 10',
      ],

      floors: [
        'Hochparterre',
        '1. Stock',
        '2. Stock',
        '3. Stock',
        '4. Stock',
        '5. Stock',
        '6. Stock',
      ],

      rooms: [
        '2',
        '2&frac12;',
        '3',
        '3&frac12;',
        '4',
        '4&frac12;',
        '5',
        '5&frac12;',
      ],

      validationErrors: [],

      routes: {
        store: '/api/form/register'
      },

      isSent: false,
      isLoading: false,
    }
  },

  methods: {

    submit() {
      this.isSent = false;
      this.isLoading = true;
      this.validationErrors = [];
      NProgress.start();
      this.axios.post(this.routes.store, this.form).then(response => {
        NProgress.done();
        this.reset();
        this.isSent = true;
        this.isLoading = false;
      })
      .catch(error => {
        NProgress.done();
        this.isLoading = false;
        this.handleValidationErrors(error.response.data);
      });
    },

    updateCheckboxInput(event, field) {
      // check if value is already in array
      if (this.form[field].includes(event)) {
        // remove value from array
        this.form[field] = this.form[field].filter(item => item !== event);
      }
      else {
        // add value to array
        this.form[field].push(event);
      }

      this.errors[field] = false;
    },

    validateField(field) {
      if (this.form[field] === null || this.form[field] === '') {
        this.errors[field] = true;
      } 
      else {
        this.errors[field] = false;
      }
    },

    validateEmail(field) {
      if (this.validEmail(field)) {
        this.errors[field] = false;
        return;
      }
      this.errors[field] = true;
    },

    validEmail(field) {
      if (field === null || field === '') {
        return false;
      }
      const rgx = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!rgx.test(this.form[field])) {
        return false;
      }
      return true;
    },

    handleValidationErrors(data) {
      let errors = [];
      for (let key in data.errors) {
        this.validationErrors.push(
          data.errors[key][0]
        );
        this.errors[key] = true;
      }

      // scroll to ref="validationErrors"
      this.$nextTick(() => {
        this.$refs.validationErrors.scrollIntoView({
          behavior: 'smooth',
          block: 'start',
        });
      });

    },

    removeError(field) {
      this.errors[field] = null;
    },

    reset() {
      this.form = {};
      this.errors = {};
    },
  },

  watch: {
    'form.main_tenant_employment_status': {
      handler: function (after, before) {
        if (this.form.main_tenant_employment_status != 'Angestellt') {
          this.form.main_tenant_current_employer_name = null;
          this.form.main_tenant_workload = null;
          this.form.main_tenant_annual_income = null;
          this.errors.main_tenant_current_employer_name = null;
          this.errors.main_tenant_workload = null;
          this.errors.main_tenant_annual_income = null;
        }
      },
      deep: true,
    },

    'form.main_tenant_nationality': {
      handler: function(after, before) {
        if (this.form.main_tenant_nationality == 'CH') {
          this.form.main_tenant_residence_permit = null;
          this.form.main_tenant_swiss_residence_since = null;
          this.errors.main_tenant_residence_permit = null;
          this.errors.main_tenant_swiss_residence_since = null;
        }
        else if (this.form.main_tenant_nationality == 'Andere') {
          this.form.main_tenant_home_town = null;
          this.errors.main_tenant_home_town = null;
        }
      },
      deep: true
    },

    'form.main_tenant_current_rent_terminator': {
      handler: function(after, before) {
        if (this.form.main_tenant_current_rent_terminator == 'Nein') {
          this.form.main_tenant_current_rent_terminator_reason = null;
          this.errors.main_tenant_current_rent_terminator_reason = null;
        }
      },
      deep: true,
    },  

    'form.main_tenant_current_renter_rent_duration': {
      handler: function(after, before) {
        if (this.form.main_tenant_current_renter_rent_duration != 'Weniger als 1 Jahr') {
          this.form.main_tenant_current_renter_previous_renter = null;
          this.errors.main_tenant_current_renter_previous_renter = null;
        }
      },
      deep: true,
    },

    'form.sub_tenant_type': {
      handler: function (after, before) {
        if (this.form.sub_tenant_yn == 'Ja') {
          // if this.form.sub_tenant_type has at least 1 item other than 'Kinder', set this.form.has_sub_tenant to true
          if (this.form.sub_tenant_type.length >= 1) {
            // check if there is at least 1 item other than 'Kinder'
            if (this.form.sub_tenant_type.filter(item => item !== 'Kinder').length >= 1) {
              this.form.has_sub_tenant = true;
            }
            else {
              this.form.has_sub_tenant = false;
            }
          }
        }
      },
      deep: true
    },

    'form.sub_tenant_employment_status': {
      handler: function (after, before) {
        if (this.form.sub_tenant_employment_status != 'Angestellt') {
          this.form.sub_tenant_current_employer_name = null;
          this.form.sub_tenant_workload = null;
          this.form.sub_tenant_annual_income = null;
          this.errors.sub_tenant_current_employer_name = null;
          this.errors.sub_tenant_workload = null;
          this.errors.sub_tenant_annual_income = null;
        }
      },
      deep: true,
    },

    'form.sub_tenant_nationality': {
      handler: function(after, before) {
        if (this.form.sub_tenant_nationality == 'CH') {
          this.form.sub_tenant_residence_permit = null;
          this.form.sub_tenant_swiss_residence_since = null;
          this.errors.sub_tenant_residence_permit = null;
          this.errors.sub_tenant_swiss_residence_since = null;
        }
        else if (this.form.sub_tenant_nationality == 'Andere') {
          this.form.sub_tenant_home_town = null;
          this.errors.sub_tenant_home_town = null;
        }
      },
      deep: true
    },

    'form.sub_tenant_current_rent_terminator': {
      handler: function(after, before) {
        if (this.form.sub_tenant_current_rent_terminator == 'Nein') {
          this.form.sub_tenant_current_rent_terminator_reason = null;
          this.errors.sub_tenant_current_rent_terminator_reason = null;
        }
      },
      deep: true,
    },  

    'form.sub_tenant_current_renter_rent_duration': {
      handler: function(after, before) {
        if (this.form.sub_tenant_current_renter_rent_duration != 'Weniger als 1 Jahr') {
          this.form.sub_tenant_current_renter_previous_renter = null;
          this.errors.sub_tenant_current_renter_previous_renter = null;
        }
      },
      deep: true,
    },

    'form.accomodation_play_music_yn': {
      handler: function(after, before) {
        if (this.form.accomodation_play_music_yn == 'Nein') {
          this.form.accomodation_musical_instruments = null;
          this.errors.accomodation_musical_instruments = null;
        }
      }
    },

    'form.accomodation_pets_yn': {
      handler: function(after, before) {
        if (this.form.accomodation_pets_yn == 'Nein') {
          this.form.accomodation_pets = null;
          this.errors.accomodation_pets = null;
        }
      }
    },

  },

}
</script>