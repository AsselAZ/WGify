<script setup>
    import logo from '/logo.png'

    defineProps({
      width: {
        type: Number,
        default: 50
      },
      height: {
        type: Number,
        default: 50
      },
      margin: {
        type: String,
        default: '0 auto'
      },
      display: {
        type: String,
        default: 'block'
      },
    })
</script>

<template>
  <img 
    :src="logo" 
    :alt="logoAlt" 
    :class="['logo', { 'mx-auto': margin === '0 auto' }]"
    :style="{ width: `${width}px`, height: `${height}px`, margin, display}"
  >
</template>