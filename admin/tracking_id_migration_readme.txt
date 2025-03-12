TRACKING ID SYSTEM MIGRATION
==========================

This document explains the migration from the old tracking ID system to the new database-based system.

OVERVIEW
--------
The tracking ID system has been updated to use a more structured format (XXX-NNNNNN) where:
- XXX is a 3-letter code derived from the category name
- NNNNNN is a 6-digit sequence number that increments for each new ticket in that category

Previously, sequence numbers were stored in files in the cache directory. The new system stores these
sequence numbers directly in the database, which provides better reliability and performance.

MIGRATION STEPS
--------------
Follow these steps to migrate to the new system:

1. Add Required Database Columns
   - Visit: http://your-hesk-url/admin/add_next_seq_column.php
   - This script will add the 'next_seq' and 'code' columns to the categories table
   - You must have admin privileges to run this script

2. Migrate Existing Sequence Files (if any)
   - Visit: http://your-hesk-url/admin/migrate_sequence_files.php
   - This script will read sequence numbers from existing files and update the database
   - The original files will be renamed with a .bak extension as a backup

3. Test the New System
   - Visit: http://your-hesk-url/admin/test_category_codes.php
   - This page shows how category codes are generated and what the next tracking ID will be for each category

4. Create a Test Ticket
   - Create a new ticket to verify that the new tracking ID format is working correctly

TECHNICAL DETAILS
----------------
The following files have been modified:

1. inc/common.inc.php
   - Updated hesk_createID() to use the database for sequence numbers
   - Updated hesk_createID_fallback() to use the new format
   - Updated hesk_cleanID() to handle the new format

2. admin/test_category_codes.php
   - Updated to show category codes and next sequence numbers from the database

New files added:

1. admin/add_next_seq_column.php
   - Script to add required database columns

2. admin/migrate_sequence_files.php
   - Script to migrate sequence numbers from files to the database

BENEFITS OF THE NEW SYSTEM
-------------------------
1. Improved readability: Tracking IDs now have a consistent format
2. Category identification: The first part of the ID indicates the ticket category
3. Sequential numbering: Makes it easier to track ticket volume by category
4. Database storage: More reliable than file-based storage, especially in clustered environments
5. No file system access required: Better for security and performance

TROUBLESHOOTING
--------------
If you encounter issues:

1. Verify that the database columns were added successfully
2. Check that the migration script completed without errors
3. Ensure the web server has write permissions to the database
4. If all else fails, the system will fall back to using "GEN-NNNNNN" format with random numbers

For further assistance, please contact support. 