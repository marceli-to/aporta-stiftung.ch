<template>
  <div class="border-2 border-red-500 text-red-500 p-15 mb-30 md:mb-50 inline-flex w-auto">
    <p class="text-base md:text-md xl:text-lg">Es sind Fehler aufgetreten, bitte überprüfen Sie ihre Angaben:</p>
    <!-- <ul class="list-disc">
      <li v-for="error in $props.validationErrors" class="ml-24 text-xxs md:text-xs xl:text-sm text-red-500 list-item">
        {{ error }}
      </li>
    </ul> -->
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