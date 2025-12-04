import { Suspense } from "react";
import { createBrowserRouter, Navigate } from "react-router-dom";
import { ProtectedLayout } from "./protected";
import { lazyRoutes } from "./routeImports";

const {
  Login,
  Dashboard,
  Server,
  Connection,
  Analytics,
  Security,
  Database,
  Settings,
  Users,
  Notification,
  Logout,
  Packages,
  Promotions,
  Protocols,
  GeoMap,
  Subscriptions,
  Reports,
} = lazyRoutes;

const loadingFallback = (
  <div className="flex items-center justify-center min-h-screen">
    <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
  </div>
);

const wrapWithSuspense = (element) => (
  <Suspense fallback={loadingFallback}>
    {element}
  </Suspense>
);

// Router configuration
export const router = createBrowserRouter([
  {
    // Public routes (no authentication required)
    path: "/login",
  element: wrapWithSuspense(<Login />),
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
  element: wrapWithSuspense(<Dashboard />),
      },
      {
        path: "servers",
  element: wrapWithSuspense(<Server />),
      },
      {
        path: "connections",
  element: wrapWithSuspense(<Connection />),
      },
      {
        path: "analytics",
  element: wrapWithSuspense(<Analytics />),
      },
      {
        path: "security",
  element: wrapWithSuspense(<Security />),
      },
      {
        path: "database",
  element: wrapWithSuspense(<Database />),
      },
      {
        path: "settings",
  element: wrapWithSuspense(<Settings />),
      },
      {
        path: "users",
  element: wrapWithSuspense(<Users />),
      },
      {
        path: "notification",
  element: wrapWithSuspense(<Notification />),
      },
      {
        path: "packages",
  element: wrapWithSuspense(<Packages />),
      },
      {
        path: "promotions",
  element: wrapWithSuspense(<Promotions />),
      },
      {
        path: "protocols",
  element: wrapWithSuspense(<Protocols />),
      },
      {
        path: "geomap",
  element: wrapWithSuspense(<GeoMap />),
      },
      {
        path: "subscriptions",
  element: wrapWithSuspense(<Subscriptions />),
      },
      {
        path: "reports",
  element: wrapWithSuspense(<Reports />),
      },
      {
        path: "logout",
  element: wrapWithSuspense(<Logout />),
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