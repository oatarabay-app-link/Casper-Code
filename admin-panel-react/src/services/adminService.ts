import api from './api';
import {
  ApiResponse,
  PaginatedResponse,
  PaginationParams,
  DashboardStats,
  AnalyticsDto,
  RevenueDto,
  UserListItemDto,
  CreateUserRequest,
  UpdateUserRequest,
  VpnServerDto,
  CreateServerRequest,
  UpdateServerRequest,
  PlanDto,
  CreatePlanRequest,
  UpdatePlanRequest,
  PaymentDto,
  InvoiceDto,
  ChangePasswordRequest,
  UpdateProfileRequest,
} from '../types';

// Helper to build query params
const buildQueryParams = (params: PaginationParams): string => {
  const queryParams = new URLSearchParams();
  queryParams.append('page', params.page.toString());
  queryParams.append('pageSize', params.pageSize.toString());
  if (params.sortBy) queryParams.append('sortBy', params.sortBy);
  if (params.sortDescending !== undefined) queryParams.append('sortDescending', params.sortDescending.toString());
  if (params.search) queryParams.append('search', params.search);
  return queryParams.toString();
};

export const adminService = {
  // Dashboard
  async getDashboardStats(): Promise<DashboardStats> {
    const response = await api.get<ApiResponse<DashboardStats>>('/api/admin/dashboard');
    if (response.data.success && response.data.data) {
      return response.data.data;
    }
    throw new Error(response.data.message || 'Failed to fetch dashboard stats');
  },

  // Analytics
  async getAnalytics(startDate?: string, endDate?: string): Promise<AnalyticsDto> {
    const params = new URLSearchParams();
    if (startDate) params.append('startDate', startDate);
    if (endDate) params.append('endDate', endDate);
    
    const response = await api.get<ApiResponse<AnalyticsDto>>(`/api/admin/analytics?${params.toString()}`);
    if (response.data.success && response.data.data) {
      return response.data.data;
    }
    throw new Error(response.data.message || 'Failed to fetch analytics');
  },

  // Revenue
  async getRevenue(): Promise<RevenueDto> {
    const response = await api.get<ApiResponse<RevenueDto>>('/api/admin/revenue');
    if (response.data.success && response.data.data) {
      return response.data.data;
    }
    throw new Error(response.data.message || 'Failed to fetch revenue data');
  },

  // Users
  async getUsers(params: PaginationParams): Promise<PaginatedResponse<UserListItemDto>> {
    const response = await api.get<ApiResponse<PaginatedResponse<UserListItemDto>>>(
      `/api/admin/users?${buildQueryParams(params)}`
    );
    if (response.data.success && response.data.data) {
      return response.data.data;
    }
    throw new Error(response.data.message || 'Failed to fetch users');
  },

  async getUserById(id: string): Promise<UserListItemDto> {
    const response = await api.get<ApiResponse<UserListItemDto>>(`/api/admin/users/${id}`);
    if (response.data.success && response.data.data) {
      return response.data.data;
    }
    throw new Error(response.data.message || 'Failed to fetch user');
  },

  async createUser(data: CreateUserRequest): Promise<UserListItemDto> {
    const response = await api.post<ApiResponse<UserListItemDto>>('/api/admin/users', data);
    if (response.data.success && response.data.data) {
      return response.data.data;
    }
    throw new Error(response.data.message || 'Failed to create user');
  },

  async updateUser(id: string, data: UpdateUserRequest): Promise<UserListItemDto> {
    const response = await api.put<ApiResponse<UserListItemDto>>(`/api/admin/users/${id}`, data);
    if (response.data.success && response.data.data) {
      return response.data.data;
    }
    throw new Error(response.data.message || 'Failed to update user');
  },

  async deleteUser(id: string): Promise<void> {
    const response = await api.delete<ApiResponse<void>>(`/api/admin/users/${id}`);
    if (!response.data.success) {
      throw new Error(response.data.message || 'Failed to delete user');
    }
  },

  // Servers
  async getServers(): Promise<VpnServerDto[]> {
    const response = await api.get<ApiResponse<VpnServerDto[]>>('/api/admin/servers');
    if (response.data.success && response.data.data) {
      return response.data.data;
    }
    throw new Error(response.data.message || 'Failed to fetch servers');
  },

  async createServer(data: CreateServerRequest): Promise<VpnServerDto> {
    const response = await api.post<ApiResponse<VpnServerDto>>('/api/admin/servers', data);
    if (response.data.success && response.data.data) {
      return response.data.data;
    }
    throw new Error(response.data.message || 'Failed to create server');
  },

  async updateServer(id: string, data: UpdateServerRequest): Promise<VpnServerDto> {
    const response = await api.put<ApiResponse<VpnServerDto>>(`/api/admin/servers/${id}`, data);
    if (response.data.success && response.data.data) {
      return response.data.data;
    }
    throw new Error(response.data.message || 'Failed to update server');
  },

  async deleteServer(id: string): Promise<void> {
    const response = await api.delete<ApiResponse<void>>(`/api/admin/servers/${id}`);
    if (!response.data.success) {
      throw new Error(response.data.message || 'Failed to delete server');
    }
  },

  // Plans
  async getPlans(): Promise<PlanDto[]> {
    const response = await api.get<ApiResponse<PlanDto[]>>('/api/plans');
    if (response.data.success && response.data.data) {
      return response.data.data;
    }
    throw new Error(response.data.message || 'Failed to fetch plans');
  },

  async createPlan(data: CreatePlanRequest): Promise<PlanDto> {
    const response = await api.post<ApiResponse<PlanDto>>('/api/admin/plans', data);
    if (response.data.success && response.data.data) {
      return response.data.data;
    }
    throw new Error(response.data.message || 'Failed to create plan');
  },

  async updatePlan(id: string, data: UpdatePlanRequest): Promise<PlanDto> {
    const response = await api.put<ApiResponse<PlanDto>>(`/api/admin/plans/${id}`, data);
    if (response.data.success && response.data.data) {
      return response.data.data;
    }
    throw new Error(response.data.message || 'Failed to update plan');
  },

  async deletePlan(id: string): Promise<void> {
    const response = await api.delete<ApiResponse<void>>(`/api/admin/plans/${id}`);
    if (!response.data.success) {
      throw new Error(response.data.message || 'Failed to delete plan');
    }
  },

  // Payments
  async getPaymentHistory(params: PaginationParams & { status?: string; startDate?: string; endDate?: string }): Promise<PaginatedResponse<PaymentDto>> {
    const queryParams = new URLSearchParams();
    queryParams.append('page', params.page.toString());
    queryParams.append('pageSize', params.pageSize.toString());
    if (params.status) queryParams.append('status', params.status);
    if (params.startDate) queryParams.append('startDate', params.startDate);
    if (params.endDate) queryParams.append('endDate', params.endDate);
    
    const response = await api.get<ApiResponse<PaginatedResponse<PaymentDto>>>(`/api/payments/history?${queryParams.toString()}`);
    if (response.data.success && response.data.data) {
      return response.data.data;
    }
    throw new Error(response.data.message || 'Failed to fetch payment history');
  },

  async getInvoices(params: PaginationParams): Promise<PaginatedResponse<InvoiceDto>> {
    const response = await api.get<ApiResponse<PaginatedResponse<InvoiceDto>>>(`/api/payments/invoices?${buildQueryParams(params)}`);
    if (response.data.success && response.data.data) {
      return response.data.data;
    }
    throw new Error(response.data.message || 'Failed to fetch invoices');
  },

  // Admin Profile
  async changePassword(data: ChangePasswordRequest): Promise<void> {
    const response = await api.post<ApiResponse<void>>('/api/auth/change-password', data);
    if (!response.data.success) {
      throw new Error(response.data.message || 'Failed to change password');
    }
  },

  async updateProfile(data: UpdateProfileRequest): Promise<void> {
    const response = await api.put<ApiResponse<void>>('/api/auth/profile', data);
    if (!response.data.success) {
      throw new Error(response.data.message || 'Failed to update profile');
    }
  },
};

export default adminService;
