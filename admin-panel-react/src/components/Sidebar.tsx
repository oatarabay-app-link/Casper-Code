import React from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import {
  Box,
  Drawer,
  List,
  ListItem,
  ListItemButton,
  ListItemIcon,
  ListItemText,
  Typography,
  Divider,
  Avatar,
} from '@mui/material';
import {
  Dashboard as DashboardIcon,
  People as PeopleIcon,
  Dns as DnsIcon,
  CreditCard as CreditCardIcon,
  Payment as PaymentIcon,
  Analytics as AnalyticsIcon,
  Settings as SettingsIcon,
  VpnKey as VpnKeyIcon,
} from '@mui/icons-material';

interface NavItem {
  path: string;
  label: string;
  icon: React.ReactNode;
}

const navItems: NavItem[] = [
  { path: '/dashboard', label: 'Dashboard', icon: <DashboardIcon /> },
  { path: '/users', label: 'Users', icon: <PeopleIcon /> },
  { path: '/servers', label: 'Servers', icon: <DnsIcon /> },
  { path: '/subscriptions', label: 'Subscriptions', icon: <CreditCardIcon /> },
  { path: '/payments', label: 'Payments', icon: <PaymentIcon /> },
  { path: '/analytics', label: 'Analytics', icon: <AnalyticsIcon /> },
  { path: '/settings', label: 'Settings', icon: <SettingsIcon /> },
];

interface SidebarProps {
  width: number;
  mobileOpen: boolean;
  onMobileClose: () => void;
}

export const Sidebar: React.FC<SidebarProps> = ({ width, mobileOpen, onMobileClose }) => {
  const navigate = useNavigate();
  const location = useLocation();

  const handleNavigation = (path: string) => {
    navigate(path);
    onMobileClose();
  };

  const drawerContent = (
    <Box
      sx={{
        height: '100%',
        display: 'flex',
        flexDirection: 'column',
        background: 'linear-gradient(180deg, #1a1a2e 0%, #16162a 100%)',
      }}
    >
      {/* Logo */}
      <Box
        sx={{
          p: 3,
          display: 'flex',
          alignItems: 'center',
          gap: 2,
        }}
      >
        <Avatar
          sx={{
            width: 45,
            height: 45,
            background: 'linear-gradient(135deg, #7c3aed 0%, #06b6d4 100%)',
          }}
        >
          <VpnKeyIcon />
        </Avatar>
        <Box>
          <Typography
            variant="h6"
            fontWeight="bold"
            sx={{
              background: 'linear-gradient(135deg, #7c3aed 0%, #06b6d4 100%)',
              WebkitBackgroundClip: 'text',
              WebkitTextFillColor: 'transparent',
            }}
          >
            CasperVPN
          </Typography>
          <Typography variant="caption" color="text.secondary">
            Admin Panel
          </Typography>
        </Box>
      </Box>

      <Divider sx={{ borderColor: 'rgba(255, 255, 255, 0.05)' }} />

      {/* Navigation */}
      <List sx={{ flex: 1, px: 2, py: 2 }}>
        {navItems.map((item) => {
          const isActive = location.pathname === item.path;
          return (
            <ListItem key={item.path} disablePadding sx={{ mb: 0.5 }}>
              <ListItemButton
                onClick={() => handleNavigation(item.path)}
                sx={{
                  borderRadius: 2,
                  py: 1.5,
                  px: 2,
                  transition: 'all 0.2s ease',
                  backgroundColor: isActive ? 'rgba(124, 58, 237, 0.15)' : 'transparent',
                  borderLeft: isActive ? '3px solid #7c3aed' : '3px solid transparent',
                  '&:hover': {
                    backgroundColor: isActive
                      ? 'rgba(124, 58, 237, 0.2)'
                      : 'rgba(255, 255, 255, 0.05)',
                  },
                }}
              >
                <ListItemIcon
                  sx={{
                    minWidth: 40,
                    color: isActive ? '#7c3aed' : 'text.secondary',
                  }}
                >
                  {item.icon}
                </ListItemIcon>
                <ListItemText
                  primary={item.label}
                  primaryTypographyProps={{
                    fontWeight: isActive ? 600 : 400,
                    color: isActive ? 'white' : 'text.secondary',
                  }}
                />
              </ListItemButton>
            </ListItem>
          );
        })}
      </List>

      {/* Footer */}
      <Box sx={{ p: 2 }}>
        <Divider sx={{ borderColor: 'rgba(255, 255, 255, 0.05)', mb: 2 }} />
        <Typography variant="caption" color="text.secondary" textAlign="center" display="block">
          © 2024 CasperVPN
        </Typography>
        <Typography variant="caption" color="text.secondary" textAlign="center" display="block">
          v1.0.0
        </Typography>
      </Box>
    </Box>
  );

  return (
    <>
      {/* Mobile drawer */}
      <Drawer
        variant="temporary"
        open={mobileOpen}
        onClose={onMobileClose}
        ModalProps={{ keepMounted: true }}
        sx={{
          display: { xs: 'block', lg: 'none' },
          '& .MuiDrawer-paper': {
            width: width,
            boxSizing: 'border-box',
            border: 'none',
          },
        }}
      >
        {drawerContent}
      </Drawer>

      {/* Desktop drawer */}
      <Drawer
        variant="permanent"
        sx={{
          display: { xs: 'none', lg: 'block' },
          '& .MuiDrawer-paper': {
            width: width,
            boxSizing: 'border-box',
            border: 'none',
          },
        }}
        open
      >
        {drawerContent}
      </Drawer>
    </>
  );
};

export default Sidebar;
