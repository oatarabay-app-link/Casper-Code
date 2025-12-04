import { useUiStore } from '../Store/uiStore';
import { useNavigate, useLocation } from 'react-router-dom';
import {
  Dashboard,
  Storage,
  Wifi,
  BarChart,
  Security,
  Dns,
  Settings,
  Logout,
  Close,
  PeopleAlt,
  NotificationsActive,
  Menu,
  ChevronLeft,
  ChevronRight,
  ShoppingCart,
  LocalOffer,
  VpnLock,
  Public,
  Subscriptions,
  Assessment,
} from '@mui/icons-material';

const Sidebar = () => {
  const isSidebarOpen = useUiStore((state) => state.isSidebarOpen);
  const sidebarCollapsed = useUiStore((state) => state.sidebarCollapsed);
  const setSidebarCollapsed = useUiStore((state) => state.setSidebarCollapsed);
  const navigate = useNavigate();
  const location = useLocation();

  // Determine sidebar width and visibility
  // Limit width on small screens so it doesn't push layout; overlay mode handles space.
  const sidebarWidth = isSidebarOpen ? 'w-64 max-w-[80vw]' : (sidebarCollapsed ? 'w-16' : 'w-0');
  const slideClass = isSidebarOpen || sidebarCollapsed ? 'translate-x-0 opacity-100' : '-translate-x-full opacity-0';
  const showCollapsed = sidebarCollapsed && !isSidebarOpen;

  return (
    <div
      id="app-sidebar"
      className={`fixed top-[64px] left-0 h-[calc(100%-64px)] ${sidebarWidth} bg-gray-900 text-white border-r border-gray-700 z-40 transition-[width,transform] duration-300 ease-in-out ${slideClass} overflow-y-auto overflow-x-hidden overscroll-contain sidebar-scroll pr-1`}
    >
      <nav className="flex-1 py-4">
        <div className="space-y-2">
          <NavItem icon={<Dashboard />} label="Dashboard" path="/dashboard" isCollapsed={showCollapsed} isActive={location.pathname === '/dashboard'} />
          <NavItem icon={<Storage />} label="Servers" path="/servers" isCollapsed={showCollapsed} isActive={location.pathname === '/servers'} />
          <NavItem icon={<Wifi />} label="Connections" path="/connections" isCollapsed={showCollapsed} isActive={location.pathname === '/connections'} />
          <NavItem icon={<BarChart />} label="Analytics" path="/analytics" isCollapsed={showCollapsed} isActive={location.pathname === '/analytics'} />
          <NavItem icon={<Security />} label="Security" path="/security" isCollapsed={showCollapsed} isActive={location.pathname === '/security'} />
          <NavItem icon={<Dns />} label="Database" path="/database" isCollapsed={showCollapsed} isActive={location.pathname === '/database'} />
         
          <NavItem icon={<PeopleAlt />} label="Users" path="/users" isCollapsed={showCollapsed} isActive={location.pathname === '/users'} />
          <NavItem icon={<NotificationsActive />} label="Notification" path="/notification" isCollapsed={showCollapsed} isActive={location.pathname === '/notification'} />
          <NavItem icon={<ShoppingCart />} label="Packages" path="/packages" isCollapsed={showCollapsed} isActive={location.pathname === '/packages'} />
          <NavItem icon={<LocalOffer />} label="Promotions" path="/promotions" isCollapsed={showCollapsed} isActive={location.pathname === '/promotions'} />
          <NavItem icon={<VpnLock />} label="Protocols" path="/protocols" isCollapsed={showCollapsed} isActive={location.pathname === '/protocols'} />
          <NavItem icon={<Public />} label="Geo Map" path="/geomap" isCollapsed={showCollapsed} isActive={location.pathname === '/geomap'} />
          <NavItem icon={<Subscriptions />} label="Subscriptions" path="/subscriptions" isCollapsed={showCollapsed} isActive={location.pathname === '/subscriptions'} />
          <NavItem icon={<Assessment />} label="Reports" path="/reports" isCollapsed={showCollapsed} isActive={location.pathname === '/reports'} />
        </div>
      </nav>
      <div className="border-t border-gray-700 p-4 space-y-2">
         <NavItem icon={<Settings />} label="Settings" path="/settings" isCollapsed={showCollapsed} isActive={location.pathname === '/settings'} />
        <NavItem icon={<Logout />} label="Logout" path="/logout" isCollapsed={showCollapsed} />
      </div>
    </div>
  );
};

const NavItem = ({ icon, label, path, isCollapsed, isActive }) => {
  const navigate = useNavigate();

  return (
    <div
      onClick={() => navigate(path)}
      className={`
        flex items-center p-2 rounded cursor-pointer transition-all duration-200
        ${isActive ? 'bg-blue-600 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-800'}
        ${isCollapsed ? 'justify-center' : 'space-x-3'}
      `}
      title={isCollapsed ? label : ''}
    >
      <div className={`${isActive ? 'text-white' : 'text-blue-400'}`}>{icon}</div>
      {!isCollapsed && <span>{label}</span>}
    </div>
  );
};

export default Sidebar;