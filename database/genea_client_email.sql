-- Email collected on the Generic EA standalone form.
-- Safe to run once. Skip if the column already exists.

ALTER TABLE job_general_assembly
    ADD COLUMN client_email VARCHAR(255) NULL AFTER client_reference_no;
