import React, { useEffect, useMemo, useState } from 'react';
import { useUiStore } from '../Store/uiStore';
import { useTheme } from '../hooks/useTheme.jsx';

const AppLayout = ({ children }) => {
  const isSidebarOpen = useUiStore((state) => state.isSidebarOpen);
  const sidebarCollapsed = useUiStore((state) => state.sidebarCollapsed);
  const { getThemeClasses } = useTheme();
  const themeClasses = getThemeClasses();

  // Track viewport to apply desktop-specific shrink
  const [vw, setVw] = useState(typeof window !== 'undefined' ? window.innerWidth : 1280);
  useEffect(() => {
    const onR = () => setVw(window.innerWidth);
    window.addEventListener('resize', onR);
    return () => window.removeEventListener('resize', onR);
  }, []);
  const isLgAndUp = vw >= 1024; // Tailwind lg breakpoint

  // Measure actual sidebar width for precise layout
  const [sidebarWidthPx, setSidebarWidthPx] = useState(0);
  useEffect(() => {
    const readSidebarWidth = () => {
      const el = document.getElementById('app-sidebar');
      if (el) setSidebarWidthPx(el.offsetWidth || 0);
      else setSidebarWidthPx(isSidebarOpen ? 256 : (sidebarCollapsed ? 64 : 0));
    };
    readSidebarWidth();
    const ro = new ResizeObserver(() => readSidebarWidth());
    const el = document.getElementById('app-sidebar');
    if (el) ro.observe(el);
    const onW = () => readSidebarWidth();
    window.addEventListener('resize', onW);
    return () => { ro.disconnect(); window.removeEventListener('resize', onW); };
  }, [isSidebarOpen, sidebarCollapsed]);

  // On large screens, actually shrink the content's width so big canvases fit.
  const wrapperStyle = useMemo(() => {
    if (!isLgAndUp) return undefined; // overlay mode handled by pages if needed
    const w = sidebarWidthPx || (isSidebarOpen ? 256 : (sidebarCollapsed ? 64 : 0));
    return {
      // Use 100% instead of 100vw so we don't include the vertical scrollbar
      // width on Windows, which can cause a tiny horizontal overflow.
      width: `calc(100% - ${w}px)`,
      marginLeft: `${w}px`,
      transition: 'width 300ms ease, margin 300ms ease',
    };
  }, [isLgAndUp, sidebarWidthPx, isSidebarOpen, sidebarCollapsed]);

  return (
    <div
      className={`transition-all duration-300 ease-in-out ${themeClasses.pageBackground}`}
      style={wrapperStyle}
    >
      {children}
    </div>
  );
};

export default AppLayout;