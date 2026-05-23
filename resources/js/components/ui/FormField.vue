<script setup>
defineProps({
    label: {
        type: String,
        required: true,
    },
    modelValue: {
        type: [String, Number],
        default: '',
    },
    type: {
        type: String,
        default: 'text',
    },
    autocomplete: {
        type: String,
        default: undefined,
    },
    required: {
        type: Boolean,
        default: false,
    },
    error: {
        type: String,
        default: null,
    },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <label class="field" :class="{ 'field--invalid': error }">
        <span>{{ label }}</span>
        <input
            :value="modelValue"
            :type="type"
            :autocomplete="autocomplete"
            :required="required"
            :aria-invalid="Boolean(error)"
            @input="$emit('update:modelValue', $event.target.value)"
        />
        <span v-if="error" class="field__error">{{ error }}</span>
    </label>
</template>
