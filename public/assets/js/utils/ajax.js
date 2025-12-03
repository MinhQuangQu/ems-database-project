/**
 * utils/ajax.js
 * Helper functions for AJAX requests (Fetch API) with CSRF token support
 */

const Ajax = (() => {

    // Lấy CSRF token từ meta hoặc input hidden
    const getCsrfToken = () => {
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        if (tokenMeta) return tokenMeta.getAttribute('content');

        const tokenInput = document.querySelector('input[name="csrf_token"]');
        if (tokenInput) return tokenInput.value;

        return '';
    };

    // ==========================
    // GET request
    // ==========================
    const get = async (url, params = {}) => {
        const queryString = new URLSearchParams(params).toString();
        const fullUrl = queryString ? `${url}?${queryString}` : url;

        const response = await fetch(fullUrl, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        });

        return response.json();
    };

    // ==========================
    // POST request
    // ==========================
    const post = async (url, data = {}) => {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify(data),
            credentials: 'same-origin'
        });

        return response.json();
    };

    // ==========================
    // Helper: handle response
    // ==========================
    const handleResponse = async (promise) => {
        try {
            const data = await promise;
            if (data.success === false) {
                console.error('AJAX Error:', data.message || 'Unknown error');
                return null;
            }
            return data;
        } catch (err) {
            console.error('AJAX Exception:', err);
            return null;
        }
    };

    return {
        get,
        post,
        handleResponse
    };

})();
