<script setup>
import logo from '/logo.png'

defineProps({
  width: {
    type: Number,
    default: 50,
  },
  height: {
    type: Number,
    default: 50,
  },
  margin: {
    type: String,
    default: '0 auto',
  },
  display: {
    type: String,
    default: 'block',
  },
  alt: {
    type: String,
    default: 'WGify Logo',
  },
})
</script>

<template>
  <img
    :src="logo"
    :alt="alt"
    class="object-contain"
    :style="{
      width: `${width}px`,
      height: `${height}px`,
      margin,
      display,
    }"
  />
</template>