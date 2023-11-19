<template>
  <div :class="[error ? '' : '', 'col-span-6 relative mb-28 first-of-type:mb-15 first-of-type:md:mb-0']">
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