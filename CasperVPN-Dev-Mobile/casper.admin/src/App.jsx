
import { useEffect } from 'react';
import { RouterProvider } from 'react-router-dom';
import { router } from './router/router';
import { initializeTheme } from './Store/themestore';

function App() {
  useEffect(() => {
    // Initialize theme on app start
    initializeTheme();
  }, []);

  return <RouterProvider router={router} />;
}

export default App;
