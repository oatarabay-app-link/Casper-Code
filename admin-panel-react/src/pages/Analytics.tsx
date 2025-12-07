import React, { useEffect, useState, useCallback } from 'react';
import {
  Box,
  Typography,
  Grid,
  Paper,
  Skeleton,
  Button,
  ButtonGroup,
} from '@mui/material';
import { DatePicker } from '@mui/x-date-pickers/DatePicker';
import { LocalizationProvider } from '@mui/x-date-pickers/LocalizationProvider';
import { AdapterDateFns } from '@mui/x-date-pickers/AdapterDateFns';
import {
  LineChart,
  Line,
  BarChart,
  Bar,
  AreaChart,
  Area,
  PieChart,
  Pie,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  Legend,
  ResponsiveContainer,
  Cell,
  RadialBarChart,
  RadialBar,
} from 'recharts';
import { toast } from 'react-toastify';
import { format, subDays, subMonths } from 'date-fns';
import adminService from '../services/adminService';
import { AnalyticsDto } from '../types';

const COLORS = ['#7c3aed', '#06b6d4', '#10b981', '#f59e0b', '#ef4444', '#ec4899', '#8b5cf6'];

type DateRange = '7d' | '30d' | '90d' | 'custom';

export const Analytics: React.FC = () => {
  const [analytics, setAnalytics] = useState<AnalyticsDto | null>(null);
  const [loading, setLoading] = useState(true);
  const [dateRange, setDateRange] = useState<DateRange>('30d');
  const [startDate, setStartDate] = useState<Date | null>(subDays(new Date(), 30));
  const [endDate, setEndDate] = useState<Date | null>(new Date());

  const fetchAnalytics = useCallback(async () => {
    setLoading(true);
    try {
      const data = await adminService.getAnalytics(
        startDate ? format(startDate, 'yyyy-MM-dd') : undefined,
        endDate ? format(endDate, 'yyyy-MM-dd') : undefined
      );
      setAnalytics(data);
    } catch (error: any) {
      toast.error('Failed to load analytics');
    } finally {
      setLoading(false);
    }
  }, [startDate, endDate]);

  useEffect(() => {
    fetchAnalytics();
  }, [fetchAnalytics]);

  const handleDateRangeChange = (range: DateRange) => {
    setDateRange(range);
    const now = new Date();
    
    switch (range) {
      case '7d':
        setStartDate(subDays(now, 7));
        setEndDate(now);
        break;
      case '30d':
        setStartDate(subDays(now, 30));
        setEndDate(now);
        break;
      case '90d':
        setStartDate(subMonths(now, 3));
        setEndDate(now);
        break;
      case 'custom':
        break;
    }
  };

  const ChartCard: React.FC<{ title: string; children: React.ReactNode; height?: number }> = ({
    title,
    children,
    height = 350,
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
      <Box height={height}>{children}</Box>
    </Paper>
  );

  const tooltipStyle = {
    backgroundColor: '#1e1e2e',
    border: '1px solid rgba(255,255,255,0.1)',
    borderRadius: 8,
  };

  if (loading) {
    return (
      <Box>
        <Typography variant="h4" fontWeight="bold" mb={3}>
          Analytics
        </Typography>
        <Grid container spacing={3}>
          {[1, 2, 3, 4, 5, 6].map((i) => (
            <Grid item xs={12} md={6} key={i}>
              <Skeleton variant="rounded" height={400} sx={{ borderRadius: 3 }} />
            </Grid>
          ))}
        </Grid>
      </Box>
    );
  }

  // Transform server loads for radial chart
  const serverLoadData = (analytics?.serverLoads || []).map((server, index) => ({
    name: server.serverName,
    load: server.load,
    fill: COLORS[index % COLORS.length],
  }));

  return (
    <LocalizationProvider dateAdapter={AdapterDateFns}>
      <Box>
        <Box display="flex" justifyContent="space-between" alignItems="center" mb={3} flexWrap="wrap" gap={2}>
          <Typography variant="h4" fontWeight="bold">
            Analytics
          </Typography>
          <Box display="flex" gap={2} alignItems="center" flexWrap="wrap">
            <ButtonGroup size="small">
              {(['7d', '30d', '90d', 'custom'] as DateRange[]).map((range) => (
                <Button
                  key={range}
                  variant={dateRange === range ? 'contained' : 'outlined'}
                  onClick={() => handleDateRangeChange(range)}
                >
                  {range === 'custom' ? 'Custom' : range}
                </Button>
              ))}
            </ButtonGroup>
            {dateRange === 'custom' && (
              <>
                <DatePicker
                  label="Start"
                  value={startDate}
                  onChange={(date) => setStartDate(date)}
                  slotProps={{ textField: { size: 'small', sx: { width: 150 } } }}
                />
                <DatePicker
                  label="End"
                  value={endDate}
                  onChange={(date) => setEndDate(date)}
                  slotProps={{ textField: { size: 'small', sx: { width: 150 } } }}
                />
              </>
            )}
          </Box>
        </Box>

        <Grid container spacing={3}>
          {/* User Growth Trend */}
          <Grid item xs={12} md={8}>
            <ChartCard title="User Growth Trend">
              <ResponsiveContainer width="100%" height="100%">
                <AreaChart data={analytics?.userGrowth || []}>
                  <defs>
                    <linearGradient id="colorUsers" x1="0" y1="0" x2="0" y2="1">
                      <stop offset="5%" stopColor="#7c3aed" stopOpacity={0.3} />
                      <stop offset="95%" stopColor="#7c3aed" stopOpacity={0} />
                    </linearGradient>
                  </defs>
                  <CartesianGrid strokeDasharray="3 3" stroke="rgba(255,255,255,0.1)" />
                  <XAxis dataKey="date" stroke="#888" fontSize={12} />
                  <YAxis stroke="#888" fontSize={12} />
                  <Tooltip contentStyle={tooltipStyle} />
                  <Area
                    type="monotone"
                    dataKey="count"
                    name="New Users"
                    stroke="#7c3aed"
                    strokeWidth={3}
                    fillOpacity={1}
                    fill="url(#colorUsers)"
                  />
                </AreaChart>
              </ResponsiveContainer>
            </ChartCard>
          </Grid>

          {/* Top Countries Pie Chart */}
          <Grid item xs={12} md={4}>
            <ChartCard title="Users by Country">
              <ResponsiveContainer width="100%" height="100%">
                <PieChart>
                  <Pie
                    data={analytics?.topCountries || []}
                    cx="50%"
                    cy="50%"
                    innerRadius={60}
                    outerRadius={100}
                    paddingAngle={5}
                    dataKey="users"
                    nameKey="country"
                    label={({ country, percent }) =>
                      `${country}: ${(percent * 100).toFixed(0)}%`
                    }
                    labelLine={false}
                  >
                    {(analytics?.topCountries || []).map((_, index) => (
                      <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                    ))}
                  </Pie>
                  <Tooltip contentStyle={tooltipStyle} />
                </PieChart>
              </ResponsiveContainer>
            </ChartCard>
          </Grid>

          {/* Server Load Distribution */}
          <Grid item xs={12} md={6}>
            <ChartCard title="Server Load Distribution" height={300}>
              <ResponsiveContainer width="100%" height="100%">
                <RadialBarChart
                  cx="50%"
                  cy="50%"
                  innerRadius="20%"
                  outerRadius="90%"
                  data={serverLoadData}
                  startAngle={90}
                  endAngle={-270}
                >
                  <RadialBar
                    background
                    dataKey="load"
                    cornerRadius={5}
                  />
                  <Legend
                    iconSize={10}
                    layout="vertical"
                    verticalAlign="middle"
                    align="right"
                    wrapperStyle={{ fontSize: 12 }}
                  />
                  <Tooltip contentStyle={tooltipStyle} formatter={(value: number) => `${value}%`} />
                </RadialBarChart>
              </ResponsiveContainer>
            </ChartCard>
          </Grid>

          {/* Bandwidth Usage by Server */}
          <Grid item xs={12} md={6}>
            <ChartCard title="Bandwidth Usage by Server" height={300}>
              <ResponsiveContainer width="100%" height="100%">
                <BarChart
                  data={analytics?.bandwidthByServer || []}
                  layout="vertical"
                  margin={{ left: 80 }}
                >
                  <CartesianGrid strokeDasharray="3 3" stroke="rgba(255,255,255,0.1)" />
                  <XAxis type="number" stroke="#888" fontSize={12} />
                  <YAxis
                    type="category"
                    dataKey="serverName"
                    stroke="#888"
                    fontSize={12}
                    width={75}
                  />
                  <Tooltip
                    contentStyle={tooltipStyle}
                    formatter={(value: number) => `${value.toFixed(2)} GB`}
                  />
                  <Bar dataKey="bandwidthGB" name="Bandwidth (GB)" radius={[0, 4, 4, 0]}>
                    {(analytics?.bandwidthByServer || []).map((_, index) => (
                      <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                    ))}
                  </Bar>
                </BarChart>
              </ResponsiveContainer>
            </ChartCard>
          </Grid>

          {/* Connections Over Time */}
          <Grid item xs={12}>
            <ChartCard title="Connection Patterns" height={300}>
              <ResponsiveContainer width="100%" height="100%">
                <LineChart data={analytics?.connectionsOverTime || []}>
                  <CartesianGrid strokeDasharray="3 3" stroke="rgba(255,255,255,0.1)" />
                  <XAxis dataKey="date" stroke="#888" fontSize={12} />
                  <YAxis stroke="#888" fontSize={12} />
                  <Tooltip contentStyle={tooltipStyle} />
                  <Legend />
                  <Line
                    type="monotone"
                    dataKey="count"
                    name="Active Connections"
                    stroke="#06b6d4"
                    strokeWidth={2}
                    dot={{ fill: '#06b6d4', strokeWidth: 2 }}
                    activeDot={{ r: 6 }}
                  />
                </LineChart>
              </ResponsiveContainer>
            </ChartCard>
          </Grid>

          {/* Top Countries Bar Chart */}
          <Grid item xs={12}>
            <ChartCard title="Top Countries by Users" height={300}>
              <ResponsiveContainer width="100%" height="100%">
                <BarChart data={analytics?.topCountries || []}>
                  <CartesianGrid strokeDasharray="3 3" stroke="rgba(255,255,255,0.1)" />
                  <XAxis dataKey="country" stroke="#888" fontSize={12} />
                  <YAxis stroke="#888" fontSize={12} />
                  <Tooltip contentStyle={tooltipStyle} />
                  <Bar dataKey="users" name="Users" radius={[4, 4, 0, 0]}>
                    {(analytics?.topCountries || []).map((_, index) => (
                      <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                    ))}
                  </Bar>
                </BarChart>
              </ResponsiveContainer>
            </ChartCard>
          </Grid>
        </Grid>
      </Box>
    </LocalizationProvider>
  );
};

export default Analytics;
