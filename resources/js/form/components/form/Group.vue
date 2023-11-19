<template>
  <div :class="[error ? '' : '', 'col-span-6 relative mb-28 sm:mb-0']">
    <slot />
  </div>
</template>
<script>

export default {
  props: {
    error: {
      type: Boolean,
      default: null
    },
  }
}
</script>