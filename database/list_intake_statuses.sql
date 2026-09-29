-- Statuses used by the standalone form intake tables.
-- For Inquiries rows stay in the For Inquiries list.
-- For Quotation rows stay in the For Quotation list.
-- Safe to run more than once.

INSERT INTO statuses (name, color, font_color, created_at, updated_at)
SELECT 'For Inquiries', '#06b6d4', '#083344', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM statuses WHERE name = 'For Inquiries');

INSERT INTO statuses (name, color, font_color, created_at, updated_at)
SELECT 'For Quotation', '#f59e0b', '#333333', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM statuses WHERE name = 'For Quotation');
