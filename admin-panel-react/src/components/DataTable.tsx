import React, { useState } from 'react';
import {
  Box,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  TablePagination,
  TableSortLabel,
  Paper,
  TextField,
  InputAdornment,
  IconButton,
  Tooltip,
  Typography,
  Skeleton,
} from '@mui/material';
import {
  Search as SearchIcon,
  Refresh as RefreshIcon,
  Add as AddIcon,
} from '@mui/icons-material';
import { TableColumn } from '../types';

interface DataTableProps<T> {
  columns: TableColumn<T>[];
  data: T[];
  totalCount: number;
  page: number;
  pageSize: number;
  loading?: boolean;
  searchPlaceholder?: string;
  title?: string;
  onPageChange: (page: number) => void;
  onPageSizeChange: (pageSize: number) => void;
  onSearch?: (search: string) => void;
  onSort?: (column: string, descending: boolean) => void;
  onRefresh?: () => void;
  onAdd?: () => void;
  sortBy?: string;
  sortDescending?: boolean;
  rowKey?: keyof T | ((row: T) => string);
}

export function DataTable<T extends { id?: string }>({
  columns,
  data,
  totalCount,
  page,
  pageSize,
  loading = false,
  searchPlaceholder = 'Search...',
  title,
  onPageChange,
  onPageSizeChange,
  onSearch,
  onSort,
  onRefresh,
  onAdd,
  sortBy,
  sortDescending = false,
  rowKey = 'id',
}: DataTableProps<T>) {
  const [searchValue, setSearchValue] = useState('');

  const handleSearchChange = (event: React.ChangeEvent<HTMLInputElement>) => {
    setSearchValue(event.target.value);
  };

  const handleSearchKeyDown = (event: React.KeyboardEvent<HTMLInputElement>) => {
    if (event.key === 'Enter' && onSearch) {
      onSearch(searchValue);
    }
  };

  const handleSort = (column: string) => {
    if (onSort) {
      const isCurrentColumn = sortBy === column;
      const newDirection = isCurrentColumn ? !sortDescending : false;
      onSort(column, newDirection);
    }
  };

  const getRowKey = (row: T, index: number): string => {
    if (typeof rowKey === 'function') {
      return rowKey(row);
    }
    return (row[rowKey] as string) || String(index);
  };

  return (
    <Paper
      sx={{
        width: '100%',
        overflow: 'hidden',
        background: 'linear-gradient(145deg, #1e1e2e 0%, #2d2d44 100%)',
        borderRadius: 3,
        border: '1px solid rgba(255, 255, 255, 0.05)',
      }}
    >
      {/* Header */}
      <Box
        sx={{
          p: 2,
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'space-between',
          borderBottom: '1px solid rgba(255, 255, 255, 0.05)',
        }}
      >
        <Box display="flex" alignItems="center" gap={2}>
          {title && (
            <Typography variant="h6" fontWeight="600">
              {title}
            </Typography>
          )}
          {onSearch && (
            <TextField
              size="small"
              placeholder={searchPlaceholder}
              value={searchValue}
              onChange={handleSearchChange}
              onKeyDown={handleSearchKeyDown}
              InputProps={{
                startAdornment: (
                  <InputAdornment position="start">
                    <SearchIcon sx={{ color: 'text.secondary' }} />
                  </InputAdornment>
                ),
              }}
              sx={{
                width: 250,
                '& .MuiOutlinedInput-root': {
                  borderRadius: 2,
                  backgroundColor: 'rgba(255, 255, 255, 0.03)',
                },
              }}
            />
          )}
        </Box>
        <Box display="flex" gap={1}>
          {onRefresh && (
            <Tooltip title="Refresh">
              <IconButton onClick={onRefresh} disabled={loading}>
                <RefreshIcon />
              </IconButton>
            </Tooltip>
          )}
          {onAdd && (
            <Tooltip title="Add New">
              <IconButton
                onClick={onAdd}
                sx={{
                  backgroundColor: 'primary.main',
                  '&:hover': { backgroundColor: 'primary.dark' },
                }}
              >
                <AddIcon />
              </IconButton>
            </Tooltip>
          )}
        </Box>
      </Box>

      {/* Table */}
      <TableContainer sx={{ maxHeight: 600 }}>
        <Table stickyHeader size="small">
          <TableHead>
            <TableRow>
              {columns.map((column) => (
                <TableCell
                  key={String(column.id)}
                  align={column.align || 'left'}
                  style={{ minWidth: column.minWidth }}
                  sx={{
                    backgroundColor: '#1a1a2e',
                    fontWeight: 600,
                    color: 'text.secondary',
                    borderBottom: '1px solid rgba(255, 255, 255, 0.1)',
                  }}
                >
                  {column.sortable && onSort ? (
                    <TableSortLabel
                      active={sortBy === column.id}
                      direction={sortBy === column.id && sortDescending ? 'desc' : 'asc'}
                      onClick={() => handleSort(String(column.id))}
                    >
                      {column.label}
                    </TableSortLabel>
                  ) : (
                    column.label
                  )}
                </TableCell>
              ))}
            </TableRow>
          </TableHead>
          <TableBody>
            {loading ? (
              // Loading skeleton
              Array.from({ length: 5 }).map((_, index) => (
                <TableRow key={index}>
                  {columns.map((column) => (
                    <TableCell key={String(column.id)}>
                      <Skeleton variant="text" animation="wave" />
                    </TableCell>
                  ))}
                </TableRow>
              ))
            ) : data.length === 0 ? (
              // Empty state
              <TableRow>
                <TableCell colSpan={columns.length} align="center" sx={{ py: 4 }}>
                  <Typography color="text.secondary">No data available</Typography>
                </TableCell>
              </TableRow>
            ) : (
              // Data rows
              data.map((row, index) => (
                <TableRow
                  hover
                  key={getRowKey(row, index)}
                  sx={{
                    '&:hover': {
                      backgroundColor: 'rgba(124, 58, 237, 0.05)',
                    },
                  }}
                >
                  {columns.map((column) => {
                    const columnId = String(column.id);
                    const value = columnId.includes('.')
                      ? columnId.split('.').reduce((obj: any, key: string) => obj?.[key], row)
                      : (row as any)[column.id];
                    return (
                      <TableCell
                        key={String(column.id)}
                        align={column.align || 'left'}
                        sx={{ borderBottom: '1px solid rgba(255, 255, 255, 0.05)' }}
                      >
                        {column.format ? column.format(value, row) : value}
                      </TableCell>
                    );
                  })}
                </TableRow>
              ))
            )}
          </TableBody>
        </Table>
      </TableContainer>

      {/* Pagination */}
      <TablePagination
        rowsPerPageOptions={[10, 20, 50, 100]}
        component="div"
        count={totalCount}
        rowsPerPage={pageSize}
        page={page - 1}
        onPageChange={(_, newPage) => onPageChange(newPage + 1)}
        onRowsPerPageChange={(e) => onPageSizeChange(parseInt(e.target.value, 10))}
        sx={{
          borderTop: '1px solid rgba(255, 255, 255, 0.05)',
          '.MuiTablePagination-selectLabel, .MuiTablePagination-displayedRows': {
            color: 'text.secondary',
          },
        }}
      />
    </Paper>
  );
}

export default DataTable;
