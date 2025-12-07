import React, { useState } from 'react';
import { useNavigate, useLocation, Link as RouterLink } from 'react-router-dom';
import { useForm } from 'react-hook-form';
import { yupResolver } from '@hookform/resolvers/yup';
import * as yup from 'yup';
import {
  Box,
  Paper,
  Typography,
  TextField,
  Button,
  FormControlLabel,
  Checkbox,
  Link,
  Avatar,
  Alert,
  InputAdornment,
  IconButton,
  CircularProgress,
} from '@mui/material';
import {
  VpnKey as VpnKeyIcon,
  Visibility,
  VisibilityOff,
  Email as EmailIcon,
  Lock as LockIcon,
} from '@mui/icons-material';
import { toast } from 'react-toastify';
import { useAuth } from '../contexts/AuthContext';
import { LoginRequest } from '../types';

const schema = yup.object({
  email: yup.string().email('Invalid email address').required('Email is required'),
  password: yup
    .string()
    .min(6, 'Password must be at least 6 characters')
    .required('Password is required'),
  rememberMe: yup.boolean(),
}).required();

type LoginFormData = yup.InferType<typeof schema>;

export const Login: React.FC = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const { login } = useAuth();
  const [showPassword, setShowPassword] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const from = (location.state as any)?.from?.pathname || '/dashboard';

  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm<LoginFormData>({
    resolver: yupResolver(schema),
    defaultValues: {
      email: '',
      password: '',
      rememberMe: false,
    },
  });

  const onSubmit = async (data: LoginFormData) => {
    setIsLoading(true);
    setError(null);

    try {
      await login(data as LoginRequest);
      toast.success('Login successful! Welcome back.');
      navigate(from, { replace: true });
    } catch (err: any) {
      const errorMessage =
        err.response?.data?.message || err.message || 'Login failed. Please try again.';
      setError(errorMessage);
      toast.error(errorMessage);
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <Box
      sx={{
        minHeight: '100vh',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        background: 'linear-gradient(135deg, #1a1a2e 0%, #16162a 50%, #0f0f1a 100%)',
        p: 2,
      }}
    >
      <Paper
        elevation={0}
        sx={{
          p: { xs: 3, sm: 5 },
          width: '100%',
          maxWidth: 440,
          borderRadius: 4,
          background: 'linear-gradient(145deg, #1e1e2e 0%, #2d2d44 100%)',
          border: '1px solid rgba(255, 255, 255, 0.05)',
          boxShadow: '0 25px 50px -12px rgba(0, 0, 0, 0.5)',
        }}
      >
        {/* Header */}
        <Box textAlign="center" mb={4}>
          <Avatar
            sx={{
              width: 70,
              height: 70,
              mx: 'auto',
              mb: 2,
              background: 'linear-gradient(135deg, #7c3aed 0%, #06b6d4 100%)',
            }}
          >
            <VpnKeyIcon sx={{ fontSize: 35 }} />
          </Avatar>
          <Typography
            variant="h4"
            fontWeight="bold"
            sx={{
              background: 'linear-gradient(135deg, #7c3aed 0%, #06b6d4 100%)',
              WebkitBackgroundClip: 'text',
              WebkitTextFillColor: 'transparent',
            }}
          >
            CasperVPN
          </Typography>
          <Typography variant="body2" color="text.secondary" mt={1}>
            Admin Panel Login
          </Typography>
        </Box>

        {/* Error Alert */}
        {error && (
          <Alert severity="error" sx={{ mb: 3, borderRadius: 2 }}>
            {error}
          </Alert>
        )}

        {/* Login Form */}
        <form onSubmit={handleSubmit(onSubmit)}>
          <TextField
            {...register('email')}
            fullWidth
            label="Email Address"
            placeholder="admin@caspervpn.com"
            error={!!errors.email}
            helperText={errors.email?.message}
            InputProps={{
              startAdornment: (
                <InputAdornment position="start">
                  <EmailIcon sx={{ color: 'text.secondary' }} />
                </InputAdornment>
              ),
            }}
            sx={{ mb: 2.5 }}
          />

          <TextField
            {...register('password')}
            fullWidth
            label="Password"
            type={showPassword ? 'text' : 'password'}
            placeholder="Enter your password"
            error={!!errors.password}
            helperText={errors.password?.message}
            InputProps={{
              startAdornment: (
                <InputAdornment position="start">
                  <LockIcon sx={{ color: 'text.secondary' }} />
                </InputAdornment>
              ),
              endAdornment: (
                <InputAdornment position="end">
                  <IconButton
                    onClick={() => setShowPassword(!showPassword)}
                    edge="end"
                    size="small"
                  >
                    {showPassword ? <VisibilityOff /> : <Visibility />}
                  </IconButton>
                </InputAdornment>
              ),
            }}
            sx={{ mb: 2 }}
          />

          <Box
            display="flex"
            justifyContent="space-between"
            alignItems="center"
            mb={3}
          >
            <FormControlLabel
              control={
                <Checkbox
                  {...register('rememberMe')}
                  size="small"
                  sx={{
                    color: 'text.secondary',
                    '&.Mui-checked': { color: 'primary.main' },
                  }}
                />
              }
              label={
                <Typography variant="body2" color="text.secondary">
                  Remember me
                </Typography>
              }
            />
            <Link
              component={RouterLink}
              to="/forgot-password"
              variant="body2"
              sx={{
                color: 'primary.main',
                textDecoration: 'none',
                '&:hover': { textDecoration: 'underline' },
              }}
            >
              Forgot password?
            </Link>
          </Box>

          <Button
            type="submit"
            fullWidth
            variant="contained"
            size="large"
            disabled={isLoading}
            sx={{
              py: 1.5,
              borderRadius: 2,
              fontWeight: 600,
              background: 'linear-gradient(135deg, #7c3aed 0%, #06b6d4 100%)',
              boxShadow: '0 4px 15px rgba(124, 58, 237, 0.4)',
              '&:hover': {
                background: 'linear-gradient(135deg, #6d28d9 0%, #0891b2 100%)',
                boxShadow: '0 6px 20px rgba(124, 58, 237, 0.5)',
              },
            }}
          >
            {isLoading ? (
              <CircularProgress size={24} color="inherit" />
            ) : (
              'Sign In'
            )}
          </Button>
        </form>

        {/* Footer */}
        <Box mt={4} textAlign="center">
          <Typography variant="caption" color="text.secondary">
            © 2024 CasperVPN. All rights reserved.
          </Typography>
        </Box>
      </Paper>
    </Box>
  );
};

export default Login;
