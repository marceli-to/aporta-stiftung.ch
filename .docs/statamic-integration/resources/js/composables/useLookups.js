import { ref, computed } from 'vue'

/**
 * Fetches /api/v1/lookups from the Aporta backend and exposes the result as
 * a reactive map.
 *
 * Caching: the backend issues an ETag and Cache-Control: max-age=86400. The
 * browser handles 304s automatically; nothing extra needed here.
 *
 * Failure behaviour: if the fetch fails (network, 5xx, parse), `error` is set
 * and `lookups` stays null. The form MUST refuse to render dropdowns and show
 * an error state in that case — do not fall back to bundled stale data.
 *
 * Usage:
 *
 *   const { lookups, loading, error, load, activeOnly } = useLookups()
 *   onMounted(load)
 *   // ...
 *   <option v-for="o in activeOnly('districts')" :key="o.slug" :value="o.slug">
 *     {{ o.label }}
 *   </option>
 */
export function useLookups(url = window.APORTA_LOOKUPS_URL) {
	const lookups = ref(null)
	const loading = ref(false)
	const error = ref(null)

	async function load() {
		if (loading.value) return
		loading.value = true
		error.value = null

		try {
			const response = await fetch(url, {
				credentials: 'omit', // Anonymous endpoint — cookies break CORS.
				headers: { Accept: 'application/json' },
			})

			if (!response.ok) {
				throw new Error(`Lookups fetch failed: ${response.status}`)
			}

			lookups.value = await response.json()
		} catch (e) {
			error.value = e
			lookups.value = null
			console.error('[useLookups]', e)
		} finally {
			loading.value = false
		}
	}

	/**
	 * Active entries only — filters out items with active === false.
	 * Use for populating dropdowns the visitor selects from.
	 */
	function activeOnly(key) {
		const list = lookups.value?.[key]
		if (!Array.isArray(list)) return []
		return list.filter((entry) => entry.active !== false)
	}

	/**
	 * True once a successful fetch has populated lookups. Use this to gate
	 * form rendering: while ready === false and error === null, show a spinner.
	 */
	const ready = computed(() => lookups.value !== null)

	return { lookups, loading, error, ready, load, activeOnly }
}
