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
hesk_checkPermission('can_man_cat');

// Get all categories with their codes and next_seq values
$res = hesk_dbQuery("SELECT `id`, `name`, `code`, `next_seq` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."categories` ORDER BY `id` ASC");

// Prepare to display results
$title = 'Test Category Codes';
require_once(HESK_PATH . 'inc/header.inc.php');
require_once(HESK_PATH . 'inc/show_admin_nav.inc.php');
?>

<div class="main__content categories">
    <div class="table-wrap">
        <h3>Category Code Test</h3>
        <p>This page shows the category codes and next sequence numbers for each category.</p>
        
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Category Name</th>
                    <th>Category Code</th>
                    <th>Next Sequence</th>
                    <th>Next Tracking ID</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($category = hesk_dbFetchAssoc($res)) {
                    // Get the stored code or generate a new one
                    $code = $category['code'];
                    
                    // If code is not set, generate it using the same logic as in hesk_createID()
                    if (empty($code)) {
                        $name = trim($category['name']);
                        
                        // Split by spaces and take first letters of first three words
                        $words = preg_split('/\s+/', $name);
                        $code = '';
                        
                        // If we have multiple words, try to use first letter of each word (up to 3)
                        if (count($words) >= 3) {
                            $code = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1) . substr($words[2], 0, 1));
                        }
                        // If we have 2 words, use first letter of first word and first 2 letters of second word
                        elseif (count($words) == 2) {
                            $code = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 2));
                        }
                        // Otherwise just use first 3 letters of the name
                        else {
                            $code = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $name), 0, 3));
                        }
                        
                        // If code is empty or less than 3 chars, pad it or use "GEN"
                        if (empty($code)) {
                            $code = 'GEN';
                        } elseif (strlen($code) == 1) {
                            $code .= 'EN';
                        } elseif (strlen($code) == 2) {
                            $code .= 'N';
                        }
                    }
                    
                    // Get the next sequence number or default to 1
                    $next_seq = isset($category['next_seq']) ? intval($category['next_seq']) : 1;
                    if ($next_seq < 1) {
                        $next_seq = 1;
                    }
                    
                    // Format the sequence number to 6 digits
                    $sequence_str = str_pad($next_seq, 6, '0', STR_PAD_LEFT);
                    
                    // Generate example tracking ID
                    $example = $code . '-' . $sequence_str;
                    
                    echo '<tr>';
                    echo '<td>' . $category['id'] . '</td>';
                    echo '<td>' . $category['name'] . '</td>';
                    echo '<td>' . $code . '</td>';
                    echo '<td>' . $next_seq . '</td>';
                    echo '<td>' . $example . '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once(HESK_PATH . 'inc/footer.inc.php');
exit();
?> 