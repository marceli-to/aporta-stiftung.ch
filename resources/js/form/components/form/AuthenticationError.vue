<template>
  <div class="bg-red-500 text-white p-15 flex w-full sm:inline-flex sm:w-auto">
    <p class="font-bold text-base md:text-md xl:text-lg">{{ $props.message }}</p>
  </div>
</template>
<script>
export default {
  props: {
    message: {
      type: String,
      default: 'Es sind Fehler aufgetreten, bitte überprüfen Sie ihre Angaben!'
    }
  }
}
</script>