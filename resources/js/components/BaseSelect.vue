<template>
    <div>
        <label v-if="label">{{ label }}</label>
        <select :value="value" v-on="$listeners" @change="updateValue" v-bind="$attrs">
            <option
                    v-for="option in options"
                    :value="option.value"
                    :key="option.value"
                    :selected="option.label === value"
            >{{ option.label }}</option>
        </select>
    </div>
</template>

<script>
  export default {
    name   : "BaseSelect",
    inheritAttrs: false,
    props  : {
      options: {
        type: Array,
        required: true,
      },
      label: {
        type   : String,
        default: '',
      },
      value: [String, Number],
    },
    methods: {
      updateValue(event) {
        this.$emit('input', event.target.value)
      }
    }
  }
</script>

<style scoped>

</style>
