import { Navigate, Outlet } from 'react-router-dom';
import Sidebar from '../Components/SideBar';
import Header from '../Components/Header';
import AppLayout from '../Components/AppLayout';
import { useUiStore } from '../Store/uiStore';
import { useAuthStore } from "../Store/authenticationStore.js";
import { useEffect, useState } from 'react';
import { preloadProtectedRoutes } from './routeImports';

// Protected Route Wrapper Component
const ProtectedRoute = ({ children }) => {
  const { isAuthenticated, isLoading, initializeAuth } = useAuthStore();
  const [isInitialized, setIsInitialized] = useState(false);
  
  useEffect(() => {
    // Initialize auth on component mount
    const initialize = async () => {
      await initializeAuth();
      setIsInitialized(true);
    };
    
    if (!isInitialized) {
      initialize();
    }
  }, [initializeAuth, isInitialized]);
  
  // Show loading while initializing
  if (!isInitialized || isLoading) {
    return (
      <div className="flex items-center justify-center min-h-screen">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
      </div>
    );
  }
  
  // Check authentication status using the state
  if (!isAuthenticated) {
    return <Navigate to="/login" replace />;
  }
  
  return children;
};

// Layout component for all protected pages
const ProtectedLayout = () => {
  const isSidebarOpen = useUiStore((state) => state.isSidebarOpen);

  useEffect(() => {
    preloadProtectedRoutes();
  }, []);

  return (
    <ProtectedRoute>
      <div className="flex min-h-screen bg-gray-900">
        {/* Fixed Sidebar */}
        <Sidebar />

        {/* Mobile overlay when sidebar is open */}
        {isSidebarOpen && (
          <div
            onClick={() => useUiStore.getState().toggleSidebar()}
            className="lg:hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-30"
          />
        )}

        {/* Main content area */}
        <div className="flex-1 flex flex-col">
          {/* Header */}
          <Header />
          
          {/* Page content */}
          <main className="flex-1 p-6 overflow-y-auto overflow-x-hidden bg-gray-900">
            <AppLayout>
              <Outlet />
            </AppLayout>
          </main>
        </div>
      </div>
    </ProtectedRoute>
  );
};

export { ProtectedRoute, ProtectedLayout };
