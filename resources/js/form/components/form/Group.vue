<template>
  <div :class="'col-span-6 relative ' + $props.cls">
    <slot />
    <field-error :message="message" />
  </div>
</template>
<script>
import FieldError from '@/form/components/form/FieldError.vue';

export default {
  components: {
    FieldError,
  },

  props: {
    error: {
      type: Boolean,
      default: null
    },

    message: {
      type: String,
      default: ''
    },

    cls: {
      type: String,
      default: ''
    }
  }
}
</script>
