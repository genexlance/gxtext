/**
 * GX Text Admin JavaScript
 * by Genex Marketing Agency Ltd
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        var mediaFrame = null;

        // Initialize color pickers
        $('.gx-color-picker').wpColorPicker({
            change: function() {
                setTimeout(updatePreview, 50);
            }
        });

        // Range slider value display
        $('input[type="range"]').on('input', function() {
            var unit = $(this).attr('id') === 'button_border_radius' ? '%' : 'px';
            $(this).next('.gx-range-value').text($(this).val() + unit);
            updatePreview();
        });

        // Preview triggers
        $('.gx-preview-trigger').on('change input', function() {
            updatePreview();
        });

        bindGraphicPicker('#gx-text-select-brand-logo', '#brand_logo_url', '#gx-text-brand-logo-preview', gxTextAdmin.strings.chooseBrandLogo, gxTextAdmin.strings.useBrandLogo);
        bindGraphicPicker('#gx-text-select-graphic', '#button_graphic_url', '#gx-text-graphic-preview', gxTextAdmin.strings.chooseGraphic, gxTextAdmin.strings.useGraphic);
        bindGraphicPicker('#gx-text-select-hover-graphic', '#button_hover_graphic_url', '#gx-text-hover-graphic-preview', gxTextAdmin.strings.chooseHoverGraphic, gxTextAdmin.strings.useHoverGraphic);

        bindGraphicRemove('#gx-text-remove-brand-logo', '#brand_logo_url', '#gx-text-brand-logo-preview');
        bindGraphicRemove('#gx-text-remove-graphic', '#button_graphic_url', '#gx-text-graphic-preview');
        bindGraphicRemove('#gx-text-remove-hover-graphic', '#button_hover_graphic_url', '#gx-text-hover-graphic-preview');
        $('#brand_logo_url').on('input change', function() {
            renderGraphicPreview($('#gx-text-brand-logo-preview'), $(this).val() || '');
        });
        $('#button_graphic_url').on('input change', function() {
            renderGraphicPreview($('#gx-text-graphic-preview'), $(this).val() || '');
        });
        $('#button_hover_graphic_url').on('input change', function() {
            renderGraphicPreview($('#gx-text-hover-graphic-preview'), $(this).val() || '');
        });

        // Initial preview
        updatePreview();
        renderGraphicPreview($('#gx-text-brand-logo-preview'), $('#brand_logo_url').val() || '');
        renderGraphicPreview($('#gx-text-graphic-preview'), $('#button_graphic_url').val() || '');
        renderGraphicPreview($('#gx-text-hover-graphic-preview'), $('#button_hover_graphic_url').val() || '');

        // Test Twilio Connection
        $('#gx-text-test-twilio').on('click', function() {
            var $btn = $(this);
            var $result = $('#gx-text-test-result');
            $btn.prop('disabled', true);
            $result.text(gxTextAdmin.strings.testing).removeClass('success error');

            $.post(gxTextAdmin.ajaxUrl, {
                action: 'gx_text_test_twilio',
                nonce: gxTextAdmin.nonce
            }, function(response) {
                $btn.prop('disabled', false);
                if (response.success) {
                    $result.text(gxTextAdmin.strings.success).addClass('success');
                } else {
                    $result.text(gxTextAdmin.strings.failed + response.data.message).addClass('error');
                }
            }).fail(function() {
                $btn.prop('disabled', false);
                $result.text(gxTextAdmin.strings.failed + 'Network error').addClass('error');
            });
        });

        // Broadcast character counter
        $('#broadcast-message').on('input', function() {
            var len = $(this).val().length;
            $('#broadcast-char-count').text(len);
            $('#broadcast-segment-count').text(Math.ceil(len / 160) || 0);
        });

        // Send Broadcast
        $('#gx-text-send-broadcast').on('click', function() {
            var message = $('#broadcast-message').val().trim();
            if (!message) {
                alert('Please enter a message.');
                return;
            }
            if (!confirm(gxTextAdmin.strings.confirmBroadcast)) {
                return;
            }

            var $btn = $(this);
            var $result = $('#gx-text-broadcast-result');
            $btn.prop('disabled', true).text(gxTextAdmin.strings.sending);

            $.post(gxTextAdmin.ajaxUrl, {
                action: 'gx_text_send_broadcast',
                nonce: gxTextAdmin.nonce,
                message: message
            }, function(response) {
                $btn.prop('disabled', false).html('<span class="dashicons dashicons-megaphone"></span> Send Broadcast');
                $result.show();
                if (response.success) {
                    $result.addClass('success').removeClass('error').text(response.data.message);
                } else {
                    $result.addClass('error').removeClass('success').text(response.data.message);
                }
            }).fail(function() {
                $btn.prop('disabled', false).html('<span class="dashicons dashicons-megaphone"></span> Send Broadcast');
                $result.show().addClass('error').removeClass('success').text('Network error occurred.');
            });
        });

        // Delete Subscriber
        $('.gx-text-delete-sub').on('click', function() {
            if (!confirm(gxTextAdmin.strings.confirmDelete)) return;
            var $btn = $(this);
            var id = $btn.data('id');

            $.post(gxTextAdmin.ajaxUrl, {
                action: 'gx_text_delete_subscriber',
                nonce: gxTextAdmin.nonce,
                subscriber_id: id
            }, function(response) {
                if (response.success) {
                    $('#subscriber-row-' + id).fadeOut(300, function() { $(this).remove(); });
                }
            });
        });

        // Export CSV
        $('#gx-text-export-csv').on('click', function() {
            window.location.href = gxTextAdmin.ajaxUrl + '?action=gx_text_export_subscribers&nonce=' + gxTextAdmin.nonce;
        });

        /**
         * Update the live preview in appearance settings.
         */
        function updatePreview() {
            var $btn = $('#gx-preview-btn');
            if (!$btn.length) return;

            var position = $('#button_position').val() || 'bottom-right';
            var color = $('#button_color').val() || '#25D366';
            var textColor = $('#button_text_color').val() || '#ffffff';
            var label = $('#button_label').val() || 'Text Us!';
            var size = parseInt($('#button_size').val()) || 60;
            var radius = parseInt($('#button_border_radius').val()) || 50;
            var animation = $('#animation_type').val() || 'pulse';
            var offsetX = parseInt($('#offset_x').val()) || 20;
            var offsetY = parseInt($('#offset_y').val()) || 20;
            var icon = $('#button_icon').val() || 'chat';
            var graphicUrl = $('#button_graphic_url').val() || '';
            var hoverGraphicUrl = $('#button_hover_graphic_url').val() || '';
            var graphicMode = $('#button_graphic_mode').val() || 'badge';
            var graphicSize = parseInt($('#button_graphic_size').val()) || 28;
            var $icon = $btn.find('.gx-preview-icon');
            var $graphic = $btn.find('.gx-preview-graphic');
            var $graphicImg = $graphic.find('img');
            var $graphicStack = $btn.find('.gx-preview-graphic-stack');
            var $graphicDefault = $graphicStack.find('.gx-preview-graphic-layer-default img');
            var $graphicHover = $graphicStack.find('.gx-preview-graphic-layer-hover img');
            var $label = $btn.find('.gx-preview-label');
            var $manualNote = $('#gx-preview-manual-note');

            // Scale for preview (smaller while still showing the label)
            var previewSize = Math.round(size * 0.78);
            var previewOffsetX = Math.round(offsetX * 0.5);
            var previewOffsetY = Math.round(offsetY * 0.5);
            var isGraphicReplace = graphicMode === 'replace' && !!graphicUrl;
            var isTextOnly = icon === 'text' && !graphicUrl && !isGraphicReplace;

            // Reset position
            $btn.css({ top: 'auto', bottom: 'auto', left: 'auto', right: 'auto' });

            // Set position
            if (position === 'manual') {
                $btn.css({ top: '50%', left: '50%', right: 'auto', bottom: 'auto', transform: 'translate(-50%, -50%)' });
                $btn.addClass('is-manual-preview');
                $manualNote.prop('hidden', false);
            } else {
                var parts = position.split('-');
                $btn.css(parts[0], previewOffsetY + 'px');
                $btn.css(parts[1], previewOffsetX + 'px');
                $btn.css('transform', '');
                $btn.removeClass('is-manual-preview');
                $manualNote.prop('hidden', true);
            }

            // Set styles
            $btn.css({
                backgroundColor: isGraphicReplace ? 'transparent' : color,
                color: textColor,
                minHeight: isGraphicReplace ? '0' : previewSize + 'px',
                borderRadius: isGraphicReplace ? '0' : radius + '%',
                fontSize: '11px',
                padding: isGraphicReplace ? '0' : '0 14px',
                width: 'auto'
            });

            // Set label
            $label.text(label);

            // Set icon / graphic
            var iconSvg = '';
            if (isGraphicReplace) {
                $graphicDefault.attr('src', graphicUrl);
                $graphicHover.attr('src', hoverGraphicUrl || graphicUrl);
                $graphicStack.find('.gx-preview-graphic-layer-default').toggleClass('has-hover', !!hoverGraphicUrl);
                $graphicStack.css('display', 'inline-flex').prop('hidden', false);
                $graphic.prop('hidden', true).hide();
                $icon.hide().empty();
                $label.hide();
            } else if (graphicUrl) {
                $graphicImg.attr('src', graphicUrl);
                $graphic.css({
                    width: graphicSize + 'px',
                    height: graphicSize + 'px',
                    display: 'inline-flex'
                }).prop('hidden', false);
                $graphicStack.find('.gx-preview-graphic-layer-default').removeClass('has-hover');
                $graphicStack.prop('hidden', true).hide();
                $icon.hide().empty();
            } else {
                if (icon === 'chat') {
                    iconSvg = '<svg width="18" height="18" viewBox="0 0 24 24" fill="' + textColor + '"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>';
                } else if (icon === 'sms') {
                    iconSvg = '<svg width="18" height="18" viewBox="0 0 24 24" fill="' + textColor + '"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>';
                } else if (icon === 'message') {
                    iconSvg = '<svg width="18" height="18" viewBox="0 0 24 24" fill="' + textColor + '"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>';
                }
                $icon.html(iconSvg).toggle(!!iconSvg);
                $graphic.prop('hidden', true).hide();
                $graphicStack.find('.gx-preview-graphic-layer-default').removeClass('has-hover');
                $graphicStack.prop('hidden', true).hide();
            }
            if (!isGraphicReplace) {
                $label.toggle(!!label);
            }

            if (isTextOnly) {
                $icon.hide().empty();
            }

            // Animation class
            $btn.removeClass('gx-anim-pulse gx-anim-bounce gx-anim-shake gx-anim-glow');
            if (animation !== 'none') {
                $btn.addClass('gx-anim-' + animation);
            }
        }

        function bindGraphicPicker(buttonSelector, inputSelector, previewSelector, title, buttonText) {
            $(buttonSelector).on('click', function(e) {
                e.preventDefault();

                mediaFrame = wp.media({
                    title: title,
                    button: {
                        text: buttonText
                    },
                    multiple: false,
                    library: {
                        type: 'image'
                    }
                });

                mediaFrame.on('select', function() {
                    var attachment = mediaFrame.state().get('selection').first().toJSON();
                    $(inputSelector).val(attachment.url).trigger('input');
                    renderGraphicPreview($(previewSelector), attachment.url);
                });

                mediaFrame.open();
            });
        }

        function bindGraphicRemove(buttonSelector, inputSelector, previewSelector) {
            $(buttonSelector).on('click', function(e) {
                e.preventDefault();
                $(inputSelector).val('').trigger('input');
                renderGraphicPreview($(previewSelector), '');
            });
        }

        function renderGraphicPreview($preview, url) {
            if (!$preview.length) return;

            if (url) {
                $preview.removeClass('is-empty').html('<img src="' + url + '" alt="" />');
            } else {
                $preview.addClass('is-empty').empty();
            }
        }
    });

})(jQuery);
