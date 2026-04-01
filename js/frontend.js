/**
 * Link Counter - Frontend JavaScript (Crawler Protection)
 *
 * @author      WBCE Community, Beach
 * @copyright   2026-01 WBCE Community, Beach
 * @license     MIT License
 * @version     1.1.0
 */

(function() {
    'use strict';

    // Store page load timestamp
    var pageLoadTime = 0;

    // XOR encoding key (simple obfuscation)
    var xorKey = 42;

    /**
     * XOR encode string for obfuscation
     * @param {string} str - String to encode
     * @return {string} Base64 encoded XOR result
     */
    function xorEncode(str) {
        var result = '';
        for (var i = 0; i < str.length; i++) {
            result += String.fromCharCode(str.charCodeAt(i) ^ xorKey);
        }
        // Base64 encode the XOR result
        try {
            return btoa(result);
        } catch (e) {
            // Fallback for older browsers
            return btoa(unescape(encodeURIComponent(result)));
        }
    }

    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {

        // Capture page load time
        pageLoadTime = Date.now();

        // Find all LinkCounter links
        var links = document.querySelectorAll('a.linkcounter-link[data-linkcounter-id]');

        if (links.length === 0) {
            return; // No links to protect
        }

        // Attach click handler to each link
        links.forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();

                // Get current time
                var clickTime = Date.now();

                // Get base URL and link ID
                var baseUrl = this.getAttribute('href');
                var linkId = this.getAttribute('data-linkcounter-id');

                // Create timestamp data: pageLoadTime|clickTime
                var timeData = pageLoadTime + '|' + clickTime;

                // Encode with XOR + Base64 for obfuscation
                var encoded = xorEncode(timeData);

                // Build new URL with encoded timestamp
                var separator = baseUrl.indexOf('?') !== -1 ? '&' : '?';
                var newUrl = baseUrl + separator + '_t=' + encodeURIComponent(encoded);

                // Redirect to track URL with timestamps, respecting target attribute
                var target = this.getAttribute('target');
                if (target === '_blank') {
                    window.open(newUrl, '_blank', 'noopener,noreferrer');
                } else {
                    window.location.href = newUrl;
                }
            });
        });
    });

})();
