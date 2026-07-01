import { ref, computed } from 'vue';

/**
 * Fetches /api/v1/lookups from the Aporta backend and exposes the result as
 * a reactive map. Browser handles ETag / 304 caching automatically.
 *
 * If the fetch fails the form MUST refuse to render dropdowns and show an
 * error state instead — never fall back to bundled stale data.
 */
export function useLookups(url = window.APORTA_LOOKUPS_URL) {
  const lookups = ref(null);
  const loading = ref(false);
  const error = ref(null);

  async function load() {
    if (loading.value) return;
    loading.value = true;
    error.value = null;

    try {
      const response = await fetch(url, {
        credentials: 'omit',
        headers: { Accept: 'application/json' },
      });

      if (!response.ok) {
        throw new Error(`Lookups fetch failed: ${response.status}`);
      }

      lookups.value = await response.json();
    } catch (e) {
      error.value = e;
      lookups.value = null;
      console.error('[useLookups]', e);
    } finally {
      loading.value = false;
    }
  }

  function activeOnly(key) {
    const list = lookups.value?.[key];
    if (!Array.isArray(list)) return [];
    return list.filter((entry) => entry.active !== false);
  }

  const ready = computed(() => lookups.value !== null);

  return { lookups, loading, error, ready, load, activeOnly };
}
