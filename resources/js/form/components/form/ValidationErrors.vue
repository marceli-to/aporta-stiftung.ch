<template>
  <div class="bg-rosewater p-15 mt-30">
    <h2 class="text-base md:text-md xl:text-lg font-bold text-red-500 mb-10">Es sind Fehler aufgetreten, bitte überprüfen Sie ihre Angaben:</h2>
    <ul class="list-disc">
      <li v-for="error in $props.validationErrors" class="ml-24 text-xxs md:text-xs xl:text-sm text-red-500 list-item">
        {{ error }}
      </li>
    </ul>
  </div>
</template>
<script>
export default {
  props: {
    validationErrors: {
      type: Array,
      default: () => []
    }
  }
}
</script>