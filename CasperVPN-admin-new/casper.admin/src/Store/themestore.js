import { create } from 'zustand';
import { persist } from 'zustand/middleware';

export const useThemeStore = create(
  persist(
    (set, get) => ({
      // Theme state
      isDarkMode: true, // Default to dark mode for consistency
      
      // Theme configuration
      themes: {
        dark: {
          background: 'bg-gray-900',
          cardBackground: 'bg-gray-800',
          border: 'border-gray-700',
          text: {
            primary: 'text-white',
            secondary: 'text-gray-300',
            tertiary: 'text-gray-400',
          },
          input: {
            background: 'bg-gray-700',
            border: 'border-gray-600',
            focus: 'focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
          },
          button: {
            primary: 'bg-blue-600 hover:bg-blue-700',
            secondary: 'bg-gray-700 hover:bg-gray-600',
          },
        },
        light: {
          background: 'bg-gray-50',
          cardBackground: 'bg-white',
          border: 'border-gray-200',
          text: {
            primary: 'text-gray-900',
            secondary: 'text-gray-600',
            tertiary: 'text-gray-500',
          },
          input: {
            background: 'bg-white',
            border: 'border-gray-300',
            focus: 'focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
          },
          button: {
            primary: 'bg-blue-600 hover:bg-blue-700',
            secondary: 'bg-gray-200 hover:bg-gray-300',
          },
        },
      },

      // Actions
      // Dark mode is enforced; toggling is disabled
      toggleTheme: () => {
        // no-op to keep UI stable in enforced dark mode
        if (!get().isDarkMode) {
          document.documentElement.classList.add('dark');
          document.documentElement.classList.remove('light');
          set({ isDarkMode: true });
        }
      },

      // setTheme remains but will enforce dark regardless of input
      setTheme: () => {
        document.documentElement.classList.add('dark');
        document.documentElement.classList.remove('light');
        set({ isDarkMode: true });
      },

      // Get current theme configuration
      getCurrentTheme: () => {
        const { isDarkMode, themes } = get();
        return isDarkMode ? themes.dark : themes.light;
      },
    }),
    {
      name: 'casper-theme-storage',
      getStorage: () => localStorage,
    }
  )
);

// Initialize theme on app start
export const initializeTheme = () => {
  // Enforce dark mode
  document.documentElement.classList.add('dark');
  document.documentElement.classList.remove('light');
  useThemeStore.setState({ isDarkMode: true });
};