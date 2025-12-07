import React from 'react';
import { Box, Paper, Typography, SvgIconProps } from '@mui/material';
import { TrendingUp, TrendingDown } from '@mui/icons-material';

interface StatCardProps {
  title: string;
  value: string | number;
  subtitle?: string;
  icon: React.ReactElement<SvgIconProps>;
  trend?: {
    value: number;
    label: string;
  };
  color?: string;
}

export const StatCard: React.FC<StatCardProps> = ({
  title,
  value,
  subtitle,
  icon,
  trend,
  color = '#7c3aed',
}) => {
  const isPositiveTrend = trend && trend.value >= 0;

  return (
    <Paper
      sx={{
        p: 3,
        height: '100%',
        background: 'linear-gradient(145deg, #1e1e2e 0%, #2d2d44 100%)',
        borderRadius: 3,
        border: '1px solid rgba(255, 255, 255, 0.05)',
        transition: 'transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out',
        '&:hover': {
          transform: 'translateY(-4px)',
          boxShadow: `0 8px 25px rgba(124, 58, 237, 0.15)`,
        },
      }}
    >
      <Box display="flex" justifyContent="space-between" alignItems="flex-start">
        <Box>
          <Typography
            variant="subtitle2"
            color="text.secondary"
            sx={{ mb: 1, textTransform: 'uppercase', letterSpacing: 1 }}
          >
            {title}
          </Typography>
          <Typography
            variant="h4"
            fontWeight="bold"
            sx={{
              background: `linear-gradient(135deg, ${color} 0%, #06b6d4 100%)`,
              WebkitBackgroundClip: 'text',
              WebkitTextFillColor: 'transparent',
            }}
          >
            {value}
          </Typography>
          {subtitle && (
            <Typography variant="body2" color="text.secondary" sx={{ mt: 0.5 }}>
              {subtitle}
            </Typography>
          )}
          {trend && (
            <Box display="flex" alignItems="center" gap={0.5} mt={1}>
              {isPositiveTrend ? (
                <TrendingUp sx={{ fontSize: 16, color: 'success.main' }} />
              ) : (
                <TrendingDown sx={{ fontSize: 16, color: 'error.main' }} />
              )}
              <Typography
                variant="caption"
                sx={{
                  color: isPositiveTrend ? 'success.main' : 'error.main',
                  fontWeight: 600,
                }}
              >
                {Math.abs(trend.value)}%
              </Typography>
              <Typography variant="caption" color="text.secondary">
                {trend.label}
              </Typography>
            </Box>
          )}
        </Box>
        <Box
          sx={{
            p: 1.5,
            borderRadius: 2,
            background: `linear-gradient(135deg, ${color}20 0%, ${color}10 100%)`,
            color: color,
          }}
        >
          {React.cloneElement(icon, { sx: { fontSize: 28 } })}
        </Box>
      </Box>
    </Paper>
  );
};

export default StatCard;
