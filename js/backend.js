/**
 * Link Counter - Backend JavaScript
 *
 * @author      WBCE Community, Beach
 * @copyright   2026-01 WBCE Community, Beach
 * @license     MIT License
 * @version     1.0.0
 */

(function() {
    'use strict';

    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {

        // Confirm delete actions
        var deleteLinks = document.querySelectorAll('a[href*="delete.php"]');
        deleteLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                var confirmMsg = this.getAttribute('data-confirm') || 'Do you really want to delete this download?';
                if (!confirm(confirmMsg)) {
                    e.preventDefault();
                    return false;
                }
            });
        });

        // Confirm reset counter actions
        var resetLinks = document.querySelectorAll('a[href*="reset_counter.php"]');
        resetLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                var confirmMsg = this.getAttribute('data-confirm') || 'Do you really want to reset the counter?';
                if (!confirm(confirmMsg)) {
                    e.preventDefault();
                    return false;
                }
            });
        });

        // Copy to clipboard functionality
        window.copyToClipboard = function(text) {
            // Modern clipboard API
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(function() {
                    showNotification('Copied to clipboard!', 'success');
                }).catch(function(err) {
                    fallbackCopyToClipboard(text);
                });
            } else {
                fallbackCopyToClipboard(text);
            }
        };

        // Fallback copy method for older browsers
        function fallbackCopyToClipboard(text) {
            var textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.focus();
            textarea.select();

            try {
                var successful = document.execCommand('copy');
                if (successful) {
                    showNotification('Copied to clipboard!', 'success');
                } else {
                    showNotification('Failed to copy', 'error');
                }
            } catch (err) {
                showNotification('Failed to copy', 'error');
            }

            document.body.removeChild(textarea);
        }

        // Show notification
        function showNotification(message, type) {
            var notification = document.createElement('div');
            notification.className = 'notification notification-' + type;
            notification.textContent = message;
            notification.style.position = 'fixed';
            notification.style.top = '20px';
            notification.style.right = '20px';
            notification.style.padding = '15px 20px';
            notification.style.borderRadius = '4px';
            notification.style.zIndex = '9999';
            notification.style.boxShadow = '0 2px 8px rgba(0,0,0,0.2)';
            notification.style.fontWeight = '500';
            notification.style.transition = 'opacity 0.3s';

            if (type === 'success') {
                notification.style.backgroundColor = '#28a745';
                notification.style.color = 'white';
            } else {
                notification.style.backgroundColor = '#dc3545';
                notification.style.color = 'white';
            }

            document.body.appendChild(notification);

            // Fade out and remove after 3 seconds
            setTimeout(function() {
                notification.style.opacity = '0';
                setTimeout(function() {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }

        // Form validation
        var forms = document.querySelectorAll('.linkcounter-form');
        forms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                var titleInput = form.querySelector('input[name="title"]');
                var urlInput = form.querySelector('input[name="url"]');
                var linkTypeInput = form.querySelector('select[name="link_type"]');
                var pageIdInput = form.querySelector('select[name="page_id"]');

                var errors = [];

                // Validate title
                if (!titleInput.value.trim()) {
                    errors.push('Please enter a title.');
                    titleInput.classList.add('is-invalid');
                } else {
                    titleInput.classList.remove('is-invalid');
                }

                // Validate based on link type
                if (linkTypeInput && linkTypeInput.value === 'url') {
                    // Validate URL only if link_type is 'url'
                    if (!urlInput.value.trim()) {
                        errors.push('Please enter a URL.');
                        urlInput.classList.add('is-invalid');
                    } else {
                        urlInput.classList.remove('is-invalid');
                    }
                } else if (linkTypeInput && linkTypeInput.value === 'page') {
                    // Validate page_id only if link_type is 'page'
                    if (pageIdInput && !pageIdInput.value) {
                        errors.push('Please select a page.');
                        pageIdInput.classList.add('is-invalid');
                    } else if (pageIdInput) {
                        pageIdInput.classList.remove('is-invalid');
                    }
                }

                // Show errors if any
                if (errors.length > 0) {
                    e.preventDefault();
                    showNotification(errors.join('\n'), 'error');
                    return false;
                }
            });
        });

        // Auto-dismiss alerts after 5 seconds
        var alerts = document.querySelectorAll('.alert-success, .alert-info');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            }, 5000);
        });

        // Table row highlighting
        var tableRows = document.querySelectorAll('.linkcounter-table tbody tr');
        tableRows.forEach(function(row) {
            row.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#f8f9fa';
            });
            row.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
            });
        });

    });

})();
