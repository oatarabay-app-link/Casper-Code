import axios from 'axios';
import { API_CONFIG } from '../router/api-routes.js';

const baseURL = API_CONFIG.BASE_URL;;

const api = axios.create({
	baseURL,
	headers: {
		'Content-Type': 'application/json',
		'Accept': 'application/json',
	},
});

// Request interceptor to add Authorization header if token exists
api.interceptors.request.use(
	(config) => {
		// Do not attach Authorization for auth endpoints
		const url = (config?.url || '').toString().toLowerCase();
		const isAuthEndpoint = url.includes('/auth/login') || url.includes('/auth/register') || url.includes('/auth/refresh-token');

		if (!isAuthEndpoint) {
			const token = localStorage.getItem('token');
			if (token) {
				config.headers['Authorization'] = `Bearer ${token}`;
			}
		}
		return config;
	},
	(error) => Promise.reject(error)
);

// Response interceptor for handling errors globally and automatic token refresh
api.interceptors.response.use(
	(response) => response,
	async (error) => {
		const originalRequest = error.config;

		// Handle 401 errors with automatic token refresh
		if (error.response?.status === 401 && !originalRequest._retry) {
			originalRequest._retry = true;

			try {
				// Import auth store dynamically to avoid circular dependencies
				const { useAuthStore } = await import('../Store/authenticationStore.js');
				const authStore = useAuthStore.getState();
				
				const refreshResult = await authStore.refreshAccessToken();

				if (refreshResult.success) {
					// Token is already updated in localStorage by refreshAccessToken
					// The request interceptor will automatically add the new token
					return api(originalRequest);
				} else {
					// Refresh failed, logout user
					authStore.logout();
				}
			} catch (refreshError) {
				console.error('Token refresh failed:', refreshError);
			}
		}

		return Promise.reject(error);
	}
);

export default api;
