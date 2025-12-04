// API Base Configuration
export const API_CONFIG = {
  BASE_URL: 'http://localhost:5098/api/v1', // Matches your axios.js configuration
  TIMEOUT: 10000, // 10 seconds
};

// API Endpoints
export const API_ROUTES = {
  // Authentication routes
  AUTH: {
    LOGIN: '/auth/login',
    REGISTER: '/auth/register',
    LOGOUT: '/auth/logout',
    REFRESH_TOKEN: '/auth/refresh-token',
    CHANGE_PASSWORD: '/auth/change-password',
    UPDATE_PROFILE: '/auth/update',
    USERS: '/auth/users',
    REVOKE_TOKEN: (userId) => `/auth/revoke/${userId}`,
    DELETE_USER: (userId) => `/auth/delete/${userId}`,
  },

  // Server routes
  SERVER: {
    GET_SERVERS: '/admin/server',
    CREATE_SERVER: '/admin/server',
    UPDATE_SERVER: '/admin/server',
    DELETE_SERVER: (id) => `/admin/server/${id}`,
    GET_SERVER_STATS: (id) => `/server/${id}/stats`,
  },

  VPNPROTOCOL: {
    GET_VPNPROTOCOLS: '/admin/vpnprotocols',
    GET_VPNPROTOCOLS_BY_ID: (id) => `/admin/vpnprotocols/${id}`,
    CREATE_VPNPROTOCOL: '/admin/vpnprotocols',
    UPDATE_VPNPROTOCOL: `/admin/vpnprotocols`,
    DELETE_VPNPROTOCOL: (id) => `/admin/vpnprotocols/${id}`,
  },

  COUPON: {
    GET_COUPONS: '/admin/coupon',
    CREATE_COUPON: '/admin/coupon',
    UPDATE_COUPON: `/admin/coupon`,
    DELETE_COUPON: (id) => `/admin/coupon/${id}`,
    VALIDATE_COUPON: (code) => `/admin/coupon/validate/${code}`,
  },

  DISCOUNT: {
    GET_DISCOUNTS: '/admin/discount',
    CREATE_DISCOUNT: '/admin/discount',
    UPDATE_DISCOUNT: `/admin/discount`,
    DELETE_DISCOUNT: (id) => `/admin/discount/${id}`,
  },
  PACKAGE: {
    GET_PACKAGES: '/admin/package',
    CREATE_PACKAGE: '/admin/package',
    UPDATE_PACKAGE: '/admin/package',
    DELETE_PACKAGE_BY_ID: (id) => `/admin/package/${id}`,
  },
  ROLES: {
    GET_ALL_ROLES: '/admin/roles/summary',
    GET_ROLES: '/admin/roles',
    CREATE_ROLE: '/admin/roles',
    GET_ROLE_BY_ID: (roleId) => `/admin/roles/${roleId}`,
    UPDATE_ROLE_BY_ID: (roleId) => `/admin/roles/${roleId}`,
    DELETE_ROLE_BY_ID: (roleId) => `/admin/roles/${roleId}`,
    CREATE_ROLE_PERMISSION: (roleId) => `/admin/roles/${roleId}/permission`,
    DELETE_ROLE_PERMISSION: (roleId) => `/admin/roles/${roleId}/permission/`,
    GET_ROLE_PERMISSIONS: (roleId) => `/admin/roles/${roleId}/permissions`,
    CREATE_ROLE_USER_BY_ID: (roleId, userId) => `/admin/roles/${roleId}/user/${userId}`,
    DELETE_ROLE_USER_BY_ID: (roleId, userId) => `/admin/roles/${roleId}/user/${userId}`,
    GET_ROLE_USERS: (roleId) => `/admin/roles/${roleId}/users`,
  },
  SEASONAL_DEAL: {
    GET_SEASONAL_DEALS: '/admin/seasonaldeal',
    CREATE_SEASONAL_DEAL: '/admin/seasonaldeal',
    UPDATE_SEASONAL_DEAL: `/admin/seasonaldeal`,
    DELETE_SEASONAL_DEAL: (id) => `/admin/seasonaldeal/${id}`,
    ACTIVE_SEASONAL_DEAL: '/admin/seasonaldeal/active',
  },
};

// Note: Full URLs are handled by the axios instance in utils/axios.js
// Just use the endpoint paths directly with the api instance

// Helper function for pagination
export const buildPaginatedUrl = (baseEndpoint, pageNumber = 1, pageSize = 10, additionalParams = {}) => {
  const params = new URLSearchParams({
    pageNumber: pageNumber.toString(),
    pageSize: pageSize.toString(),
    ...additionalParams
  });

  return `${baseEndpoint}?${params.toString()}`;
};

// Common request headers
export const getAuthHeaders = (token) => ({
  'Content-Type': 'application/json',
  'Authorization': `Bearer ${token}`,
});

// Error messages mapping
export const ERROR_MESSAGES = {
  NETWORK_ERROR: 'Network error occurred. Please check your connection.',
  UNAUTHORIZED: 'You are not authorized to perform this action.',
  FORBIDDEN: 'Access forbidden. You do not have permission.',
  NOT_FOUND: 'The requested resource was not found.',
  SERVER_ERROR: 'Internal server error. Please try again later.',
  VALIDATION_ERROR: 'Please check your input and try again.',
  TOKEN_EXPIRED: 'Your session has expired. Please login again.',
  LOGIN_FAILED: 'Invalid credentials. Please try again.',
  REGISTRATION_FAILED: 'Registration failed. Please try again.',
};

// Success messages
export const SUCCESS_MESSAGES = {
  LOGIN_SUCCESS: 'Login successful!',
  LOGOUT_SUCCESS: 'Logged out successfully!',
  REGISTRATION_SUCCESS: 'Registration successful!',
  PASSWORD_CHANGED: 'Password changed successfully!',
  PROFILE_UPDATED: 'Profile updated successfully!',
  USER_CREATED: 'User created successfully!',
  USER_UPDATED: 'User updated successfully!',
  USER_DELETED: 'User deleted successfully!',
  SERVER_CREATED: 'Server created successfully!',
  SERVER_UPDATED: 'Server updated successfully!',
  SERVER_DELETED: 'Server deleted successfully!',
  SETTINGS_SAVED: 'Settings saved successfully!',
};
