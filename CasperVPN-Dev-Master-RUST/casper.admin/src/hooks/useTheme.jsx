import { useThemeStore } from '../Store/themestore';

// Custom hook for easy theme access
export const useTheme = () => {
  const { isDarkMode, toggleTheme, getCurrentTheme } = useThemeStore();
  
  const theme = getCurrentTheme();
  
  // Helper function to get theme-aware classes
  const getThemeClasses = () => {
    const baseClasses = {
      // Page backgrounds
      pageBackground: isDarkMode ? 'bg-gray-900 min-h-screen' : 'bg-gray-50 min-h-screen',
      
      // Card backgrounds
      cardBackground: isDarkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200',
      
      // Text colors
      textPrimary: isDarkMode ? 'text-white' : 'text-gray-900',
      textSecondary: isDarkMode ? 'text-gray-300' : 'text-gray-600',
      textTertiary: isDarkMode ? 'text-gray-400' : 'text-gray-500',
      
      // Input fields
      input: isDarkMode 
        ? 'bg-gray-700 border-gray-600 text-white focus:ring-blue-500 focus:border-blue-500' 
        : 'bg-white border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500',
      
      // Buttons
      buttonPrimary: isDarkMode 
        ? 'bg-blue-600 hover:bg-blue-700 text-white' 
        : 'bg-blue-600 hover:bg-blue-700 text-white',
      buttonSecondary: isDarkMode 
        ? 'bg-gray-700 hover:bg-gray-600 text-gray-300' 
        : 'bg-gray-200 hover:bg-gray-300 text-gray-700',
      
      // Table elements
      tableHeader: isDarkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-50 text-gray-500',
      tableRow: isDarkMode ? 'border-gray-700 hover:bg-gray-750' : 'border-gray-200 hover:bg-gray-50',
      
      // Status badges
      statusActive: isDarkMode 
        ? 'bg-green-900 text-green-300 border-green-700' 
        : 'bg-green-100 text-green-700 border-green-200',
      statusInactive: isDarkMode 
        ? 'bg-red-900 text-red-300 border-red-700' 
        : 'bg-red-100 text-red-700 border-red-200',
      
      // Sidebar
      sidebarBackground: isDarkMode ? 'bg-gray-900 border-gray-700' : 'bg-white border-gray-200',
      sidebarText: isDarkMode ? 'text-gray-300 hover:text-white' : 'text-gray-600 hover:text-gray-900',
      sidebarHover: isDarkMode ? 'hover:bg-gray-800' : 'hover:bg-gray-100',
    };
    
    return baseClasses;
  };
  
  return {
    isDarkMode,
    toggleTheme,
    theme,
    getThemeClasses,
  };
};