/* global jQuery */
/* global wp */
function media_upload(button_class) {
    'use strict';
    jQuery('body').on('click', button_class, function() {
        var button_id = '#' + jQuery(this).attr('id');
        var display_field = jQuery(this).parent().children('input:text');
        var _custom_media = true;

        wp.media.editor.send.attachment = function(props, attachment) {

            if (_custom_media) {
                if (typeof display_field !== 'undefined') {
                    switch (props.size) {
                        case 'full':
                            display_field.val(attachment.sizes.full.url);
                            display_field.trigger('change');
                            break;
                        case 'medium':
                            display_field.val(attachment.sizes.medium.url);
                            display_field.trigger('change');
                            break;
                        case 'thumbnail':
                            display_field.val(attachment.sizes.thumbnail.url);
                            display_field.trigger('change');
                            break;
                        default:
                            display_field.val(attachment.url);
                            display_field.trigger('change');
                    }
                }
                _custom_media = false;
            } else {
                return wp.media.editor.send.attachment(button_id, [props, attachment]);
            }
			customizer_repeater_refresh_general_control_values();
        };
        wp.media.editor.open(button_class);
        window.send_to_editor = function(html) {

        };
        return false;
    });
}

/********************************************
 *** Generate unique id ***
 *********************************************/
function customizer_repeater_uniqid(prefix, more_entropy) {
    'use strict';
    if (typeof prefix === 'undefined') {
        prefix = '';
    }

    var retId;
    var php_js;
    var formatSeed = function(seed, reqWidth) {
        seed = parseInt(seed, 10)
            .toString(16); // to hex str
        if (reqWidth < seed.length) { // so long we split
            return seed.slice(seed.length - reqWidth);
        }
        if (reqWidth > seed.length) { // so short we pad
            return new Array(1 + (reqWidth - seed.length))
                .join('0') + seed;
        }
        return seed;
    };

    // BEGIN REDUNDANT
    if (!php_js) {
        php_js = {};
    }
    // END REDUNDANT
    if (!php_js.uniqidSeed) { // init seed with big random int
        php_js.uniqidSeed = Math.floor(Math.random() * 0x75bcd15);
    }
    php_js.uniqidSeed++;

    retId = prefix; // start with prefix, add current milliseconds hex string
    retId += formatSeed(parseInt(new Date()
        .getTime() / 1000, 10), 8);
    retId += formatSeed(php_js.uniqidSeed, 5); // add seed hex string
    if (more_entropy) {
        // for more entropy we add a float lower to 10
        retId += (Math.random() * 10)
            .toFixed(8)
            .toString();
    }

    return retId;
}


function customizer_repeater_refresh_general_control_values() {
    'use strict';
    jQuery('.ace-repeater-general-control-repeater').each(function() {
        var values = [];
        var th = jQuery(this);
        th.find('.ace-repeater-general-control-repeater-container').each(function() {


            var text = jQuery(this).find('.ace-repeater-text-control').val();
            var link = jQuery(this).find('.ace-repeater-link-control').val();
			var choice = jQuery(this).find('.customizer-repeater-image-choice').val();
            var image_url = jQuery(this).find('.custom-media-url').val();
            var image_box = jQuery(this).find('.custom-media-box');
            var id = jQuery(this).find('.logo-repeater-box-id').val();
            if (!id) {
                id = 'logo-repeater-' + customizer_repeater_uniqid();
                jQuery(this).find('.logo-repeater-box-id').val(id);
            }


            if ( text !== '' || image_url !== '' || link !== '' || choice !== '' ) {
                image_box.attr('src', image_url);
                values.push({
                    'text': (text),
                    'link': link,
                    'image_url': (choice === 'customizer_repeater_none' ? '' : image_url),
                    'id': id,
                });
            }

        });
        th.find('.ace-repeater-collector').val(JSON.stringify(values));
        th.find('.ace-repeater-collector').trigger('change');
    });
}


jQuery(document).ready(function($) {
    'use strict';
    var theme_conrols = jQuery('#customize-theme-controls');

    theme_conrols.on('click', '.customizer-repeater-customize-control-title', function() {
        jQuery(this).next().slideToggle('medium', function() {
            if (jQuery(this).is(':visible')) {
                jQuery(this).css('display', 'block');
            }
        });
    });
    media_upload('.customizer-repeater-custom-media-button');
    jQuery('.custom-media-url').on('change', function() {
        customizer_repeater_refresh_general_control_values();
        return false;
    });

    /**
     * This adds a new box to repeater
     *
     */

    theme_conrols.on('click', '.customizer-repeater-new-field', function() {

        var add_btn = jQuery('.customizer-repeater-new-field');
        var btn_count = jQuery(this).prev(".ace-repeater-general-control-repeater").children(".ace-repeater-general-control-repeater-container").length;


            var count_pls = ++btn_count;
            add_btn.attr('value', count_pls);

        var th = jQuery(this).parent();
        var id = 'customizer-repeater-' + customizer_repeater_uniqid();
        if (typeof th !== 'undefined') {

            /* Clone the first box*/
                var field = th.find('.ace-repeater-general-control-repeater-container:first').clone();

            if (typeof field !== 'undefined') {
                /*Set the default value for choice between image and icon to icon*/
                field.find('.customizer-repeater-image-choice').val('customizer_repeater_icon');

                /*Show delete box button because it's not the first box*/
                field.find('.logo-repeater-general-control-remove-field').show();


                /*Remove all repeater fields except first one*/

                field.find('.ace-logo-repeater-socials-repeater-collector').val('');

                /*Remove value from text field*/
                field.find('.ace-repeater-text-control').val('');

                /*Remove value from link field*/
                field.find('.ace-repeater-link-control').val('');

                /*Set box id*/
                field.find('.logo-repeater-box-id').val(id);

                /*Remove value from media field*/
                field.find('.custom-media-url').val('');

                /*Remove value from title field*/
                field.find('.ace-repeater-title-control').val('');

                /*Append new box*/
                th.find('.ace-repeater-general-control-repeater-container:first').parent().append(field);

                /*Refresh values*/
                customizer_repeater_refresh_general_control_values();
            }

        }
        return false;

    });


    theme_conrols.on('click', '.logo-repeater-general-control-remove-field', function() {

        var add_btn = jQuery('.customizer-repeater-new-field');

        var btn_count = jQuery(this).prev(".ace-repeater-general-control-repeater").children(".ace-repeater-general-control-repeater-container").length;
        // console.log(btn_count);

        var count_mins = --btn_count;
        add_btn.attr('value', count_mins);

        if (typeof jQuery(this).parent() !== 'undefined') {
            jQuery(this).parent().parent().remove();
            customizer_repeater_refresh_general_control_values();
        }
        return false;
    });


    theme_conrols.on('keyup', '.ace-repeater-text-control', function() {
        customizer_repeater_refresh_general_control_values();
    });

    theme_conrols.on('keyup', '.ace-repeater-link-control', function() {
        customizer_repeater_refresh_general_control_values();
    });

    /*Drag and drop to change icons order*/

    jQuery('.customizer-repeater-general-control-droppable').sortable({
        update: function() {
            customizer_repeater_refresh_general_control_values();
        }
    });



});