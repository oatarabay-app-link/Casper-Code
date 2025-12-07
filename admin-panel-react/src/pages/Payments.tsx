import React, { useEffect, useState, useCallback } from 'react';
import {
  Box,
  Typography,
  Grid,
  Paper,
  Chip,
  IconButton,
  Tooltip,
  TextField,
  Skeleton,
  Tab,
  Tabs,
} from '@mui/material';
import {
  Download as DownloadIcon,
  Visibility as ViewIcon,
  AttachMoney as MoneyIcon,
  TrendingUp as TrendingUpIcon,
  Receipt as ReceiptIcon,
  CreditCard as CreditCardIcon,
} from '@mui/icons-material';
import { DatePicker } from '@mui/x-date-pickers/DatePicker';
import { LocalizationProvider } from '@mui/x-date-pickers/LocalizationProvider';
import { AdapterDateFns } from '@mui/x-date-pickers/AdapterDateFns';
import { toast } from 'react-toastify';
import { format } from 'date-fns';
import { DataTable, StatCard } from '../components';
import adminService from '../services/adminService';
import { usePagination } from '../hooks';
import {
  PaymentDto,
  InvoiceDto,
  RevenueDto,
  TableColumn,
  PaginatedResponse,
} from '../types';

interface TabPanelProps {
  children?: React.ReactNode;
  index: number;
  value: number;
}

const TabPanel: React.FC<TabPanelProps> = ({ children, value, index }) => (
  <Box role="tabpanel" hidden={value !== index} sx={{ py: 3 }}>
    {value === index && children}
  </Box>
);

export const Payments: React.FC = () => {
  const [tabValue, setTabValue] = useState(0);
  const [payments, setPayments] = useState<PaginatedResponse<PaymentDto>>({
    items: [],
    totalCount: 0,
    page: 1,
    pageSize: 20,
    totalPages: 0,
  });
  const [invoices, setInvoices] = useState<PaginatedResponse<InvoiceDto>>({
    items: [],
    totalCount: 0,
    page: 1,
    pageSize: 20,
    totalPages: 0,
  });
  const [revenue, setRevenue] = useState<RevenueDto | null>(null);
  const [loading, setLoading] = useState(true);
  const [startDate, setStartDate] = useState<Date | null>(null);
  const [endDate, setEndDate] = useState<Date | null>(null);
  const [statusFilter, setStatusFilter] = useState<string>('');

  const paymentsPagination = usePagination(20);
  const invoicesPagination = usePagination(20);

  const fetchPayments = useCallback(async () => {
    setLoading(true);
    try {
      const data = await adminService.getPaymentHistory({
        ...paymentsPagination.paginationParams,
        status: statusFilter || undefined,
        startDate: startDate ? format(startDate, 'yyyy-MM-dd') : undefined,
        endDate: endDate ? format(endDate, 'yyyy-MM-dd') : undefined,
      });
      setPayments(data);
    } catch (error: any) {
      toast.error('Failed to load payments');
    } finally {
      setLoading(false);
    }
  }, [paymentsPagination.paginationParams, statusFilter, startDate, endDate]);

  const fetchInvoices = useCallback(async () => {
    setLoading(true);
    try {
      const data = await adminService.getInvoices(invoicesPagination.paginationParams);
      setInvoices(data);
    } catch (error: any) {
      toast.error('Failed to load invoices');
    } finally {
      setLoading(false);
    }
  }, [invoicesPagination.paginationParams]);

  const fetchRevenue = useCallback(async () => {
    try {
      const data = await adminService.getRevenue();
      setRevenue(data);
    } catch (error: any) {
      console.error('Failed to load revenue data');
    }
  }, []);

  useEffect(() => {
    fetchRevenue();
  }, [fetchRevenue]);

  useEffect(() => {
    if (tabValue === 0) {
      fetchPayments();
    } else {
      fetchInvoices();
    }
  }, [tabValue, fetchPayments, fetchInvoices]);

  const formatCurrency = (value: number, currency: string = 'USD'): string => {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: currency,
    }).format(value);
  };

  const getStatusColor = (status: string): 'success' | 'warning' | 'error' | 'default' => {
    switch (status.toLowerCase()) {
      case 'succeeded':
      case 'paid':
        return 'success';
      case 'pending':
      case 'processing':
        return 'warning';
      case 'failed':
      case 'canceled':
        return 'error';
      default:
        return 'default';
    }
  };

  const paymentColumns: TableColumn<PaymentDto>[] = [
    {
      id: 'id',
      label: 'Transaction ID',
      minWidth: 120,
      format: (value) => (
        <Typography variant="body2" fontFamily="monospace">
          {value.slice(0, 8)}...
        </Typography>
      ),
    },
    { id: 'userEmail', label: 'Customer', minWidth: 180 },
    { id: 'planName', label: 'Plan', minWidth: 100 },
    {
      id: 'amount',
      label: 'Amount',
      minWidth: 100,
      format: (value, row) => formatCurrency(value, row.currency),
    },
    { id: 'paymentMethod', label: 'Method', minWidth: 100 },
    {
      id: 'status',
      label: 'Status',
      minWidth: 100,
      format: (value) => (
        <Chip
          label={value}
          size="small"
          color={getStatusColor(value)}
          sx={{ borderRadius: 1 }}
        />
      ),
    },
    {
      id: 'createdAt',
      label: 'Date',
      minWidth: 150,
      sortable: true,
      format: (value) => format(new Date(value), 'MMM dd, yyyy HH:mm'),
    },
  ];

  const invoiceColumns: TableColumn<InvoiceDto>[] = [
    {
      id: 'invoiceNumber',
      label: 'Invoice #',
      minWidth: 120,
      format: (value) => (
        <Typography variant="body2" fontWeight="600">
          {value}
        </Typography>
      ),
    },
    {
      id: 'amount',
      label: 'Amount',
      minWidth: 100,
      format: (value, row) => formatCurrency(value, row.currency),
    },
    {
      id: 'status',
      label: 'Status',
      minWidth: 100,
      format: (value) => (
        <Chip
          label={value}
          size="small"
          color={getStatusColor(value)}
          sx={{ borderRadius: 1 }}
        />
      ),
    },
    {
      id: 'dueDate',
      label: 'Due Date',
      minWidth: 120,
      format: (value) => format(new Date(value), 'MMM dd, yyyy'),
    },
    {
      id: 'paidAt',
      label: 'Paid Date',
      minWidth: 120,
      format: (value) => (value ? format(new Date(value), 'MMM dd, yyyy') : '-'),
    },
    {
      id: 'actions',
      label: 'Actions',
      minWidth: 100,
      align: 'center',
      format: (_, row) => (
        <Box>
          <Tooltip title="View Invoice">
            <IconButton size="small">
              <ViewIcon fontSize="small" />
            </IconButton>
          </Tooltip>
          <Tooltip title="Download">
            <IconButton
              size="small"
              onClick={() => window.open(row.downloadUrl, '_blank')}
            >
              <DownloadIcon fontSize="small" />
            </IconButton>
          </Tooltip>
        </Box>
      ),
    },
  ];

  return (
    <LocalizationProvider dateAdapter={AdapterDateFns}>
      <Box>
        <Typography variant="h4" fontWeight="bold" mb={3}>
          Payments
        </Typography>

        {/* Revenue Summary Cards */}
        <Grid container spacing={3} mb={3}>
          <Grid item xs={12} sm={6} md={3}>
            <StatCard
              title="Total Revenue"
              value={formatCurrency(revenue?.totalRevenue || 0)}
              subtitle="All time"
              icon={<MoneyIcon />}
              color="#10b981"
            />
          </Grid>
          <Grid item xs={12} sm={6} md={3}>
            <StatCard
              title="Monthly Revenue"
              value={formatCurrency(revenue?.mrr || 0)}
              subtitle="MRR"
              icon={<TrendingUpIcon />}
              trend={{ value: 15, label: 'vs last month' }}
              color="#7c3aed"
            />
          </Grid>
          <Grid item xs={12} sm={6} md={3}>
            <StatCard
              title="Annual Revenue"
              value={formatCurrency(revenue?.arr || 0)}
              subtitle="ARR"
              icon={<ReceiptIcon />}
              color="#06b6d4"
            />
          </Grid>
          <Grid item xs={12} sm={6} md={3}>
            <StatCard
              title="Avg Revenue/User"
              value={formatCurrency(revenue?.averageRevenuePerUser || 0)}
              subtitle="ARPU"
              icon={<CreditCardIcon />}
              color="#f59e0b"
            />
          </Grid>
        </Grid>

        {/* Tabs */}
        <Paper
          sx={{
            background: 'linear-gradient(145deg, #1e1e2e 0%, #2d2d44 100%)',
            borderRadius: 3,
            border: '1px solid rgba(255, 255, 255, 0.05)',
          }}
        >
          <Tabs
            value={tabValue}
            onChange={(_, newValue) => setTabValue(newValue)}
            sx={{
              borderBottom: '1px solid rgba(255, 255, 255, 0.05)',
              px: 2,
            }}
          >
            <Tab label="Payment History" />
            <Tab label="Invoices" />
          </Tabs>

          <TabPanel value={tabValue} index={0}>
            {/* Filters */}
            <Box display="flex" gap={2} mb={3} px={2} flexWrap="wrap">
              <DatePicker
                label="Start Date"
                value={startDate}
                onChange={(newValue) => setStartDate(newValue)}
                slotProps={{
                  textField: { size: 'small', sx: { width: 160 } },
                }}
              />
              <DatePicker
                label="End Date"
                value={endDate}
                onChange={(newValue) => setEndDate(newValue)}
                slotProps={{
                  textField: { size: 'small', sx: { width: 160 } },
                }}
              />
              <TextField
                select
                label="Status"
                value={statusFilter}
                onChange={(e) => setStatusFilter(e.target.value)}
                size="small"
                sx={{ width: 140 }}
                SelectProps={{ native: true }}
              >
                <option value="">All</option>
                <option value="succeeded">Succeeded</option>
                <option value="pending">Pending</option>
                <option value="failed">Failed</option>
              </TextField>
            </Box>

            {loading ? (
              <Box px={2}>
                <Skeleton variant="rounded" height={400} sx={{ borderRadius: 2 }} />
              </Box>
            ) : (
              <Box px={2}>
                <DataTable
                  columns={paymentColumns}
                  data={payments.items}
                  totalCount={payments.totalCount}
                  page={paymentsPagination.paginationParams.page}
                  pageSize={paymentsPagination.paginationParams.pageSize}
                  onPageChange={paymentsPagination.setPage}
                  onPageSizeChange={paymentsPagination.setPageSize}
                  onRefresh={fetchPayments}
                  sortBy={paymentsPagination.paginationParams.sortBy}
                  sortDescending={paymentsPagination.paginationParams.sortDescending}
                  onSort={(col, desc) => paymentsPagination.setSorting(col, desc)}
                />
              </Box>
            )}
          </TabPanel>

          <TabPanel value={tabValue} index={1}>
            {loading ? (
              <Box px={2}>
                <Skeleton variant="rounded" height={400} sx={{ borderRadius: 2 }} />
              </Box>
            ) : (
              <Box px={2}>
                <DataTable
                  columns={invoiceColumns}
                  data={invoices.items}
                  totalCount={invoices.totalCount}
                  page={invoicesPagination.paginationParams.page}
                  pageSize={invoicesPagination.paginationParams.pageSize}
                  onPageChange={invoicesPagination.setPage}
                  onPageSizeChange={invoicesPagination.setPageSize}
                  onRefresh={fetchInvoices}
                />
              </Box>
            )}
          </TabPanel>
        </Paper>
      </Box>
    </LocalizationProvider>
  );
};

export default Payments;
