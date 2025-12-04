import { createBrowserRouter, Navigate } from "react-router-dom";
import { lazy, Suspense } from "react";
import { ProtectedLayout } from "./protected";

// Lazy load all components for better performance
const Login = lazy(() => import('../pages/login/Login'));
const Dashboard = lazy(() => import('../pages/dashboards/Dashbord'));
const Server = lazy(() => import('../pages/server/Server'));
const Connection = lazy(() => import('../pages/connection/Connection'));
const Analytics = lazy(() => import('../pages/analytics/Analytics'));
const Security = lazy(() => import('../pages/security/Security'));
const Database = lazy(() => import('../pages/database/Database'));
const Settings = lazy(() => import('../pages/setting/Setting'));
const Users = lazy(() => import('../pages/users/Users'));
const Notification = lazy(() => import('../pages/notification/Notification'));
const Logout = lazy(() => import('../pages/logout/logout'));
const Packages = lazy(() => import('../pages/packages/Packages.jsx'));
const Promotions = lazy(() => import('../pages/promotions/Promotions'));
const Protocols = lazy(() => import('../pages/protocols/Protocols'));
const GeoMap = lazy(() => import('../pages/geomap/GeoMap'));
const Subscriptions = lazy(() => import('../pages/subscriptions/Subscriptions'));
const Reports = lazy(() => import('../pages/reports/Reports'));

// Loading component
const LoadingSpinner = () => (
  <div className="flex items-center justify-center min-h-screen">
    <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
  </div>
);

// Wrapper for lazy components with suspense
const LazyWrapper = ({ children }) => (
  <Suspense fallback={<LoadingSpinner />}>
    {children}
  </Suspense>
);

// Router configuration
export const router = createBrowserRouter([
  {
    // Public routes (no authentication required)
    path: "/login",
    element: (
      <LazyWrapper>
        <Login />
      </LazyWrapper>
    ),
  },
  {
    // All protected routes under this layout
    path: "/",
    element: <ProtectedLayout />,
    children: [
      {
        index: true,
        element: <Navigate to="/dashboard" replace />,
      },
      {
        path: "dashboard",
        element: (
          <LazyWrapper>
            <Dashboard />
          </LazyWrapper>
        ),
      },
      {
        path: "servers",
        element: (
          <LazyWrapper>
            <Server />
          </LazyWrapper>
        ),
      },
      {
        path: "connections",
        element: (
          <LazyWrapper>
            <Connection />
          </LazyWrapper>
        ),
      },
      {
        path: "analytics",
        element: (
          <LazyWrapper>
            <Analytics />
          </LazyWrapper>
        ),
      },
      {
        path: "security",
        element: (
          <LazyWrapper>
            <Security />
          </LazyWrapper>
        ),
      },
      {
        path: "database",
        element: (
          <LazyWrapper>
            <Database />
          </LazyWrapper>
        ),
      },
      {
        path: "settings",
        element: (
          <LazyWrapper>
            <Settings />
          </LazyWrapper>
        ),
      },
      {
        path: "users",
        element: (
          <LazyWrapper>
            <Users />
          </LazyWrapper>
        ),
      },
      {
        path: "notification",
        element: (
          <LazyWrapper>
            <Notification />
          </LazyWrapper>
        ),
      },
      {
        path: "packages",
        element: (
          <LazyWrapper>
            <Packages />
          </LazyWrapper>
        ),
      },
      {
        path: "promotions",
        element: (
          <LazyWrapper>
            <Promotions />
          </LazyWrapper>
        ),
      },
      {
        path: "protocols",
        element: (
          <LazyWrapper>
            <Protocols />
          </LazyWrapper>
        ),
      },
      {
        path: "geomap",
        element: (
          <LazyWrapper>
            <GeoMap />
          </LazyWrapper>
        ),
      },
      {
        path: "subscriptions",
        element: (
          <LazyWrapper>
            <Subscriptions />
          </LazyWrapper>
        ),
      },
      {
        path: "reports",
        element: (
          <LazyWrapper>
            <Reports />
          </LazyWrapper>
        ),
      },
      {
        path: "logout",
        element: (
          <LazyWrapper>
            <Logout />
          </LazyWrapper>
        ),
      },
      // Catch-all route for 404s within protected area
      {
        path: "*",
        element: (
          <div className="flex items-center justify-center min-h-screen">
            <div className="text-center">
              <h1 className="text-4xl font-bold text-gray-700">404</h1>
              <p className="text-gray-500">Page not found</p>
            </div>
          </div>
        ),
      },
    ],
  },
]);