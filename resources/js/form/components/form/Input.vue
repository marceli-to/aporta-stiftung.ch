<template>
  <input
    :id="id"
    :type="type"
    :value="modelValue"
    :placeholder="placeholder"
    @input="updateInput"
    :class="[$props.error ? 'border-red-500' : 'bg-white', 'border ring-0 focus:ring-0 focus:border-black px-12 py-10 w-full text-lg']"
  />
</template>
<script>
export default {
  name: "FormInput",
  props: {
    
    id: {
      type: String,
      default: "",
    },
    
    modelValue: {
      type: [String, Number],
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

    error: {
      type: Boolean,
      default: false,
    },
  },

  methods: {
    updateInput(event) {
      this.$emit("update:modelValue", event.target.value);
    }
  }
};
</script>