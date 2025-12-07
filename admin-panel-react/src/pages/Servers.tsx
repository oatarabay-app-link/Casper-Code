import React, { useEffect, useState, useCallback } from 'react';
import {
  Box,
  Typography,
  Grid,
  Card,
  CardContent,
  CardActions,
  IconButton,
  Tooltip,
  Chip,
  LinearProgress,
  Dialog,
  DialogTitle,
  DialogContent,
  DialogActions,
  Button,
  TextField,
  FormControl,
  InputLabel,
  Select,
  MenuItem,
  Switch,
  FormControlLabel,
  ToggleButton,
  ToggleButtonGroup,
  Skeleton,
} from '@mui/material';
import {
  Edit as EditIcon,
  Delete as DeleteIcon,
  Add as AddIcon,
  Public as PublicIcon,
  Dns as DnsIcon,
  ViewList as ViewListIcon,
  ViewModule as ViewModuleIcon,
} from '@mui/icons-material';
import { useForm, Controller } from 'react-hook-form';
import { yupResolver } from '@hookform/resolvers/yup';
import * as yup from 'yup';
import { toast } from 'react-toastify';
import { DataTable, ConfirmDialog } from '../components';
import adminService from '../services/adminService';
import { VpnServerDto, CreateServerRequest, UpdateServerRequest, TableColumn } from '../types';

const serverSchema = yup.object({
  name: yup.string().required('Server name is required'),
  hostname: yup.string().required('Hostname is required'),
  ipAddress: yup.string().required('IP address is required'),
  country: yup.string().required('Country is required'),
  city: yup.string().required('City is required'),
  maxConnections: yup.number().min(1).required('Max connections is required'),
  bandwidthLimit: yup.number().min(0).required('Bandwidth limit is required'),
  protocol: yup.string().required('Protocol is required'),
  isActive: yup.boolean(),
});

type ServerFormData = CreateServerRequest & { isActive?: boolean };

const getLoadColor = (load: number): string => {
  if (load < 50) return '#10b981';
  if (load < 80) return '#f59e0b';
  return '#ef4444';
};

export const Servers: React.FC = () => {
  const [servers, setServers] = useState<VpnServerDto[]>([]);
  const [loading, setLoading] = useState(true);
  const [viewMode, setViewMode] = useState<'cards' | 'table'>('cards');
  const [modalOpen, setModalOpen] = useState(false);
  const [editingServer, setEditingServer] = useState<VpnServerDto | null>(null);
  const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
  const [serverToDelete, setServerToDelete] = useState<VpnServerDto | null>(null);
  const [saving, setSaving] = useState(false);

  const form = useForm<ServerFormData>({
    resolver: yupResolver(serverSchema),
    defaultValues: {
      name: '',
      hostname: '',
      ipAddress: '',
      country: '',
      city: '',
      maxConnections: 1000,
      bandwidthLimit: 1000,
      protocol: 'WireGuard',
      isActive: true,
    },
  });

  const fetchServers = useCallback(async () => {
    setLoading(true);
    try {
      const data = await adminService.getServers();
      setServers(data);
    } catch (error: any) {
      toast.error('Failed to load servers');
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchServers();
  }, [fetchServers]);

  const handleCreate = () => {
    setEditingServer(null);
    form.reset({
      name: '',
      hostname: '',
      ipAddress: '',
      country: '',
      city: '',
      maxConnections: 1000,
      bandwidthLimit: 1000,
      protocol: 'WireGuard',
      isActive: true,
    });
    setModalOpen(true);
  };

  const handleEdit = (server: VpnServerDto) => {
    setEditingServer(server);
    form.reset({
      name: server.name,
      hostname: server.hostname,
      ipAddress: server.ipAddress,
      country: server.country,
      city: server.city,
      maxConnections: server.maxConnections,
      bandwidthLimit: server.bandwidthLimit,
      protocol: server.protocol,
      isActive: server.isActive,
    });
    setModalOpen(true);
  };

  const handleDeleteClick = (server: VpnServerDto) => {
    setServerToDelete(server);
    setDeleteDialogOpen(true);
  };

  const handleDelete = async () => {
    if (!serverToDelete) return;
    setSaving(true);
    try {
      await adminService.deleteServer(serverToDelete.id);
      toast.success('Server deleted successfully');
      setDeleteDialogOpen(false);
      setServerToDelete(null);
      fetchServers();
    } catch (error: any) {
      toast.error(error.message || 'Failed to delete server');
    } finally {
      setSaving(false);
    }
  };

  const onSubmit = async (data: ServerFormData) => {
    setSaving(true);
    try {
      if (editingServer) {
        await adminService.updateServer(editingServer.id, data as UpdateServerRequest);
        toast.success('Server updated successfully');
      } else {
        await adminService.createServer(data as CreateServerRequest);
        toast.success('Server created successfully');
      }
      setModalOpen(false);
      fetchServers();
    } catch (error: any) {
      toast.error(error.message || 'Failed to save server');
    } finally {
      setSaving(false);
    }
  };

  const columns: TableColumn<VpnServerDto>[] = [
    { id: 'name', label: 'Name', minWidth: 150 },
    {
      id: 'location',
      label: 'Location',
      minWidth: 150,
      format: (_, row) => `${row.city}, ${row.country}`,
    },
    { id: 'ipAddress', label: 'IP Address', minWidth: 130 },
    {
      id: 'load',
      label: 'Load',
      minWidth: 120,
      format: (value) => (
        <Box display="flex" alignItems="center" gap={1}>
          <LinearProgress
            variant="determinate"
            value={value}
            sx={{
              width: 60,
              height: 6,
              borderRadius: 3,
              backgroundColor: 'rgba(255,255,255,0.1)',
              '& .MuiLinearProgress-bar': { backgroundColor: getLoadColor(value) },
            }}
          />
          <Typography variant="caption" color={getLoadColor(value)}>
            {value}%
          </Typography>
        </Box>
      ),
    },
    {
      id: 'connections',
      label: 'Connections',
      minWidth: 120,
      format: (_, row) => `${row.currentConnections}/${row.maxConnections}`,
    },
    { id: 'protocol', label: 'Protocol', minWidth: 100 },
    {
      id: 'isActive',
      label: 'Status',
      minWidth: 100,
      format: (value) => (
        <Chip
          label={value ? 'Online' : 'Offline'}
          size="small"
          color={value ? 'success' : 'default'}
        />
      ),
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

  const ServerCard: React.FC<{ server: VpnServerDto }> = ({ server }) => (
    <Card
      sx={{
        height: '100%',
        background: 'linear-gradient(145deg, #1e1e2e 0%, #2d2d44 100%)',
        borderRadius: 3,
        border: '1px solid rgba(255, 255, 255, 0.05)',
        transition: 'transform 0.2s ease, box-shadow 0.2s ease',
        '&:hover': {
          transform: 'translateY(-4px)',
          boxShadow: '0 8px 25px rgba(124, 58, 237, 0.15)',
        },
      }}
    >
      <CardContent>
        <Box display="flex" justifyContent="space-between" alignItems="flex-start" mb={2}>
          <Box display="flex" alignItems="center" gap={1}>
            <DnsIcon sx={{ color: server.isActive ? 'success.main' : 'text.secondary' }} />
            <Typography variant="h6" fontWeight="600">
              {server.name}
            </Typography>
          </Box>
          <Chip
            label={server.isActive ? 'Online' : 'Offline'}
            size="small"
            color={server.isActive ? 'success' : 'default'}
          />
        </Box>

        <Box display="flex" alignItems="center" gap={1} mb={2}>
          <PublicIcon sx={{ fontSize: 18, color: 'text.secondary' }} />
          <Typography variant="body2" color="text.secondary">
            {server.city}, {server.country}
          </Typography>
        </Box>

        <Box mb={2}>
          <Box display="flex" justifyContent="space-between" mb={0.5}>
            <Typography variant="caption" color="text.secondary">
              Load
            </Typography>
            <Typography variant="caption" sx={{ color: getLoadColor(server.load) }}>
              {server.load}%
            </Typography>
          </Box>
          <LinearProgress
            variant="determinate"
            value={server.load}
            sx={{
              height: 6,
              borderRadius: 3,
              backgroundColor: 'rgba(255,255,255,0.1)',
              '& .MuiLinearProgress-bar': { backgroundColor: getLoadColor(server.load) },
            }}
          />
        </Box>

        <Grid container spacing={1}>
          <Grid item xs={6}>
            <Typography variant="caption" color="text.secondary">
              IP Address
            </Typography>
            <Typography variant="body2">{server.ipAddress}</Typography>
          </Grid>
          <Grid item xs={6}>
            <Typography variant="caption" color="text.secondary">
              Connections
            </Typography>
            <Typography variant="body2">
              {server.currentConnections}/{server.maxConnections}
            </Typography>
          </Grid>
          <Grid item xs={6}>
            <Typography variant="caption" color="text.secondary">
              Protocol
            </Typography>
            <Typography variant="body2">{server.protocol}</Typography>
          </Grid>
          <Grid item xs={6}>
            <Typography variant="caption" color="text.secondary">
              Bandwidth
            </Typography>
            <Typography variant="body2">{server.bandwidthLimit} Mbps</Typography>
          </Grid>
        </Grid>
      </CardContent>
      <CardActions sx={{ justifyContent: 'flex-end', pt: 0 }}>
        <Tooltip title="Edit">
          <IconButton size="small" onClick={() => handleEdit(server)}>
            <EditIcon fontSize="small" />
          </IconButton>
        </Tooltip>
        <Tooltip title="Delete">
          <IconButton size="small" color="error" onClick={() => handleDeleteClick(server)}>
            <DeleteIcon fontSize="small" />
          </IconButton>
        </Tooltip>
      </CardActions>
    </Card>
  );

  return (
    <Box>
      <Box display="flex" justifyContent="space-between" alignItems="center" mb={3}>
        <Typography variant="h4" fontWeight="bold">
          Servers
        </Typography>
        <Box display="flex" gap={2}>
          <ToggleButtonGroup
            value={viewMode}
            exclusive
            onChange={(_, val) => val && setViewMode(val)}
            size="small"
          >
            <ToggleButton value="cards">
              <ViewModuleIcon />
            </ToggleButton>
            <ToggleButton value="table">
              <ViewListIcon />
            </ToggleButton>
          </ToggleButtonGroup>
          <Button
            variant="contained"
            startIcon={<AddIcon />}
            onClick={handleCreate}
            sx={{
              background: 'linear-gradient(135deg, #7c3aed 0%, #06b6d4 100%)',
              '&:hover': { background: 'linear-gradient(135deg, #6d28d9 0%, #0891b2 100%)' },
            }}
          >
            Add Server
          </Button>
        </Box>
      </Box>

      {loading ? (
        viewMode === 'cards' ? (
          <Grid container spacing={3}>
            {[1, 2, 3, 4, 5, 6].map((i) => (
              <Grid item xs={12} sm={6} md={4} key={i}>
                <Skeleton variant="rounded" height={280} sx={{ borderRadius: 3 }} />
              </Grid>
            ))}
          </Grid>
        ) : (
          <Skeleton variant="rounded" height={400} sx={{ borderRadius: 3 }} />
        )
      ) : viewMode === 'cards' ? (
        <Grid container spacing={3}>
          {servers.map((server) => (
            <Grid item xs={12} sm={6} md={4} key={server.id}>
              <ServerCard server={server} />
            </Grid>
          ))}
        </Grid>
      ) : (
        <DataTable
          columns={columns}
          data={servers}
          totalCount={servers.length}
          page={1}
          pageSize={100}
          onPageChange={() => {}}
          onPageSizeChange={() => {}}
          onRefresh={fetchServers}
        />
      )}

      {/* Create/Edit Modal */}
      <Dialog
        open={modalOpen}
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
        <DialogTitle>{editingServer ? 'Edit Server' : 'Add Server'}</DialogTitle>
        <form onSubmit={form.handleSubmit(onSubmit)}>
          <DialogContent>
            <Grid container spacing={2}>
              <Grid item xs={12}>
                <Controller
                  name="name"
                  control={form.control}
                  render={({ field, fieldState }) => (
                    <TextField
                      {...field}
                      fullWidth
                      label="Server Name"
                      error={!!fieldState.error}
                      helperText={fieldState.error?.message}
                    />
                  )}
                />
              </Grid>
              <Grid item xs={12} sm={6}>
                <Controller
                  name="hostname"
                  control={form.control}
                  render={({ field, fieldState }) => (
                    <TextField
                      {...field}
                      fullWidth
                      label="Hostname"
                      error={!!fieldState.error}
                      helperText={fieldState.error?.message}
                    />
                  )}
                />
              </Grid>
              <Grid item xs={12} sm={6}>
                <Controller
                  name="ipAddress"
                  control={form.control}
                  render={({ field, fieldState }) => (
                    <TextField
                      {...field}
                      fullWidth
                      label="IP Address"
                      error={!!fieldState.error}
                      helperText={fieldState.error?.message}
                    />
                  )}
                />
              </Grid>
              <Grid item xs={12} sm={6}>
                <Controller
                  name="country"
                  control={form.control}
                  render={({ field, fieldState }) => (
                    <TextField
                      {...field}
                      fullWidth
                      label="Country"
                      error={!!fieldState.error}
                      helperText={fieldState.error?.message}
                    />
                  )}
                />
              </Grid>
              <Grid item xs={12} sm={6}>
                <Controller
                  name="city"
                  control={form.control}
                  render={({ field, fieldState }) => (
                    <TextField
                      {...field}
                      fullWidth
                      label="City"
                      error={!!fieldState.error}
                      helperText={fieldState.error?.message}
                    />
                  )}
                />
              </Grid>
              <Grid item xs={12} sm={6}>
                <Controller
                  name="maxConnections"
                  control={form.control}
                  render={({ field, fieldState }) => (
                    <TextField
                      {...field}
                      fullWidth
                      type="number"
                      label="Max Connections"
                      error={!!fieldState.error}
                      helperText={fieldState.error?.message}
                    />
                  )}
                />
              </Grid>
              <Grid item xs={12} sm={6}>
                <Controller
                  name="bandwidthLimit"
                  control={form.control}
                  render={({ field, fieldState }) => (
                    <TextField
                      {...field}
                      fullWidth
                      type="number"
                      label="Bandwidth Limit (Mbps)"
                      error={!!fieldState.error}
                      helperText={fieldState.error?.message}
                    />
                  )}
                />
              </Grid>
              <Grid item xs={12} sm={6}>
                <Controller
                  name="protocol"
                  control={form.control}
                  render={({ field, fieldState }) => (
                    <FormControl fullWidth error={!!fieldState.error}>
                      <InputLabel>Protocol</InputLabel>
                      <Select {...field} label="Protocol">
                        <MenuItem value="WireGuard">WireGuard</MenuItem>
                        <MenuItem value="OpenVPN">OpenVPN</MenuItem>
                        <MenuItem value="IKEv2">IKEv2</MenuItem>
                      </Select>
                    </FormControl>
                  )}
                />
              </Grid>
              <Grid item xs={12} sm={6}>
                <Controller
                  name="isActive"
                  control={form.control}
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
              {saving ? 'Saving...' : editingServer ? 'Update' : 'Create'}
            </Button>
          </DialogActions>
        </form>
      </Dialog>

      {/* Delete Confirmation */}
      <ConfirmDialog
        open={deleteDialogOpen}
        title="Delete Server"
        message={`Are you sure you want to delete "${serverToDelete?.name}"? This action cannot be undone.`}
        confirmText="Delete"
        loading={saving}
        onConfirm={handleDelete}
        onCancel={() => setDeleteDialogOpen(false)}
      />
    </Box>
  );
};

export default Servers;
