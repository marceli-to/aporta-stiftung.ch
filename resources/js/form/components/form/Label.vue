<template>
  <label :class="[$props.error ? 'text-red-500' : '', 'text-md mb-8 block']">
    <slot />
    <strong v-if="required">*</strong>
  </label>
</template>
<script>
export default {
  props: {
    required: {
      type: Boolean,
      default: true,
    },
    error: {
      type: Boolean,
      default: false,
    },
  },
}
</script>