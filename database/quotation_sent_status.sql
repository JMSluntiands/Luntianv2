-- Status used when a For Quotation job is marked sent from the Generic EA list.
-- Safe to run once. Skip if the name already exists.

INSERT INTO statuses (name, color, font_color, show_on_form, created_at, updated_at)
SELECT 'Quotation Sent', '#10b981', '#064e3b', 0, NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM statuses WHERE name = 'Quotation Sent');
