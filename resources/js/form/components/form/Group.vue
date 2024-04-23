<template>
  <div :class="[error ? '' : '', 'col-span-6 relative ' + $props.cls]">
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

    cls: {
      type: String,
      default: ''
    }
  }
}
</script>