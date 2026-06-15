<template>
  <p v-if="message" class="text-red-500 text-sm mt-8 block">{{ message }}</p>
</template>
<script>
export default {
  name: "FormFieldError",
  props: {
    message: {
      type: String,
      default: "",
    },
  },
};
</script>
