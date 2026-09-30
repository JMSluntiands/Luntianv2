-- Quotation box on Generic EA For Quotation jobs (Email Thread and Quote).
-- Safe to run once.

ALTER TABLE job_general_assembly
    ADD COLUMN quotation_files LONGTEXT NULL AFTER upload_project_files;
