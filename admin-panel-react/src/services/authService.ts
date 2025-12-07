import api, { setTokens, setUser, clearAuth } from './api';
import { ApiResponse, LoginRequest, LoginResponse, UserDto } from '../types';

export const authService = {
  async login(credentials: LoginRequest): Promise<LoginResponse> {
    const response = await api.post<ApiResponse<LoginResponse>>('/api/auth/login', credentials);
    
    if (response.data.success && response.data.data) {
      const { accessToken, refreshToken, user } = response.data.data;
      setTokens(accessToken, refreshToken);
      setUser(user);
      return response.data.data;
    }
    
    throw new Error(response.data.message || 'Login failed');
  },

  async register(data: { email: string; password: string; firstName: string; lastName: string }): Promise<UserDto> {
    const response = await api.post<ApiResponse<UserDto>>('/api/auth/register', data);
    
    if (response.data.success && response.data.data) {
      return response.data.data;
    }
    
    throw new Error(response.data.message || 'Registration failed');
  },

  async logout(): Promise<void> {
    try {
      await api.post('/api/auth/logout');
    } catch (error) {
      console.error('Logout API call failed:', error);
    } finally {
      clearAuth();
    }
  },

  async forgotPassword(email: string): Promise<void> {
    const response = await api.post<ApiResponse<void>>('/api/auth/forgot-password', { email });
    
    if (!response.data.success) {
      throw new Error(response.data.message || 'Failed to send reset email');
    }
  },

  async resetPassword(token: string, newPassword: string): Promise<void> {
    const response = await api.post<ApiResponse<void>>('/api/auth/reset-password', {
      token,
      newPassword,
    });
    
    if (!response.data.success) {
      throw new Error(response.data.message || 'Failed to reset password');
    }
  },

  async getCurrentUser(): Promise<UserDto> {
    const response = await api.get<ApiResponse<UserDto>>('/api/auth/me');
    
    if (response.data.success && response.data.data) {
      setUser(response.data.data);
      return response.data.data;
    }
    
    throw new Error(response.data.message || 'Failed to get user info');
  },
};

export default authService;
