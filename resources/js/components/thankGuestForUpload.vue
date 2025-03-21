<script setup>
import { PartyPopper } from 'lucide-vue-next'
import { useTranslate } from '@tolgee/vue'

const { t } = useTranslate()
</script>

<template>
  <div class="thank-guest-for-upload">
    <svg id="gradientDefs">
      <linearGradient id="gradient">
        <stop offset="0%" style="stop-color: var(--link-color); stop-opacity: 1" />
        <stop offset="100%" style="stop-color: var(--link-color-hover); stop-opacity: 1" />
      </linearGradient>
    </svg>
    <PartyPopper class="success-message-icon" />
    <p class="success-message">{{ t('guest.upload_success') }}</p>
    <p class="px-5">
      <strong>{{ t('guest.upload_success_message') }}</strong>
    </p>
  </div>
</template>
