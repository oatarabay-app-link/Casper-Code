import { useUiStore } from '../Store/uiStore';
import { useNavigate, useLocation } from 'react-router-dom';
import {
  Dashboard, Storage, Wifi, BarChart, Security, Dns, Settings,
  Logout, PeopleAlt, NotificationsActive, ShoppingCart, LocalOffer,
  VpnLock, Public, Subscriptions, Assessment
} from '@mui/icons-material';

const Sidebar = () => {
  const { isSidebarOpen, sidebarCollapsed, setSidebarCollapsed } = useUiStore();
  const navigate = useNavigate();
  const location = useLocation();

  // Sidebar width states
  const sidebarWidth =
    isSidebarOpen ? "w-64 max-w-[80vw]" :
    sidebarCollapsed ? "w-16" :
    "w-0";

  // Smooth animation classes
  const slideClass =
    isSidebarOpen || sidebarCollapsed
      ? "translate-x-0 opacity-100"
      : "-translate-x-full opacity-0";

  // Detect icon-only collapsed mode
  const collapsedMode = sidebarCollapsed && !isSidebarOpen;

  return (
    <div
      id="app-sidebar"
      className={`
        absolute top-[64px] left-0 h-[calc(100%-64px)]
        bg-gray-900 text-white border-r border-gray-700 z-40 pr-1

        /* Smooth transitions */
        transition-[width,transform,opacity]
        duration-300 ease-in-out

        /* Dynamic values */
        ${sidebarWidth} 
        ${slideClass}

        overflow-y-auto overflow-x-hidden overscroll-contain sidebar-scroll
      `}
    >
      <nav className="flex-1 py-4">
        <div className="space-y-2">
          {navItems.map((item) => (
            <NavItem
              key={item.path}
              icon={item.icon}
              label={item.label}
              path={item.path}
              isCollapsed={collapsedMode}
              isActive={location.pathname === item.path}
            />
          ))}
        </div>
      </nav>

      <div className="border-t border-gray-700 p-4 space-y-2">
        <NavItem
          icon={<Settings />} label="Settings" path="/settings"
          isCollapsed={collapsedMode} isActive={location.pathname === "/settings"}
        />
        <NavItem icon={<Logout />} label="Logout" path="/logout" isCollapsed={collapsedMode} />
      </div>
    </div>
  );
};

// NAVIGATION ITEMS CONFIG
const navItems = [
  { icon: <Dashboard />, label: "Dashboard", path: "/dashboard" },
  { icon: <Storage />, label: "Servers", path: "/servers" },
  { icon: <Wifi />, label: "Connections", path: "/connections" },
  { icon: <BarChart />, label: "Analytics", path: "/analytics" },
  { icon: <Security />, label: "Security", path: "/security" },
  { icon: <Dns />, label: "Database", path: "/database" },
  { icon: <PeopleAlt />, label: "Users", path: "/users" },
  { icon: <NotificationsActive />, label: "Notification", path: "/notification" },
  { icon: <ShoppingCart />, label: "Packages", path: "/packages" },
  { icon: <LocalOffer />, label: "Promotions", path: "/promotions" },
  { icon: <VpnLock />, label: "Protocols", path: "/protocols" },
  { icon: <Public />, label: "Geo Map", path: "/geomap" },
  { icon: <Subscriptions />, label: "Subscriptions", path: "/subscriptions" },
  { icon: <Assessment />, label: "Reports", path: "/reports" },
];

// NAV ITEM COMPONENT
const NavItem = ({ icon, label, path, isCollapsed, isActive }) => {
  const navigate = useNavigate();

  return (
    <div
      onClick={() => navigate(path)}
      className={`
        flex items-center p-2 rounded cursor-pointer transition-all duration-300
        ${isActive ? "bg-blue-600 text-white" : "text-gray-300 hover:text-white hover:bg-gray-800"}
      `}
      title={isCollapsed ? label : ""}
    >

      {/* FIX: Icon wrapper keeps same width in both states */}
      <div className="w-6 flex justify-center">
        <span className={`${isActive ? "text-white" : "text-blue-400"}`}>
          {icon}
        </span>
      </div>

      {/* Smooth label hide */}
      <span
        className={`
          overflow-hidden whitespace-nowrap transition-all duration-300
          ${isCollapsed ? "opacity-0 w-0" : "opacity-100 w-auto ml-3"}
        `}
      >
        {label}
      </span>
    </div>
  );
};

export default Sidebar;
