import { useUiStore } from '../Store/uiStore';
import { useAuth } from '../hooks/useAuth';
import { useNavigate } from 'react-router-dom';

const Header = () => {
  const navigate = useNavigate();
  const toggleSidebar = useUiStore((state) => state.toggleSidebar);
  const isSidebarOpen = useUiStore((state) => state.isSidebarOpen);
  const darkMode = useUiStore((state) => state.darkMode);
  const { user, logout } = useAuth();

  const handleLogout = async () => {
    const result = await logout();
    if (result.success) {
      navigate('/login', { replace: true });
    }
  };

  return (
    <header className="sticky top-0 z-50 bg-gray-900 border-b border-gray-700 shadow-lg">
      <div className="flex items-center justify-between px-6 py-4">
        {/* Left section: Logo and menu toggle */}
        <div className="flex items-center space-x-4">
          <button
            onClick={toggleSidebar}
            className="p-2 rounded-lg hover:bg-gray-800 transition-colors duration-200"
          >
            <svg
              className="w-6 h-6 text-gray-300"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={2}
                d="M4 6h16M4 12h16M4 18h16"
              />
            </svg>
          </button>
          <h1 className="text-xl font-bold text-white tracking-wide">
            Casper VPN
          </h1>
        </div>

        {/* Middle section */}
        <div className="hidden md:block">
          <h2 className="text-lg font-semibold text-gray-200">
            VPN Admin Panel
          </h2>
        </div>

        {/* Right section: User info and actions */}
        <div className="flex items-center space-x-4">
          <span className="text-gray-300 text-sm">
            Welcome, {user?.userName || 'Admin'}
          </span>
          <button
            onClick={handleLogout}
            className="px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg transition-colors duration-200"
          >
            Logout
          </button>
        </div>
      </div>
    </header>
  );
};

export default Header;
