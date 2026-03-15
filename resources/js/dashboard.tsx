import './bootstrap';
import { createRoot } from 'react-dom/client';
import Dashboard from './components/Dashboard';
import AnnouncementTicker from './components/AnnouncementTicker';

function mountDashboard() {
  const dashboardRoot = document.getElementById('dashboard-root');
  if (dashboardRoot) {
    createRoot(dashboardRoot).render(<Dashboard />);
  }

  const announcementRoot = document.getElementById('announcement-root');
  if (announcementRoot) {
    createRoot(announcementRoot).render(<AnnouncementTicker />);
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', mountDashboard);
} else {
  mountDashboard();
}
