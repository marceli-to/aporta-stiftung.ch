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
          <form-group class="col-span-6">
            <form-label :required="true" :errors="errors.main_tenant_salutation">Anrede</form-label>
            <select v-model="form.main_tenant_salutation" class="border ring-0 focus:ring-0 focus:border-black px-12 py-10 w-full text-lg outline-none">
              <option value="Frau">Frau</option>
              <option value="Herr">Herr</option>
            </select>
          </form-group>
        </form-grid>
        <form-grid>
          <form-group :error="errors.main_tenant_lastname" class="mb-15 sm:mb-0">
            <form-label :required="true" :error="errors.main_tenant_lastname">Familienname</form-label>
            <form-input 
              type="text" 
              v-model="form.main_tenant_lastname" 
              :error="errors.main_tenant_lastname"
              @blur="validateField('main_tenant_lastname')"
              @focus="removeError('main_tenant_lastname')">
            </form-input>
          </form-group>
          <form-group :error="errors.main_tenant_firstname">
            <form-label :required="true" :error="errors.main_tenant_firstname">Vorname</form-label>
            <form-input 
              type="text" 
              v-model="form.main_tenant_firstname" 
              :error="errors.main_tenant_firstname"
              @blur="validateField('main_tenant_firstname')"
              @focus="removeError('main_tenant_firstname')">
            </form-input>
          </form-group>
        </form-grid>
        <form-grid>
          <form-group :error="errors.main_tenant_street_number" class="mb-15 sm:mb-0">
            <form-label :required="true" :error="errors.main_tenant_street_number">Strasse und Hausnummer</form-label>
            <form-input 
              type="text" 
              v-model="form.main_tenant_street_number" 
              :error="errors.main_tenant_street_number"
              @blur="validateField('main_tenant_street_number')"
              @focus="removeError('main_tenant_street_number')">
            </form-input>
          </form-group>
          <form-group :error="errors.main_tenant_postal_code_city">
            <form-label :required="true" :error="errors.main_tenant_postal_code_city">PLZ und Ort</form-label>
            <form-input 
              type="text" 
              v-model="form.main_tenant_postal_code_city" 
              :error="errors.main_tenant_postal_code_city"
              @blur="validateField('main_tenant_postal_code_city')"
              @focus="removeError('main_tenant_postal_code_city')">
            </form-input>
          </form-group>
        </form-grid>
        <form-grid>
          <form-group :error="errors.main_tenant_birthdate" class="mb-15 sm:mb-0">
            <form-label :required="true" :error="errors.main_tenant_birthdate">Geburtsdatum</form-label>
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
            <form-label :required="true" :error="errors.main_tenant_marital_status">Familienstand</form-label>
            <select v-model="form.main_tenant_marital_status" class="border ring-0 focus:ring-0 focus:border-black px-12 py-10 w-full text-lg outline-none">
              <option value="ledig">ledig</option>
              <option value="verheiratet">verheiratet</option>
              <option value="geschieden">geschieden</option>
              <option value="eingetragene Partnerschaft">eingetragene Partnerschaft</option>
              <option value="aufgelöste Partnerschaft">aufgelöste Partnerschaft</option>
              <option value="verwitwet">verwitwet</option>
            </select>
          </form-group>
        </form-grid>
        <form-grid>
          <form-group :error="errors.main_tenant_nationality" class="col-span-6">
            <form-label :required="true" :errors="errors.main_tenant_nationality">Nationalität</form-label>
            <select v-model="form.main_tenant_nationality" class="border ring-0 focus:ring-0 focus:border-black px-12 py-10 w-full text-lg outline-none">
              <option value="CH">CH</option>
              <option value="Andere">Andere</option>
            </select>
          </form-group>
        </form-grid>
        <form-grid>
          <form-group :error="errors.main_tenant_private_phone" class="mb-15 md:mb-0">
            <form-label :required="true" :error="errors.main_tenant_private_phone">Telefon* (privat)</form-label>
            <form-input 
              type="text" 
              v-model="form.main_tenant_private_phone" 
              :error="errors.main_tenant_private_phone"
              @blur="validateField('main_tenant_private_phone')"
              @focus="removeError('main_tenant_private_phone')">
            </form-input>
          </form-group>
          <form-group :error="errors.main_tenant_work_phone">
            <form-label :required="true" :error="errors.main_tenant_work_phone">Telefon* (geschäftlich)</form-label>
            <form-input 
              type="text" 
              v-model="form.main_tenant_work_phone" 
              :error="errors.main_tenant_work_phone"
              @blur="validateField('main_tenant_work_phone')"
              @focus="removeError('main_tenant_work_phone')">
            </form-input>
          </form-group>
        </form-grid>
        <form-grid>
          <form-group :error="errors.main_tenant_email" class="mb-15 md:mb-0">
            <form-label :required="true" :error="errors.main_tenant_email">E-Mail-Adresse</form-label>
            <form-input 
              type="email" 
              v-model="form.main_tenant_email" 
              :error="errors.main_tenant_email"
              @blur="validateEmail('main_tenant_email')"
              @focus="removeError('main_tenant_email')">
            </form-input>
          </form-group>
          <form-group :error="errors.main_tenant_occupation">
            <form-label :required="true" :error="errors.main_tenant_occupation">Beruf</form-label>
            <form-input 
              type="text" 
              v-model="form.main_tenant_occupation" 
              :error="errors.main_tenant_occupation"
              @blur="validateField('main_tenant_occupation')"
              @focus="removeError('main_tenant_occupation')">
            </form-input>
          </form-group>
        </form-grid>
        <form-grid>
          <form-group :error="errors.main_tenant_employment_status" class="mb-15 md:mb-0">
            <form-label :required="true" :error="errors.main_tenant_employment_status">Erwerbssituation</form-label>
            <select v-model="form.main_tenant_employment_status" class="border ring-0 focus:ring-0 focus:border-black px-12 py-10 w-full text-lg outline-none">
              <option value="Angestellt">Angestellt</option>
              <option value="Student*in">Student*in</option>
              <option value="Selbständig">Selbständig</option>
              <option value="Arbeitslos">Arbeitslos</option>
              <option value="IV-Rente">IV-Rente</option>
              <option value="Im Ruhestand (pensioniert)">Im Ruhestand (pensioniert)</option>
              <option value="Familienmanager*in">Familienmanager*in</option>
            </select>
          </form-group>
          <form-group :error="errors.main_tenant_debt_enforcement_yn">
            <form-label :required="true" :error="errors.main_tenant_debt_enforcement_yn">Betreibungen</form-label>
            <select v-model="form.main_tenant_debt_enforcement_yn" class="border ring-0 focus:ring-0 focus:border-black px-12 py-10 w-full text-lg outline-none">
              <option value="Ja, ich hatte Betreibungen in den letzten zwei Jahren">Ja, ich hatte Betreibungen in den letzten zwei Jahren</option>
              <option value="Nein, ich hatte keine Betreibungen in den letzten zwei Jahren">Nein, ich hatte keine Betreibungen in den letzten zwei Jahren</option>
            </select>
          </form-group>
        </form-grid>
        <h2>Aktuelle Wohnsituation</h2>
        <form-grid>
          <form-group>
            <form-label :required="true" :error="errors.main_tenant_current_rent_tenant_role">Aktuelle Wohnsituation</form-label>
            <select v-model="form.main_tenant_current_rent_tenant_role" class="border ring-0 focus:ring-0 focus:border-black px-12 py-10 w-full text-lg outline-none">
              <option value="Ich bin Hauptmieter*in">Ich bin Hauptmieter*in</option>
              <option value="Ich bin Untermieter*in">Ich bin Untermieter*in</option>
            </select>
          </form-group>
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
        main_tenant_email: null,
        main_tenant_private_phone: null,
        main_tenant_work_phone: null,
        main_tenant_occupation: null,
        main_tenant_employment_status: 'Angestellt',
        main_tenant_debt_enforcement_yn: 'Ja, ich hatte Betreibungen in den letzten zwei Jahren',
        main_tenant_current_rent_tenant_role: 'Ich bin Hauptmieter*in',
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
        main_tenant_email: null,
        main_tenant_private_phone: null,
        main_tenant_work_phone: null,
        main_tenant_occupation: null,
        main_tenant_employment_status: null,
        main_tenant_debt_enforcement_yn: null,
        main_tenant_current_rent_tenant_role: null,
      },

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