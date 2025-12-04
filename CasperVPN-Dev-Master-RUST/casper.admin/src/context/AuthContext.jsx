import React, { createContext, useContext, useEffect } from 'react';
import { useAuthStore } from '../Store/authenticationStore';

const AuthContext = createContext();

/**
 * AuthProvider component to wrap your app and provide authentication context
 * This is an alternative to using the useAuth hook directly
 */
export const AuthProvider = ({ children }) => {
  const authStore = useAuthStore();

  useEffect(() => {
    // Initialize authentication on app start
    authStore.initializeAuth();
  }, [authStore.initializeAuth]);

  const contextValue = {
    // State
    user: authStore.user,
    isAuthenticated: authStore.isAuthenticated,
    isLoading: authStore.isLoading,
    error: authStore.error,
    
    // Actions
    login: authStore.login,
    register: authStore.register,
    logout: authStore.logout,
    changePassword: authStore.changePassword,
    updateProfile: authStore.updateProfile,
    clearError: authStore.clearError,
    
    // Admin actions
    getUsers: authStore.getUsers,
    revokeUserToken: authStore.revokeUserToken,
    deleteUser: authStore.deleteUser,
    
    // Utilities
    isTokenExpired: authStore.isTokenExpired,
  };

  return (
    <AuthContext.Provider value={contextValue}>
      {children}
    </AuthContext.Provider>
  );
};

/**
 * Hook to use the auth context
 * Alternative to useAuth hook when using AuthProvider
 */
export const useAuthContext = () => {
  const context = useContext(AuthContext);
  
  if (context === undefined) {
    throw new Error('useAuthContext must be used within an AuthProvider');
  }
  
  return context;
};

export default AuthProvider;