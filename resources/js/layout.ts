import './bootstrap';
import { createRoot } from 'react-dom/client';
import AnnouncementTicker from './components/AnnouncementTicker';
import React from 'react';

function mountGlobalAnnouncement() {
  const el = document.getElementById('announcement-root') as HTMLElement | null;
  if (el && !(el as any)._reactRootContainer) {
    const text = el.dataset.announcementText;
    const root = createRoot(el);
    root.render(React.createElement(AnnouncementTicker, { text: text || undefined }));
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', mountGlobalAnnouncement);
} else {
  mountGlobalAnnouncement();
}
