<!--
  Skeleton showing how to wire the existing Register.vue against the new contract.
  This is not a drop-in replacement — it's a guide for adapting the existing
  form. Take the patterns shown here (lookups gate, payload shape, dog-keyword
  inline check) and apply them to the real form.

  Key changes from the legacy form:
    - Lookups fetched on mount; form refuses to render if fetch fails.
    - Field names follow the contract (see Statamic-Integration.md §2.2 and §3.1).
    - "No dogs" check happens inline on pets_description.
    - Submit posts to the Laravel controller (/anmelden), not the backend directly.
-->

<script setup>
import { reactive, ref, onMounted } from 'vue'
import { useLookups } from './composables/useLookups.js'

const { lookups, loading, error, ready, load, activeOnly } = useLookups()
onMounted(load)

// The form's reactive state. Field names align 1:1 with the contract.
// Nested where the contract is nested.
const form = reactive({
	shares_apartment: false,

	main_applicant: {
		salutation: null,
		first_name: '',
		last_name: '',
		street: '',
		street_number: '',
		postal_code: '',
		city: '',
		birth_date: '',
		marital_status: null,
		nationality: null,
		place_of_origin: '',
		residence_permit: null,
		swiss_residence_since: '',
		mobile_phone: '',
		landline_phone: '',
		email: '',
		occupation: '',
		employment_status: null,
		debt_enforcement_last_2y: false,
		employer: {
			name: '',
			workload_percent: null,
			annual_income_bracket: null,
		},
		current_housing: {
			tenant_role: null,
			terminated_by_landlord: false,
			termination_reason: '',
			landlord_name: '',
			landlord_contact_person: '',
			landlord_phone: '',
			rent_duration: null,
			previous_landlord: '',
		},
	},

	co_applicant: null, // or the same shape as main_applicant + relationship_to_main + same_address_as_main

	housing_wish: {
		earliest_move_in: '',
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
		plays_music: false,
		musical_instruments: '',
		has_pets: false,
		pets_description: '',
		remarks: '',
	},

	children: [], // each: { position: 1, birth_year: 2020 }
})

// Inline "no dogs" check. Show the message under the field when this is true.
function isDogMention(value) {
	return typeof value === 'string' && /\b(Hund|Hunde|Dog)\b/i.test(value)
}

const submitting = ref(false)
const serverErrors = ref({})

async function submit() {
	if (submitting.value) return
	submitting.value = true
	serverErrors.value = {}

	try {
		const response = await fetch('/anmelden', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'Accept': 'application/json',
				'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
			},
			body: JSON.stringify(form),
		})

		if (response.ok) {
			window.location.href = response.headers.get('Location') || '/danke'
			return
		}

		if (response.status === 422) {
			const body = await response.json()
			serverErrors.value = body.errors || {}
			return
		}

		throw new Error(`Submit failed: ${response.status}`)
	} catch (e) {
		serverErrors.value = { _: ['Unerwarteter Fehler. Bitte versuchen Sie es erneut.'] }
		console.error('[submit]', e)
	} finally {
		submitting.value = false
	}
}
</script>

<template>
	<!--
		Render gate: until lookups are loaded, show a spinner. If they fail,
		show an error. Never render the form with empty dropdowns.
	-->
	<div v-if="loading" class="loading">Formular wird geladen…</div>

	<div v-else-if="error" class="error">
		Das Anmeldeformular ist momentan nicht verfügbar. Bitte versuchen Sie es in ein paar Minuten erneut.
	</div>

	<form v-else-if="ready" @submit.prevent="submit" novalidate>
		<!-- Salutation — example of a slug-driven select fed from /lookups. -->
		<label>
			Anrede
			<select v-model="form.main_applicant.salutation">
				<option :value="null" disabled>Bitte wählen</option>
				<option v-for="opt in activeOnly('salutations')" :key="opt.slug" :value="opt.slug">
					{{ opt.label }}
				</option>
			</select>
		</label>

		<!-- Districts — example of a multi-select. -->
		<fieldset>
			<legend>Bevorzugte Kreise</legend>
			<label v-for="opt in activeOnly('districts')" :key="opt.slug">
				<input
					type="checkbox"
					:value="opt.slug"
					v-model="form.housing_wish.districts"
				/>
				{{ opt.label }}
			</label>
		</fieldset>

		<!-- pets_description with inline no-dogs guard. -->
		<label v-if="form.household_info.has_pets">
			Welche Haustiere?
			<input v-model="form.household_info.pets_description" maxlength="200" />
			<p v-if="isDogMention(form.household_info.pets_description)" class="error">
				Hunde sind in den Liegenschaften nicht erlaubt.
			</p>
		</label>

		<button type="submit" :disabled="submitting || isDogMention(form.household_info.pets_description)">
			Anmeldung absenden
		</button>

		<p v-if="serverErrors._">{{ serverErrors._[0] }}</p>
	</form>
</template>
