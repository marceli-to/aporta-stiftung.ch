<template>
  <div :class="[error ? '' : '', 'col-span-6 relative']">
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