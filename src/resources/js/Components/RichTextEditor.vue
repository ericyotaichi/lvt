<template>
  <div class="rich-text-editor">
    <div :id="editorId" ref="editorElement"></div>
  </div>
</template>

<script setup>
import { onMounted, onBeforeUnmount, watch, ref } from 'vue'
import Quill from 'quill'
import 'quill/dist/quill.snow.css'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: '请输入内容...'
  }
})

const emit = defineEmits(['update:modelValue'])

const editorElement = ref(null)
const editorId = `quill-editor-${Math.random().toString(36).substr(2, 9)}`
let quill = null

// 图片大小调整功能
const setupImageResize = () => {
  if (!quill) return

  // 等待图片加载完成后再设置事件监听
  const handleImageClick = (e) => {
    const img = e.target
    if (img.tagName !== 'IMG') return

    e.preventDefault()
    e.stopPropagation()

    // 获取当前图片的宽度
    let currentWidthValue = 500
    if (img.style.width) {
      const widthMatch = img.style.width.match(/(\d+)/)
      if (widthMatch) currentWidthValue = parseInt(widthMatch[1])
    } else if (img.width) {
      currentWidthValue = img.width
    } else if (img.offsetWidth) {
      currentWidthValue = img.offsetWidth
    }

    // 弹出对话框让用户输入宽度
    const newWidth = prompt('請輸入圖片寬度（像素，留空則使用原始大小）：', currentWidthValue)
    
    if (newWidth === null) return // 用户取消

    // 更新图片大小
    if (newWidth === '' || newWidth === '0') {
      // 移除宽度限制，使用原始大小
      img.style.width = ''
      img.style.height = ''
      img.removeAttribute('width')
      img.removeAttribute('height')
    } else {
      const width = parseInt(newWidth)
      if (!isNaN(width) && width > 0) {
        // 如果图片已加载，计算高度以保持比例
        if (img.naturalWidth && img.naturalHeight) {
          const aspectRatio = img.naturalHeight / img.naturalWidth
          const height = Math.round(width * aspectRatio)
          img.style.height = height + 'px'
          img.setAttribute('height', height)
        } else {
          // 如果图片未加载，等待加载完成
          img.onload = () => {
            const aspectRatio = img.naturalHeight / img.naturalWidth
            const height = Math.round(width * aspectRatio)
            img.style.height = height + 'px'
            img.setAttribute('height', height)
          }
        }
        
        img.style.width = width + 'px'
        img.setAttribute('width', width)
      }
    }

    // 触发内容更新
    setTimeout(() => {
      const content = quill.root.innerHTML
      emit('update:modelValue', content)
    }, 100)
  }

  // 监听编辑器点击事件
  quill.root.addEventListener('click', handleImageClick, true)
}

onMounted(() => {
  if (!editorElement.value) return

  quill = new Quill(editorElement.value, {
    theme: 'snow',
    placeholder: props.placeholder,
    modules: {
      toolbar: [
        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        [{ 'script': 'sub'}, { 'script': 'super' }],
        [{ 'indent': '-1'}, { 'indent': '+1' }],
        [{ 'color': [] }, { 'background': [] }],
        [{ 'align': [] }],
        ['link', 'image'],
        ['clean']
      ]
    }
  })

  // 设置初始内容
  if (props.modelValue) {
    quill.root.innerHTML = props.modelValue
  }

  // 设置图片大小调整功能
  setupImageResize()

  // 监听内容变化
  quill.on('text-change', () => {
    const content = quill.root.innerHTML
    emit('update:modelValue', content)
  })
})

onBeforeUnmount(() => {
  if (quill) {
    quill = null
  }
})

// 监听外部值变化
watch(() => props.modelValue, (newValue) => {
  if (quill && quill.root.innerHTML !== newValue) {
    quill.root.innerHTML = newValue || ''
  }
})
</script>

<style scoped>
.rich-text-editor {
  background: white;
}

:deep(.ql-container) {
  min-height: 200px;
  font-size: 16px;
}

:deep(.ql-editor) {
  min-height: 200px;
}

:deep(.ql-editor img) {
  cursor: pointer;
  max-width: 100%;
  transition: opacity 0.2s;
}

:deep(.ql-editor img:hover) {
  opacity: 0.8;
  outline: 2px solid #4CAF50;
  outline-offset: 2px;
}
</style>

