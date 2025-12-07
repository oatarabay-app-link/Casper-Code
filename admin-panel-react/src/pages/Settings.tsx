import React, { useState } from 'react';
import {
  Box,
  Typography,
  Grid,
  Paper,
  TextField,
  Button,
  Divider,
  Avatar,
  Alert,
  List,
  ListItem,
  ListItemIcon,
  ListItemText,
  Switch,
  CircularProgress,
} from '@mui/material';
import {
  Person as PersonIcon,
  Lock as LockIcon,
  Email as EmailIcon,
  Badge as BadgeIcon,
  CalendarToday as CalendarIcon,
  Security as SecurityIcon,
  Notifications as NotificationsIcon,
  DarkMode as DarkModeIcon,
  Language as LanguageIcon,
} from '@mui/icons-material';
import { useForm, Controller } from 'react-hook-form';
import { yupResolver } from '@hookform/resolvers/yup';
import * as yup from 'yup';
import { toast } from 'react-toastify';
import { format } from 'date-fns';
import { useAuth } from '../contexts/AuthContext';
import adminService from '../services/adminService';
import { ChangePasswordRequest, UpdateProfileRequest } from '../types';

const profileSchema = yup.object({
  firstName: yup.string().required('First name is required'),
  lastName: yup.string().required('Last name is required'),
  email: yup.string().email('Invalid email').required('Email is required'),
});

const passwordSchema = yup.object({
  currentPassword: yup.string().required('Current password is required'),
  newPassword: yup
    .string()
    .min(8, 'Password must be at least 8 characters')
    .matches(/[a-z]/, 'Password must contain a lowercase letter')
    .matches(/[A-Z]/, 'Password must contain an uppercase letter')
    .matches(/[0-9]/, 'Password must contain a number')
    .required('New password is required'),
  confirmPassword: yup
    .string()
    .oneOf([yup.ref('newPassword')], 'Passwords must match')
    .required('Please confirm your password'),
});

export const Settings: React.FC = () => {
  const { user, refreshUser } = useAuth();
  const [savingProfile, setSavingProfile] = useState(false);
  const [savingPassword, setSavingPassword] = useState(false);
  const [profileSuccess, setProfileSuccess] = useState(false);
  const [passwordSuccess, setPasswordSuccess] = useState(false);

  const profileForm = useForm<UpdateProfileRequest>({
    resolver: yupResolver(profileSchema),
    defaultValues: {
      firstName: user?.firstName || '',
      lastName: user?.lastName || '',
      email: user?.email || '',
    },
  });

  const passwordForm = useForm<ChangePasswordRequest>({
    resolver: yupResolver(passwordSchema),
    defaultValues: {
      currentPassword: '',
      newPassword: '',
      confirmPassword: '',
    },
  });

  const onProfileSubmit = async (data: UpdateProfileRequest) => {
    setSavingProfile(true);
    setProfileSuccess(false);
    try {
      await adminService.updateProfile(data);
      await refreshUser();
      setProfileSuccess(true);
      toast.success('Profile updated successfully');
    } catch (error: any) {
      toast.error(error.message || 'Failed to update profile');
    } finally {
      setSavingProfile(false);
    }
  };

  const onPasswordSubmit = async (data: ChangePasswordRequest) => {
    setSavingPassword(true);
    setPasswordSuccess(false);
    try {
      await adminService.changePassword(data);
      passwordForm.reset();
      setPasswordSuccess(true);
      toast.success('Password changed successfully');
    } catch (error: any) {
      toast.error(error.message || 'Failed to change password');
    } finally {
      setSavingPassword(false);
    }
  };

  const getInitials = () => {
    if (user?.firstName && user?.lastName) {
      return `${user.firstName[0]}${user.lastName[0]}`.toUpperCase();
    }
    return user?.email?.[0]?.toUpperCase() || 'A';
  };

  const SettingsSection: React.FC<{ title: string; icon: React.ReactNode; children: React.ReactNode }> = ({
    title,
    icon,
    children,
  }) => (
    <Paper
      sx={{
        p: 3,
        background: 'linear-gradient(145deg, #1e1e2e 0%, #2d2d44 100%)',
        borderRadius: 3,
        border: '1px solid rgba(255, 255, 255, 0.05)',
        mb: 3,
      }}
    >
      <Box display="flex" alignItems="center" gap={2} mb={3}>
        <Box
          sx={{
            p: 1,
            borderRadius: 2,
            background: 'linear-gradient(135deg, #7c3aed20 0%, #06b6d420 100%)',
            color: 'primary.main',
          }}
        >
          {icon}
        </Box>
        <Typography variant="h6" fontWeight="600">
          {title}
        </Typography>
      </Box>
      {children}
    </Paper>
  );

  return (
    <Box>
      <Typography variant="h4" fontWeight="bold" mb={3}>
        Settings
      </Typography>

      <Grid container spacing={3}>
        {/* Left Column - Profile & Password */}
        <Grid item xs={12} lg={8}>
          {/* Profile Section */}
          <SettingsSection title="Profile Information" icon={<PersonIcon />}>
            {profileSuccess && (
              <Alert severity="success" sx={{ mb: 3 }}>
                Profile updated successfully!
              </Alert>
            )}
            <form onSubmit={profileForm.handleSubmit(onProfileSubmit)}>
              <Grid container spacing={3}>
                <Grid item xs={12} display="flex" justifyContent="center" mb={2}>
                  <Avatar
                    sx={{
                      width: 100,
                      height: 100,
                      fontSize: '2.5rem',
                      background: 'linear-gradient(135deg, #7c3aed 0%, #06b6d4 100%)',
                    }}
                  >
                    {getInitials()}
                  </Avatar>
                </Grid>
                <Grid item xs={12} sm={6}>
                  <Controller
                    name="firstName"
                    control={profileForm.control}
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
                    control={profileForm.control}
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
                    control={profileForm.control}
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
                  <Button
                    type="submit"
                    variant="contained"
                    disabled={savingProfile}
                    sx={{
                      background: 'linear-gradient(135deg, #7c3aed 0%, #06b6d4 100%)',
                      '&:hover': { background: 'linear-gradient(135deg, #6d28d9 0%, #0891b2 100%)' },
                    }}
                  >
                    {savingProfile ? <CircularProgress size={24} /> : 'Save Changes'}
                  </Button>
                </Grid>
              </Grid>
            </form>
          </SettingsSection>

          {/* Password Section */}
          <SettingsSection title="Change Password" icon={<LockIcon />}>
            {passwordSuccess && (
              <Alert severity="success" sx={{ mb: 3 }}>
                Password changed successfully!
              </Alert>
            )}
            <form onSubmit={passwordForm.handleSubmit(onPasswordSubmit)}>
              <Grid container spacing={3}>
                <Grid item xs={12}>
                  <Controller
                    name="currentPassword"
                    control={passwordForm.control}
                    render={({ field, fieldState }) => (
                      <TextField
                        {...field}
                        fullWidth
                        type="password"
                        label="Current Password"
                        error={!!fieldState.error}
                        helperText={fieldState.error?.message}
                      />
                    )}
                  />
                </Grid>
                <Grid item xs={12} sm={6}>
                  <Controller
                    name="newPassword"
                    control={passwordForm.control}
                    render={({ field, fieldState }) => (
                      <TextField
                        {...field}
                        fullWidth
                        type="password"
                        label="New Password"
                        error={!!fieldState.error}
                        helperText={fieldState.error?.message}
                      />
                    )}
                  />
                </Grid>
                <Grid item xs={12} sm={6}>
                  <Controller
                    name="confirmPassword"
                    control={passwordForm.control}
                    render={({ field, fieldState }) => (
                      <TextField
                        {...field}
                        fullWidth
                        type="password"
                        label="Confirm Password"
                        error={!!fieldState.error}
                        helperText={fieldState.error?.message}
                      />
                    )}
                  />
                </Grid>
                <Grid item xs={12}>
                  <Button
                    type="submit"
                    variant="contained"
                    disabled={savingPassword}
                    sx={{
                      background: 'linear-gradient(135deg, #7c3aed 0%, #06b6d4 100%)',
                      '&:hover': { background: 'linear-gradient(135deg, #6d28d9 0%, #0891b2 100%)' },
                    }}
                  >
                    {savingPassword ? <CircularProgress size={24} /> : 'Update Password'}
                  </Button>
                </Grid>
              </Grid>
            </form>
          </SettingsSection>
        </Grid>

        {/* Right Column - Account Info & Preferences */}
        <Grid item xs={12} lg={4}>
          {/* Account Information */}
          <SettingsSection title="Account Information" icon={<BadgeIcon />}>
            <List disablePadding>
              <ListItem sx={{ px: 0 }}>
                <ListItemIcon>
                  <EmailIcon sx={{ color: 'text.secondary' }} />
                </ListItemIcon>
                <ListItemText
                  primary="Email"
                  secondary={user?.email}
                  primaryTypographyProps={{ variant: 'body2', color: 'text.secondary' }}
                />
              </ListItem>
              <Divider sx={{ my: 1, borderColor: 'rgba(255,255,255,0.05)' }} />
              <ListItem sx={{ px: 0 }}>
                <ListItemIcon>
                  <SecurityIcon sx={{ color: 'text.secondary' }} />
                </ListItemIcon>
                <ListItemText
                  primary="Role"
                  secondary={user?.role}
                  primaryTypographyProps={{ variant: 'body2', color: 'text.secondary' }}
                />
              </ListItem>
              <Divider sx={{ my: 1, borderColor: 'rgba(255,255,255,0.05)' }} />
              <ListItem sx={{ px: 0 }}>
                <ListItemIcon>
                  <CalendarIcon sx={{ color: 'text.secondary' }} />
                </ListItemIcon>
                <ListItemText
                  primary="Member Since"
                  secondary={user?.createdAt ? format(new Date(user.createdAt), 'MMM dd, yyyy') : '-'}
                  primaryTypographyProps={{ variant: 'body2', color: 'text.secondary' }}
                />
              </ListItem>
              <Divider sx={{ my: 1, borderColor: 'rgba(255,255,255,0.05)' }} />
              <ListItem sx={{ px: 0 }}>
                <ListItemIcon>
                  <CalendarIcon sx={{ color: 'text.secondary' }} />
                </ListItemIcon>
                <ListItemText
                  primary="Last Login"
                  secondary={user?.lastLoginAt ? format(new Date(user.lastLoginAt), 'MMM dd, yyyy HH:mm') : '-'}
                  primaryTypographyProps={{ variant: 'body2', color: 'text.secondary' }}
                />
              </ListItem>
            </List>
          </SettingsSection>

          {/* Preferences */}
          <SettingsSection title="Preferences" icon={<NotificationsIcon />}>
            <List disablePadding>
              <ListItem sx={{ px: 0 }}>
                <ListItemIcon>
                  <NotificationsIcon sx={{ color: 'text.secondary' }} />
                </ListItemIcon>
                <ListItemText
                  primary="Email Notifications"
                  primaryTypographyProps={{ variant: 'body2' }}
                />
                <Switch defaultChecked />
              </ListItem>
              <ListItem sx={{ px: 0 }}>
                <ListItemIcon>
                  <DarkModeIcon sx={{ color: 'text.secondary' }} />
                </ListItemIcon>
                <ListItemText
                  primary="Dark Mode"
                  primaryTypographyProps={{ variant: 'body2' }}
                />
                <Switch defaultChecked disabled />
              </ListItem>
              <ListItem sx={{ px: 0 }}>
                <ListItemIcon>
                  <LanguageIcon sx={{ color: 'text.secondary' }} />
                </ListItemIcon>
                <ListItemText
                  primary="Language"
                  secondary="English (US)"
                  primaryTypographyProps={{ variant: 'body2' }}
                />
              </ListItem>
            </List>
          </SettingsSection>

          {/* System Info */}
          <Paper
            sx={{
              p: 3,
              background: 'linear-gradient(145deg, #1e1e2e 0%, #2d2d44 100%)',
              borderRadius: 3,
              border: '1px solid rgba(255, 255, 255, 0.05)',
            }}
          >
            <Typography variant="subtitle2" color="text.secondary" mb={2}>
              System Information
            </Typography>
            <Box display="flex" justifyContent="space-between" mb={1}>
              <Typography variant="caption" color="text.secondary">
                Version
              </Typography>
              <Typography variant="caption">1.0.0</Typography>
            </Box>
            <Box display="flex" justifyContent="space-between" mb={1}>
              <Typography variant="caption" color="text.secondary">
                Environment
              </Typography>
              <Typography variant="caption">Production</Typography>
            </Box>
            <Box display="flex" justifyContent="space-between">
              <Typography variant="caption" color="text.secondary">
                API Status
              </Typography>
              <Typography variant="caption" sx={{ color: 'success.main' }}>
                Connected
              </Typography>
            </Box>
          </Paper>
        </Grid>
      </Grid>
    </Box>
  );
};

export default Settings;
