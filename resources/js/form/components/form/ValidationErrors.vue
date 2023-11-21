<template>
  <div class="fixed z-[200] top-0 right-0 sm:top-20 sm:right-20 bg-red-500 text-white p-15 mb-30 md:mb-50 flex w-full sm:inline-flex sm:w-auto">
    <p class="font-bold text-base md:text-md xl:text-lg">Es sind Fehler aufgetreten, bitte überprüfen Sie ihre Angaben!</p>
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