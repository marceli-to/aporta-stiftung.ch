<template>
  <textarea
    :id="id"
    :type="type"
    :value="modelValue"
    :placeholder="placeholder"
    @input="updateInput"
    :class="[$props.error ? 'bg-red-100' : 'bg-white', ' border-0 ring-0 focus:ring-0 px-10 lg:py-15 w-full text-base placeholder:text-base placeholder:text-graphite min-h-[150px]']">
  </textarea>
</template>
<script>
export default {
  name: "FormTextarea",
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