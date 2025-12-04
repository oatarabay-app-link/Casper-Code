import { useAuthStore } from '../Store/authenticationStore';
import { useEffect } from 'react';

/**
 * Custom hook for authentication functionality
 * Provides easy access to auth state and actions
 */
export const useAuth = () => {
  const store = useAuthStore();

  // Auto-initialize auth on first use
  useEffect(() => {
    store.initializeAuth();
  }, [store.initializeAuth]);

  return {
    // State
    user: store.user,
    isAuthenticated: store.isAuthenticated,
    isLoading: store.isLoading,
    error: store.error,
    
    // Actions
    login: store.login,
    register: store.register,
    logout: store.logout,
    changePassword: store.changePassword,
    updateProfile: store.updateProfile,
    clearError: store.clearError,
    
    // Admin actions
    getUsers: store.getUsers,
    revokeUserToken: store.revokeUserToken,
    deleteUser: store.deleteUser,
    
    // Utilities
    isTokenExpired: store.isTokenExpired,
  };
};

/**
 * Hook for checking if user has specific permissions
 * You can extend this based on your user roles/permissions structure
 */
export const usePermissions = () => {
  const { user, isAuthenticated } = useAuth();

  const hasRole = (role) => {
    if (!isAuthenticated || !user) return false;
    // Assuming user object has roles property
    return user.roles?.includes(role) || false;
  };

  const isAdmin = () => hasRole('Admin');
  const isModerator = () => hasRole('Moderator');
  const isUser = () => hasRole('User');

  return {
    hasRole,
    isAdmin,
    isModerator,
    isUser,
    canManageUsers: isAdmin,
    canManageServers: isAdmin || isModerator,
    canViewAnalytics: isAdmin || isModerator,
  };
};

/**
 * Hook for protected actions that require authentication
 */
export const useProtectedAction = () => {
  const { isAuthenticated, logout } = useAuth();

  const executeProtected = async (action, options = {}) => {
    const { 
      requireAuth = true, 
      onUnauthorized = () => logout(),
      fallback = null 
    } = options;

    if (requireAuth && !isAuthenticated) {
      if (onUnauthorized) {
        onUnauthorized();
      }
      return fallback;
    }

    try {
      return await action();
    } catch (error) {
      if (error.response?.status === 401) {
        if (onUnauthorized) {
          onUnauthorized();
        }
      }
      throw error;
    }
  };

  return { executeProtected };
};

/**
 * Hook for form validation with auth context
 */
export const useAuthForm = (initialValues = {}) => {
  const { error, clearError } = useAuth();

  const validateLoginForm = (values) => {
    const errors = {};

    if (!values.email) {
      errors.email = 'Email or username is required';
    }

    if (!values.password) {
      errors.password = 'Password is required';
    } else if (values.password.length < 6) {
      errors.password = 'Password must be at least 6 characters';
    }

    return errors;
  };

  const validateRegisterForm = (values) => {
    const errors = validateLoginForm(values);
    
    if (!values.userName) {
      errors.userName = 'Username is required';
    } else if (values.userName.length < 3) {
      errors.userName = 'Username must be at least 3 characters';
    }
    
    if (!values.confirmPassword) {
      errors.confirmPassword = 'Please confirm your password';
    } else if (values.password !== values.confirmPassword) {
      errors.confirmPassword = 'Passwords do not match';
    }
    
    return errors;
  };

  const validateChangePasswordForm = (values) => {
    const errors = {};
    
    if (!values.currentPassword) {
      errors.currentPassword = 'Current password is required';
    }
    
    if (!values.newPassword) {
      errors.newPassword = 'New password is required';
    } else if (values.newPassword.length < 6) {
      errors.newPassword = 'Password must be at least 6 characters';
    }
    
    if (!values.confirmPassword) {
      errors.confirmPassword = 'Please confirm your new password';
    } else if (values.newPassword !== values.confirmPassword) {
      errors.confirmPassword = 'Passwords do not match';
    }
    
    return errors;
  };

  return {
    error,
    clearError,
    validateLoginForm,
    validateRegisterForm,
    validateChangePasswordForm,
  };
};

export default useAuth;