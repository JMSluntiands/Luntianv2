-- Next status after Quotation Sent.
-- Safe to run once. Skip if the name already exists.

INSERT INTO statuses (name, color, font_color, show_on_form, created_at, updated_at)
SELECT 'Quotation Accepted', '#2563eb', '#ffffff', 0, NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM statuses WHERE name = 'Quotation Accepted');
