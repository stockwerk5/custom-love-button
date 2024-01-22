jQuery(document).ready(function($) {
    $(document).on('click', '.clb-love-button', function() {
        var post_id = $(this).data('post_id');
        var counter = $(this).next('.clb-love-counter');

        $.ajax({
            url: clb_ajax.ajax_url,
            type: 'post',
            data: {
                action: 'clb_handle_love',
                post_id: post_id,
                nonce: clb_ajax.nonce
            },
            success: function(response) {
                counter.text(response);
            },
            error: function(error) {
                console.log('AJAX error:', error);
            }
        });
    });
});
