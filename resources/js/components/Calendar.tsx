import { useState } from 'react';

const DAYS = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
const MONTHS = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December',
];

export default function Calendar() {
  const [date, setDate] = useState(() => {
    const d = new Date();
    return new Date(d.getFullYear(), d.getMonth(), 1);
  });
  const [selected, setSelected] = useState<Date | null>(() => new Date());

  const year = date.getFullYear();
  const month = date.getMonth();
  const monthName = MONTHS[month];
  const firstDay = new Date(year, month, 1).getDay();
  const daysInMonth = new Date(year, month + 1, 0).getDate();
  const daysInPrevMonth = new Date(year, month, 0).getDate();

  const prevMonth = (e: React.MouseEvent) => {
    e.preventDefault();
    e.stopPropagation();
    setDate(new Date(year, month - 1, 1));
  };
  const nextMonth = (e: React.MouseEvent) => {
    e.preventDefault();
    e.stopPropagation();
    setDate(new Date(year, month + 1, 1));
  };

  const cells: (number | null)[] = [];
  for (let i = 0; i < firstDay; i++) {
    cells.push(daysInPrevMonth - firstDay + i + 1);
  }
  for (let d = 1; d <= daysInMonth; d++) {
    cells.push(d);
  }
  const remaining = 42 - cells.length;
  for (let d = 1; d <= remaining; d++) {
    cells.push(d);
  }

  const isCurrentMonth = (cell: number | null, index: number) => {
    if (cell === null) return false;
    if (index < firstDay) return false;
    if (index >= firstDay + daysInMonth) return false;
    return true;
  };

  const isSelected = (cell: number | null, index: number) => {
    if (!selected || cell === null) return false;
    if (!isCurrentMonth(cell, index)) return false;
    const d = new Date(year, month, cell);
    return d.toDateString() === selected.toDateString();
  };

  const handleCellClick = (cell: number | null, index: number) => {
    if (cell === null) return;
    if (index < firstDay) {
      setDate(new Date(year, month - 1, 1));
      setSelected(new Date(year, month - 1, cell));
    } else if (index >= firstDay + daysInMonth) {
      setDate(new Date(year, month + 1, 1));
      setSelected(new Date(year, month + 1, cell));
    } else {
      setSelected(new Date(year, month, cell));
    }
  };

  return (
    <div className="dashboard-calendar">
      <div className="dashboard-calendar-header">
        <button type="button" onClick={prevMonth} className="dashboard-calendar-nav" aria-label="Previous month">
          ‹
        </button>
        <span className="dashboard-calendar-title">{monthName} {year}</span>
        <button type="button" onClick={nextMonth} className="dashboard-calendar-nav" aria-label="Next month">
          ›
        </button>
      </div>
      <div className="dashboard-calendar-body">
        <div className="dashboard-calendar-weekdays">
          {DAYS.map((day) => (
            <div key={day} className="dashboard-calendar-weekday">{day}</div>
          ))}
        </div>
        <div key={`${year}-${month}`} className="dashboard-calendar-grid dashboard-calendar-grid--animate">
          {cells.map((cell, index) => (
            <button
              key={index}
              type="button"
              className={`dashboard-calendar-cell ${!isCurrentMonth(cell, index) ? 'other-month' : ''} ${isSelected(cell, index) ? 'selected' : ''}`}
              onClick={() => handleCellClick(cell, index)}
            >
              {cell}
            </button>
          ))}
        </div>
      </div>
    </div>
  );
}
