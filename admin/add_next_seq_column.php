<?php
/**
 *
 * This file is part of HESK - PHP Help Desk Software.
 *
 * (c) Copyright Klemen Stirn. All rights reserved.
 * https://www.hesk.com
 *
 * For the full copyright and license agreement information visit
 * https://www.hesk.com/eula.php
 *
 */

define('IN_SCRIPT',1);
define('HESK_PATH','../');

/* Get all the required files and functions */
require(HESK_PATH . 'hesk_settings.inc.php');
require(HESK_PATH . 'inc/common.inc.php');
require(HESK_PATH . 'inc/admin_functions.inc.php');
hesk_load_database_functions();

hesk_session_start();
hesk_dbConnect();
hesk_isLoggedIn();

/* Check permissions for this feature */
hesk_checkPermission('can_man_settings');

// Define the title and include header
$title = 'Add next_seq Column';
require_once(HESK_PATH . 'inc/header.inc.php');
require_once(HESK_PATH . 'inc/show_admin_nav.inc.php');

// Check if the column already exists
$res = hesk_dbQuery("SHOW COLUMNS FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."categories` LIKE 'next_seq'");
$column_exists = (hesk_dbNumRows($res) > 0);

// Process form submission
if (isset($_POST['action']) && $_POST['action'] == 'add_column' && !$column_exists) {
    // Add the column
    hesk_dbQuery("ALTER TABLE `".hesk_dbEscape($hesk_settings['db_pfix'])."categories` ADD COLUMN `next_seq` INT DEFAULT 1");
    
    // Initialize the column for existing categories
    hesk_dbQuery("UPDATE `".hesk_dbEscape($hesk_settings['db_pfix'])."categories` SET `next_seq` = 1");
    
    // Set success message
    $success_message = 'The next_seq column has been added to the categories table successfully.';
}

// Check if the code column exists
$res = hesk_dbQuery("SHOW COLUMNS FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."categories` LIKE 'code'");
$code_column_exists = (hesk_dbNumRows($res) > 0);

// Process form submission for code column
if (isset($_POST['action']) && $_POST['action'] == 'add_code_column' && !$code_column_exists) {
    // Add the column
    hesk_dbQuery("ALTER TABLE `".hesk_dbEscape($hesk_settings['db_pfix'])."categories` ADD COLUMN `code` VARCHAR(3) DEFAULT NULL");
    
    // Set success message
    $success_message = 'The code column has been added to the categories table successfully.';
}
?>

<div class="main__content categories">
    <div class="table-wrap">
        <h3>Database Update for Tracking ID System</h3>
        
        <?php if (isset($success_message)): ?>
        <div class="notification success">
            <p><b>Success!</b></p>
            <p><?php echo $success_message; ?></p>
        </div>
        <?php endif; ?>
        
        <div class="notification blue">
            <p><b>Information</b></p>
            <p>This script will add the necessary columns to the categories table for the new tracking ID system.</p>
        </div>
        
        <h4>Next Sequence Column</h4>
        <?php if ($column_exists): ?>
        <div class="notification success">
            <p>The <b>next_seq</b> column already exists in the categories table.</p>
        </div>
        <?php else: ?>
        <p>The <b>next_seq</b> column needs to be added to the categories table to store the next sequence number for each category.</p>
        <form action="" method="post">
            <input type="hidden" name="action" value="add_column">
            <button type="submit" class="btn btn-full">Add next_seq Column</button>
        </form>
        <?php endif; ?>
        
        <h4>Category Code Column</h4>
        <?php if ($code_column_exists): ?>
        <div class="notification success">
            <p>The <b>code</b> column already exists in the categories table.</p>
        </div>
        <?php else: ?>
        <p>The <b>code</b> column needs to be added to the categories table to store the 3-letter code for each category.</p>
        <form action="" method="post">
            <input type="hidden" name="action" value="add_code_column">
            <button type="submit" class="btn btn-full">Add code Column</button>
        </form>
        <?php endif; ?>
        
        <?php if ($column_exists && $code_column_exists): ?>
        <div class="notification success">
            <p><b>All required columns exist!</b></p>
            <p>Your database is ready for the new tracking ID system. You can now:</p>
            <ul>
                <li><a href="test_category_codes.php">Test category codes</a></li>
                <li><a href="manage_categories.php">Manage categories</a></li>
            </ul>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
require_once(HESK_PATH . 'inc/footer.inc.php');
exit();
?> 