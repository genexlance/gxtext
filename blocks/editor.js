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
    var __ = i18n.__;

    /* ─── GX Text Button Block ─────────────────── */
    blocks.registerBlockType('gx-text/button', {
        title: __('GX Text Button', 'gx-text'),
        description: __('Add an inline "Text Us" button that opens the GX Text widget.', 'gx-text'),
        icon: 'format-chat',
        category: 'widgets',
        keywords: [__('text', 'gx-text'), __('sms', 'gx-text'), __('button', 'gx-text'), __('genex', 'gx-text')],
        attributes: {
            label: { type: 'string', default: 'Text Us!' },
            color: { type: 'string', default: '#25D366' },
            textColor: { type: 'string', default: '#ffffff' },
            size: { type: 'string', default: 'medium' },
            icon: { type: 'string', default: 'chat' }
        },
        edit: function(props) {
            var attrs = props.attributes;
            var sizes = { small: '8px 18px', medium: '12px 24px', large: '16px 32px' };

            return el(Fragment, {},
                el(InspectorControls, {},
                    el(PanelBody, { title: __('Button Settings', 'gx-text'), initialOpen: true },
                        el(TextControl, {
                            label: __('Label', 'gx-text'),
                            value: attrs.label,
                            onChange: function(v) { props.setAttributes({ label: v }); }
                        }),
                        el(SelectControl, {
                            label: __('Size', 'gx-text'),
                            value: attrs.size,
                            options: [
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
                                { label: 'Chat Bubble', value: 'chat' },
                                { label: 'SMS / Phone', value: 'sms' },
                                { label: 'Message', value: 'message' },
                                { label: 'None', value: 'none' }
                            ],
                            onChange: function(v) { props.setAttributes({ icon: v }); }
                        })
                    ),
                    el(PanelBody, { title: __('Colors', 'gx-text'), initialOpen: false },
                        el('p', {}, __('Button Color', 'gx-text')),
                        el(ColorPicker, {
                            color: attrs.color,
                            onChangeComplete: function(c) { props.setAttributes({ color: c.hex }); },
                            disableAlpha: true
                        }),
                        el('p', {}, __('Text Color', 'gx-text')),
                        el(ColorPicker, {
                            color: attrs.textColor,
                            onChangeComplete: function(c) { props.setAttributes({ textColor: c.hex }); },
                            disableAlpha: true
                        })
                    )
                ),
                el('div', { className: 'gx-text-block-preview' },
                    el('button', {
                        className: 'gx-text-block-btn',
                        style: {
                            backgroundColor: attrs.color,
                            color: attrs.textColor,
                            padding: sizes[attrs.size] || sizes.medium,
                            borderRadius: '50px',
                            border: 'none',
                            fontSize: attrs.size === 'large' ? '17px' : (attrs.size === 'small' ? '13px' : '15px'),
                            fontWeight: '600',
                            cursor: 'pointer',
                            display: 'inline-flex',
                            alignItems: 'center',
                            gap: '8px'
                        }
                    },
                        attrs.icon !== 'none' ? el('span', { className: 'dashicons dashicons-format-chat', style: { fontSize: '16px' } }) : null,
                        el('span', {}, attrs.label)
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
                el('div', { className: 'gx-text-block-preview gx-text-block-form-preview' },
                    el('div', { className: 'gx-block-form-header', style: { background: '#25D366', color: '#fff', padding: '16px', borderRadius: '8px 8px 0 0' } },
                        el('h4', { style: { margin: 0, color: '#fff' } }, attrs.title || 'Text Us Now'),
                        el('p', { style: { margin: '4px 0 0', opacity: 0.85, fontSize: '12px', color: '#fff' } }, attrs.subtitle || 'Send us a message')
                    ),
                    el('div', { style: { padding: '16px', background: '#f9f9f9', borderRadius: '0 0 8px 8px', border: '1px solid #e0e0e0' } },
                        el('div', { style: { background: '#fff', border: '1px solid #ddd', borderRadius: '6px', padding: '10px', marginBottom: '8px', color: '#aaa', fontSize: '13px' } }, 'Your Name'),
                        el('div', { style: { background: '#fff', border: '1px solid #ddd', borderRadius: '6px', padding: '10px', marginBottom: '8px', color: '#aaa', fontSize: '13px' } }, 'Your Phone Number'),
                        el('div', { style: { background: '#fff', border: '1px solid #ddd', borderRadius: '6px', padding: '10px', marginBottom: '8px', color: '#aaa', fontSize: '13px', minHeight: '60px' } }, 'Type your message...'),
                        el('div', { style: { background: '#25D366', color: '#fff', padding: '10px', borderRadius: '6px', textAlign: 'center', fontWeight: '600', fontSize: '14px' } }, 'Send Message')
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
                el('div', { className: 'gx-text-block-preview gx-text-block-subscribe-preview' },
                    el('div', { style: { background: '#FF6B35', color: '#fff', padding: '16px', borderRadius: '8px 8px 0 0' } },
                        el('h4', { style: { margin: 0, color: '#fff' } }, attrs.heading || 'Get Deals via Text'),
                        el('p', { style: { margin: '4px 0 0', opacity: 0.85, fontSize: '12px', color: '#fff' } }, attrs.description || 'Subscribe for exclusive deals!')
                    ),
                    el('div', { style: { padding: '16px', background: '#f9f9f9', borderRadius: '0 0 8px 8px', border: '1px solid #e0e0e0' } },
                        el('div', { style: { background: '#fff', border: '1px solid #ddd', borderRadius: '6px', padding: '10px', marginBottom: '8px', color: '#aaa', fontSize: '13px' } }, 'Your Name'),
                        el('div', { style: { background: '#fff', border: '1px solid #ddd', borderRadius: '6px', padding: '10px', marginBottom: '8px', color: '#aaa', fontSize: '13px' } }, 'Your Phone Number'),
                        el('div', { style: { background: '#fff', border: '1px solid #ddd', borderRadius: '6px', padding: '10px', marginBottom: '8px', color: '#aaa', fontSize: '13px' } }, 'Email (optional)'),
                        el('div', { style: { background: '#FF6B35', color: '#fff', padding: '10px', borderRadius: '6px', textAlign: 'center', fontWeight: '600', fontSize: '14px' } }, 'Subscribe')
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
