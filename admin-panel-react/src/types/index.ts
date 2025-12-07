// API Response Types
export interface ApiResponse<T> {
  success: boolean;
  message?: string;
  data?: T;
  errors?: string[];
  timestamp: string;
}

export interface PaginatedResponse<T> {
  items: T[];
  totalCount: number;
  page: number;
  pageSize: number;
  totalPages: number;
}

export interface PaginationParams {
  page: number;
  pageSize: number;
  sortBy?: string;
  sortDescending?: boolean;
  search?: string;
}

// Auth Types
export interface LoginRequest {
  email: string;
  password: string;
  rememberMe?: boolean;
}

export interface LoginResponse {
  accessToken: string;
  refreshToken: string;
  expiresIn: number;
  user: UserDto;
}

export interface UserDto {
  id: string;
  email: string;
  firstName: string;
  lastName: string;
  role: string;
  isActive: boolean;
  createdAt: string;
  lastLoginAt: string;
}

// Dashboard Types
export interface DashboardStats {
  totalUsers: number;
  activeUsers: number;
  totalServers: number;
  activeConnections: number;
  totalBandwidthGB: number;
  newUsersToday: number;
  newUsersThisWeek: number;
  newUsersThisMonth: number;
}

// Analytics Types
export interface AnalyticsDto {
  userGrowth: TimeSeriesData[];
  connectionsOverTime: TimeSeriesData[];
  serverLoads: ServerLoadData[];
  topCountries: CountryData[];
  bandwidthByServer: BandwidthData[];
}

export interface TimeSeriesData {
  date: string;
  count: number;
}

export interface ServerLoadData {
  serverId: string;
  serverName: string;
  load: number;
}

export interface CountryData {
  country: string;
  users: number;
}

export interface BandwidthData {
  serverId: string;
  serverName: string;
  bandwidthGB: number;
}

// Revenue Types
export interface RevenueDto {
  mrr: number;
  arr: number;
  totalRevenue: number;
  revenueByPlan: PlanRevenueData[];
  monthlyRevenue: MonthlyRevenueData[];
  churnRate: number;
  averageRevenuePerUser: number;
}

export interface PlanRevenueData {
  planName: string;
  revenue: number;
  subscribers: number;
}

export interface MonthlyRevenueData {
  month: string;
  revenue: number;
}

// User Types
export interface UserListItemDto {
  id: string;
  email: string;
  firstName: string;
  lastName: string;
  role: string;
  isActive: boolean;
  createdAt: string;
  lastLoginAt: string;
  subscriptionStatus: string;
}

export interface CreateUserRequest {
  email: string;
  password: string;
  firstName: string;
  lastName: string;
  role: string;
}

export interface UpdateUserRequest {
  email?: string;
  firstName?: string;
  lastName?: string;
  role?: string;
  isActive?: boolean;
}

// Server Types
export interface VpnServerDto {
  id: string;
  name: string;
  hostname: string;
  ipAddress: string;
  country: string;
  city: string;
  load: number;
  isActive: boolean;
  maxConnections: number;
  currentConnections: number;
  bandwidthLimit: number;
  protocol: string;
}

export interface CreateServerRequest {
  name: string;
  hostname: string;
  ipAddress: string;
  country: string;
  city: string;
  maxConnections: number;
  bandwidthLimit: number;
  protocol: string;
}

export interface UpdateServerRequest {
  name?: string;
  hostname?: string;
  ipAddress?: string;
  country?: string;
  city?: string;
  maxConnections?: number;
  bandwidthLimit?: number;
  protocol?: string;
  isActive?: boolean;
}

// Plan Types
export interface PlanDto {
  id: string;
  name: string;
  description: string;
  priceMonthly: number;
  priceYearly: number;
  features: string[];
  maxDevices: number;
  isActive: boolean;
}

export interface CreatePlanRequest {
  name: string;
  description: string;
  priceMonthly: number;
  priceYearly: number;
  features: string[];
  maxDevices: number;
}

export interface UpdatePlanRequest {
  name?: string;
  description?: string;
  priceMonthly?: number;
  priceYearly?: number;
  features?: string[];
  maxDevices?: number;
  isActive?: boolean;
}

// Payment Types
export interface PaymentDto {
  id: string;
  userId: string;
  userEmail: string;
  amount: number;
  currency: string;
  status: string;
  paymentMethod: string;
  planName: string;
  createdAt: string;
}

export interface InvoiceDto {
  id: string;
  invoiceNumber: string;
  userId: string;
  amount: number;
  currency: string;
  status: string;
  dueDate: string;
  paidAt?: string;
  downloadUrl: string;
}

// Settings Types
export interface AdminProfile {
  id: string;
  email: string;
  firstName: string;
  lastName: string;
  role: string;
  createdAt: string;
}

export interface ChangePasswordRequest {
  currentPassword: string;
  newPassword: string;
  confirmPassword: string;
}

export interface UpdateProfileRequest {
  firstName: string;
  lastName: string;
  email: string;
}

// UI Types
export interface TableColumn<T> {
  id: keyof T | string;
  label: string;
  minWidth?: number;
  align?: 'left' | 'right' | 'center';
  format?: (value: any, row: T) => React.ReactNode;
  sortable?: boolean;
}

export interface SelectOption {
  value: string;
  label: string;
}
