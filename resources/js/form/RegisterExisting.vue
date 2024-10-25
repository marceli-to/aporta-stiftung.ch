<template>
<section class="content w-full py-48 !pt-0">
  <div class="max-w-page mx-auto px-15 md:px-45 xl:px-60">
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
          <form-group :error="errors.main_tenant_private_phone">
            <form-label :error="errors.main_tenant_private_phone">Telefon</form-label>
            <form-input 
              type="text" 
              v-model="form.main_tenant_private_phone" 
              :error="errors.main_tenant_private_phone"
              @blur="validateField('main_tenant_private_phone')"
              @focus="removeError('main_tenant_private_phone')">
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
          <template v-if="form.main_tenant_nationality != 'CH'">
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
          <template v-if="form.main_tenant_employment_status == 1">
            <div class="!col-span-12">
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
            </div>
          </template>
        </form-grid>
        <h2 class="!mt-35 md:!mt-70">Weitere Person(en)</h2>
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
          <template v-if="form.sub_tenant_yn == 1">
            <form-grid class="col-span-12 !mb-0">
              <form-group class="!col-span-12" :error="errors.sub_tenant_type">
                <form-label :error="errors.sub_tenant_type" class="mb-12 xl:mb-16">Mit wem werden Sie die Wohnung teilen?</form-label>
                <div class="grid grid-cols-12 gap-30">
                  <form-group class="!col-span-12 md:!col-span-6 xl:!col-span-4" v-for="(type, index) in tenant_types" :key="index">
                    <form-checkbox
                      :id="`sub_tenant_${index}`" 
                      v-model="form.sub_tenant_type"
                      :value="type.value"
                      @update="updateCheckboxInput($event, 'sub_tenant_type')">
                      <template v-slot:label>{{ type.label }}</template>
                    </form-checkbox>
                  </form-group>
                </div>
              </form-group>
            </form-grid>
          </template>
        </form-grid>
        <template v-if="form.sub_tenant_type.includes('1') || form.sub_tenant_type.includes('2') || form.sub_tenant_type.includes('3') || form.sub_tenant_type.includes('4')">
          <h2>Personendaten Mitbewohner*in</h2>
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
            <form-group :error="errors.sub_tenant_private_phone">
              <form-label :error="errors.sub_tenant_private_phone">Telefon</form-label>
              <form-input 
                type="text" 
                v-model="form.sub_tenant_private_phone" 
                :error="errors.sub_tenant_private_phone"
                @blur="validateField('sub_tenant_private_phone')"
                @focus="removeError('sub_tenant_private_phone')" >
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
              <form-label :error="errors.sub_tenant_marital_status">Familienstand</form-label>
              <form-select
                  v-model="form.sub_tenant_marital_status"
                  :options="marital_status"
                  :error="errors.sub_tenant_marital_status"
                  @blur="validateField('sub_tenant_marital_status')"
                  @focus="removeError('sub_tenant_marital_status')">
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
            <template v-if="form.sub_tenant_nationality != 'CH'">
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

            <template v-if="form.sub_tenant_employment_status == 1">
              <div class="!col-span-12">
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
              </div>
            </template>
          </form-grid>
        </template>
        <template v-if="form.sub_tenant_yn == 1 && form.sub_tenant_type.includes('5')">
          <h2 class="!mt-35 md:!mt-70">Aufteilung Erwachsene/Kinder</h2>
          <form-grid>
            <form-group :error="errors.accomodation_total_persons">
              <form-label :error="errors.accomodation_total_persons">Wie viele Personen werden in Ihrem Haushalt leben?</form-label>
              <form-input 
                type="number" 
                v-model="form.accomodation_total_persons" 
                :error="errors.accomodation_total_persons"
                @blur="validateField('accomodation_total_persons')"
                @focus="removeError('accomodation_total_persons')">
              </form-input>
            </form-group>
            <form-group :error="errors.accomodation_adults_qty">
              <form-label :error="errors.accomodation_adults_qty">Anzahl Erwachsene?</form-label>
              <form-input 
                type="number" 
                v-model="form.accomodation_adults_qty" 
                :error="errors.accomodation_adults_qty"
                @blur="validateField('accomodation_adults_qty')"
                @focus="removeError('accomodation_adults_qty')">
              </form-input>
            </form-group>
            <template v-if="form.sub_tenant_type.includes('5')">
              <div class="!col-span-12">
                <div class="sm:grid sm:grid-cols-12 gap-30">
                  <form-group :error="errors.accomodation_children_qty">
                    <form-label :error="errors.accomodation_children_qty">Anzahl Kinder?</form-label>
                    <form-input 
                      type="number" 
                      v-model="form.accomodation_children_qty" 
                      :error="errors.accomodation_children_qty"
                      @blur="validateField('accomodation_children_qty')"
                      @focus="removeError('accomodation_children_qty')">
                    </form-input>
                  </form-group>
                </div>
              </div>
            </template>
            <template v-if="form.sub_tenant_type.includes('5') && form.accomodation_children_qty > 0">
              <form-group v-for="(index, child) in parseInt(form.accomodation_children_qty)" :error="errors.accomodation_children_age_group">
                <form-label :error="errors.accomodation_children_age_group">Jahrgang Kind {{ index }}</form-label>
                <form-input 
                  type="text"
                  v-model="form.children_year_of_birth[index]"
                  @change="updateChildAgeGroup($event, index)"
                  @focus="removeError('accomodation_children_age_group')">
                </form-input>
              </form-group>
            </template>
          </form-grid>
        </template>
        <h2 class="!mt-35 md:!mt-70">Weitere Angaben</h2>
        <form-grid>
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
            <template v-if="form.accomodation_pets_yn == 1">
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
        </form-grid>
        <h2 class="!mt-35 md:!mt-70">Notfallkontakt</h2>
        <form-grid>
          <form-group :error="errors.emergency_contact_lastname">
            <form-label :error="errors.emergency_contact_lastname">Familienname</form-label>
            <form-input 
              type="text" 
              v-model="form.emergency_contact_lastname" 
              :error="errors.emergency_contact_lastname"
              @blur="validateField('emergency_contact_lastname')"
              @focus="removeError('emergency_contact_lastname')">
            </form-input>
          </form-group>
          <form-group :error="errors.emergency_contact_firstname">
            <form-label :error="errors.emergency_contact_firstname">Vorname</form-label>
            <form-input 
              type="text" 
              v-model="form.emergency_contact_firstname" 
              :error="errors.emergency_contact_firstname"
              @blur="validateField('emergency_contact_firstname')"
              @focus="removeError('emergency_contact_firstname')">
            </form-input>
          </form-group>
          <form-group :error="errors.emergency_contact_phone">
            <form-label :error="errors.emergency_contact_phone">Telefon</form-label>
            <form-input 
              type="text" 
              v-model="form.emergency_contact_phone" 
              :error="errors.emergency_contact_phone"
              @blur="validateField('emergency_contact_phone')"
              @focus="removeError('emergency_contact_phone')">
            </form-input>
          </form-group>
          <form-group :error="errors.emergency_contact_email">
            <form-label :error="errors.emergency_contact_email">E-Mail</form-label>
            <form-input 
              type="text" 
              v-model="form.emergency_contact_email" 
              :error="errors.emergency_contact_email"
              @blur="validateField('emergency_contact_email')"
              @focus="removeError('emergency_contact_email')">
            </form-input>
          </form-group>
        </form-grid>
        <form-grid class="!mt-35 md:!mt-70">
          <form-group class="!col-span-12">
            <form-label :required="false">Bemerkungen</form-label>
            <form-textarea v-model="form.remarks">
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
            @blur="validateField('password')"
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

  data() {
    return {
      auth: {
        password: null,
      },

      form: {
        token: null,
        main_tenant_salutation: 'Frau',
        main_tenant_lastname: null,
        main_tenant_firstname: null,
        main_tenant_birthdate: null,
        main_tenant_marital_status: 1,
        main_tenant_nationality: 'CH',
        main_tenant_residence_permit: null,
        main_tenant_swiss_residence_since: null,
        main_tenant_home_town: null,
        main_tenant_email: null,
        main_tenant_private_phone: null,
        main_tenant_occupation: null,
        main_tenant_employment_status: null,
        main_tenant_current_employer_name: null,
        sub_tenant_yn: 0,
        sub_tenant_type: [],
        sub_tenant_salutation: 'Frau',
        sub_tenant_lastname: null,
        sub_tenant_firstname: null,
        sub_tenant_birthdate: null,
        sub_tenant_marital_status: 1,
        sub_tenant_nationality: 'CH',
        sub_tenant_residence_permit: null,
        sub_tenant_swiss_residence_since: null,
        sub_tenant_home_town: null,
        sub_tenant_email: null,
        sub_tenant_private_phone: null,
        sub_tenant_occupation: null,
        sub_tenant_employment_status: null,
        sub_tenant_current_employer_name: null,
        accomodation_total_persons: null,
        accomodation_adults_qty: null,
        accomodation_children_qty: null,
        accomodation_children_age_group: null,
        accomodation_pets_yn: 0,
        accomodation_pets: null,
        emergency_contact_lastname: null,
        emergency_contact_firstname: null,
        emergency_contact_phone: null,
        emergency_contact_email: null,
        remarks: null,
        has_sub_tenant: false,
        has_children: false,
        children_year_of_birth: [],
      },

      errors: {
        password: null,
        main_tenant_salutation: null,
        main_tenant_lastname: null,
        main_tenant_firstname: null,
        main_tenant_birthdate: null,
        main_tenant_marital_status: null,
        main_tenant_nationality: null,
        main_tenant_residence_permit: null,
        main_tenant_swiss_residence_since: null,
        main_tenant_home_town: null,
        main_tenant_email: null,
        main_tenant_private_phone: null,
        main_tenant_occupation: null,
        main_tenant_employment_status: null,
        main_tenant_current_employer_name: null,
        sub_tenant_yn: null,
        sub_tenant_type: null,
        sub_tenant_salutation: null,
        sub_tenant_lastname: null,
        sub_tenant_firstname: null,
        sub_tenant_birthdate: null,
        sub_tenant_marital_status: null,
        sub_tenant_nationality: null,
        sub_tenant_residence_permit: null,
        sub_tenant_swiss_residence_since: null,
        sub_tenant_home_town: null,
        sub_tenant_email: null,
        sub_tenant_private_phone: null,
        sub_tenant_occupation: null,
        sub_tenant_employment_status: null,
        sub_tenant_current_employer_name: null,
        accomodation_total_persons: null,
        accomodation_adults_qty: null,
        accomodation_children_qty: null,
        accomodation_children_age_group: null,
        accomodation_pets_yn: null,
        accomodation_pets: null,
        emergency_contact_lastname: null,
        emergency_contact_firstname: null,
        emergency_contact_phone: null,
        emergency_contact_email: null,
        remarks: null,
      },

      salutations: [
        { label: 'Frau', value: 'Frau' },
        { label: 'Herr', value: 'Herr' },
        { label: 'Andere', value: 'Andere' },
      ],

      marital_status: [
        { label: 'ledig', value: '1' },
        { label: 'verheiratet', value: '2' },
        { label: 'geschieden', value: '3' },
        { label: 'eingetragene Partnerschaft', value: '6' },
        { label: 'aufgelöste Partnerschaft', value: '5' },
        { label: 'verwitwet', value: '4' },
      ],

      employment_status: [
        { label: null, value: null},
        { label: 'Angestellt', value: 1},
        { label: 'Student*in', value: 2 },
        { label: 'Selbständig', value: 3 },
        { label: 'Arbeitslos', value: 4 },
        { label: 'Im Ruhestand (pensioniert)', value: 5 },
        { label: 'Familienmanager*in', value: 6 },
      ],

      nationality: [
        { label: null, value: null },
        { label: 'Schweiz', value: 'CH' },
        { label: 'Deutschland', value: 'Deutschland' },
        { label: 'Österreich', value: 'Österreich' },
        { label: 'Liechtenstein', value: 'Liechtenstein' },
        { label: 'Afghanistan', value: 'Afghanistan' },
        { label: 'Ägypten', value: 'Ägypten' },
        { label: 'Åland', value: 'Åland' },
        { label: 'Albanien', value: 'Albanien' },
        { label: 'Algerien', value: 'Algerien' },
        { label: 'Amerikanisch-Samoa', value: 'Amerikanisch-Samoa' },
        { label: 'Amerikanische Jungferninseln', value: 'Amerikanische Jungferninseln' },
        { label: 'Andorra', value: 'Andorra' },
        { label: 'Angola', value: 'Angola' },
        { label: 'Anguilla', value: 'Anguilla' },
        { label: 'Antigua und Barbuda', value: 'Antigua und Barbuda' },
        { label: 'Äquatorialguinea', value: 'Äquatorialguinea' },
        { label: 'Argentinien', value: 'Argentinien' },
        { label: 'Armenien', value: 'Armenien' },
        { label: 'Aruba', value: 'Aruba' },
        { label: 'Aserbaidschan', value: 'Aserbaidschan' },
        { label: 'Äthiopien', value: 'Äthiopien' },
        { label: 'Australien', value: 'Australien' },
        { label: 'Bahamas', value: 'Bahamas' },
        { label: 'Bahrain', value: 'Bahrain' },
        { label: 'Bangladesch', value: 'Bangladesch' },
        { label: 'Barbados', value: 'Barbados' },
        { label: 'Belarus', value: 'Belarus' },
        { label: 'Belgien', value: 'Belgien' },
        { label: 'Belize', value: 'Belize' },
        { label: 'Benin', value: 'Benin' },
        { label: 'Bermuda', value: 'Bermuda' },
        { label: 'Bhutan', value: 'Bhutan' },
        { label: 'Bolivien', value: 'Bolivien' },
        { label: 'Bonaire, Saba, Sint Eustatius', value: 'Bonaire, Saba, Sint Eustatius' },
        { label: 'Bosnien und Herzegowina', value: 'Bosnien und Herzegowina' },
        { label: 'Botswana', value: 'Botswana' },
        { label: 'Bouvetinsel', value: 'Bouvetinsel' },
        { label: 'Brasilien', value: 'Brasilien' },
        { label: 'Britische Jungferninseln', value: 'Britische Jungferninseln' },
        { label: 'Britisches Territorium im Indischen Ozean', value: 'Britisches Territorium im Indischen Ozean' },
        { label: 'Brunei', value: 'Brunei' },
        { label: 'Bulgarien', value: 'Bulgarien' },
        { label: 'Burkina Faso', value: 'Burkina Faso' },
        { label: 'Burundi', value: 'Burundi' },
        { label: 'Chile', value: 'Chile' },
        { label: 'Volksrepublik China', value: 'Volksrepublik China' },
        { label: 'Cookinseln', value: 'Cookinseln' },
        { label: 'Costa Rica', value: 'Costa Rica' },
        { label: 'Curaçao', value: 'Curaçao' },
        { label: 'Dänemark', value: 'Dänemark' },
        { label: 'Dominica', value: 'Dominica' },
        { label: 'Dominikanische Republik', value: 'Dominikanische Republik' },
        { label: 'Dschibuti', value: 'Dschibuti' },
        { label: 'Ecuador', value: 'Ecuador' },
        { label: 'Elfenbeinküste', value: 'Elfenbeinküste' },
        { label: 'El Salvador', value: 'El Salvador' },
        { label: 'Eritrea', value: 'Eritrea' },
        { label: 'Estland', value: 'Estland' },
        { label: 'Eswatini', value: 'Eswatini' },
        { label: 'Falklandinseln', value: 'Falklandinseln' },
        { label: 'Färöer', value: 'Färöer' },
        { label: 'Fidschi', value: 'Fidschi' },
        { label: 'Finnland', value: 'Finnland' },
        { label: 'Frankreich', value: 'Frankreich' },
        { label: 'Französisch-Guayana', value: 'Französisch-Guayana' },
        { label: 'Französisch-Polynesien', value: 'Französisch-Polynesien' },
        { label: 'Französische Süd- und Antarktisgebiete', value: 'Französische Süd- und Antarktisgebiete' },
        { label: 'Gabun', value: 'Gabun' },
        { label: 'Gambia', value: 'Gambia' },
        { label: 'Georgien', value: 'Georgien' },
        { label: 'Ghana', value: 'Ghana' },
        { label: 'Gibraltar', value: 'Gibraltar' },
        { label: 'Grenada', value: 'Grenada' },
        { label: 'Griechenland', value: 'Griechenland' },
        { label: 'Grönland', value: 'Grönland' },
        { label: 'Guadeloupe', value: 'Guadeloupe' },
        { label: 'Guam', value: 'Guam' },
        { label: 'Guatemala', value: 'Guatemala' },
        { label: 'Guernsey (Kanalinsel)', value: 'Guernsey (Kanalinsel)' },
        { label: 'Guinea', value: 'Guinea' },
        { label: 'Guinea-Bissau', value: 'Guinea-Bissau' },
        { label: 'Guyana', value: 'Guyana' },
        { label: 'Haiti', value: 'Haiti' },
        { label: 'Heard und McDonaldinseln', value: 'Heard und McDonaldinseln' },
        { label: 'Honduras', value: 'Honduras' },
        { label: 'Hongkong', value: 'Hongkong' },
        { label: 'Indien', value: 'Indien' },
        { label: 'Indonesien', value: 'Indonesien' },
        { label: 'Insel Man', value: 'Insel Man' },
        { label: 'Irak', value: 'Irak' },
        { label: 'Iran', value: 'Iran' },
        { label: 'Irland', value: 'Irland' },
        { label: 'Island', value: 'Island' },
        { label: 'Israel', value: 'Israel' },
        { label: 'Italien', value: 'Italien' },
        { label: 'Jamaika', value: 'Jamaika' },
        { label: 'Japan', value: 'Japan' },
        { label: 'Jemen', value: 'Jemen' },
        { label: 'Jersey (Kanalinsel)', value: 'Jersey (Kanalinsel)' },
        { label: 'Jordanien', value: 'Jordanien' },
        { label: 'Kaimaninseln', value: 'Kaimaninseln' },
        { label: 'Kambodscha', value: 'Kambodscha' },
        { label: 'Kamerun', value: 'Kamerun' },
        { label: 'Kanada', value: 'Kanada' },
        { label: 'Kap Verde', value: 'Kap Verde' },
        { label: 'Kasachstan', value: 'Kasachstan' },
        { label: 'Katar', value: 'Katar' },
        { label: 'Kenia', value: 'Kenia' },
        { label: 'Kirgisistan', value: 'Kirgisistan' },
        { label: 'Kiribati', value: 'Kiribati' },
        { label: 'Kokosinseln', value: 'Kokosinseln' },
        { label: 'Kolumbien', value: 'Kolumbien' },
        { label: 'Komoren', value: 'Komoren' },
        { label: 'Kongo, Demokratische Republik', value: 'Kongo, Demokratische Republik' },
        { label: 'Kongo, Republik', value: 'Kongo, Republik' },
        { label: 'Korea, Nord', value: 'Korea, Nord' },
        { label: 'Korea, Süd', value: 'Korea, Süd' },
        { label: 'Kroatien', value: 'Kroatien' },
        { label: 'Kuba', value: 'Kuba' },
        { label: 'Kuwait', value: 'Kuwait' },
        { label: 'Laos', value: 'Laos' },
        { label: 'Lesotho', value: 'Lesotho' },
        { label: 'Lettland', value: 'Lettland' },
        { label: 'Libanon', value: 'Libanon' },
        { label: 'Liberia', value: 'Liberia' },
        { label: 'Libyen', value: 'Libyen' },
        { label: 'Litauen', value: 'Litauen' },
        { label: 'Luxemburg', value: 'Luxemburg' },
        { label: 'Macau', value: 'Macau' },
        { label: 'Madagaskar', value: 'Madagaskar' },
        { label: 'Malawi', value: 'Malawi' },
        { label: 'Malaysia', value: 'Malaysia' },
        { label: 'Malediven', value: 'Malediven' },
        { label: 'Mali', value: 'Mali' },
        { label: 'Malta', value: 'Malta' },
        { label: 'Marokko', value: 'Marokko' },
        { label: 'Marshallinseln', value: 'Marshallinseln' },
        { label: 'Martinique', value: 'Martinique' },
        { label: 'Mauretanien', value: 'Mauretanien' },
        { label: 'Mauritius', value: 'Mauritius' },
        { label: 'Mayotte', value: 'Mayotte' },
        { label: 'Mexiko', value: 'Mexiko' },
        { label: 'Mikronesien', value: 'Mikronesien' },
        { label: 'Moldau', value: 'Moldau' },
        { label: 'Monaco', value: 'Monaco' },
        { label: 'Mongolei', value: 'Mongolei' },
        { label: 'Montenegro', value: 'Montenegro' },
        { label: 'Montserrat', value: 'Montserrat' },
        { label: 'Mosambik', value: 'Mosambik' },
        { label: 'Myanmar', value: 'Myanmar' },
        { label: 'Namibia', value: 'Namibia' },
        { label: 'Nauru', value: 'Nauru' },
        { label: 'Nepal', value: 'Nepal' },
        { label: 'Neukaledonien', value: 'Neukaledonien' },
        { label: 'Neuseeland', value: 'Neuseeland' },
        { label: 'Nicaragua', value: 'Nicaragua' },
        { label: 'Niederlande', value: 'Niederlande' },
        { label: 'Niger', value: 'Niger' },
        { label: 'Nigeria', value: 'Nigeria' },
        { label: 'Niue', value: 'Niue' },
        { label: 'Nördliche Marianen', value: 'Nördliche Marianen' },
        { label: 'Nordmazedonien', value: 'Nordmazedonien' },
        { label: 'Norfolkinsel', value: 'Norfolkinsel' },
        { label: 'Norwegen', value: 'Norwegen' },
        { label: 'Oman', value: 'Oman' },
        { label: 'Osttimor', value: 'Osttimor' },
        { label: 'Pakistan', value: 'Pakistan' },
        { label: 'Palästina', value: 'Palästina' },
        { label: 'Palau', value: 'Palau' },
        { label: 'Panama', value: 'Panama' },
        { label: 'Papua-Neuguinea', value: 'Papua-Neuguinea' },
        { label: 'Paraguay', value: 'Paraguay' },
        { label: 'Peru', value: 'Peru' },
        { label: 'Philippinen', value: 'Philippinen' },
        { label: 'Pitcairninseln', value: 'Pitcairninseln' },
        { label: 'Polen', value: 'Polen' },
        { label: 'Portugal', value: 'Portugal' },
        { label: 'Puerto Rico', value: 'Puerto Rico' },
        { label: 'Réunion', value: 'Réunion' },
        { label: 'Ruanda', value: 'Ruanda' },
        { label: 'Rumänien', value: 'Rumänien' },
        { label: 'Russland', value: 'Russland' },
        { label: 'Salomonen', value: 'Salomonen' },
        { label: 'Saint-Barthélemy', value: 'Saint-Barthélemy' },
        { label: 'Saint-Martin (französischer Teil)', value: 'Saint-Martin (französischer Teil)' },
        { label: 'Sambia', value: 'Sambia' },
        { label: 'Samoa', value: 'Samoa' },
        { label: 'San Marino', value: 'San Marino' },
        { label: 'São Tomé und Príncipe', value: 'São Tomé und Príncipe' },
        { label: 'Saudi-Arabien', value: 'Saudi-Arabien' },
        { label: 'Schweden', value: 'Schweden' },
        { label: 'Senegal', value: 'Senegal' },
        { label: 'Serbien', value: 'Serbien' },
        { label: 'Seychellen', value: 'Seychellen' },
        { label: 'Sierra Leone', value: 'Sierra Leone' },
        { label: 'Simbabwe', value: 'Simbabwe' },
        { label: 'Singapur', value: 'Singapur' },
        { label: 'Sint Maarten', value: 'Sint Maarten' },
        { label: 'Slowakei', value: 'Slowakei' },
        { label: 'Slowenien', value: 'Slowenien' },
        { label: 'Somalia', value: 'Somalia' },
        { label: 'Spanien', value: 'Spanien' },
        { label: 'Sri Lanka', value: 'Sri Lanka' },
        { label: 'St. Helena, Ascension und Tristan da Cunha', value: 'St. Helena, Ascension und Tristan da Cunha' },
        { label: 'St. Kitts und Nevis', value: 'St. Kitts und Nevis' },
        { label: 'St. Lucia', value: 'St. Lucia' },
        { label: 'Saint-Pierre und Miquelon', value: 'Saint-Pierre und Miquelon' },
        { label: 'St. Vincent und die Grenadinen', value: 'St. Vincent und die Grenadinen' },
        { label: 'Südafrika', value: 'Südafrika' },
        { label: 'Sudan', value: 'Sudan' },
        { label: 'Südgeorgien und die Südlichen Sandwichinseln', value: 'Südgeorgien und die Südlichen Sandwichinseln' },
        { label: 'Südsudan', value: 'Südsudan' },
        { label: 'Suriname', value: 'Suriname' },
        { label: 'Spitzbergen und Jan Mayen', value: 'Spitzbergen und Jan Mayen' },
        { label: 'Syrien', value: 'Syrien' },
        { label: 'Tadschikistan', value: 'Tadschikistan' },
        { label: 'Tansania', value: 'Tansania' },
        { label: 'Thailand', value: 'Thailand' },
        { label: 'Togo', value: 'Togo' },
        { label: 'Tokelau', value: 'Tokelau' },
        { label: 'Tonga', value: 'Tonga' },
        { label: 'Trinidad und Tobago', value: 'Trinidad und Tobago' },
        { label: 'Tschad', value: 'Tschad' },
        { label: 'Tschechien', value: 'Tschechien' },
        { label: 'Tunesien', value: 'Tunesien' },
        { label: 'Türkei', value: 'Türkei' },
        { label: 'Turkmenistan', value: 'Turkmenistan' },
        { label: 'Turks- und Caicosinseln', value: 'Turks- und Caicosinseln' },
        { label: 'Tuvalu', value: 'Tuvalu' },
        { label: 'Uganda', value: 'Uganda' },
        { label: 'Ukraine', value: 'Ukraine' },
        { label: 'Ungarn', value: 'Ungarn' },
        { label: 'United States Minor Outlying Islands', value: 'United States Minor Outlying Islands' },
        { label: 'Uruguay', value: 'Uruguay' },
        { label: 'Usbekistan', value: 'Usbekistan' },
        { label: 'Vanuatu', value: 'Vanuatu' },
        { label: 'Vatikanstadt', value: 'Vatikanstadt' },
        { label: 'Venezuela', value: 'Venezuela' },
        { label: 'Vereinigte Arabische Emirate', value: 'Vereinigte Arabische Emirate' },
        { label: 'Vereinigte Staaten', value: 'Vereinigte Staaten' },
        { label: 'Vereinigtes Königreich', value: 'Vereinigtes Königreich' },
        { label: 'Vietnam', value: 'Vietnam' },
        { label: 'Wallis und Futuna', value: 'Wallis und Futuna' },
        { label: 'Weihnachtsinsel', value: 'Weihnachtsinsel' },
        { label: 'Westsahara', value: 'Westsahara' },
        { label: 'Zentralafrikanische Republik', value: 'Zentralafrikanische Republik' },
        { label: 'Zypern', value: 'Zypern' },
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

      tenant_types: [
        { label: 'Ehepartner*in', value: 1 },
        { label: 'Lebenspartner*in mit eingetragener Partnerschaft', value: 2 },
        { label: 'Lebenspartner*in', value: 3 },
        { label: 'Mitbewohner*in', value: 4 },
        { label: 'Kinder', value: 5 },
      ],

      yes_no: [
        { label: 'Ja', value: 1 },
        { label: 'Nein', value: 0 },
      ],

      validationErrors: [],

      routes: {
        authenticate: '/api/form/register-existing/authenticate',
        store: '/api/form/register-existing'
      },

      isSent: false,
      isLoading: false,
      isAuthenticated: false,
      hasAuthenticationError: false,
    }
  },

  methods: {
    authenticate() {
      this.isLoading = true;
      this.validationErrors = [];
      NProgress.start();
      this.axios.post(this.routes.authenticate, this.auth).then(response => {
        NProgress.done();
        this.auth.password = null;
        this.form.token = response.data.token;
        this.hasAuthenticationError = false;
        this.isAuthenticated = true;
        this.isLoading = false;
      })
      .catch(error => {
        NProgress.done();
        this.errors.password = true;
        this.isAuthenticated = false;
        this.isLoading = false;
        this.hasAuthenticationError = true;
      });
    },

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
      if (this.form[field].includes(event)) {
        this.form[field] = this.form[field].filter(item => item !== event);
      }
      else {
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
      for (let key in data.errors) {
        this.validationErrors.push(
          data.errors[key][0]
        );
        this.errors[key] = true;
      }

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

    updateChildAgeGroup(event, index) {
      if (this.form.accomodation_children_age_group === null) {
        this.form.accomodation_children_age_group = `Kind ${index}: ${event.target.value}\n`;
      }
      else {
        this.form.accomodation_children_age_group += `Kind ${index}: ${event.target.value}\n`;
      }

      this.form.children_year_of_birth[index] = event.target.value;
    },

    reset() {
      this.form = {};
      this.errors = {};
    },
  },

  watch: {
    'form.main_tenant_employment_status': {
      handler: function (after, before) {
        if (this.form.main_tenant_employment_status != 1) {
          this.form.main_tenant_current_employer_name = null;
          this.errors.main_tenant_current_employer_name = null;
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

    'form.sub_tenant_yn': {
      handler: function (after, before) {
        if (this.form.sub_tenant_yn == 0) {
          this.form.accomodation_total_persons = null;
          this.form.accomodation_adults_qty = null;
          this.form.accomodation_children_qty = null;
          this.form.accomodation_children_age_group = null;
        }
      }, 
      deep: true,
    },
    
    'form.accomodation_children_qty': {
      handler: function(after, before) {
        if (this.form.accomodation_children_qty < 1) {
          this.form.accomodation_children_age_group = null;
          this.errors.accomodation_children_age_group = null;
        }
        this.form.children_year_of_birth = [];
      },
      deep: true,
    },

    'form.sub_tenant_type': {
      handler: function (after, before) {
        if (this.form.sub_tenant_yn == 1) {
          if (this.form.sub_tenant_type.length >= 1) {
            if (this.form.sub_tenant_type.filter(item => item.value !== '5').length >= 1) {
              this.form.has_sub_tenant = true;
            }
            else {
              this.form.has_sub_tenant = false;
            }

            if (this.form.sub_tenant_type.includes('5')) {
              this.form.has_children = true;
            }
            else {
              this.form.has_children = false;
            }
          }
        }
      },
      deep: true
    },

    'form.sub_tenant_employment_status': {
      handler: function (after, before) {
        if (this.form.sub_tenant_employment_status != 1) {
          this.form.sub_tenant_current_employer_name = null;
          this.errors.sub_tenant_current_employer_name = null;
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

    'form.accomodation_pets_yn': {
      handler: function(after, before) {
        if (this.form.accomodation_pets_yn == 0) {
          this.form.accomodation_pets = null;
          this.errors.accomodation_pets = null;
        }
      }
    },
  },
}
</script>