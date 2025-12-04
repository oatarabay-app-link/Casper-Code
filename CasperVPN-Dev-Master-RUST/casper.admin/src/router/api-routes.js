// API Base Configuration
export const API_CONFIG = {
  BASE_URL: 'https://casper.m7slabs.com/api', // Matches your axios.js configuration
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
    GET_SERVERS: '/server',
    CREATE_SERVER: '/server',
    UPDATE_SERVER: (id) => `/server/${id}`,
    DELETE_SERVER: (id) => `/server/${id}`,
    GET_SERVER_STATS: (id) => `/server/${id}/stats`,
  },

  // User management routes
  USERS: {
    GET_USERS: '/users',
    CREATE_USER: '/users',
    UPDATE_USER: (id) => `/users/${id}`,
    DELETE_USER: (id) => `/users/${id}`,
    GET_USER_PROFILE: (id) => `/users/${id}/profile`,
  },

  // Connection routes
  CONNECTIONS: {
    GET_CONNECTIONS: '/connections',
    CREATE_CONNECTION: '/connections',
    UPDATE_CONNECTION: (id) => `/connections/${id}`,
    DELETE_CONNECTION: (id) => `/connections/${id}`,
    GET_CONNECTION_LOGS: (id) => `/connections/${id}/logs`,
  },

  // Analytics routes
  ANALYTICS: {
    DASHBOARD_STATS: '/analytics/dashboard',
    USER_STATS: '/analytics/users',
    CONNECTION_STATS: '/analytics/connections',
    SERVER_STATS: '/analytics/servers',
    TRAFFIC_STATS: '/analytics/traffic',
  },

  // Security routes
  SECURITY: {
    AUDIT_LOGS: '/security/audit-logs',
    SECURITY_EVENTS: '/security/events',
    BLACKLIST: '/security/blacklist',
    WHITELIST: '/security/whitelist',
  },

  // Notification routes
  NOTIFICATIONS: {
    GET_NOTIFICATIONS: '/notifications',
    MARK_READ: (id) => `/notifications/${id}/read`,
    MARK_ALL_READ: '/notifications/read-all',
    DELETE_NOTIFICATION: (id) => `/notifications/${id}`,
    CREATE_NOTIFICATION: '/notifications',
  },

  // Settings routes
  SETTINGS: {
    GET_SETTINGS: '/settings',
    UPDATE_SETTINGS: '/settings',
    SYSTEM_INFO: '/settings/system-info',
    BACKUP: '/settings/backup',
    RESTORE: '/settings/restore',
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
