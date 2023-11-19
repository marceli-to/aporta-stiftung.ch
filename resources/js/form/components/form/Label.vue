<template>
  <label :class="[$props.error ? 'text-red-500' : '', 'text-lg mb-6 block']">
    <slot />
    <strong v-if="required">*</strong>
  </label>
</template>
<script>
export default {
  props: {
    required: {
      type: Boolean,
      default: false,
    },
    error: {
      type: Boolean,
      default: false,
    },
  },
}
</script>