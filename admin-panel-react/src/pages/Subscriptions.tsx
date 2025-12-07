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
  Dialog,
  DialogTitle,
  DialogContent,
  DialogActions,
  Button,
  TextField,
  Switch,
  FormControlLabel,
  List,
  ListItem,
  ListItemIcon,
  ListItemText,
  Skeleton,
  InputAdornment,
} from '@mui/material';
import {
  Edit as EditIcon,
  Delete as DeleteIcon,
  Add as AddIcon,
  Check as CheckIcon,
  Close as CloseIcon,
  Star as StarIcon,
  AttachMoney as MoneyIcon,
} from '@mui/icons-material';
import { useForm, Controller, useFieldArray } from 'react-hook-form';
import { yupResolver } from '@hookform/resolvers/yup';
import * as yup from 'yup';
import { toast } from 'react-toastify';
import { ConfirmDialog } from '../components';
import adminService from '../services/adminService';
import { PlanDto, CreatePlanRequest, UpdatePlanRequest } from '../types';

const planSchema = yup.object({
  name: yup.string().required('Plan name is required'),
  description: yup.string().required('Description is required'),
  priceMonthly: yup.number().min(0).required('Monthly price is required'),
  priceYearly: yup.number().min(0).required('Yearly price is required'),
  maxDevices: yup.number().min(1).required('Max devices is required'),
  features: yup.array().of(yup.string().required()).min(1, 'At least one feature is required').required(),
  isActive: yup.boolean().required(),
});

type PlanFormData = {
  name: string;
  description: string;
  priceMonthly: number;
  priceYearly: number;
  maxDevices: number;
  features: string[];
  isActive: boolean;
};

const PLAN_COLORS: Record<string, string> = {
  Free: '#64748b',
  Basic: '#06b6d4',
  Pro: '#7c3aed',
  Premium: '#f59e0b',
  Enterprise: '#ef4444',
};

export const Subscriptions: React.FC = () => {
  const [plans, setPlans] = useState<PlanDto[]>([]);
  const [loading, setLoading] = useState(true);
  const [modalOpen, setModalOpen] = useState(false);
  const [editingPlan, setEditingPlan] = useState<PlanDto | null>(null);
  const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
  const [planToDelete, setPlanToDelete] = useState<PlanDto | null>(null);
  const [saving, setSaving] = useState(false);

  const form = useForm<PlanFormData>({
    resolver: yupResolver(planSchema),
    defaultValues: {
      name: '',
      description: '',
      priceMonthly: 0,
      priceYearly: 0,
      maxDevices: 1,
      features: [''],
      isActive: true,
    },
  });

  const { fields, append, remove } = useFieldArray({
    control: form.control,
    name: 'features' as never,
  });

  const fetchPlans = useCallback(async () => {
    setLoading(true);
    try {
      const data = await adminService.getPlans();
      setPlans(data);
    } catch (error: any) {
      toast.error('Failed to load plans');
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchPlans();
  }, [fetchPlans]);

  const handleCreate = () => {
    setEditingPlan(null);
    form.reset({
      name: '',
      description: '',
      priceMonthly: 0,
      priceYearly: 0,
      maxDevices: 1,
      features: [''],
      isActive: true,
    });
    setModalOpen(true);
  };

  const handleEdit = (plan: PlanDto) => {
    setEditingPlan(plan);
    form.reset({
      name: plan.name,
      description: plan.description,
      priceMonthly: plan.priceMonthly,
      priceYearly: plan.priceYearly,
      maxDevices: plan.maxDevices,
      features: plan.features.length > 0 ? plan.features : [''],
      isActive: plan.isActive,
    });
    setModalOpen(true);
  };

  const handleDeleteClick = (plan: PlanDto) => {
    setPlanToDelete(plan);
    setDeleteDialogOpen(true);
  };

  const handleDelete = async () => {
    if (!planToDelete) return;
    setSaving(true);
    try {
      await adminService.deletePlan(planToDelete.id);
      toast.success('Plan deleted successfully');
      setDeleteDialogOpen(false);
      setPlanToDelete(null);
      fetchPlans();
    } catch (error: any) {
      toast.error(error.message || 'Failed to delete plan');
    } finally {
      setSaving(false);
    }
  };

  const onSubmit = async (data: PlanFormData) => {
    setSaving(true);
    try {
      const cleanedFeatures = (data.features as string[]).filter((f) => f.trim() !== '');
      const payload = { ...data, features: cleanedFeatures };

      if (editingPlan) {
        await adminService.updatePlan(editingPlan.id, payload as UpdatePlanRequest);
        toast.success('Plan updated successfully');
      } else {
        await adminService.createPlan(payload as CreatePlanRequest);
        toast.success('Plan created successfully');
      }
      setModalOpen(false);
      fetchPlans();
    } catch (error: any) {
      toast.error(error.message || 'Failed to save plan');
    } finally {
      setSaving(false);
    }
  };

  const handleToggleActive = async (plan: PlanDto) => {
    try {
      await adminService.updatePlan(plan.id, { isActive: !plan.isActive });
      toast.success(`Plan ${plan.isActive ? 'deactivated' : 'activated'}`);
      fetchPlans();
    } catch (error: any) {
      toast.error('Failed to update plan status');
    }
  };

  const formatCurrency = (value: number): string => {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD',
    }).format(value);
  };

  const getPlanColor = (planName: string): string => {
    return PLAN_COLORS[planName] || '#7c3aed';
  };

  const PlanCard: React.FC<{ plan: PlanDto }> = ({ plan }) => (
    <Card
      sx={{
        height: '100%',
        display: 'flex',
        flexDirection: 'column',
        background: 'linear-gradient(145deg, #1e1e2e 0%, #2d2d44 100%)',
        borderRadius: 3,
        border: '1px solid rgba(255, 255, 255, 0.05)',
        position: 'relative',
        overflow: 'visible',
        transition: 'transform 0.2s ease, box-shadow 0.2s ease',
        '&:hover': {
          transform: 'translateY(-4px)',
          boxShadow: `0 8px 25px ${getPlanColor(plan.name)}30`,
        },
      }}
    >
      {plan.name === 'Pro' && (
        <Chip
          icon={<StarIcon sx={{ fontSize: 14 }} />}
          label="Popular"
          size="small"
          sx={{
            position: 'absolute',
            top: -12,
            left: '50%',
            transform: 'translateX(-50%)',
            bgcolor: 'warning.main',
            color: 'black',
            fontWeight: 600,
          }}
        />
      )}
      <CardContent sx={{ flex: 1 }}>
        <Box display="flex" justifyContent="space-between" alignItems="center" mb={2}>
          <Typography
            variant="h5"
            fontWeight="bold"
            sx={{ color: getPlanColor(plan.name) }}
          >
            {plan.name}
          </Typography>
          <Chip
            label={plan.isActive ? 'Active' : 'Inactive'}
            size="small"
            color={plan.isActive ? 'success' : 'default'}
          />
        </Box>

        <Box mb={3}>
          <Box display="flex" alignItems="baseline" gap={1}>
            <Typography variant="h3" fontWeight="bold">
              {formatCurrency(plan.priceMonthly)}
            </Typography>
            <Typography color="text.secondary">/month</Typography>
          </Box>
          <Typography variant="body2" color="text.secondary">
            or {formatCurrency(plan.priceYearly)}/year
          </Typography>
        </Box>

        <Typography variant="body2" color="text.secondary" mb={2}>
          {plan.description}
        </Typography>

        <Typography variant="subtitle2" fontWeight="600" mb={1}>
          Features:
        </Typography>
        <List dense disablePadding>
          {plan.features.map((feature, index) => (
            <ListItem key={index} disableGutters sx={{ py: 0.5 }}>
              <ListItemIcon sx={{ minWidth: 28 }}>
                <CheckIcon sx={{ fontSize: 18, color: 'success.main' }} />
              </ListItemIcon>
              <ListItemText
                primary={feature}
                primaryTypographyProps={{ variant: 'body2' }}
              />
            </ListItem>
          ))}
          <ListItem disableGutters sx={{ py: 0.5 }}>
            <ListItemIcon sx={{ minWidth: 28 }}>
              <CheckIcon sx={{ fontSize: 18, color: 'success.main' }} />
            </ListItemIcon>
            <ListItemText
              primary={`Up to ${plan.maxDevices} device${plan.maxDevices > 1 ? 's' : ''}`}
              primaryTypographyProps={{ variant: 'body2' }}
            />
          </ListItem>
        </List>
      </CardContent>
      <CardActions sx={{ justifyContent: 'space-between', px: 2, pb: 2 }}>
        <FormControlLabel
          control={
            <Switch
              checked={plan.isActive}
              onChange={() => handleToggleActive(plan)}
              size="small"
            />
          }
          label={<Typography variant="caption">Active</Typography>}
        />
        <Box>
          <Tooltip title="Edit">
            <IconButton size="small" onClick={() => handleEdit(plan)}>
              <EditIcon fontSize="small" />
            </IconButton>
          </Tooltip>
          <Tooltip title="Delete">
            <IconButton size="small" color="error" onClick={() => handleDeleteClick(plan)}>
              <DeleteIcon fontSize="small" />
            </IconButton>
          </Tooltip>
        </Box>
      </CardActions>
    </Card>
  );

  return (
    <Box>
      <Box display="flex" justifyContent="space-between" alignItems="center" mb={3}>
        <Typography variant="h4" fontWeight="bold">
          Subscription Plans
        </Typography>
        <Button
          variant="contained"
          startIcon={<AddIcon />}
          onClick={handleCreate}
          sx={{
            background: 'linear-gradient(135deg, #7c3aed 0%, #06b6d4 100%)',
            '&:hover': { background: 'linear-gradient(135deg, #6d28d9 0%, #0891b2 100%)' },
          }}
        >
          Add Plan
        </Button>
      </Box>

      {loading ? (
        <Grid container spacing={3}>
          {[1, 2, 3, 4].map((i) => (
            <Grid item xs={12} sm={6} md={3} key={i}>
              <Skeleton variant="rounded" height={420} sx={{ borderRadius: 3 }} />
            </Grid>
          ))}
        </Grid>
      ) : (
        <Grid container spacing={3}>
          {plans.map((plan) => (
            <Grid item xs={12} sm={6} md={3} key={plan.id}>
              <PlanCard plan={plan} />
            </Grid>
          ))}
        </Grid>
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
        <DialogTitle>{editingPlan ? 'Edit Plan' : 'Create Plan'}</DialogTitle>
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
                      label="Plan Name"
                      error={!!fieldState.error}
                      helperText={fieldState.error?.message}
                    />
                  )}
                />
              </Grid>
              <Grid item xs={12}>
                <Controller
                  name="description"
                  control={form.control}
                  render={({ field, fieldState }) => (
                    <TextField
                      {...field}
                      fullWidth
                      multiline
                      rows={2}
                      label="Description"
                      error={!!fieldState.error}
                      helperText={fieldState.error?.message}
                    />
                  )}
                />
              </Grid>
              <Grid item xs={12} sm={6}>
                <Controller
                  name="priceMonthly"
                  control={form.control}
                  render={({ field, fieldState }) => (
                    <TextField
                      {...field}
                      fullWidth
                      type="number"
                      label="Monthly Price"
                      InputProps={{
                        startAdornment: (
                          <InputAdornment position="start">
                            <MoneyIcon fontSize="small" />
                          </InputAdornment>
                        ),
                      }}
                      error={!!fieldState.error}
                      helperText={fieldState.error?.message}
                    />
                  )}
                />
              </Grid>
              <Grid item xs={12} sm={6}>
                <Controller
                  name="priceYearly"
                  control={form.control}
                  render={({ field, fieldState }) => (
                    <TextField
                      {...field}
                      fullWidth
                      type="number"
                      label="Yearly Price"
                      InputProps={{
                        startAdornment: (
                          <InputAdornment position="start">
                            <MoneyIcon fontSize="small" />
                          </InputAdornment>
                        ),
                      }}
                      error={!!fieldState.error}
                      helperText={fieldState.error?.message}
                    />
                  )}
                />
              </Grid>
              <Grid item xs={12} sm={6}>
                <Controller
                  name="maxDevices"
                  control={form.control}
                  render={({ field, fieldState }) => (
                    <TextField
                      {...field}
                      fullWidth
                      type="number"
                      label="Max Devices"
                      error={!!fieldState.error}
                      helperText={fieldState.error?.message}
                    />
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
              <Grid item xs={12}>
                <Typography variant="subtitle2" mb={1}>
                  Features
                </Typography>
                {fields.map((field, index) => (
                  <Box key={field.id} display="flex" gap={1} mb={1}>
                    <Controller
                      name={`features.${index}` as const}
                      control={form.control}
                      render={({ field, fieldState }) => (
                        <TextField
                          {...field}
                          fullWidth
                          size="small"
                          placeholder={`Feature ${index + 1}`}
                          error={!!fieldState.error}
                        />
                      )}
                    />
                    <IconButton
                      size="small"
                      color="error"
                      onClick={() => fields.length > 1 && remove(index)}
                      disabled={fields.length <= 1}
                    >
                      <CloseIcon fontSize="small" />
                    </IconButton>
                  </Box>
                ))}
                <Button
                  size="small"
                  startIcon={<AddIcon />}
                  onClick={() => append('')}
                >
                  Add Feature
                </Button>
              </Grid>
            </Grid>
          </DialogContent>
          <DialogActions sx={{ p: 2, pt: 0 }}>
            <Button onClick={() => setModalOpen(false)}>Cancel</Button>
            <Button type="submit" variant="contained" disabled={saving}>
              {saving ? 'Saving...' : editingPlan ? 'Update' : 'Create'}
            </Button>
          </DialogActions>
        </form>
      </Dialog>

      {/* Delete Confirmation */}
      <ConfirmDialog
        open={deleteDialogOpen}
        title="Delete Plan"
        message={`Are you sure you want to delete "${planToDelete?.name}" plan? This action cannot be undone.`}
        confirmText="Delete"
        loading={saving}
        onConfirm={handleDelete}
        onCancel={() => setDeleteDialogOpen(false)}
      />
    </Box>
  );
};

export default Subscriptions;
