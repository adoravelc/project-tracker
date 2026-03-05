import { beforeEach, describe, expect, it, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { createPinia, setActivePinia } from 'pinia'
import { createMemoryHistory, createRouter } from 'vue-router'
import Sidebar from '../Sidebar.vue'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
	history: createMemoryHistory(),
	routes: [
		{ path: '/dashboard', component: { template: '<div>Dashboard</div>' } },
		{ path: '/projects', component: { template: '<div>Projects</div>' } },
		{ path: '/tasks', component: { template: '<div>Tasks</div>' } },
		{ path: '/login', component: { template: '<div>Login</div>' } },
	],
})

describe('Sidebar', () => {
	beforeEach(async () => {
		setActivePinia(createPinia())
		const authStore = useAuthStore()
		authStore.logout = vi.fn().mockResolvedValue(undefined)

		if (!router.currentRoute.value.matched.length) {
			await router.push('/dashboard')
		}
	})

	it('renders TaskTracker logo', async () => {
		const wrapper = mount(Sidebar, {
			global: {
				plugins: [router],
			},
		})

		await router.isReady()

		expect(wrapper.text()).toContain('TaskTracker')
	})

	it('renders Dashboard and Project navigation links', async () => {
		const wrapper = mount(Sidebar, {
			global: {
				plugins: [router],
			},
		})

		await router.isReady()

		const navText = wrapper.text()
		expect(navText).toContain('Dashboard')
		expect(navText).toContain('Project')
	})
})

