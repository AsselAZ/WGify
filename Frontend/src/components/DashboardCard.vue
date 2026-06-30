<template>
  <div class="rounded-xl border border-border bg-card p-4 transition-colors hover:bg-card/80 sm:p-6">
    <div class="flex items-start justify-between gap-4">
      <div class="min-w-0 space-y-2">
        <p class="text-sm font-medium leading-snug text-muted-foreground">
          {{ title }}
        </p>

        <p class="whitespace-nowrap text-2xl font-bold leading-tight text-card-foreground">
          {{ value }}
        </p>

        <p
          v-if="subtitle"
          class="text-xs text-muted-foreground"
        >
          {{ subtitle }}
        </p>

        <p
          v-if="trend"
          :class="[
            'text-xs font-medium',
            trend.positive ? 'text-success' : 'text-destructive',
          ]"
        >
          {{ trend.positive ? '+' : '-' }}{{ trend.value }}% zum Vormonat
        </p>
      </div>

      <div
        :class="[
          'flex h-10 w-10 shrink-0 items-center justify-center rounded-lg sm:h-12 sm:w-12',
          iconBgClass || 'bg-primary/10',
        ]"
      >
        <component
          :is="icon"
          :class="[
            'h-5 w-5 sm:h-6 sm:w-6',
            iconColorClass || 'text-primary',
          ]"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true,
  },
  value: {
    type: [String, Number],
    required: true,
  },
  subtitle: {
    type: String,
    default: '',
  },
  icon: {
    type: Object,
    required: true,
  },
  trend: {
    type: Object,
    default: null,
  },
  iconBgClass: {
    type: String,
    default: '',
  },
  iconColorClass: {
    type: String,
    default: '',
  },
})
</script>