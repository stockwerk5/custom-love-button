<?php
// Spalte hinzufügen
add_filter('manage_posts_columns', 'clb_add_love_count_column');
function clb_add_love_count_column($columns) {
    $columns['clb_love_count'] = 'Love Count';
    return $columns;
}

// Spalteninhalt ausfüllen
add_action('manage_posts_custom_column', 'clb_show_love_count', 10, 2);
function clb_show_love_count($column_name, $post_id) {
    if ($column_name == 'clb_love_count') {
        $love_count = get_post_meta($post_id, 'clb_love_count', true) ?: '0';
        echo $love_count;
    }
}

// Quick Edit-Feld hinzufügen
add_action('quick_edit_custom_box', 'clb_quick_edit_love_count', 10, 2);
function clb_quick_edit_love_count($column_name, $post_type) {
    if ($column_name != 'clb_love_count') return;
    ?>
    <fieldset class="inline-edit-col-right">
      <div class="inline-edit-col">
        <label>
          <span class="title">Love Count</span>
          <span class="input-text-wrap"><input type="number" name="clb_love_count" value=""></span>
        </label>
      </div>
    </fieldset>
    <?php
}

// JavaScript für das Quick Edit-Feld
add_action('admin_footer', 'clb_quick_edit_javascript');
function clb_quick_edit_javascript() {
    $current_screen = get_current_screen();
    if ($current_screen->id != 'edit-post') return;
    ?>
    <script type="text/javascript">
    jQuery(function($){
        $('a.editinline').on('click', function(){
            var post_id = $(this).data('post-id');
            var $love_count = inlineEditPost.revert().find('input[name="clb_love_count"]');
            $love_count.val($('#clb_love_count_' + post_id).text());
        });
    });
    </script>
    <?php
}

// Love-Count-Wert speichern, wenn Quick Edit verwendet wird
add_action('save_post', 'clb_save_love_count_from_quick_edit');
function clb_save_love_count_from_quick_edit($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (isset($_REQUEST['clb_love_count'])) {
        update_post_meta($post_id, 'clb_love_count', $_REQUEST['clb_love_count']);
    }
}
