import './bootstrap';
import { createRoot } from 'react-dom/client';
import Dashboard from './components/Dashboard';

function mountDashboard() {
  const dashboardRoot = document.getElementById('dashboard-root');
  if (!dashboardRoot) {
    return;
  }

  dashboardRoot.setAttribute('data-dashboard-mounted', '1');

  createRoot(dashboardRoot).render(<Dashboard />);
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', mountDashboard);
} else {
  mountDashboard();
}
