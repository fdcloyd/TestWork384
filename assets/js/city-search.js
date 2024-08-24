jQuery(document).ready(function($) {
    $('#city-search').on('keyup', function(e) {
        e.preventDefault();
        var search = $(this).val();

        $.ajax({
            url: citySearch.ajax_url,
            type: 'post',
            data: {
                action: 'city_search',
                search: search
            },
            success: function(response) {
                $('#cities-table tbody').html(response);
            }
        });
    });
});
