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
$title = 'Migrate Sequence Files';
require_once(HESK_PATH . 'inc/header.inc.php');
require_once(HESK_PATH . 'inc/show_admin_nav.inc.php');

// Check if the next_seq column exists
$res = hesk_dbQuery("SHOW COLUMNS FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."categories` LIKE 'next_seq'");
$column_exists = (hesk_dbNumRows($res) > 0);

if (!$column_exists) {
    $error_message = 'The next_seq column does not exist in the categories table. Please run the add_next_seq_column.php script first.';
}

// Process form submission
if (isset($_POST['action']) && $_POST['action'] == 'migrate' && $column_exists) {
    // Get all categories
    $res = hesk_dbQuery("SELECT `id` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."categories` ORDER BY `id` ASC");
    
    $migrated_count = 0;
    $cache_dir = HESK_PATH . 'cache/';
    
    while ($category = hesk_dbFetchAssoc($res)) {
        $category_id = $category['id'];
        $seq_file = $cache_dir . 'seq_' . $category_id . '.txt';
        
        // Check if sequence file exists
        if (file_exists($seq_file)) {
            // Read the sequence number from the file
            $sequence = intval(file_get_contents($seq_file));
            
            // Update the database with the sequence number
            hesk_dbQuery("UPDATE `".hesk_dbEscape($hesk_settings['db_pfix'])."categories` SET `next_seq` = " . intval($sequence) . " WHERE `id` = " . intval($category_id));
            
            // Rename the file to keep a backup
            rename($seq_file, $seq_file . '.bak');
            
            $migrated_count++;
        }
    }
    
    // Set success message
    $success_message = 'Migration completed. ' . $migrated_count . ' sequence files were migrated to the database.';
}
?>

<div class="main__content categories">
    <div class="table-wrap">
        <h3>Migrate Sequence Files to Database</h3>
        
        <?php if (isset($error_message)): ?>
        <div class="notification red">
            <p><b>Error!</b></p>
            <p><?php echo $error_message; ?></p>
            <p><a href="add_next_seq_column.php" class="btn btn-full">Go to Add Column Script</a></p>
        </div>
        <?php else: ?>
        
        <?php if (isset($success_message)): ?>
        <div class="notification success">
            <p><b>Success!</b></p>
            <p><?php echo $success_message; ?></p>
        </div>
        <?php endif; ?>
        
        <div class="notification blue">
            <p><b>Information</b></p>
            <p>This script will migrate sequence numbers from files in the cache directory to the database.</p>
            <p>The existing sequence files will be renamed with a .bak extension as a backup.</p>
        </div>
        
        <form action="" method="post">
            <input type="hidden" name="action" value="migrate">
            <button type="submit" class="btn btn-full">Migrate Sequence Files to Database</button>
        </form>
        
        <?php endif; ?>
    </div>
</div>

<?php
require_once(HESK_PATH . 'inc/footer.inc.php');
exit();
?> 