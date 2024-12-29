jQuery(document).ready(function ($) {

    $('#dl-prelaunch-reset').on('click', function (e) {
        e.preventDefault();
        const ajaxurl = dl_mass_editor.translations.ajaxurl;

        console.info('Resetting all links');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'dlpl_empty'
            },

            success: function (response) {
                $('.dl-url-list').html(response.data.table)
                $('#dl-prelaunch-execute').click();
            }

        });
    });

    $('#dl-prelaunch-execute').on('click', function (e) {
        e.preventDefault();

        const ajaxurl = dl_mass_editor.translations.ajaxurl;
        let currentIndex = 0;

        const processNextForm = () => {

            let link = $('.dl-url-list .link:not(.read)').first();

            if (link.text() != '') {
                $('.dl-url-list .link').parents('tr').removeClass('active');
                link.parents('tr').addClass('active');
                $('html, body').scrollTop(link.offset().top - 100);
                url = link.find('a').text();

                console.info('Processing: ' + url);

                $('.dl-prelaunch-status').addClass('show')
                $('#dl-prelaunch-execute').prop('disabled', true);

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'dlpl_get_links',
                        url: url
                    },

                    beforeSend: function () {
                        total = $('.dl-url-list .link').parents('tr').length;
                        proccessed = $('.dl-url-list .link.read').parents('tr').length;

                        $('.dl-prelaunch-status').text('Processing: ' + url + ' (' + proccessed + '/' + total + ')');
                    },

                    success: function (response) {
                        if (response.success) {
                            $('.dl-prelaunch-status').text('Processed: ' + url);
                        } else {
                            $('.dl-prelaunch-status').text('Error: ' + url);
                        }

                        $('.dl-url-list').html(response.data.table)

                        currentIndex++;
                        processNextForm();
                    },

                    error: function () {
                        $('.dl-prelaunch-status').text(dl_mass_editor.translations.error);
                        currentIndex++;
                        //processNextForm();
                    }

                });

            } else {

                $('.dl-prelaunch-status').text(dl_mass_editor.translations.allSaved);
                $('.dl-prelaunch-status').removeClass('show')
                $('#dl-prelaunch-execute').prop('disabled', false);

            }
        };

        processNextForm();
    });


});
