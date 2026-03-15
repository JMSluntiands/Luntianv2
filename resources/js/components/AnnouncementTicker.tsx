import { useState, useCallback, useRef, useEffect } from 'react';

const DEFAULT_MESSAGE = 'Welcome to Luntian Dashboard. Check your jobs and calendar for updates.';

type Props = {
  text?: string;
  /** Duration in ms for one full scroll (right to left) */
  scrollDuration?: number;
  /** Pause in ms after text exits before showing again */
  pauseAfterExit?: number;
};

export default function AnnouncementTicker({
  text = DEFAULT_MESSAGE,
  scrollDuration = 15000,
  pauseAfterExit = 2500,
}: Props) {
  const [runId, setRunId] = useState(0);
  const [isPaused, setIsPaused] = useState(false);
  const timeoutRef = useRef<ReturnType<typeof setTimeout> | null>(null);

  const handleAnimationEnd = useCallback(() => {
    setIsPaused(true);
    if (timeoutRef.current) clearTimeout(timeoutRef.current);
    timeoutRef.current = setTimeout(() => {
      timeoutRef.current = null;
      setIsPaused(false);
      setRunId((id) => id + 1);
    }, pauseAfterExit);
  }, [pauseAfterExit]);

  useEffect(() => {
    return () => {
      if (timeoutRef.current) clearTimeout(timeoutRef.current);
    };
  }, []);

  return (
    <div className="announcement-ticker">
      <div className="announcement-ticker__track">
        <div
          key={runId}
          className="announcement-ticker__text"
          style={{
            animation: isPaused
              ? 'none'
              : `announcement-scroll-kf ${scrollDuration}ms linear forwards`,
          }}
          onAnimationEnd={handleAnimationEnd}
        >
          {text}
        </div>
      </div>
    </div>
  );
}
