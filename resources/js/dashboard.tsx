import './bootstrap';
import { createRoot } from 'react-dom/client';
import Calendar from './components/Calendar';
import AnnouncementTicker from './components/AnnouncementTicker';

function mountDashboard() {
  const calendarRoot = document.getElementById('calendar-root');
  if (calendarRoot) {
    createRoot(calendarRoot).render(<Calendar />);
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
