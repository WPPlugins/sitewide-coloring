(function(factory) {
    factory(window.jQuery, window, document);
}(function($, window, document, undefined) {
    var bannersList = $("#box_list");
    var previewWidget = $('#poststuff');
    var lipsum = {
        1: "Lorem ipsum dolor sit amet, vero movet delenit cum id, mei graeci principes at, mea clita epicuri ea. Eu habeo tempor temporibus sit, nec ut ancillae voluptatibus, tamquam dignissim eos cu. Sed idque lorem cu. Virtute minimum vis ei, ut bonorum detracto iracundia eam, vim in iudico soluta deserunt." +
        "Lorem ipsum dolor sit amet, vero movet delenit cum id, mei graeci principes at, mea clita epicuri ea. Eu habeo tempor temporibus sit, nec ut ancillae voluptatibus, tamquam dignissim eos cu. Sed idque lorem cu. Virtute minimum vis ei, ut bonorum detracto iracundia eam, vim in iudico soluta deserunt.",
        2: "Probo reformidans eu sea. Cu delenit vulputate eos. Illud malorum dignissim no has, oblique vivendum mel et. Dicta nominavi usu no, mei te errem noluisse.Probo reformidans eu sea. Cu delenit vulputate eos. Illud malorum dignissim no has, oblique vivendum mel et. Dicta nominavi usu no, mei te errem noluisse.Probo reformidans eu sea. Cu delenit vulputate eos. Illud malorum dignissim no has, oblique vivendum mel et. Dicta nominavi usu no, mei te errem noluisse.Probo reformidans eu sea. Cu delenit vulputate eos. Illud malorum dignissim no has, oblique vivendum mel et. Dicta nominavi usu no, mei te errem noluisse.",
        3: "No mea doctus incorrupte, ea enim sint accommodare usu. Ei etiam cetero voluptatibus mei, admodum expetenda assentior ea eam, latine appetere intellegebat no pro. Quo ad dicant everti commune. Est dicit semper honestatis in. Omnes alterum mei ei, sale quas verear sed ei.No mea doctus incorrupte, ea enim sint accommodare usu. Ei etiam cetero voluptatibus mei, admodum expetenda assentior ea eam, latine appetere intellegebat no pro. Quo ad dicant everti commune. Est dicit semper honestatis in. Omnes alterum mei ei, sale quas verear sed ei.",
        4: "Ea vidisse volutpat sea, prima efficiantur sed cu. Id stet graeci accusamus sit. Has in sapientem adipiscing comprehensam, petentium signiferumque at pro. Ea has assum gloriatur expetendis, duo at dicit solet. Eam mundi nemore no.Ea vidisse volutpat sea, prima efficiantur sed cu. Id stet graeci accusamus sit. Has in sapientem adipiscing comprehensam, petentium signiferumque at pro. Ea has assum gloriatur expetendis, duo at dicit solet. Eam mundi nemore no.",
        5: "Pro persius senserit concludaturque cu, at feugiat ceteros eam. Ei stet prodesset duo, per ex vidit delectus convenire. Eam autem sanctus an, ne nec iudico tibique. Mei te inani fabulas, inciderint theophrastus ne per. Cu rebum putant vim, no regione platonem sea.Pro persius senserit concludaturque cu, at feugiat ceteros eam. Ei stet prodesset duo, per ex vidit delectus convenire. Eam autem sanctus an, ne nec iudico tibique. Mei te inani fabulas, inciderint theophrastus ne per. Cu rebum putant vim, no regione platonem sea.",
        255: "Probo reformidans eu sea. Cu delenit vulputate eos. Illud malorum dignissim no has, oblique vivendum mel et. Dicta nominavi usu no, mei te errem noluisse.Probo reformidans eu sea. Cu delenit vulputate eos. Illud malorum dignissim no has, oblique vivendum mel et. Dicta nominavi usu no, mei te errem noluisse.Probo reformidans eu sea. Cu delenit vulputate eos. Illud malorum dignissim no has, oblique vivendum mel et. Dicta nominavi usu no, mei te errem noluisse."
    };

    var addBanners = function(count) {
        var bannersToAdd = 1;

        if ($.isNumeric(count)) {
            bannersToAdd = count;
        }

        for (var i = 0; i < bannersToAdd; i++) {
            bannersList
                .append($('#settings-banner-form')
                .microTpl({
                    index: bannersList
                        .find('> tr')
                        .length + 1
                }));
        }

        previewBanners();
    };

    var previewBanners = function() {
        var banners = {};

        bannersList
            .find("> tr")
            .each(function(index) {
                var row = $(this);
                var paragraph = parseInt(row.find('.paragraph').val());
                var banner = {
                    index: index,
                    position: row.find('.position').val()
                };

                (paragraph in banners)
                    ? banners[paragraph].push(banner)
                    : banners[paragraph] = [banner];
            });

        previewWidget.html(
            $('#settings-banners-preview')
             .microTpl({
                 "$": $,
                 banners: banners,
                 paragraphsTexts: lipsum
             }));
    };

    $(function() {
        'use strict';

        var body = $('body');
        var deviceToggler = $('input[type=radio][name=banner_device_toggle]');
        var displayRule = $('#display_rules');
        var addButton = $('#add_block');

        deviceToggler.on('change', function () {
            $('.desktop_content_block, .mobile_content_block')
               .toggleClass('hiddenBox');
        });

        displayRule.on('change', function () {
            var val = $(this).val();

            $('#sitewide_page_ids').prop(
                "disabled", ~[
                    'everywhere',
                    'all_pages',
                    'all_posts',
                    'exclude_home'
                ].indexOf(val)
            );
        });

        addButton.on("click", addBanners);

        body.on("change", ".position, .paragraph", previewBanners);

        body.on("click", ".remove_box", function () {
            var $but = $(this);
            var line = $but.closest('tr').index() + 1;
            
            $('#overlay').fadeIn(400,
                    function(){ 
                        $('#modal_form') 
                            .css('display', 'block')
                            .animate({opacity: 1, top: '50%'}, 200);
                    
                            
                        $('#modal_form .popup_title .number').html( line );
            });
        });
        
        body.on("click", ".remove_box_yes", function () {
            var $modal = $(this).closest('.modal')
            var line = $modal.find('.popup_title .number').text() - 1;
            
            $('#box_list').children('tr').eq( line ).remove();
            previewBanners();
        });

        displayRule.trigger('change');
        bannersList.find('> tr').length
            ? previewBanners()
            : addBanners();
    });
    $('#modal_close, #overlay, .remove_box_yes, .remove_box_no').click( function(){
        $('#modal_form')
            .animate({opacity: 0, top: '45%'}, 200,
                function(){
                        $(this).css('display', 'none');
                        $('#overlay').fadeOut(400);
                }
        );
    });
}));
