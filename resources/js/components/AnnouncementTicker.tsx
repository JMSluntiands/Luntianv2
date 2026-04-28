import { useMemo } from 'react';

const DEFAULT_MESSAGE = 'Welcome to Luntian Dashboard. Check your jobs and calendar for updates.';

type Props = {
  text?: string;
  /** Duration in ms for one full loop */
  scrollDuration?: number;
};

export default function AnnouncementTicker({
  text = DEFAULT_MESSAGE,
  scrollDuration = 9000,
}: Props) {
  const announcement = useMemo(() => {
    const clean = text.trim();
    return clean.length ? clean : DEFAULT_MESSAGE;
  }, [text]);

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
