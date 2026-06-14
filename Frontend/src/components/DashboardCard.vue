<template>
  <div class="rounded-xl border border-border bg-card p-6 transition-colors hover:bg-card/80">
    <div class="flex items-start justify-between">
      <div class="space-y-2">
        <p class="text-sm font-medium text-muted-foreground">{{ title }}</p>
        <p class="text-3xl font-bold text-card-foreground">{{ value }}</p>
        <p v-if="subtitle" class="text-xs text-muted-foreground">{{ subtitle }}</p>
        <p
          v-if="trend"
          :class="['text-xs font-medium', trend.positive ? 'text-success' : 'text-destructive']"
        >
          {{ trend.positive ? '+' : '-' }}{{ trend.value }}% zum Vormonat
        </p>
      </div>
      <div :class="['flex h-12 w-12 items-center justify-center rounded-lg', iconBgClass ?? 'bg-primary/10']">
        <component :is="icon" :class="['h-6 w-6', iconColorClass ?? 'text-primary']" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Component } from 'vue'

defineProps<{
  title: string
  value: string | number
  subtitle?: string
  icon: Component
  trend?: { value: number; positive: boolean }
  iconBgClass?: string
  iconColorClass?: string
}>()
</script>
