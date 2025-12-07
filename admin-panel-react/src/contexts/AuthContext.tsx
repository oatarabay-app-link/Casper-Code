import React, { createContext, useContext, useState, useEffect, useCallback, ReactNode } from 'react';
import { UserDto, LoginRequest } from '../types';
import authService from '../services/authService';
import { getAccessToken, getStoredUser, clearAuth } from '../services/api';

interface AuthContextType {
  user: UserDto | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  login: (credentials: LoginRequest) => Promise<void>;
  logout: () => Promise<void>;
  refreshUser: () => Promise<void>;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

interface AuthProviderProps {
  children: ReactNode;
}

export const AuthProvider: React.FC<AuthProviderProps> = ({ children }) => {
  const [user, setUser] = useState<UserDto | null>(null);
  const [isLoading, setIsLoading] = useState(true);

  const initializeAuth = useCallback(async () => {
    const token = getAccessToken();
    const storedUser = getStoredUser();

    if (token && storedUser) {
      // Verify the user role is admin
      if (storedUser.role === 'Admin' || storedUser.role === 'SuperAdmin') {
        setUser(storedUser);
      } else {
        clearAuth();
      }
    }
    setIsLoading(false);
  }, []);

  useEffect(() => {
    initializeAuth();
  }, [initializeAuth]);

  const login = async (credentials: LoginRequest): Promise<void> => {
    const response = await authService.login(credentials);
    
    // Check if user has admin role
    if (response.user.role !== 'Admin' && response.user.role !== 'SuperAdmin') {
      clearAuth();
      throw new Error('Access denied. Admin privileges required.');
    }
    
    setUser(response.user);
  };

  const logout = async (): Promise<void> => {
    await authService.logout();
    setUser(null);
  };

  const refreshUser = async (): Promise<void> => {
    try {
      const currentUser = await authService.getCurrentUser();
      setUser(currentUser);
    } catch (error) {
      console.error('Failed to refresh user:', error);
    }
  };

  const value: AuthContextType = {
    user,
    isAuthenticated: !!user,
    isLoading,
    login,
    logout,
    refreshUser,
  };

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
};

export const useAuth = (): AuthContextType => {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};

export default AuthContext;
