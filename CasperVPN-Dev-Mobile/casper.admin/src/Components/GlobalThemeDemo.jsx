import React from 'react';
import { useTheme } from '../hooks/useTheme';
import { useThemeStore } from '../Store/themestore';
import { 
  DarkMode, 
  LightMode, 
  Palette, 
  Settings,
  CheckCircle,
  Error
} from '@mui/icons-material';

const GlobalThemeDemo = () => {
  const { isDarkMode, toggleTheme } = useThemeStore();
  const { getThemeClasses } = useTheme();
  const themeClasses = getThemeClasses();

  return (
    <div className={`p-6 ${themeClasses.pageBackground} transition-all duration-300`}>
      {/* Theme Control Header */}
      <div className={`${themeClasses.cardBackground} rounded-lg shadow-lg p-6 mb-6`}>
        <div className="flex items-center justify-between">
          <div>
            <h1 className={`text-2xl font-bold ${themeClasses.textPrimary}`}>
              🎨 Global Theme System
            </h1>
            <p className={`mt-2 ${themeClasses.textSecondary}`}>
              Toggle between light and dark modes. Changes apply instantly across all components.
            </p>
          </div>
          
          {/* Global Theme Toggle Button */}
          <button
            onClick={toggleTheme}
            className={`
              flex items-center gap-3 px-6 py-3 rounded-lg transition-all duration-300
              ${themeClasses.buttonPrimary} shadow-lg hover:shadow-xl
            `}
          >
            {isDarkMode ? <LightMode /> : <DarkMode />}
            <span className="font-medium">
              Switch to {isDarkMode ? 'Light' : 'Dark'} Mode
            </span>
          </button>
        </div>
      </div>

      {/* Theme Features Demo */}
      <div className="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        
        {/* Card Components */}
        <div className={`${themeClasses.cardBackground} rounded-lg shadow-lg p-6`}>
          <h3 className={`text-lg font-semibold ${themeClasses.textPrimary} mb-4`}>
            📦 Card Components
          </h3>
          <p className={`${themeClasses.textSecondary} mb-4`}>
            All cards automatically adapt to the selected theme.
          </p>
          <div className={`p-4 rounded ${isDarkMode ? 'bg-gray-700' : 'bg-gray-100'}`}>
            <p className={`text-sm ${themeClasses.textTertiary}`}>
              Nested content also follows theme
            </p>
          </div>
        </div>

        {/* Input Components */}
        <div className={`${themeClasses.cardBackground} rounded-lg shadow-lg p-6`}>
          <h3 className={`text-lg font-semibold ${themeClasses.textPrimary} mb-4`}>
            📝 Input Components
          </h3>
          <div className="space-y-3">
            <input
              type="text"
              placeholder="Theme-aware input"
              className={`
                w-full px-3 py-2 rounded-lg border transition-colors duration-200
                ${themeClasses.input}
              `}
            />
            <select className={`
              w-full px-3 py-2 rounded-lg border transition-colors duration-200
              ${themeClasses.input}
            `}>
              <option>Theme-aware select</option>
              <option>Option 2</option>
            </select>
          </div>
        </div>

        {/* Button Components */}
        <div className={`${themeClasses.cardBackground} rounded-lg shadow-lg p-6`}>
          <h3 className={`text-lg font-semibold ${themeClasses.textPrimary} mb-4`}>
            🔘 Button Components
          </h3>
          <div className="space-y-3">
            <button className={`
              w-full px-4 py-2 rounded-lg transition-colors duration-200
              ${themeClasses.buttonPrimary}
            `}>
              Primary Button
            </button>
            <button className={`
              w-full px-4 py-2 rounded-lg transition-colors duration-200
              ${themeClasses.buttonSecondary}
            `}>
              Secondary Button
            </button>
          </div>
        </div>

        {/* Status Badges */}
        <div className={`${themeClasses.cardBackground} rounded-lg shadow-lg p-6`}>
          <h3 className={`text-lg font-semibold ${themeClasses.textPrimary} mb-4`}>
            🏷️ Status Badges
          </h3>
          <div className="flex gap-3 flex-wrap">
            <span className={`
              px-3 py-1 rounded-full text-xs font-medium border
              ${themeClasses.statusActive}
            `}>
              <CheckCircle fontSize="small" className="mr-1" />
              Active
            </span>
            <span className={`
              px-3 py-1 rounded-full text-xs font-medium border
              ${themeClasses.statusInactive}
            `}>
              <Error fontSize="small" className="mr-1" />
              Inactive
            </span>
          </div>
        </div>

        {/* Table Demo */}
        <div className={`${themeClasses.cardBackground} rounded-lg shadow-lg p-6 lg:col-span-2`}>
          <h3 className={`text-lg font-semibold ${themeClasses.textPrimary} mb-4`}>
            📊 Table Components
          </h3>
          <div className="overflow-x-auto">
            <table className="w-full text-sm">
              <thead className={`${themeClasses.tableHeader}`}>
                <tr>
                  <th className="px-4 py-3 text-left">Name</th>
                  <th className="px-4 py-3 text-left">Status</th>
                  <th className="px-4 py-3 text-left">Value</th>
                </tr>
              </thead>
              <tbody>
                <tr className={`border-t ${themeClasses.tableRow} transition-colors duration-200`}>
                  <td className={`px-4 py-3 ${themeClasses.textPrimary}`}>Server Alpha</td>
                  <td className="px-4 py-3">
                    <span className={`px-2 py-1 rounded-full text-xs ${themeClasses.statusActive}`}>
                      Online
                    </span>
                  </td>
                  <td className={`px-4 py-3 ${themeClasses.textSecondary}`}>100%</td>
                </tr>
                <tr className={`border-t ${themeClasses.tableRow} transition-colors duration-200`}>
                  <td className={`px-4 py-3 ${themeClasses.textPrimary}`}>Server Beta</td>
                  <td className="px-4 py-3">
                    <span className={`px-2 py-1 rounded-full text-xs ${themeClasses.statusInactive}`}>
                      Offline
                    </span>
                  </td>
                  <td className={`px-4 py-3 ${themeClasses.textSecondary}`}>0%</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {/* Theme Implementation Guide */}
      <div className={`${themeClasses.cardBackground} rounded-lg shadow-lg p-6 mt-6`}>
        <h3 className={`text-lg font-semibold ${themeClasses.textPrimary} mb-4 flex items-center gap-2`}>
          <Settings className="text-blue-500" />
          How to Use Global Theme System
        </h3>
        <div className={`space-y-4 ${themeClasses.textSecondary}`}>
          <div>
            <h4 className={`font-medium ${themeClasses.textPrimary} mb-2`}>1. Import the theme hook:</h4>
            <code className={`block p-3 rounded ${isDarkMode ? 'bg-gray-700' : 'bg-gray-100'} text-sm`}>
              import {`{ useTheme }`} from '../hooks/useTheme';
            </code>
          </div>
          
          <div>
            <h4 className={`font-medium ${themeClasses.textPrimary} mb-2`}>2. Use theme classes in your component:</h4>
            <code className={`block p-3 rounded ${isDarkMode ? 'bg-gray-700' : 'bg-gray-100'} text-sm`}>
              const {`{ getThemeClasses }`} = useTheme();{'\n'}
              const themeClasses = getThemeClasses();
            </code>
          </div>
          
          <div>
            <h4 className={`font-medium ${themeClasses.textPrimary} mb-2`}>3. Apply theme-aware styles:</h4>
            <code className={`block p-3 rounded ${isDarkMode ? 'bg-gray-700' : 'bg-gray-100'} text-sm`}>
              {`<div className={themeClasses.pageBackground}>`}{'\n'}
              {`  <div className={themeClasses.cardBackground}>`}{'\n'}
              {`    <h1 className={themeClasses.textPrimary}>Title</h1>`}{'\n'}
              {`  </div>`}{'\n'}
              {`</div>`}
            </code>
          </div>

          <div>
            <h4 className={`font-medium ${themeClasses.textPrimary} mb-2`}>4. Toggle theme globally:</h4>
            <code className={`block p-3 rounded ${isDarkMode ? 'bg-gray-700' : 'bg-gray-100'} text-sm`}>
              const {`{ toggleTheme }`} = useThemeStore();{'\n'}
              {`<button onClick={toggleTheme}>Toggle Theme</button>`}
            </code>
          </div>
        </div>
      </div>
    </div>
  );
};

export default GlobalThemeDemo;