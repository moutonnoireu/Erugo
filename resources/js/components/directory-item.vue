<!-- directory-item.vue -->
<template>
  <div class="directory-structure" :class="{ 'is-root': isRoot }">
    <!-- Root-level files -->
    <div v-if="structure.files && structure.files.length" class="root-files">
      <div class="upload-basket-item" v-for="file in structure.files" :key="file.fullPath || file.name">
        <div class="name">
          {{ file.name }}
        </div>
        <div class="meta">
          <div class="size">
            {{ niceFileSize(file.size) }}
          </div>
          <div class="type">
            {{ niceFileType(file.type) }}
          </div>
        </div>
        <div class="hover-actions">
          <button class="icon-only" @click="$emit('remove-file', file)">
            <Trash />
          </button>
        </div>
      </div>
    </div>

    <!-- Directories -->
    <template v-for="(dirContent, dirName) in getDirectories(structure)" :key="dirName">
      <div class="directory-header">
        <FolderPlus />
        <span>{{ dirName }}</span>
      </div>

      <!-- Files in this directory -->
      <div class="directory-files" v-if="dirContent.files && dirContent.files.length">
        <div class="upload-basket-item" v-for="file in dirContent.files" :key="file.fullPath || file.name">
          <div class="name">
            {{ file.name }}
          </div>
          <div class="meta">
            <div class="size">
              {{ niceFileSize(file.size) }}
            </div>
            <div class="type">
              {{ niceFileType(file.type) }}
            </div>
          </div>
          <div class="hover-actions">
            <button class="icon-only" @click="$emit('remove-file', file)">
              <Trash />
            </button>
          </div>
        </div>
      </div>

      <!-- Subdirectories (recursive) -->
      <directory-item
        :structure="dirContent.directories"
        :is-root="false"
        @remove-file="$emit('remove-file', $event)"
        class="subdirectory"
      />
    </template>
  </div>
</template>

<script setup>
import { FolderPlus, Trash } from 'lucide-vue-next'
import { niceFileSize, niceFileType } from '../utils'

defineProps({
  structure: {
    type: Object,
    required: true
  },
  isRoot: {
    type: Boolean,
    default: false
  }
})

defineEmits(['remove-file'])

// Helper function to get directories from structure
function getDirectories(structure) {
  const directories = {}

  // Filter out non-directory entries
  Object.entries(structure).forEach(([key, value]) => {
    if (key !== 'files' && typeof value === 'object') {
      directories[key] = value
    }
  })

  return directories
}
</script>

<style scoped>
.directory-structure {
  width: 100%;
  padding-left: 0;
}

.subdirectory {
  padding-left: 20px;
  border-left: 1px dashed var(--border-color, #ccc);
  margin-left: 10px;
}

.directory-header {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: bold;
  margin: 8px 0;
  padding: 4px 8px;
  background-color: var(--panel-item-background-color, #f5f5f5);
  border-radius: 4px;
}

.directory-files {
  margin-left: 16px;
}
</style>
