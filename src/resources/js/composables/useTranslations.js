import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function useTranslations() {
  const page = usePage()
  const translations = computed(() => page.props.translations || {})
  
  const t = (key) => {
    const keys = key.split('.')
    let value = translations.value
    for (const k of keys) {
      value = value?.[k]
      if (value === undefined) return key
    }
    return value || key
  }
  
  return { t, translations }
}

