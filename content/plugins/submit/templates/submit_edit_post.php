<?php
/**
 * Template for Submit: Edit Post
 *
 * PHP version 5
 *
 * LICENSE: Hotaru CMS is free software: you can redistribute it and/or 
 * modify it under the terms of the GNU General Public License as 
 * published by the Free Software Foundation, either version 3 of 
 * the License, or (at your option) any later version. 
 *
 * Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT 
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or 
 * FITNESS FOR A PARTICULAR PURPOSE. 
 *
 * You should have received a copy of the GNU General Public License along 
 * with Hotaru CMS. If not, see http://www.gnu.org/licenses/.
 * 
 * @category  Content Management System
 * @package   HotaruCMS
 * @author    Nick Ramsay <admin@hotarucms.org>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
 
global $hotaru, $cage, $lang, $post, $plugin, $post_orig_url, $post_orig_title, $current_user;

if ($cage->post->getAlpha('edit_post') == 'true') {
    // Submitted this form...
    $title_check = $cage->post->noTags('post_title');    
    $content_check = sanitize($cage->post->getPurifiedHTML('post_content'), 2, $post->allowable_tags);
    if ($cage->post->keyExists('post_subscribe')) { $subscribe_check = 'checked'; } else { $subscribe_check = ''; }   
    $status_check = $cage->post->testAlnumLines('post_status');    
    $post_id = $cage->post->getInt('post_id');    
    $post->post_id = $post_id;
    
} elseif ($cage->get->testInt('post_id'))  {
    $post_id = $cage->get->testInt('post_id');
    $post->read_post($post_id);
    $title_check = $post->post_title;
    $content_check = $post->post_content;
    if ($post->post_subscribe == 1) { $subscribe_check = 'checked'; } else { $subscribe_check = ''; }
    $status_check = $post->post_status;
    $post_orig_url = $post->post_orig_url;
    $post_id = $post->post_id;
}

$user = new UserBase();
$user->get_user_basic($post->post_author);
if ($current_user->role != 'admin' && ($current_user->id != $user->id)) { 
    $hotaru->message = "You don't have permission to edit this post.";
    $hotaru->message_type = "red";
    $hotaru->show_message();
    return false;
    die();
}

$plugin->check_actions('submit_form_2_assign');

?>

    <div id="breadcrumbs">
        <a href='<?php echo BASEURL; ?>'><?php echo $lang['submit_form_home']; ?></a> &raquo; 
        <?php echo $lang["submit_edit_post_title"]; ?> &raquo; 
        <?php echo $post_id; ?>
    </div>
        
    <?php echo $hotaru->show_messages(); ?>
            
    
    <?php echo $lang["submit_edit_post_instructions"]; ?>

    <form name='submit_edit_post' action='<?php BASEURL; ?>index.php?page=edit_post&sourceurl=<?php echo $post_orig_url; ?>' method='post'>
    <table>
    <tr>
        <td><?php echo $lang["submit_form_url"]; ?>&nbsp; </td>
        <td><?php echo $post_orig_url; ?></td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td><?php echo $lang["submit_form_title"]; ?>&nbsp; </td>
        <td><input type='text' size=50 id='post_title' name='post_title' value='<?php echo $title_check; ?>'></td>
        <td>&nbsp;</td>
    </tr>
    
    <?php if ($post->use_content) { ?>
    <tr>
        <td style='vertical-align: top;'><?php echo $lang["submit_form_content"]; ?>&nbsp; </td>
        <td colspan=2><textarea id='post_content' name='post_content' rows='6' maxlength='<?php $post->post_content_length; ?>' style='width: 32em;'><?php echo $content_check; ?></textarea></td>
    </tr>
    <?php } ?>
    
    <?php $plugin->check_actions('submit_form_2_fields'); ?>
        
    <?php if ($current_user->role == 'admin') { ?>
    <!-- Admin only options -->
    
    <tr><td colspan=3><u><?php echo $lang["submit_edit_post_admin_only"]; ?></u></td></tr>
    
    <tr>
        <td><?php echo $lang["submit_form_url"]; ?>&nbsp; </td>
        <td><input type='text' size=50 id='post_orig_url' name='post_orig_url' value='<?php echo $post_orig_url; ?>'></td>
        <td>&nbsp;</td>
    </tr>
    
    <tr>
        <td style='vertical-align: top;'><?php echo $lang["submit_edit_post_status"]; ?>&nbsp; </td>
        <td><select name='post_status'>
            <option value="<?php echo $status_check; ?>"><?php echo $status_check; ?></option>
            <?php 
            $statuses = $post->get_unique_statuses(); 
            print_r($statuses);
            if ($statuses) {
                foreach ($statuses as $status) {
                    if ($status->post_status != 'unsaved') { 
                        echo "<option value=" . $status->post_status . ">" . $status->post_status . "</option>\n";
                    }
                }
            }
            ?>
        </td>
    </tr>
    <!-- END Admin only options -->
    <?php } ?>
        
    <?php if ($current_user->role != 'admin') { ?>
        <input type='hidden' name='post_orig_url' value='<?php echo $post_orig_url; ?>' />
    <?php } ?>
    <input type='hidden' name='post_id' value='<?php echo $post_id ?>' />
    <input type='hidden' name='edit_post' value='true' />
    
    <tr><td colspan=3>&nbsp;</td></tr>
    <tr><td>&nbsp; </td><td>&nbsp; </td><td style='text-align:right;'><input type='submit' name='submit_edit_post' value='<?php echo $lang["submit_edit_post_save"]; ?>' /></td></tr>    
    </table>
    </form>