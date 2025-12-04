import { create } from 'zustand';
import { persist, createJSONStorage } from 'zustand/middleware';
import api from '../utils/axios.js';
import { API_ROUTES, ERROR_MESSAGES } from '../router/api-routes.js';

// Initial state
const initialState = {
  user: null,
  accessToken: null,
  refreshToken: null,
  accessTokenExpiration: null,
  isAuthenticated: false,
  isLoading: false,
  error: null,
};

export const useAuthStore = create(
  persist(
    (set, get) => ({
      ...initialState,

      // Actions
      login: async (credentials) => {
        set({ isLoading: true, error: null });

        try {
          // Backend expects UserRequest.Login with userName and password
          const payload = {
            userName: credentials.userName || credentials.username || credentials.email, // map email -> userName
            password: credentials.password,
          };

          const response = await api.post(API_ROUTES.AUTH.LOGIN, payload);

          // On success, controller returns the LoginResponse directly in response.data
          const data = response.data || {};
          // Support both camelCase (ASP.NET default) and PascalCase (if configured)
          const accessToken = data.accessToken ?? data.AccessToken;
          const refreshToken = data.refreshToken ?? data.RefreshToken;
          const accessTokenExpiration = data.accessTokenExpiration ?? data.AccessTokenExpiration;
          const user = data.user ?? data.User;

          set({
            user,
            accessToken,
            refreshToken,
            accessTokenExpiration,
            isAuthenticated: true,
            isLoading: false,
            error: null,
          });

          // Store token in localStorage for axios interceptor
          localStorage.setItem('token', accessToken);

          return { success: true, data: data };
        } catch (error) {
          // Surface meaningful server errors (validation/unauthorized)
          const apiMsg = error?.response?.data?.message;
          const apiErrors = error?.response?.data?.errors;
          const firstError = Array.isArray(apiErrors) && apiErrors.length > 0 ? apiErrors[0] : undefined;
          const errorMessage = apiMsg || firstError || 'Network error occurred';

          set({ isLoading: false, error: errorMessage });
          return { success: false, error: errorMessage };
        }
      },

      register: async (userData) => {
        set({ isLoading: true, error: null });
        
        try {
          const response = await api.post(API_ROUTES.AUTH.REGISTER, userData);
          
          if (response.data.success) {
            set({
              isLoading: false,
              error: null,
            });
            return { success: true, data: response.data.data };
          } else {
            set({
              isLoading: false,
              error: response.data.message || 'Registration failed',
            });
            return { success: false, error: response.data.message };
          }
        } catch (error) {
          const errorMessage = error.response?.data?.message || 'Network error occurred';
          set({
            isLoading: false,
            error: errorMessage,
          });
          return { success: false, error: errorMessage };
        }
      },

      logout: async () => {
        set({ isLoading: true });
        
        try {
          // Optional: Call logout endpoint if available
          // await api.post('/auth/logout');
          
          // Clear authentication data
          set({
            ...initialState,
          });

          // Remove token from localStorage for axios interceptor
          localStorage.removeItem('token');
          
          return { success: true };
        } catch (error) {
          // Even if logout fails on server, clear local data
          set({
            ...initialState,
          });
          localStorage.removeItem('token');
          
          return { success: true };
        }
      },

      refreshAccessToken: async () => {
        const { refreshToken, accessToken } = get();
        
        if (!refreshToken) {
          get().logout();
          return { success: false, error: 'No refresh token available' };
        }

        try {
          const response = await api.post(API_ROUTES.AUTH.REFRESH_TOKEN, {
            accessToken: accessToken,
            refreshToken: refreshToken,
          });
          
          // Controller should return RefreshTokenResponse directly
          if (response.status === 200) {
            const rd = response.data || {};
            const AccessToken = rd.accessToken ?? rd.AccessToken;
            const newRefreshToken = rd.refreshToken ?? rd.RefreshToken;
            const AccessTokenExpiration = rd.accessTokenExpiration ?? rd.AccessTokenExpiration;
            
            set({
              accessToken: AccessToken,
              refreshToken: newRefreshToken,
              accessTokenExpiration: AccessTokenExpiration,
              error: null,
            });

            // Update token in localStorage for axios interceptor
            localStorage.setItem('token', AccessToken);
            
            return { success: true };
          } else {
            get().logout();
            return { success: false, error: 'Token refresh failed' };
          }
        } catch (error) {
          get().logout();
          return { success: false, error: 'Token refresh failed' };
        }
      },

      changePassword: async (passwordData) => {
        set({ isLoading: true, error: null });
        
        try {
          const response = await api.post(API_ROUTES.AUTH.CHANGE_PASSWORD, passwordData);
          
          if (response.data.success) {
            set({
              isLoading: false,
              error: null,
            });
            return { success: true, data: response.data.data };
          } else {
            set({
              isLoading: false,
              error: response.data.message || 'Password change failed',
            });
            return { success: false, error: response.data.message };
          }
        } catch (error) {
          const errorMessage = error.response?.data?.message || 'Network error occurred';
          set({
            isLoading: false,
            error: errorMessage,
          });
          return { success: false, error: errorMessage };
        }
      },

      updateProfile: async (profileData) => {
        set({ isLoading: true, error: null });
        
        try {
          const response = await api.put('/auth/update', profileData);
          
          if (response.data.success) {
            // Update user data in store
            set((state) => ({
              user: { ...state.user, ...response.data.data },
              isLoading: false,
              error: null,
            }));
            return { success: true, data: response.data.data };
          } else {
            set({
              isLoading: false,
              error: response.data.message || 'Profile update failed',
            });
            return { success: false, error: response.data.message };
          }
        } catch (error) {
          const errorMessage = error.response?.data?.message || 'Network error occurred';
          set({
            isLoading: false,
            error: errorMessage,
          });
          return { success: false, error: errorMessage };
        }
      },

      // Admin actions
      getUsers: async (pageNumber = 1, pageSize = 10) => {
        set({ isLoading: true, error: null });
        
        try {
          const response = await api.get(`/auth/users?pageNumber=${pageNumber}&pageSize=${pageSize}`);
          
          if (response.data.success) {
            set({
              isLoading: false,
              error: null,
            });
            return { success: true, data: response.data.data };
          } else {
            set({
              isLoading: false,
              error: response.data.message || 'Failed to fetch users',
            });
            return { success: false, error: response.data.message };
          }
        } catch (error) {
          const errorMessage = error.response?.data?.message || 'Network error occurred';
          set({
            isLoading: false,
            error: errorMessage,
          });
          return { success: false, error: errorMessage };
        }
      },

      revokeUserToken: async (userId) => {
        set({ isLoading: true, error: null });
        
        try {
          const response = await api.post(`/auth/revoke/${userId}`);
          
          if (response.data.success) {
            set({
              isLoading: false,
              error: null,
            });
            return { success: true, data: response.data.data };
          } else {
            set({
              isLoading: false,
              error: response.data.message || 'Failed to revoke user token',
            });
            return { success: false, error: response.data.message };
          }
        } catch (error) {
          const errorMessage = error.response?.data?.message || 'Network error occurred';
          set({
            isLoading: false,
            error: errorMessage,
          });
          return { success: false, error: errorMessage };
        }
      },

      deleteUser: async (userId) => {
        set({ isLoading: true, error: null });
        
        try {
          const response = await api.delete(`/auth/delete/${userId}`);
          
          if (response.data.success) {
            set({
              isLoading: false,
              error: null,
            });
            return { success: true, data: response.data.data };
          } else {
            set({
              isLoading: false,
              error: response.data.message || 'Failed to delete user',
            });
            return { success: false, error: response.data.message };
          }
        } catch (error) {
          const errorMessage = error.response?.data?.message || 'Network error occurred';
          set({
            isLoading: false,
            error: errorMessage,
          });
          return { success: false, error: errorMessage };
        }
      },

      // Utility functions
      clearError: () => set({ error: null }),
      
      isTokenExpired: () => {
        const { accessTokenExpiration } = get();
        if (!accessTokenExpiration) return true;
        return new Date() >= new Date(accessTokenExpiration);
      },

      initializeAuth: () => {
        const { accessToken, isTokenExpired, refreshAccessToken, logout } = get();
        
        if (accessToken) {
          if (isTokenExpired()) {
            // Try to refresh token
            refreshAccessToken().then((result) => {
              if (!result.success) {
                logout();
              }
            });
          } else {
            // Set token in localStorage for axios interceptor
            localStorage.setItem('token', accessToken);
            set({ isAuthenticated: true });
          }
        }
      },
    }),
    {
      name: 'auth-storage', // unique name for localStorage key
      storage: createJSONStorage(() => localStorage), // specify storage type
      partialize: (state) => ({
        user: state.user,
        accessToken: state.accessToken,
        refreshToken: state.refreshToken,
        accessTokenExpiration: state.accessTokenExpiration,
        isAuthenticated: state.isAuthenticated,
      }),
    }
  )
);

// Note: Your existing axios.js file handles the request interceptor for adding auth headers
// We could enhance it to also handle automatic token refresh on 401 errors by adding:
/*
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    const originalRequest = error.config;

    if (error.response?.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true;

      const authStore = useAuthStore.getState();
      const refreshResult = await authStore.refreshAccessToken();

      if (refreshResult.success) {
        // Token is already updated in localStorage by refreshAccessToken
        return api(originalRequest);
      }
    }

    return Promise.reject(error);
  }
);
*/
