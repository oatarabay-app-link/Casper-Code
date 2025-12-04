import { lazy } from 'react';

const loadLogin = () => import('../pages/login/Login');
const loadDashboard = () => import('../pages/dashboards/Dashbord');
const loadServer = () => import('../pages/server/Server');
const loadConnection = () => import('../pages/connection/Connection');
const loadAnalytics = () => import('../pages/analytics/Analytics');
const loadSecurity = () => import('../pages/security/Security');
const loadDatabase = () => import('../pages/database/Database');
const loadSettings = () => import('../pages/setting/Setting');
const loadUsers = () => import('../pages/users/Users');
const loadNotification = () => import('../pages/notification/Notification');
const loadLogout = () => import('../pages/logout/logout');
const loadPackages = () => import('../pages/packages/Packages.jsx');
const loadPromotions = () => import('../pages/promotions/Promotions');
const loadProtocols = () => import('../pages/protocols/Protocols');
const loadGeoMap = () => import('../pages/geomap/GeoMap');
const loadSubscriptions = () => import('../pages/subscriptions/Subscriptions');
const loadReports = () => import('../pages/reports/Reports');

export const lazyRoutes = {
  Login: lazy(loadLogin),
  Dashboard: lazy(loadDashboard),
  Server: lazy(loadServer),
  Connection: lazy(loadConnection),
  Analytics: lazy(loadAnalytics),
  Security: lazy(loadSecurity),
  Database: lazy(loadDatabase),
  Settings: lazy(loadSettings),
  Users: lazy(loadUsers),
  Notification: lazy(loadNotification),
  Logout: lazy(loadLogout),
  Packages: lazy(loadPackages),
  Promotions: lazy(loadPromotions),
  Protocols: lazy(loadProtocols),
  GeoMap: lazy(loadGeoMap),
  Subscriptions: lazy(loadSubscriptions),
  Reports: lazy(loadReports),
};

const preloadFactories = [
  loadDashboard,
  loadServer,
  loadConnection,
  loadAnalytics,
  loadSecurity,
  loadDatabase,
  loadSettings,
  loadUsers,
  loadNotification,
  loadPackages,
  loadPromotions,
  loadProtocols,
  loadGeoMap,
  loadSubscriptions,
  loadReports,
  loadLogout,
];

export const preloadProtectedRoutes = () => {
  preloadFactories.forEach((factory) => {
    factory().catch(() => {
      // Swallow preload errors; lazy will handle loading on demand.
    });
  });
};

export const routeLoaders = {
  loadLogin,
  loadDashboard,
  loadServer,
  loadConnection,
  loadAnalytics,
  loadSecurity,
  loadDatabase,
  loadSettings,
  loadUsers,
  loadNotification,
  loadLogout,
  loadPackages,
  loadPromotions,
  loadProtocols,
  loadGeoMap,
  loadSubscriptions,
  loadReports,
};
