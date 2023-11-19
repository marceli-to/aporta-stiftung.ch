<template>
<section class="content w-full py-48 !pt-0">
  <div class="max-w-page mx-auto px-15 md:px-45 xl:px-60">
    <template v-if="isSent">
      <feedback />
    </template>
    <template v-else>
      <validation-errors :validationErrors="validationErrors" v-if="validationErrors.length > 0" />
      <form>
        <h2>Personendaten</h2>
        <form-grid>
          <div class="sm:col-span-12 sm:grid sm:grid-cols-12 sm:gap-30">
            <form-group :error="errors.main_tenant_salutation">
              <form-label :error="errors.main_tenant_salutation">Anrede</form-label>
              <form-select 
                v-model="form.main_tenant_salutation" 
                :options="[
                  { label: 'Frau', value: 'Frau' },
                  { label: 'Herr', value: 'Herr' },
                ]"
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
            <form-select v-model="form.main_tenant_marital_status">
              <option value="ledig">ledig</option>
              <option value="verheiratet">verheiratet</option>
              <option value="geschieden">geschieden</option>
              <option value="eingetragene Partnerschaft">eingetragene Partnerschaft</option>
              <option value="aufgelöste Partnerschaft">aufgelöste Partnerschaft</option>
              <option value="verwitwet">verwitwet</option>
            </form-select>
          </form-group>
          <form-group :error="errors.main_tenant_nationality">
            <form-label :error="errors.main_tenant_nationality">Nationalität</form-label>
            <form-select v-model="form.main_tenant_nationality"
              :options="[
                { label: 'CH', value: 'CH' },
                { label: 'Andere', value: 'Andere' },
              ]"
              :error="errors.main_tenant_nationality"
              @blur="validateField('main_tenant_nationality')"
              @focus="removeError('main_tenant_nationality')">
            </form-select>
          </form-group>
          <form-group :error="errors.main_tenant_home_town">
            <form-label :error="errors.main_tenant_home_town">Heimatort</form-label>
            <form-input 
              type="text" 
              v-model="form.main_tenant_home_town" 
              :error="errors.main_tenant_home_town"
              @blur="validateField('main_tenant_home_town')"
              @focus="removeError('main_tenant_home_town')">
            </form-input>
          </form-group>
          <form-group :error="errors.main_tenant_private_phone">
            <form-label :error="errors.main_tenant_private_phone">Telefon* (privat)</form-label>
            <form-input 
              type="text" 
              v-model="form.main_tenant_private_phone" 
              :error="errors.main_tenant_private_phone"
              @blur="validateField('main_tenant_private_phone')"
              @focus="removeError('main_tenant_private_phone')">
            </form-input>
          </form-group>
          <form-group :error="errors.main_tenant_work_phone">
            <form-label :error="errors.main_tenant_work_phone">Telefon* (geschäftlich)</form-label>
            <form-input 
              type="text" 
              v-model="form.main_tenant_work_phone" 
              :error="errors.main_tenant_work_phone"
              @blur="validateField('main_tenant_work_phone')"
              @focus="removeError('main_tenant_work_phone')">
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
              :options="[
                { label: 'Student*in', value: 'Student*in' },
                { label: 'Selbständig', value: 'Selbständig' },
                { label: 'Arbeitslos', value: 'Arbeitslos' },
                { label: 'IV-Rente', value: 'IV-Rente' },
                { label: 'Im Ruhestand (pensioniert)', value: 'Im Ruhestand (pensioniert)' },
                { label: 'Familienmanager*in', value: 'Familienmanager*in' },
              ]"
              :error="errors.main_tenant_employment_status"
              @blur="validateField('main_tenant_employment_status')"
              @focus="removeError('main_tenant_employment_status')">
            </form-select>
          </form-group>
          <form-group :error="errors.main_tenant_debt_enforcement_yn">
            <form-label :error="errors.main_tenant_debt_enforcement_yn">Betreibungen</form-label>
            <form-select v-model="form.main_tenant_debt_enforcement_yn"
              :options="[
                { label: 'Ja, ich hatte Betreibungen in den letzten zwei Jahren', value: 'Ja, ich hatte Betreibungen in den letzten zwei Jahren' },
                { label: 'Nein, ich hatte keine Betreibungen in den letzten zwei Jahren', value: 'Nein, ich hatte keine Betreibungen in den letzten zwei Jahren' },
              ]"
              :error="errors.main_tenant_debt_enforcement_yn"
              @blur="validateField('main_tenant_debt_enforcement_yn')"
              @focus="removeError('main_tenant_debt_enforcement_yn')">
            </form-select>
          </form-group>
        </form-grid>
        <h2>Aktuelle Wohnsituation</h2>
        <form-grid>
          <form-group :error="errors.main_tenant_current_rent_tenant_role">
            <form-label :error="errors.main_tenant_current_rent_tenant_role">Aktuelle Wohnsituation</form-label>
            <form-select v-model="form.main_tenant_current_rent_tenant_role"
              :options="[
                { label: 'Ich bin Hauptmieter*in', value: 'Ich bin Hauptmieter*in' },
                { label: 'Ich bin Untermieter*in', value: 'Ich bin Untermieter*in' },
              ]"
              :error="errors.main_tenant_current_rent_tenant_role"
              @blur="validateField('main_tenant_current_rent_tenant_role')"
              @focus="removeError('main_tenant_current_rent_tenant_role')">
            </form-select>
          </form-group>
          <form-group :error="errors.main_tenant_current_rent_terminator">
            <form-label :error="errors.main_tenant_current_rent_terminator">Wurde Ihr aktuelles Mietverhältnis durch Ihre*n Vermieter*in gekündigt?</form-label>
            <form-select v-model="form.main_tenant_current_rent_terminator"
              :options="[
                { label: 'Ja', value: 'Ja' },
                { label: 'Nein', value: 'Nein' },
              ]"
              :error="errors.main_tenant_current_rent_terminator"
              @blur="validateField('main_tenant_current_rent_terminator')"
              @focus="removeError('main_tenant_current_rent_terminator')">
            </form-select>
          </form-group>
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
              :options="[
                { label: 'Weniger als 1 Jahr', value: 'Weniger als 1 Jahr' },
                { label: '1 bis 2 Jahre', value: '1 bis 2 Jahre' },
                { label: 'Mehr als 2 Jahre', value: 'Mehr als 2 Jahre' },
              ]"
              :error="errors.main_tenant_current_renter_rent_duration"
              @blur="validateField('main_tenant_current_renter_rent_duration')"
              @focus="removeError('main_tenant_current_renter_rent_duration')">
            </form-select>
          </form-group>
        </form-grid>
        <h2>Weitere Person</h2>
        <form-grid>
          <form-group :error="errors.sub_tenant_yn">
            <form-label :error="errors.sub_tenant_yn">Werden Sie die Wohnung teilen?</form-label>
            <form-select
              v-model="form.sub_tenant_yn"
              :options="[
                { label: 'Ja', value: 'Ja' },
                { label: 'Nein', value: 'Nein' },
              ]"
              :error="errors.sub_tenant_yn"
              @blur="validateField('sub_tenant_yn')"
              @focus="removeError('sub_tenant_yn')">
            </form-select>
          </form-group>
          <template v-if="form.sub_tenant_yn == 'Ja'">
            <div class="sm:col-span-12 sm:grid sm:grid-cols-12 sm:gap-30">
            <form-group>
              <form-label class="mb-12">Mit wem werden Sie die Wohnung teilen?</form-label>
              <div class="grid grid-cols-12 gap-15">
                <form-group>
                  <form-checkbox id="sub_tenant_1" v-model="form.sub_tenant_type_1">
                    <template v-slot:label>
                      Ehepartner*in
                    </template>
                  </form-checkbox>
                </form-group>
              </div>
            </form-group>
            </div>
          </template>
        </form-grid>
        
        <!--
        <form-grid>
          <form-group class="col-span-12">
            <form-textarea 
              v-model="form.message" 
              placeholder="Mitteilung"
              :error="errors.message"
              @blur="validateField()"
              @focus="removeError('message')">
            </form-textarea>
          </form-group>
        </form-grid>
        <form-group>
          <button 
            :class="[isValid && !isLoading ? 'bg-ocean text-white hover:bg-black transition-colors' : 'opacity-50 pointer-events-none select-none', 'bg-ocean font-black text-white uppercase py-15 px-20 leading-none inline-flex items-center w-auto text-left']"
            type="button"
            @click.prevent="submit()">
            Absenden
          </button>
        </form-group>
        -->
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
        main_tenant_nationality: 'CH',
        main_tenant_home_town: null,
        main_tenant_email: null,
        main_tenant_private_phone: null,
        main_tenant_work_phone: null,
        main_tenant_occupation: null,
        main_tenant_employment_status: 'Angestellt',
        main_tenant_debt_enforcement_yn: 'Ja, ich hatte Betreibungen in den letzten zwei Jahren',
        main_tenant_current_rent_tenant_role: 'Ich bin Hauptmieter*in',
        main_tenant_current_rent_terminator: 'Ja',
        main_tenant_current_renter_name: null,
        main_tenant_current_renter_phone: null,
        main_tenant_current_renter_rent_duration: 'Weniger als 1 Jahr',
        sub_tenant_yn: 'Nein',
        sub_tenant_type_1: null,
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
        main_tenant_home_town: null,
        main_tenant_email: null,
        main_tenant_private_phone: null,
        main_tenant_work_phone: null,
        main_tenant_occupation: null,
        main_tenant_employment_status: null,
        main_tenant_debt_enforcement_yn: null,
        main_tenant_current_rent_tenant_role: null,
        main_tenant_current_rent_terminator: null,
        main_tenant_current_renter_name: null,
        main_tenant_current_renter_phone: null,
        main_tenant_current_renter_rent_duration: null,
        sub_tenant_yn: null,
        sub_tenant_type: null,
      },

      sub_tenant_types: [
        'Ehepartner*in',
        'Lebenspartner*in mit eingetragener Partnerschaft',
        'Lebenspartner*in',
        'Mitbewohner*in',
        'Kinder'
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
    form: {
      handler(newValue, oldValue) {
        console.log(this.form);
      },
      deep: true
    }
  },

  computed: {
    // isValid() { 
    //   if (
    //     this.form.firstname &&
    //     this.form.name &&
    //     this.form.address &&
    //     this.form.main_tenant_postal_code_city &&
    //     this.form.phone
    //   )
    //   {
    //     return true;
    //   }
    //   return false;
    // },
  },
}
</script>