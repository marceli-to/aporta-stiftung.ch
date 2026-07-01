<template>
  <select
    :id="id"
    :value="bindValue"
    @input="updateInput"
    :class="[$props.error ? 'border-red-500' : 'bg-white', 'border ring-0 focus:ring-0 focus:border-black px-12 py-10 w-full text-lg outline-none']"
  >
    <option
      v-for="(option, idx) in options"
      :key="idx"
      :value="toStringValue(option.value)">
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
      // Accept anything (string, number, boolean, null) — option values may be
      // any of these and we round-trip them through string for HTML matching
      // while preserving the original type when emitting back up.
      default: null,
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
  computed: {
    bindValue() {
      return this.toStringValue(this.modelValue);
    },
  },
  methods: {
    toStringValue(v) {
      return v === null || v === undefined ? "" : String(v);
    },
    updateInput(event) {
      const str = event.target.value;
      // Look up the option whose stringified value matches what HTML emitted,
      // so we re-emit the original JS type (boolean, null, etc.).
      const opt = this.options.find((o) => this.toStringValue(o.value) === str);
      this.$emit("update:modelValue", opt ? opt.value : str);
    },
  },
};
</script>
