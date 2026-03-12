/**
 * GX Text Frontend JavaScript
 * by Genex Marketing Agency Ltd
 */
(function() {
    'use strict';

    function validatePhone(phone) {
        var cleaned = phone.replace(/(?!^\+)\+/g, '').replace(/[^\d+]/g, '');
        if (cleaned.charAt(0) !== '+') {
            if (cleaned.length === 10) {
                cleaned = '+1' + cleaned;
            } else if (cleaned.length === 11 && cleaned.charAt(0) === '1') {
                cleaned = '+' + cleaned;
            } else {
                cleaned = '+' + cleaned;
            }
        }

        return /^\+[1-9]\d{6,14}$/.test(cleaned);
    }

    function formatPhone(phone) {
        var cleaned = phone.replace(/(?!^\+)\+/g, '').replace(/[^\d+]/g, '');
        if (cleaned.charAt(0) !== '+') {
            if (cleaned.length === 10) {
                cleaned = '+1' + cleaned;
            } else if (cleaned.length === 11 && cleaned.charAt(0) === '1') {
                cleaned = '+' + cleaned;
            } else {
                cleaned = '+' + cleaned;
            }
        }
        return cleaned;
    }

    function showFieldError(field, message) {
        if (!field) {
            return;
        }

        field.classList.add('gx-error');
        field.setAttribute('title', message);
        setTimeout(function() {
            field.classList.remove('gx-error');
            field.removeAttribute('title');
        }, 4000);
    }

    function setLoading(button, isLoading) {
        if (!button) {
            return;
        }

        button.classList.toggle('is-loading', !!isLoading);
        button.disabled = !!isLoading;
    }

    function apiPost(endpoint, data) {
        return fetch(gxTextFront.restUrl + endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': gxTextFront.nonce
            },
            body: JSON.stringify(data)
        }).then(function(response) {
            return response.json().catch(function() {
                return { success: false, message: 'Invalid server response.' };
            }).then(function(payload) {
                if (typeof payload.success === 'undefined') {
                    payload.success = response.ok;
                }
                if (!response.ok && !payload.message) {
                    payload.message = 'Request failed.';
                }
                return payload;
            });
        });
    }

    function initTabs(container) {
        var tabs = container.querySelectorAll('.gx-text-tab');
        if (!tabs.length) {
            return;
        }

        var tabContents = container.querySelectorAll('.gx-text-tab-content');

        tabs.forEach(function(tab) {
            if (tab.dataset.gxBound === '1') {
                return;
            }

            tab.dataset.gxBound = '1';
            tab.addEventListener('click', function() {
                var target = tab.getAttribute('data-tab');
                tabs.forEach(function(currentTab) {
                    currentTab.classList.remove('active');
                    currentTab.setAttribute('aria-selected', 'false');
                });
                tabContents.forEach(function(content) {
                    content.classList.remove('active');
                });

                tab.classList.add('active');
                tab.setAttribute('aria-selected', 'true');

                var targetEl = container.querySelector('#gx-tab-' + target);
                if (targetEl) {
                    targetEl.classList.add('active');
                }
            });
        });
    }

    function revealSuccess(form, successEl, delay, extraHideEl) {
        if (!form || !successEl) {
            return;
        }

        form.style.display = 'none';
        if (extraHideEl) {
            extraHideEl.style.display = 'none';
        }

        successEl.style.display = 'block';

        setTimeout(function() {
            form.reset();
            form.style.display = '';
            if (extraHideEl) {
                extraHideEl.style.display = '';
                extraHideEl.style.color = '';
            }
            successEl.style.display = 'none';
        }, delay);
    }

    function bindMessageForm(form, successEl) {
        if (!form || form.dataset.gxBound === '1') {
            return;
        }

        form.dataset.gxBound = '1';
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            var name = form.querySelector('[name="gx_name"]');
            var phone = form.querySelector('[name="gx_phone"]');
            var message = form.querySelector('[name="gx_message"]');
            var website = form.querySelector('[name="gx_website"]');
            var submit = form.querySelector('.gx-text-submit');

            [name, phone, message].forEach(function(el) {
                if (el) {
                    el.classList.remove('gx-error');
                }
            });

            var hasError = false;
            if (!name || !name.value.trim()) {
                showFieldError(name, 'Please enter your name.');
                hasError = true;
            }
            if (!phone || !validatePhone(phone.value)) {
                showFieldError(phone, 'Please enter a valid phone number.');
                hasError = true;
            }
            if (!message || !message.value.trim()) {
                showFieldError(message, 'Please enter a message.');
                hasError = true;
            }
            if (hasError) {
                return;
            }

            setLoading(submit, true);

            apiPost('send-message', {
                name: name.value.trim(),
                phone: formatPhone(phone.value),
                message: message.value.trim(),
                website: website ? website.value.trim() : ''
            }).then(function(data) {
                setLoading(submit, false);

                if (data.success) {
                    if (successEl) {
                        var successCopy = successEl.querySelector('p');
                        if (successCopy) {
                            successCopy.textContent = data.message || gxTextFront.options.successMessage || 'Thanks! We\'ll text you back shortly.';
                        }
                    }
                    revealSuccess(form, successEl, 5000);
                    return;
                }

                showFieldError(message, data.message || 'Something went wrong. Please try again.');
            }).catch(function() {
                setLoading(submit, false);
                showFieldError(message, 'Network error. Please try again.');
            });
        });
    }

    function bindSubscribeForm(form, successEl, introEl) {
        if (!form || form.dataset.gxBound === '1') {
            return;
        }

        form.dataset.gxBound = '1';
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            var name = form.querySelector('[name="gx_sub_name"]');
            var phone = form.querySelector('[name="gx_sub_phone"]');
            var email = form.querySelector('[name="gx_sub_email"]');
            var consent = form.querySelector('[name="gx_consent"]');
            var website = form.querySelector('[name="gx_sub_website"]');
            var submit = form.querySelector('.gx-text-submit');

            [name, phone].forEach(function(el) {
                if (el) {
                    el.classList.remove('gx-error');
                }
            });

            if (consent && consent.parentElement) {
                consent.parentElement.style.color = '';
            }

            var hasError = false;
            if (!name || !name.value.trim()) {
                showFieldError(name, 'Please enter your name.');
                hasError = true;
            }
            if (!phone || !validatePhone(phone.value)) {
                showFieldError(phone, 'Please enter a valid phone number.');
                hasError = true;
            }
            if (!consent || !consent.checked) {
                if (consent && consent.parentElement) {
                    consent.parentElement.style.color = '#e74c3c';
                }
                hasError = true;
            }
            if (hasError) {
                return;
            }

            setLoading(submit, true);

            apiPost('subscribe', {
                name: name.value.trim(),
                phone: formatPhone(phone.value),
                email: email ? email.value.trim() : '',
                consent: !!(consent && consent.checked),
                website: website ? website.value.trim() : ''
            }).then(function(data) {
                setLoading(submit, false);

                if (data.success) {
                    if (successEl) {
                        var successCopy = successEl.querySelector('p');
                        if (successCopy) {
                            successCopy.textContent = data.message || gxTextFront.options.subscribeSuccess || 'You\'re subscribed!';
                        }
                    }
                    revealSuccess(form, successEl, 6000, introEl);
                    return;
                }

                showFieldError(phone, data.message || 'Something went wrong.');
            }).catch(function() {
                setLoading(submit, false);
                showFieldError(phone, 'Network error. Please try again.');
            });
        });
    }

    function initFloatingWidget() {
        var floating = document.getElementById('gx-text-floating');
        var toggleBtn = document.getElementById('gx-text-toggle');
        var widget = document.getElementById('gx-text-widget');
        var closeBtn = document.getElementById('gx-text-close');

        if (!floating || !toggleBtn || !widget) {
            return;
        }

        var isOpen = false;

        function openWidget() {
            isOpen = true;
            floating.classList.add('is-open');
            widget.classList.add('is-visible');
            widget.setAttribute('aria-hidden', 'false');
            toggleBtn.setAttribute('aria-expanded', 'true');
        }

        function closeWidget() {
            isOpen = false;
            floating.classList.remove('is-open');
            widget.classList.remove('is-visible');
            widget.setAttribute('aria-hidden', 'true');
            toggleBtn.setAttribute('aria-expanded', 'false');
        }

        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            if (isOpen) {
                closeWidget();
            } else {
                openWidget();
            }
        });

        if (closeBtn) {
            closeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                closeWidget();
            });
        }

        document.addEventListener('click', function(e) {
            if (isOpen && !floating.contains(e.target)) {
                closeWidget();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && isOpen) {
                closeWidget();
                toggleBtn.focus();
            }
        });

        initTabs(floating);
        bindMessageForm(document.getElementById('gx-text-message-form'), document.getElementById('gx-text-msg-success'));
        bindSubscribeForm(
            document.getElementById('gx-text-subscribe-form'),
            document.getElementById('gx-text-sub-success'),
            floating.querySelector('.gx-text-subscribe-intro')
        );

        setTimeout(function() {
            floating.style.opacity = '1';
            floating.style.transform = 'scale(1)';
        }, 500);
    }

    window.gxTextInitInline = function(container) {
        if (!container || container.dataset.gxInitialized === '1') {
            return;
        }

        container.dataset.gxInitialized = '1';

        var widget = container.querySelector('.gx-text-widget');
        if (widget) {
            widget.classList.add('is-visible');
        }

        initTabs(container);
        bindMessageForm(container.querySelector('.gx-text-inline-message-form'), container.querySelector('.gx-inline-msg-success'));
        bindSubscribeForm(
            container.querySelector('.gx-text-inline-subscribe-form'),
            container.querySelector('.gx-inline-sub-success'),
            container.querySelector('.gx-text-subscribe-intro')
        );
    };

    document.addEventListener('DOMContentLoaded', function() {
        initFloatingWidget();

        document.querySelectorAll('.gx-text-inline').forEach(function(container) {
            window.gxTextInitInline(container);
        });
    });
})();
