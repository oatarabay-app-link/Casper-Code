import React, { useEffect, useState } from 'react';
import {
  Box,
  Grid,
  Typography,
  Paper,
  Skeleton,
  List,
  ListItem,
  ListItemAvatar,
  ListItemText,
  Avatar,
  Chip,
} from '@mui/material';
import {
  People as PeopleIcon,
  PersonAdd as PersonAddIcon,
  Dns as DnsIcon,
  AttachMoney as MoneyIcon,
  TrendingUp as TrendingUpIcon,
} from '@mui/icons-material';
import {
  LineChart,
  Line,
  BarChart,
  Bar,
  PieChart,
  Pie,
  AreaChart,
  Area,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  ResponsiveContainer,
  Cell,
} from 'recharts';
import { toast } from 'react-toastify';
import { StatCard } from '../components';
import adminService from '../services/adminService';
import { DashboardStats, AnalyticsDto, RevenueDto } from '../types';

const COLORS = ['#7c3aed', '#06b6d4', '#10b981', '#f59e0b', '#ef4444'];

export const Dashboard: React.FC = () => {
  const [stats, setStats] = useState<DashboardStats | null>(null);
  const [analytics, setAnalytics] = useState<AnalyticsDto | null>(null);
  const [revenue, setRevenue] = useState<RevenueDto | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchDashboardData();
  }, []);

  const fetchDashboardData = async () => {
    setLoading(true);
    try {
      const [statsData, analyticsData, revenueData] = await Promise.all([
        adminService.getDashboardStats(),
        adminService.getAnalytics(),
        adminService.getRevenue(),
      ]);
      setStats(statsData);
      setAnalytics(analyticsData);
      setRevenue(revenueData);
    } catch (error: any) {
      toast.error('Failed to load dashboard data');
      console.error('Dashboard error:', error);
    } finally {
      setLoading(false);
    }
  };

  const formatCurrency = (value: number): string => {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD',
      minimumFractionDigits: 0,
    }).format(value);
  };

  const formatNumber = (value: number): string => {
    return new Intl.NumberFormat('en-US').format(value);
  };

  const ChartCard: React.FC<{ title: string; children: React.ReactNode }> = ({
    title,
    children,
  }) => (
    <Paper
      sx={{
        p: 3,
        height: '100%',
        background: 'linear-gradient(145deg, #1e1e2e 0%, #2d2d44 100%)',
        borderRadius: 3,
        border: '1px solid rgba(255, 255, 255, 0.05)',
      }}
    >
      <Typography variant="h6" fontWeight="600" mb={2}>
        {title}
      </Typography>
      {children}
    </Paper>
  );

  if (loading) {
    return (
      <Box>
        <Grid container spacing={3}>
          {[1, 2, 3, 4].map((i) => (
            <Grid item xs={12} sm={6} md={3} key={i}>
              <Skeleton variant="rounded" height={140} sx={{ borderRadius: 3 }} />
            </Grid>
          ))}
          {[1, 2].map((i) => (
            <Grid item xs={12} md={6} key={i}>
              <Skeleton variant="rounded" height={350} sx={{ borderRadius: 3 }} />
            </Grid>
          ))}
        </Grid>
      </Box>
    );
  }

  return (
    <Box>
      <Typography variant="h4" fontWeight="bold" mb={3}>
        Dashboard
      </Typography>

      {/* Stats Cards */}
      <Grid container spacing={3} mb={3}>
        <Grid item xs={12} sm={6} md={3}>
          <StatCard
            title="Total Users"
            value={formatNumber(stats?.totalUsers || 0)}
            subtitle={`+${stats?.newUsersToday || 0} today`}
            icon={<PeopleIcon />}
            trend={{ value: 12, label: 'vs last month' }}
            color="#7c3aed"
          />
        </Grid>
        <Grid item xs={12} sm={6} md={3}>
          <StatCard
            title="Active Users"
            value={formatNumber(stats?.activeUsers || 0)}
            subtitle="Currently online"
            icon={<PersonAddIcon />}
            trend={{ value: 8, label: 'vs last week' }}
            color="#06b6d4"
          />
        </Grid>
        <Grid item xs={12} sm={6} md={3}>
          <StatCard
            title="Servers"
            value={stats?.totalServers || 0}
            subtitle={`${stats?.activeConnections || 0} connections`}
            icon={<DnsIcon />}
            color="#10b981"
          />
        </Grid>
        <Grid item xs={12} sm={6} md={3}>
          <StatCard
            title="Monthly Revenue"
            value={formatCurrency(revenue?.mrr || 0)}
            subtitle={`ARR: ${formatCurrency(revenue?.arr || 0)}`}
            icon={<MoneyIcon />}
            trend={{ value: revenue?.churnRate || 0, label: 'churn rate' }}
            color="#f59e0b"
          />
        </Grid>
      </Grid>

      {/* Charts Row 1 */}
      <Grid container spacing={3} mb={3}>
        {/* User Growth Line Chart */}
        <Grid item xs={12} md={8}>
          <ChartCard title="User Growth">
            <ResponsiveContainer width="100%" height={300}>
              <LineChart data={analytics?.userGrowth || []}>
                <CartesianGrid strokeDasharray="3 3" stroke="rgba(255,255,255,0.1)" />
                <XAxis dataKey="date" stroke="#888" fontSize={12} />
                <YAxis stroke="#888" fontSize={12} />
                <Tooltip
                  contentStyle={{
                    backgroundColor: '#1e1e2e',
                    border: '1px solid rgba(255,255,255,0.1)',
                    borderRadius: 8,
                  }}
                />
                <Line
                  type="monotone"
                  dataKey="count"
                  name="Users"
                  stroke="#7c3aed"
                  strokeWidth={3}
                  dot={{ fill: '#7c3aed', strokeWidth: 2 }}
                  activeDot={{ r: 8 }}
                />
              </LineChart>
            </ResponsiveContainer>
          </ChartCard>
        </Grid>

        {/* Subscriptions Pie Chart */}
        <Grid item xs={12} md={4}>
          <ChartCard title="Subscriptions by Plan">
            <ResponsiveContainer width="100%" height={300}>
              <PieChart>
                <Pie
                  data={revenue?.revenueByPlan || []}
                  cx="50%"
                  cy="50%"
                  labelLine={false}
                  label={({ planName, percent }) =>
                    `${planName} ${(percent * 100).toFixed(0)}%`
                  }
                  outerRadius={100}
                  fill="#8884d8"
                  dataKey="subscribers"
                  nameKey="planName"
                >
                  {(revenue?.revenueByPlan || []).map((_, index) => (
                    <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                  ))}
                </Pie>
                <Tooltip
                  contentStyle={{
                    backgroundColor: '#1e1e2e',
                    border: '1px solid rgba(255,255,255,0.1)',
                    borderRadius: 8,
                  }}
                />
              </PieChart>
            </ResponsiveContainer>
          </ChartCard>
        </Grid>
      </Grid>

      {/* Charts Row 2 */}
      <Grid container spacing={3} mb={3}>
        {/* Monthly Revenue Bar Chart */}
        <Grid item xs={12} md={6}>
          <ChartCard title="Monthly Revenue">
            <ResponsiveContainer width="100%" height={300}>
              <BarChart data={revenue?.monthlyRevenue || []}>
                <CartesianGrid strokeDasharray="3 3" stroke="rgba(255,255,255,0.1)" />
                <XAxis dataKey="month" stroke="#888" fontSize={12} />
                <YAxis stroke="#888" fontSize={12} />
                <Tooltip
                  formatter={(value: number) => formatCurrency(value)}
                  contentStyle={{
                    backgroundColor: '#1e1e2e',
                    border: '1px solid rgba(255,255,255,0.1)',
                    borderRadius: 8,
                  }}
                />
                <Bar
                  dataKey="revenue"
                  name="Revenue"
                  fill="#06b6d4"
                  radius={[4, 4, 0, 0]}
                />
              </BarChart>
            </ResponsiveContainer>
          </ChartCard>
        </Grid>

        {/* Connections Area Chart */}
        <Grid item xs={12} md={6}>
          <ChartCard title="Connections Over Time">
            <ResponsiveContainer width="100%" height={300}>
              <AreaChart data={analytics?.connectionsOverTime || []}>
                <CartesianGrid strokeDasharray="3 3" stroke="rgba(255,255,255,0.1)" />
                <XAxis dataKey="date" stroke="#888" fontSize={12} />
                <YAxis stroke="#888" fontSize={12} />
                <Tooltip
                  contentStyle={{
                    backgroundColor: '#1e1e2e',
                    border: '1px solid rgba(255,255,255,0.1)',
                    borderRadius: 8,
                  }}
                />
                <Area
                  type="monotone"
                  dataKey="count"
                  name="Connections"
                  stroke="#10b981"
                  fill="rgba(16, 185, 129, 0.2)"
                  strokeWidth={2}
                />
              </AreaChart>
            </ResponsiveContainer>
          </ChartCard>
        </Grid>
      </Grid>

      {/* Bottom Section */}
      <Grid container spacing={3}>
        {/* Recent Activity */}
        <Grid item xs={12} md={6}>
          <Paper
            sx={{
              p: 3,
              background: 'linear-gradient(145deg, #1e1e2e 0%, #2d2d44 100%)',
              borderRadius: 3,
              border: '1px solid rgba(255, 255, 255, 0.05)',
            }}
          >
            <Typography variant="h6" fontWeight="600" mb={2}>
              Recent Activity
            </Typography>
            <List>
              {[
                { text: 'New user registered', time: '2 mins ago', type: 'user' },
                { text: 'Server US-East updated', time: '15 mins ago', type: 'server' },
                { text: 'Payment received $49.99', time: '1 hour ago', type: 'payment' },
                { text: 'New subscription activated', time: '2 hours ago', type: 'subscription' },
                { text: 'Server load alert: EU-West', time: '3 hours ago', type: 'alert' },
              ].map((item, index) => (
                <ListItem key={index} sx={{ px: 0 }}>
                  <ListItemAvatar>
                    <Avatar
                      sx={{
                        bgcolor:
                          item.type === 'user'
                            ? 'primary.main'
                            : item.type === 'server'
                            ? 'success.main'
                            : item.type === 'payment'
                            ? 'warning.main'
                            : item.type === 'alert'
                            ? 'error.main'
                            : 'info.main',
                      }}
                    >
                      {item.type === 'user' ? (
                        <PersonAddIcon />
                      ) : item.type === 'server' ? (
                        <DnsIcon />
                      ) : item.type === 'payment' ? (
                        <MoneyIcon />
                      ) : (
                        <TrendingUpIcon />
                      )}
                    </Avatar>
                  </ListItemAvatar>
                  <ListItemText
                    primary={item.text}
                    secondary={item.time}
                    primaryTypographyProps={{ fontWeight: 500 }}
                  />
                </ListItem>
              ))}
            </List>
          </Paper>
        </Grid>

        {/* Top Countries */}
        <Grid item xs={12} md={6}>
          <Paper
            sx={{
              p: 3,
              background: 'linear-gradient(145deg, #1e1e2e 0%, #2d2d44 100%)',
              borderRadius: 3,
              border: '1px solid rgba(255, 255, 255, 0.05)',
            }}
          >
            <Typography variant="h6" fontWeight="600" mb={2}>
              Top Countries
            </Typography>
            <List>
              {(analytics?.topCountries || []).slice(0, 5).map((country, index) => (
                <ListItem key={index} sx={{ px: 0 }}>
                  <ListItemAvatar>
                    <Avatar
                      sx={{
                        bgcolor: COLORS[index % COLORS.length],
                        width: 36,
                        height: 36,
                        fontSize: 14,
                      }}
                    >
                      {index + 1}
                    </Avatar>
                  </ListItemAvatar>
                  <ListItemText
                    primary={country.country}
                    secondary={`${formatNumber(country.users)} users`}
                  />
                  <Chip
                    label={`${((country.users / (stats?.totalUsers || 1)) * 100).toFixed(1)}%`}
                    size="small"
                    sx={{
                      bgcolor: 'rgba(124, 58, 237, 0.2)',
                      color: 'primary.main',
                    }}
                  />
                </ListItem>
              ))}
            </List>
          </Paper>
        </Grid>
      </Grid>
    </Box>
  );
};

export default Dashboard;
