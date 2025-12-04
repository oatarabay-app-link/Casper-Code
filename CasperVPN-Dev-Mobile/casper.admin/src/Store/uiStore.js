import { create } from 'zustand';

export const useUiStore = create((set, get) => ({
  // sidebar state - default to true (expanded)
  isSidebarOpen: true,
  sidebarCollapsed: false, // new state for collapsed view
  
  toggleSidebar: () => {
    const current = get().isSidebarOpen;
    console.log('[Sidebar Toggle] Before:', current, 'After:', !current);
    set({ 
      isSidebarOpen: !current,
      // when closing, first collapse (icon view) to allow smoother width transition
      sidebarCollapsed: current ? true : get().sidebarCollapsed
    });
  },

  setSidebarCollapsed: (collapsed) => {
    set({ 
      sidebarCollapsed: collapsed,
      isSidebarOpen: !collapsed 
    });
  },

  // 🔥 theme state - keeping for backward compatibility
  darkMode: true,
  toggleDarkMode: () => {
    const newMode = !get().darkMode;
    if (newMode) {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
    set({ darkMode: newMode });
  },
}));
