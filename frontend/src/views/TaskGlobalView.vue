<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'
import api from '@/utils/axios'

interface SimpleProject {
  id: number
  name: string
}

interface SimpleCategory {
  id: number
  name: string
}

interface TaskItem {
  id: number
  title: string
  description?: string
  project_id?: number
  category_id?: number
  due_date: string
  project: SimpleProject | null
  category: SimpleCategory | null
}

const tasks = ref<TaskItem[]>([])
const projects = ref<SimpleProject[]>([])
const categories = ref<SimpleCategory[]>([])
const search = ref('')
const selectedCategoryId = ref('')
const selectedDeadline = ref('')
const isLoading = ref(false)
const isSavingForm = ref(false)
const isDeletingId = ref<number | null>(null)
const errorMessage = ref('')
const isTaskFormModalOpen = ref(false)
const isTaskDetailModalOpen = ref(false)
const selectedTask = ref<TaskItem | null>(null)
let searchDebounceTimer: ReturnType<typeof setTimeout> | null = null

const formMode = ref<'create' | 'edit'>('create')
const editingId = ref<number | null>(null)
const form = ref({
  project_id: '',
  category_id: '',
  title: '',
  description: '',
  due_date: '',
})

const resetForm = () => {
  formMode.value = 'create'
  editingId.value = null
  form.value = {
    project_id: '',
    category_id: '',
    title: '',
    description: '',
    due_date: '',
  }
}

const openCreateTaskModal = () => {
  resetForm()
  isTaskFormModalOpen.value = true
}

const closeTaskFormModal = () => {
  isTaskFormModalOpen.value = false
}

const openTaskDetailModal = (task: TaskItem) => {
  selectedTask.value = task
  isTaskDetailModalOpen.value = true
}

const fetchProjects = async () => {
  const response = await api.get<{ data: SimpleProject[] }>('/projects')
  projects.value = response.data.data
}

const fetchCategories = async () => {
  const response = await api.get<{ data: SimpleCategory[] }>('/categories')
  categories.value = response.data.data
}

const fetchTasks = async () => {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const response = await api.get<{ data: TaskItem[] }>('/tasks', {
      params: {
        search: search.value.trim() || undefined,
        category_id: selectedCategoryId.value || undefined,
        deadline: selectedDeadline.value || undefined,
      },
    })

    tasks.value = response.data.data
  } catch {
    errorMessage.value = 'Failed to load tasks.'
  } finally {
    isLoading.value = false
  }
}

const formatDate = (value: string) => {
  return new Date(value).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
  })
}

const getCategoryHighlightClass = (categoryName?: string) => {
  switch (categoryName) {
    case 'TODO':
      return 'bg-red-100 text-red-700'
    case 'IN PROGRESS':
      return 'bg-orange-100 text-orange-700'
    case 'TESTING':
      return 'bg-amber-100 text-amber-700'
    case 'DONE':
      return 'bg-emerald-100 text-emerald-700'
    default:
      return 'bg-gray-100 text-gray-700'
  }
}

const submitTask = async () => {
  if (
    !form.value.project_id ||
    !form.value.category_id ||
    !form.value.title.trim() ||
    !form.value.description.trim() ||
    !form.value.due_date
  ) {
    errorMessage.value = 'Please complete all task fields.'
    return
  }

  isSavingForm.value = true
  errorMessage.value = ''

  try {
    const payload = {
      project_id: Number(form.value.project_id),
      category_id: Number(form.value.category_id),
      title: form.value.title.trim(),
      description: form.value.description.trim(),
      due_date: form.value.due_date,
    }

    if (formMode.value === 'create') {
      await api.post('/tasks', payload)
    } else if (editingId.value) {
      await api.put(`/tasks/${editingId.value}`, payload)
    }

    resetForm()
    closeTaskFormModal()
    await fetchTasks()
  } catch {
    errorMessage.value = 'Failed to save task.'
  } finally {
    isSavingForm.value = false
  }
}

const startEdit = (task: TaskItem) => {
  formMode.value = 'edit'
  editingId.value = task.id
  form.value = {
    project_id: String(task.project_id ?? task.project?.id ?? ''),
    category_id: String(task.category_id ?? task.category?.id ?? ''),
    title: task.title,
    description: task.description ?? '',
    due_date: task.due_date.slice(0, 10),
  }
  isTaskFormModalOpen.value = true
}

const deleteTask = async (task: TaskItem) => {
  const confirmed = window.confirm(`Delete task \"${task.title}\"?`)
  if (!confirmed) {
    return
  }

  isDeletingId.value = task.id
  errorMessage.value = ''

  try {
    await api.delete(`/tasks/${task.id}`)
    tasks.value = tasks.value.filter((item) => item.id !== task.id)
  } catch {
    errorMessage.value = 'Failed to delete task.'
  } finally {
    isDeletingId.value = null
  }
}

onMounted(fetchTasks)

watch(search, () => {
  if (searchDebounceTimer) {
    clearTimeout(searchDebounceTimer)
  }

  searchDebounceTimer = setTimeout(() => {
    fetchTasks()
  }, 300)
})

onMounted(async () => {
  try {
    await Promise.all([fetchProjects(), fetchCategories()])
  } catch {
    errorMessage.value = 'Failed to load task form options.'
  }
})
</script>

<template>
  <section class="min-h-[calc(100vh-3rem)] bg-[#333333] p-8 text-white">
    <div class="mx-auto flex max-w-7xl flex-col" style="row-gap: 6mm;">
      <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <h1 class="text-2xl font-bold text-[#cf73a4]">Task</h1>

        <div class="flex w-full flex-col gap-x-[3mm] gap-y-[6mm] md:w-auto md:flex-row">
          <form class="flex w-full max-w-md gap-x-[3mm] gap-y-[6mm]" @submit.prevent="fetchTasks">
            <input
              v-model="search"
              type="text"
              placeholder="Search task by title..."
              class="w-full rounded-lg border border-white/20 bg-white px-3 py-2 text-sm text-[#333333] outline-none"
            />
            <button
              type="submit"
              class="rounded-lg bg-[#cf73a4] px-4 py-2 text-sm font-semibold text-white"
            >
              Search
            </button>
          </form>

          <select
            v-model="selectedCategoryId"
            class="rounded-lg border border-white/20 bg-white px-3 py-2 text-sm text-[#333333] outline-none"
            @change="fetchTasks"
          >
            <option value="">Semua Category</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>

          <input
            v-model="selectedDeadline"
            type="date"
            class="rounded-lg border border-white/20 bg-white px-3 py-2 text-sm text-[#333333] outline-none"
            @change="fetchTasks"
          />

          <button
            type="button"
            class="rounded-lg bg-[#cf73a4] px-4 py-2 text-sm font-semibold text-white"
            @click="openCreateTaskModal"
          >
            + Tambah Task
          </button>
        </div>
      </div>

      <p v-if="errorMessage" class="rounded-lg bg-white/10 px-4 py-2 text-sm">{{ errorMessage }}</p>
      <p v-if="isLoading" class="text-sm text-white/80">Loading tasks...</p>

      <div v-else class="overflow-hidden rounded-xl border border-white/10 bg-white shadow-lg">
        <table class="min-w-full text-left text-sm text-[#333333]">
          <thead class="bg-[#f3f3f3] text-xs uppercase tracking-wide text-[#555555]">
            <tr>
              <th class="px-4 py-3">Task Title</th>
              <th class="px-4 py-3">Project Name</th>
              <th class="px-4 py-3">Category</th>
              <th class="px-4 py-3">Due Date</th>
              <th class="px-4 py-3">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="tasks.length === 0">
              <td colspan="5" class="px-4 py-6 text-center text-[#666666]">No tasks found.</td>
            </tr>

            <tr v-for="task in tasks" :key="task.id" class="border-t border-[#eeeeee]">
              <td class="px-4 py-3 font-semibold">{{ task.title }}</td>
              <td class="px-4 py-3">{{ task.project?.name ?? '-' }}</td>
              <td class="px-4 py-3">
                <span
                  class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"
                  :class="getCategoryHighlightClass(task.category?.name)"
                >
                  {{ task.category?.name ?? 'PENDING' }}
                </span>
              </td>
              <td class="px-4 py-3">{{ formatDate(task.due_date) }}</td>
              <td class="px-4 py-3">
                <div class="flex flex-wrap gap-x-[3mm] gap-y-[6mm]">
                  <button
                    type="button"
                    class="rounded-lg border border-[#cf73a4] px-3 py-1.5 text-xs font-semibold text-[#cf73a4] disabled:opacity-60"
                    :disabled="isDeletingId === task.id"
                    @click="openTaskDetailModal(task)"
                  >
                    Detail
                  </button>

                  <button
                    type="button"
                    class="rounded-lg border border-[#cf73a4] px-3 py-1.5 text-xs font-semibold text-[#cf73a4] disabled:opacity-60"
                    :disabled="isDeletingId === task.id"
                    @click="startEdit(task)"
                  >
                    Edit
                  </button>
                  <button
                    type="button"
                    class="rounded-lg bg-[#cf73a4] px-3 py-1.5 text-xs font-semibold text-white disabled:opacity-60"
                    :disabled="isDeletingId === task.id"
                    @click="deleteTask(task)"
                  >
                    Delete
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div
        v-if="isTaskFormModalOpen"
        class="fixed inset-0 z-40 flex items-center justify-center bg-black/40 p-4"
      >
        <div class="w-full max-w-xl rounded-xl bg-white p-5 text-[#333333] shadow-xl">
          <h2 class="text-base font-bold text-[#cf73a4]">
            {{ formMode === 'create' ? 'Create Task' : 'Edit Task' }}
          </h2>

          <form class="mt-3 grid gap-3 md:grid-cols-2" @submit.prevent="submitTask">
            <select
              v-model="form.project_id"
              class="rounded-lg border border-[#dddddd] px-3 py-2 text-sm outline-none"
            >
              <option value="">Select project</option>
              <option v-for="project in projects" :key="project.id" :value="project.id">
                {{ project.name }}
              </option>
            </select>

            <select
              v-model="form.category_id"
              class="rounded-lg border border-[#dddddd] px-3 py-2 text-sm outline-none"
            >
              <option value="">Select category</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>

            <input
              v-model="form.title"
              type="text"
              placeholder="Task title"
              class="rounded-lg border border-[#dddddd] px-3 py-2 text-sm outline-none"
            />

            <input
              v-model="form.due_date"
              type="date"
              class="rounded-lg border border-[#dddddd] px-3 py-2 text-sm outline-none"
            />

            <textarea
              v-model="form.description"
              rows="3"
              placeholder="Task description"
              class="rounded-lg border border-[#dddddd] px-3 py-2 text-sm outline-none md:col-span-2"
            />

            <div class="flex gap-2 md:col-span-2 md:justify-end">
              <button
                type="button"
                class="rounded-lg border border-[#cf73a4] px-4 py-2 text-sm font-semibold text-[#cf73a4]"
                @click="closeTaskFormModal"
              >
                Cancel
              </button>

              <button
                type="submit"
                class="rounded-lg bg-[#cf73a4] px-4 py-2 text-sm font-semibold text-white disabled:opacity-60"
                :disabled="isSavingForm"
              >
                {{ formMode === 'create' ? 'Create Task' : 'Update Task' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <div
        v-if="isTaskDetailModalOpen && selectedTask"
        class="fixed inset-0 z-40 flex items-center justify-center bg-black/40 p-4"
      >
        <div class="w-full max-w-lg rounded-xl bg-white p-5 text-[#333333] shadow-xl">
          <h2 class="text-base font-bold text-[#cf73a4]">Task Detail</h2>

          <div class="mt-3 space-y-2 text-sm">
            <p><span class="font-semibold">Title:</span> {{ selectedTask.title }}</p>
            <p><span class="font-semibold">Project:</span> {{ selectedTask.project?.name ?? '-' }}</p>
            <p><span class="font-semibold">Category:</span> {{ selectedTask.category?.name ?? '-' }}</p>
            <p><span class="font-semibold">Due Date:</span> {{ formatDate(selectedTask.due_date) }}</p>
            <p><span class="font-semibold">Description:</span> {{ selectedTask.description ?? '-' }}</p>
          </div>

          <div class="mt-4 flex justify-end">
            <button
              type="button"
              class="rounded-lg border border-[#cf73a4] px-4 py-2 text-sm font-semibold text-[#cf73a4]"
              @click="isTaskDetailModalOpen = false"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
