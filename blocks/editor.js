/**
 * GX Text Gutenberg Blocks
 * by Genex Marketing Agency Ltd
 */
(function(blocks, element, blockEditor, components, i18n) {
    var el = element.createElement;
    var Fragment = element.Fragment;
    var InspectorControls = blockEditor.InspectorControls;
    var PanelBody = components.PanelBody;
    var TextControl = components.TextControl;
    var SelectControl = components.SelectControl;
    var ColorPicker = components.ColorPicker;
    var Button = components.Button;
    var __ = i18n.__;
    var editorOptions = (window.gxTextEditor && window.gxTextEditor.options) || {};

    function getEditorOption(key, fallback) {
        return editorOptions[key] || fallback;
    }

    function getButtonPreviewConfig(attrs) {
        var sizeMap = {
            small: { padding: '8px 18px', fontSize: '13px', minHeight: '42px' },
            medium: { padding: '12px 24px', fontSize: '15px', minHeight: '52px' },
            large: { padding: '16px 32px', fontSize: '17px', minHeight: '60px' }
        };
        var defaultSize = parseInt(getEditorOption('buttonSize', '60'), 10) || 60;
        var label = attrs.label || getEditorOption('buttonLabel', 'Text Us!');
        var color = attrs.color || getEditorOption('buttonColor', '#25D366');
        var textColor = attrs.textColor || getEditorOption('buttonTextColor', '#ffffff');
        var icon = attrs.icon || getEditorOption('buttonIcon', 'chat');
        var graphicMode = getEditorOption('buttonGraphicMode', 'badge');
        var graphicUrl = getEditorOption('buttonGraphicUrl', '');
        var graphicSize = parseInt(getEditorOption('buttonGraphicSize', '28'), 10) || 28;
        var radius = parseInt(getEditorOption('buttonRadius', '50'), 10) || 50;
        var sizeChoice = attrs.size || '';
        var sizeConfig = sizeChoice ? (sizeMap[sizeChoice] || sizeMap.medium) : {
            padding: '0 22px',
            fontSize: '15px',
            minHeight: defaultSize + 'px'
        };

        return {
            label: label,
            color: color,
            textColor: textColor,
            icon: icon,
            graphicMode: graphicMode,
            graphicUrl: graphicUrl,
            graphicSize: graphicSize,
            radius: radius,
            sizeConfig: sizeConfig,
            isReplaceGraphic: graphicMode === 'replace' && !!graphicUrl,
            isBadgeGraphic: graphicMode === 'badge' && !!graphicUrl
        };
    }

    function getPreviewDashicon(icon) {
        if (icon === 'sms') {
            return 'dashicons-smartphone';
        }
        if (icon === 'message') {
            return 'dashicons-email';
        }
        return 'dashicons-format-chat';
    }

    function renderBrandLogo(size) {
        var logoUrl = getEditorOption('brandLogoUrl', '');
        if (!logoUrl) {
            return null;
        }

        return el('img', {
            src: logoUrl,
            alt: '',
            style: {
                width: size + 'px',
                height: size + 'px',
                borderRadius: '50%',
                objectFit: 'cover',
                display: 'block',
                flexShrink: 0
            }
        });
    }

    /* ─── GX Text Button Block ─────────────────── */
    blocks.registerBlockType('gx-text/button', {
        title: __('GX Text Button', 'gx-text'),
        description: __('Add an inline "Text Us" button that opens the GX Text widget.', 'gx-text'),
        icon: 'format-chat',
        category: 'widgets',
        keywords: [__('text', 'gx-text'), __('sms', 'gx-text'), __('button', 'gx-text'), __('genex', 'gx-text')],
        attributes: {
            label: { type: 'string', default: '' },
            color: { type: 'string', default: '' },
            textColor: { type: 'string', default: '' },
            size: { type: 'string', default: '' },
            icon: { type: 'string', default: '' }
        },
        edit: function(props) {
            var attrs = props.attributes;
            var preview = getButtonPreviewConfig(attrs);

            return el(Fragment, {},
                el(InspectorControls, {},
                    el(PanelBody, { title: __('Button Settings', 'gx-text'), initialOpen: true },
                        el(TextControl, {
                            label: __('Label (leave empty for Appearance default)', 'gx-text'),
                            value: attrs.label,
                            onChange: function(v) { props.setAttributes({ label: v }); }
                        }),
                        el(SelectControl, {
                            label: __('Size', 'gx-text'),
                            value: attrs.size,
                            options: [
                                { label: 'Appearance Default', value: '' },
                                { label: 'Small', value: 'small' },
                                { label: 'Medium', value: 'medium' },
                                { label: 'Large', value: 'large' }
                            ],
                            onChange: function(v) { props.setAttributes({ size: v }); }
                        }),
                        el(SelectControl, {
                            label: __('Icon', 'gx-text'),
                            value: attrs.icon,
                            options: [
                                { label: 'Appearance Default', value: '' },
                                { label: 'Chat Bubble', value: 'chat' },
                                { label: 'SMS / Phone', value: 'sms' },
                                { label: 'Message', value: 'message' },
                                { label: 'Text Only', value: 'text' },
                                { label: 'None', value: 'none' }
                            ],
                            onChange: function(v) { props.setAttributes({ icon: v }); }
                        })
                    ),
                    el(PanelBody, { title: __('Colors', 'gx-text'), initialOpen: false },
                        el('p', {}, __('Button Color', 'gx-text')),
                        el(ColorPicker, {
                            color: preview.color,
                            onChangeComplete: function(c) { props.setAttributes({ color: c.hex }); },
                            disableAlpha: true
                        }),
                        el(Button, {
                            variant: 'secondary',
                            isSmall: true,
                            onClick: function() { props.setAttributes({ color: '' }); },
                            style: { marginTop: '8px', marginBottom: '10px' }
                        }, __('Use Appearance Default', 'gx-text')),
                        el('p', {}, __('Text Color', 'gx-text')),
                        el(ColorPicker, {
                            color: preview.textColor,
                            onChangeComplete: function(c) { props.setAttributes({ textColor: c.hex }); },
                            disableAlpha: true
                        }),
                        el(Button, {
                            variant: 'secondary',
                            isSmall: true,
                            onClick: function() { props.setAttributes({ textColor: '' }); },
                            style: { marginTop: '8px' }
                        }, __('Use Appearance Default', 'gx-text'))
                    )
                ),
                el('div', { className: 'gx-text-block-preview', style: { fontFamily: getEditorOption('widgetFontFamily', 'inherit') } },
                    el('button', {
                        className: 'gx-text-block-btn',
                        style: {
                            backgroundColor: preview.isReplaceGraphic ? 'transparent' : preview.color,
                            color: preview.textColor,
                            padding: preview.isReplaceGraphic ? '0' : preview.sizeConfig.padding,
                            borderRadius: preview.isReplaceGraphic ? '0' : preview.radius + '%',
                            border: 'none',
                            fontSize: preview.sizeConfig.fontSize,
                            fontWeight: '600',
                            cursor: 'pointer',
                            display: 'inline-flex',
                            alignItems: 'center',
                            gap: '8px',
                            minHeight: preview.isReplaceGraphic ? '0' : preview.sizeConfig.minHeight,
                            boxShadow: preview.isReplaceGraphic ? 'none' : '0 2px 8px rgba(0,0,0,0.12)'
                        }
                    },
                        preview.isReplaceGraphic ? el('img', {
                            src: preview.graphicUrl,
                            alt: '',
                            style: { display: 'block', maxWidth: '220px', height: 'auto' }
                        }) : null,
                        !preview.isReplaceGraphic && preview.isBadgeGraphic ? el('img', {
                            src: preview.graphicUrl,
                            alt: '',
                            style: {
                                width: preview.graphicSize + 'px',
                                height: preview.graphicSize + 'px',
                                borderRadius: '50%',
                                objectFit: 'cover',
                                display: 'block'
                            }
                        }) : null,
                        !preview.isReplaceGraphic && preview.icon !== 'none' && preview.icon !== 'text' && !preview.isBadgeGraphic ? el('span', { className: 'dashicons ' + getPreviewDashicon(preview.icon), style: { fontSize: '16px' } }) : null,
                        !preview.isReplaceGraphic ? el('span', {}, preview.label) : null
                    )
                )
            );
        },
        save: function() {
            return null; // Server-side render
        }
    });

    /* ─── GX Text Form Block ──────────────────── */
    blocks.registerBlockType('gx-text/form', {
        title: __('GX Text Form', 'gx-text'),
        description: __('Embed the full GX Text message form inline.', 'gx-text'),
        icon: 'email',
        category: 'widgets',
        keywords: [__('text', 'gx-text'), __('form', 'gx-text'), __('message', 'gx-text'), __('genex', 'gx-text')],
        attributes: {
            title: { type: 'string', default: '' },
            subtitle: { type: 'string', default: '' }
        },
        edit: function(props) {
            var attrs = props.attributes;
            var title = attrs.title || getEditorOption('widgetTitle', 'Text Us Now');
            var subtitle = attrs.subtitle || getEditorOption('widgetSubtitle', 'Send us a message');
            var widgetBg = getEditorOption('widgetBgColor', '#ffffff');
            var headerBg = getEditorOption('widgetHeaderColor', '#25D366');
            var headerText = getEditorOption('widgetHeaderText', '#ffffff');
            var fontFamily = getEditorOption('widgetFontFamily', 'inherit');
            var buttonColor = getEditorOption('widgetHeaderColor', '#25D366');
            return el(Fragment, {},
                el(InspectorControls, {},
                    el(PanelBody, { title: __('Form Settings', 'gx-text'), initialOpen: true },
                        el(TextControl, {
                            label: __('Title (leave empty for default)', 'gx-text'),
                            value: attrs.title,
                            onChange: function(v) { props.setAttributes({ title: v }); }
                        }),
                        el(TextControl, {
                            label: __('Subtitle (leave empty for default)', 'gx-text'),
                            value: attrs.subtitle,
                            onChange: function(v) { props.setAttributes({ subtitle: v }); }
                        })
                    )
                ),
                el('div', { className: 'gx-text-block-preview gx-text-block-form-preview', style: { fontFamily: fontFamily } },
                    el('div', { className: 'gx-block-form-header', style: { background: headerBg, color: headerText, padding: '16px', borderRadius: '8px 8px 0 0' } },
                        el('div', { style: { display: 'flex', alignItems: 'center', gap: '10px' } },
                            renderBrandLogo(34),
                            el('div', {},
                                el('h4', { style: { margin: 0, color: headerText } }, title),
                                el('p', { style: { margin: '4px 0 0', opacity: 0.85, fontSize: '12px', color: headerText } }, subtitle)
                            )
                        )
                    ),
                    el('div', { style: { padding: '16px', background: widgetBg, borderRadius: '0 0 8px 8px', border: '1px solid #e0e0e0' } },
                        el('div', { style: { background: '#fff', border: '1px solid #ddd', borderRadius: '6px', padding: '10px', marginBottom: '8px', color: '#aaa', fontSize: '13px' } }, getEditorOption('placeholderName', 'Your Name')),
                        el('div', { style: { background: '#fff', border: '1px solid #ddd', borderRadius: '6px', padding: '10px', marginBottom: '8px', color: '#aaa', fontSize: '13px' } }, getEditorOption('placeholderPhone', 'Your Phone Number')),
                        el('div', { style: { background: '#fff', border: '1px solid #ddd', borderRadius: '6px', padding: '10px', marginBottom: '8px', color: '#aaa', fontSize: '13px', minHeight: '60px' } }, getEditorOption('placeholderMessage', 'Type your message...')),
                        el('div', { style: { background: buttonColor, color: '#fff', padding: '10px', borderRadius: '6px', textAlign: 'center', fontWeight: '600', fontSize: '14px' } }, 'Send Message')
                    )
                )
            );
        },
        save: function() {
            return null;
        }
    });

    /* ─── GX Text Subscribe Block ─────────────── */
    blocks.registerBlockType('gx-text/subscribe', {
        title: __('GX Text Subscribe', 'gx-text'),
        description: __('Embed the GX Text subscription form for text newsletter sign-ups.', 'gx-text'),
        icon: 'megaphone',
        category: 'widgets',
        keywords: [__('subscribe', 'gx-text'), __('newsletter', 'gx-text'), __('sms', 'gx-text'), __('genex', 'gx-text')],
        attributes: {
            heading: { type: 'string', default: '' },
            description: { type: 'string', default: '' }
        },
        edit: function(props) {
            var attrs = props.attributes;
            var heading = attrs.heading || getEditorOption('subscribeHeading', 'Get Deals via Text');
            var description = attrs.description || getEditorOption('subscribeDescription', 'Subscribe for exclusive deals!');
            var widgetBg = getEditorOption('widgetBgColor', '#ffffff');
            var headerBg = getEditorOption('widgetHeaderColor', '#25D366');
            var headerText = getEditorOption('widgetHeaderText', '#ffffff');
            var fontFamily = getEditorOption('widgetFontFamily', 'inherit');
            var subscribeButtonColor = getEditorOption('subscribeButtonColor', '#FF6B35');
            var subscribeButtonText = getEditorOption('subscribeButtonText', 'Subscribe');
            return el(Fragment, {},
                el(InspectorControls, {},
                    el(PanelBody, { title: __('Subscribe Settings', 'gx-text'), initialOpen: true },
                        el(TextControl, {
                            label: __('Heading (leave empty for default)', 'gx-text'),
                            value: attrs.heading,
                            onChange: function(v) { props.setAttributes({ heading: v }); }
                        }),
                        el(TextControl, {
                            label: __('Description (leave empty for default)', 'gx-text'),
                            value: attrs.description,
                            onChange: function(v) { props.setAttributes({ description: v }); }
                        })
                    )
                ),
                el('div', { className: 'gx-text-block-preview gx-text-block-subscribe-preview', style: { fontFamily: fontFamily } },
                    el('div', { style: { background: headerBg, color: headerText, padding: '16px', borderRadius: '8px 8px 0 0' } },
                        el('div', { style: { display: 'flex', alignItems: 'center', gap: '10px' } },
                            renderBrandLogo(34),
                            el('div', {},
                                el('h4', { style: { margin: 0, color: headerText } }, heading),
                                el('p', { style: { margin: '4px 0 0', opacity: 0.85, fontSize: '12px', color: headerText } }, description)
                            )
                        )
                    ),
                    el('div', { style: { padding: '16px', background: widgetBg, borderRadius: '0 0 8px 8px', border: '1px solid #e0e0e0' } },
                        el('div', { style: { background: '#fff', border: '1px solid #ddd', borderRadius: '6px', padding: '10px', marginBottom: '8px', color: '#aaa', fontSize: '13px' } }, getEditorOption('placeholderName', 'Your Name')),
                        el('div', { style: { background: '#fff', border: '1px solid #ddd', borderRadius: '6px', padding: '10px', marginBottom: '8px', color: '#aaa', fontSize: '13px' } }, getEditorOption('placeholderPhone', 'Your Phone Number')),
                        el('div', { style: { background: '#fff', border: '1px solid #ddd', borderRadius: '6px', padding: '10px', marginBottom: '8px', color: '#aaa', fontSize: '13px' } }, 'Email (optional)'),
                        el('div', { style: { background: subscribeButtonColor, color: '#fff', padding: '10px', borderRadius: '6px', textAlign: 'center', fontWeight: '600', fontSize: '14px' } }, subscribeButtonText)
                    )
                )
            );
        },
        save: function() {
            return null;
        }
    });

})(
    window.wp.blocks,
    window.wp.element,
    window.wp.blockEditor,
    window.wp.components,
    window.wp.i18n
);
