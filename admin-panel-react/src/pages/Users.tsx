import React, { useEffect, useState, useCallback } from 'react';
import {
  Box,
  Typography,
  Chip,
  IconButton,
  Tooltip,
  Dialog,
  DialogTitle,
  DialogContent,
  DialogActions,
  Button,
  TextField,
  Grid,
  FormControl,
  InputLabel,
  Select,
  MenuItem,
  Switch,
  FormControlLabel,
} from '@mui/material';
import {
  Edit as EditIcon,
  Delete as DeleteIcon,
  PersonAdd as PersonAddIcon,
} from '@mui/icons-material';
import { useForm, Controller } from 'react-hook-form';
import { yupResolver } from '@hookform/resolvers/yup';
import * as yup from 'yup';
import { toast } from 'react-toastify';
import { format } from 'date-fns';
import { DataTable, ConfirmDialog } from '../components';
import adminService from '../services/adminService';
import { usePagination } from '../hooks';
import {
  UserListItemDto,
  CreateUserRequest,
  TableColumn,
  PaginatedResponse,
} from '../types';

const createSchema = yup.object({
  email: yup.string().email('Invalid email').required('Email is required'),
  password: yup.string().min(8, 'Min 8 characters').required('Password is required'),
  firstName: yup.string().required('First name is required'),
  lastName: yup.string().required('Last name is required'),
  role: yup.string().required('Role is required'),
});

const updateSchema = yup.object({
  email: yup.string().email('Invalid email').required('Email is required'),
  firstName: yup.string().required('First name is required'),
  lastName: yup.string().required('Last name is required'),
  role: yup.string().required('Role is required'),
  isActive: yup.boolean().required(),
});

type UpdateFormData = {
  email: string;
  firstName: string;
  lastName: string;
  role: string;
  isActive: boolean;
};

export const Users: React.FC = () => {
  const [users, setUsers] = useState<PaginatedResponse<UserListItemDto>>({
    items: [],
    totalCount: 0,
    page: 1,
    pageSize: 20,
    totalPages: 0,
  });
  const [loading, setLoading] = useState(true);
  const [modalOpen, setModalOpen] = useState(false);
  const [editingUser, setEditingUser] = useState<UserListItemDto | null>(null);
  const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
  const [userToDelete, setUserToDelete] = useState<UserListItemDto | null>(null);
  const [saving, setSaving] = useState(false);

  const { paginationParams, setPage, setPageSize, setSearch, setSorting } = usePagination(20);

  const createForm = useForm<CreateUserRequest>({
    resolver: yupResolver(createSchema),
    defaultValues: { email: '', password: '', firstName: '', lastName: '', role: 'User' },
  });

  const updateForm = useForm<UpdateFormData>({
    resolver: yupResolver(updateSchema),
  });

  const fetchUsers = useCallback(async () => {
    setLoading(true);
    try {
      const data = await adminService.getUsers(paginationParams);
      setUsers(data);
    } catch (error: any) {
      toast.error('Failed to load users');
    } finally {
      setLoading(false);
    }
  }, [paginationParams]);

  useEffect(() => {
    fetchUsers();
  }, [fetchUsers]);

  const handleCreate = () => {
    setEditingUser(null);
    createForm.reset({ email: '', password: '', firstName: '', lastName: '', role: 'User' });
    setModalOpen(true);
  };

  const handleEdit = (user: UserListItemDto) => {
    setEditingUser(user);
    updateForm.reset({
      email: user.email,
      firstName: user.firstName,
      lastName: user.lastName,
      role: user.role,
      isActive: user.isActive,
    });
    setModalOpen(true);
  };

  const handleDeleteClick = (user: UserListItemDto) => {
    setUserToDelete(user);
    setDeleteDialogOpen(true);
  };

  const handleDelete = async () => {
    if (!userToDelete) return;
    setSaving(true);
    try {
      await adminService.deleteUser(userToDelete.id);
      toast.success('User deleted successfully');
      setDeleteDialogOpen(false);
      setUserToDelete(null);
      fetchUsers();
    } catch (error: any) {
      toast.error(error.message || 'Failed to delete user');
    } finally {
      setSaving(false);
    }
  };

  const onCreateSubmit = async (data: CreateUserRequest) => {
    setSaving(true);
    try {
      await adminService.createUser(data);
      toast.success('User created successfully');
      setModalOpen(false);
      fetchUsers();
    } catch (error: any) {
      toast.error(error.message || 'Failed to create user');
    } finally {
      setSaving(false);
    }
  };

  const onUpdateSubmit = async (data: UpdateFormData) => {
    if (!editingUser) return;
    setSaving(true);
    try {
      await adminService.updateUser(editingUser.id, data);
      toast.success('User updated successfully');
      setModalOpen(false);
      fetchUsers();
    } catch (error: any) {
      toast.error(error.message || 'Failed to update user');
    } finally {
      setSaving(false);
    }
  };

  const columns: TableColumn<UserListItemDto>[] = [
    {
      id: 'name',
      label: 'Name',
      minWidth: 150,
      sortable: true,
      format: (_, row) => `${row.firstName} ${row.lastName}`,
    },
    { id: 'email', label: 'Email', minWidth: 200, sortable: true },
    {
      id: 'role',
      label: 'Role',
      minWidth: 100,
      format: (value) => (
        <Chip
          label={value}
          size="small"
          color={value === 'Admin' ? 'secondary' : value === 'SuperAdmin' ? 'error' : 'default'}
          sx={{ borderRadius: 1 }}
        />
      ),
    },
    {
      id: 'isActive',
      label: 'Status',
      minWidth: 100,
      format: (value) => (
        <Chip
          label={value ? 'Active' : 'Inactive'}
          size="small"
          color={value ? 'success' : 'default'}
          sx={{ borderRadius: 1 }}
        />
      ),
    },
    {
      id: 'subscriptionStatus',
      label: 'Subscription',
      minWidth: 120,
      format: (value) => (
        <Chip
          label={value || 'None'}
          size="small"
          variant="outlined"
          color={value === 'active' ? 'success' : value === 'trial' ? 'warning' : 'default'}
          sx={{ borderRadius: 1 }}
        />
      ),
    },
    {
      id: 'lastLoginAt',
      label: 'Last Login',
      minWidth: 150,
      sortable: true,
      format: (value) => (value ? format(new Date(value), 'MMM dd, yyyy HH:mm') : 'Never'),
    },
    {
      id: 'actions',
      label: 'Actions',
      minWidth: 100,
      align: 'center',
      format: (_, row) => (
        <Box>
          <Tooltip title="Edit">
            <IconButton size="small" onClick={() => handleEdit(row)}>
              <EditIcon fontSize="small" />
            </IconButton>
          </Tooltip>
          <Tooltip title="Delete">
            <IconButton size="small" color="error" onClick={() => handleDeleteClick(row)}>
              <DeleteIcon fontSize="small" />
            </IconButton>
          </Tooltip>
        </Box>
      ),
    },
  ];

  return (
    <Box>
      <Box display="flex" justifyContent="space-between" alignItems="center" mb={3}>
        <Typography variant="h4" fontWeight="bold">
          Users
        </Typography>
        <Button
          variant="contained"
          startIcon={<PersonAddIcon />}
          onClick={handleCreate}
          sx={{
            background: 'linear-gradient(135deg, #7c3aed 0%, #06b6d4 100%)',
            '&:hover': { background: 'linear-gradient(135deg, #6d28d9 0%, #0891b2 100%)' },
          }}
        >
          Add User
        </Button>
      </Box>

      <DataTable
        columns={columns}
        data={users.items}
        totalCount={users.totalCount}
        page={paginationParams.page}
        pageSize={paginationParams.pageSize}
        loading={loading}
        searchPlaceholder="Search users..."
        onPageChange={setPage}
        onPageSizeChange={setPageSize}
        onSearch={setSearch}
        onSort={(col, desc) => setSorting(col, desc)}
        onRefresh={fetchUsers}
        sortBy={paginationParams.sortBy}
        sortDescending={paginationParams.sortDescending}
      />

      {/* Create Modal */}
      <Dialog
        open={modalOpen && !editingUser}
        onClose={() => setModalOpen(false)}
        maxWidth="sm"
        fullWidth
        PaperProps={{
          sx: {
            borderRadius: 3,
            background: 'linear-gradient(145deg, #1e1e2e 0%, #2d2d44 100%)',
          },
        }}
      >
        <DialogTitle>Create User</DialogTitle>
        <form onSubmit={createForm.handleSubmit(onCreateSubmit)}>
          <DialogContent>
            <Grid container spacing={2}>
              <Grid item xs={12} sm={6}>
                <Controller
                  name="firstName"
                  control={createForm.control}
                  render={({ field, fieldState }) => (
                    <TextField
                      {...field}
                      fullWidth
                      label="First Name"
                      error={!!fieldState.error}
                      helperText={fieldState.error?.message}
                    />
                  )}
                />
              </Grid>
              <Grid item xs={12} sm={6}>
                <Controller
                  name="lastName"
                  control={createForm.control}
                  render={({ field, fieldState }) => (
                    <TextField
                      {...field}
                      fullWidth
                      label="Last Name"
                      error={!!fieldState.error}
                      helperText={fieldState.error?.message}
                    />
                  )}
                />
              </Grid>
              <Grid item xs={12}>
                <Controller
                  name="email"
                  control={createForm.control}
                  render={({ field, fieldState }) => (
                    <TextField
                      {...field}
                      fullWidth
                      label="Email"
                      type="email"
                      error={!!fieldState.error}
                      helperText={fieldState.error?.message}
                    />
                  )}
                />
              </Grid>
              <Grid item xs={12}>
                <Controller
                  name="password"
                  control={createForm.control}
                  render={({ field, fieldState }) => (
                    <TextField
                      {...field}
                      fullWidth
                      label="Password"
                      type="password"
                      error={!!fieldState.error}
                      helperText={fieldState.error?.message}
                    />
                  )}
                />
              </Grid>
              <Grid item xs={12}>
                <Controller
                  name="role"
                  control={createForm.control}
                  render={({ field, fieldState }) => (
                    <FormControl fullWidth error={!!fieldState.error}>
                      <InputLabel>Role</InputLabel>
                      <Select {...field} label="Role">
                        <MenuItem value="User">User</MenuItem>
                        <MenuItem value="Admin">Admin</MenuItem>
                        <MenuItem value="SuperAdmin">Super Admin</MenuItem>
                      </Select>
                    </FormControl>
                  )}
                />
              </Grid>
            </Grid>
          </DialogContent>
          <DialogActions sx={{ p: 2, pt: 0 }}>
            <Button onClick={() => setModalOpen(false)}>Cancel</Button>
            <Button type="submit" variant="contained" disabled={saving}>
              {saving ? 'Saving...' : 'Create'}
            </Button>
          </DialogActions>
        </form>
      </Dialog>

      {/* Edit Modal */}
      <Dialog
        open={modalOpen && !!editingUser}
        onClose={() => setModalOpen(false)}
        maxWidth="sm"
        fullWidth
        PaperProps={{
          sx: {
            borderRadius: 3,
            background: 'linear-gradient(145deg, #1e1e2e 0%, #2d2d44 100%)',
          },
        }}
      >
        <DialogTitle>Edit User</DialogTitle>
        <form onSubmit={updateForm.handleSubmit(onUpdateSubmit)}>
          <DialogContent>
            <Grid container spacing={2}>
              <Grid item xs={12} sm={6}>
                <Controller
                  name="firstName"
                  control={updateForm.control}
                  render={({ field, fieldState }) => (
                    <TextField
                      {...field}
                      fullWidth
                      label="First Name"
                      error={!!fieldState.error}
                      helperText={fieldState.error?.message}
                    />
                  )}
                />
              </Grid>
              <Grid item xs={12} sm={6}>
                <Controller
                  name="lastName"
                  control={updateForm.control}
                  render={({ field, fieldState }) => (
                    <TextField
                      {...field}
                      fullWidth
                      label="Last Name"
                      error={!!fieldState.error}
                      helperText={fieldState.error?.message}
                    />
                  )}
                />
              </Grid>
              <Grid item xs={12}>
                <Controller
                  name="email"
                  control={updateForm.control}
                  render={({ field, fieldState }) => (
                    <TextField
                      {...field}
                      fullWidth
                      label="Email"
                      type="email"
                      error={!!fieldState.error}
                      helperText={fieldState.error?.message}
                    />
                  )}
                />
              </Grid>
              <Grid item xs={12} sm={6}>
                <Controller
                  name="role"
                  control={updateForm.control}
                  render={({ field, fieldState }) => (
                    <FormControl fullWidth error={!!fieldState.error}>
                      <InputLabel>Role</InputLabel>
                      <Select {...field} label="Role">
                        <MenuItem value="User">User</MenuItem>
                        <MenuItem value="Admin">Admin</MenuItem>
                        <MenuItem value="SuperAdmin">Super Admin</MenuItem>
                      </Select>
                    </FormControl>
                  )}
                />
              </Grid>
              <Grid item xs={12} sm={6}>
                <Controller
                  name="isActive"
                  control={updateForm.control}
                  render={({ field }) => (
                    <FormControlLabel
                      control={<Switch checked={field.value} onChange={field.onChange} />}
                      label="Active"
                      sx={{ mt: 1 }}
                    />
                  )}
                />
              </Grid>
            </Grid>
          </DialogContent>
          <DialogActions sx={{ p: 2, pt: 0 }}>
            <Button onClick={() => setModalOpen(false)}>Cancel</Button>
            <Button type="submit" variant="contained" disabled={saving}>
              {saving ? 'Saving...' : 'Update'}
            </Button>
          </DialogActions>
        </form>
      </Dialog>

      {/* Delete Confirmation */}
      <ConfirmDialog
        open={deleteDialogOpen}
        title="Delete User"
        message={`Are you sure you want to delete ${userToDelete?.firstName} ${userToDelete?.lastName}? This action cannot be undone.`}
        confirmText="Delete"
        loading={saving}
        onConfirm={handleDelete}
        onCancel={() => setDeleteDialogOpen(false)}
      />
    </Box>
  );
};

export default Users;
