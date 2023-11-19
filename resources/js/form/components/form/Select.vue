
<template>
  <select
    :id="id"
    :value="modelValue"
    @input="updateInput"
    :class="[$props.error ? 'border-red-500' : 'bg-white', 'border ring-0 focus:ring-0 focus:border-black px-12 py-10 w-full text-lg outline-none']"
  >
    <option 
      v-for="option in options" 
      :key="option.value" 
      :value="option.value">
      {{ option.label }}
    </option>
  </select>
</template>
<script>
export default {
  name: "FormSelect",
  props: {
    id: {
      type: String,
      default: "",
    },
    modelValue: {
      type: [String, Number],
      default: "",
    },
    options: {
      type: Array,
      default: () => [],
    },
    error: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    updateInput(event) {
      this.$emit("update:modelValue", event.target.value);
    },
  },
};
</script>