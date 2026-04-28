import { useMemo } from 'react';

type Props = {
  text?: string;
  /** Duration in ms for one full loop */
  scrollDuration?: number;
};

export default function AnnouncementTicker({
  text = '',
  scrollDuration = 9000,
}: Props) {
  const announcement = useMemo(() => {
    return String(text || '').trim();
  }, [text]);

  if (!announcement) {
    return null;
  }

  return (
    <div className="announcement-ticker" role="status" aria-live="polite">
      <div className="announcement-ticker__track">
        <div
          className="announcement-ticker__content"
          style={{ ['--ticker-duration' as string]: `${scrollDuration}ms` }}
        >
          <span className="announcement-ticker__text">{announcement}</span>
        </div>
      </div>
    </div>
  );
}
