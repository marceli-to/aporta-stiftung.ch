<template>
  <div class="relative">
    <label :for="id" class="break-words block text-lg hyphens-auto cursor-pointer pl-45 select-none">
      <input
        :id="id"
        type="checkbox"
        :value="modelValue"
        :placeholder="placeholder"
        @input="updateInput"
        class="opacity-0 absolute cursor-pointer h-0 w-0"
      />
      <span class="border border-black w-30 h-30 absolute left-0 top-0 flex items-center justify-center">
        <svg width="23" height="19" viewBox="0 0 23 19" fill="none" xmlns="http://www.w3.org/2000/svg" class="hidden text-black w-16 h-auto">
          <path d="M22.92 4.304L19.157 0.541003L8.668 11.03L3.922 6.284L0.158997 10.047L8.66699 18.555L22.92 4.304Z" fill="currentColor"/>
        </svg>
      </span>
      <slot name="label"></slot>
    </label>
  </div>
</template>
<script>

export default {
  name: "FormCheckbox",
  props: {
    
    id: {
      type: String,
      default: "",
    },
    
    modelValue: {
      type: [String, Number, Array],
      default: "",
    },

    type: {
      type: String,
      default: "text",
    },

    placeholder: {
      type: String,
      default: "",
    },
  },

  methods: {
    updateInput(event) {
      this.$emit("update:modelValue", event.target.value);
    }
  }
}
</script>

<style scoped>
input[type="checkbox"]:checked ~ span svg {
  @apply block
}
</style>
