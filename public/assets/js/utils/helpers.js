/**
 * utils/helpers.js
 * Các hàm helper dùng chung cho EMS
 */

const Helpers = (() => {

    // ==========================
    // 1. Format số thành tiền VNĐ
    // ==========================
    const formatCurrency = (amount, currency = '₫') => {
        if (isNaN(amount)) return amount;
        return amount.toLocaleString('vi-VN') + ` ${currency}`;
    };

    // ==========================
    // 2. Format ngày: YYYY-MM-DD => DD/MM/YYYY
    // ==========================
    const formatDate = (dateStr) => {
        if (!dateStr) return '';
        const date = new Date(dateStr);
        if (isNaN(date)) return dateStr;
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${day}/${month}/${year}`;
    };

    // ==========================
    // 3. Show/Hide element
    // ==========================
    const toggleVisibility = (selector, show = true) => {
        const el = document.querySelector(selector);
        if (!el) return;
        el.style.display = show ? 'block' : 'none';
    };

    // ==========================
    // 4. Add/Remove class
    // ==========================
    const toggleClass = (selector, className, add = true) => {
        const el = document.querySelector(selector);
        if (!el) return;
        if (add) el.classList.add(className);
        else el.classList.remove(className);
    };

    // ==========================
    // 5. Confirm action
    // ==========================
    const confirmAction = (message = "Bạn có chắc không?") => {
        return confirm(message);
    };

    // ==========================
    // 6. Scroll to element
    // ==========================
    const scrollTo = (selector, behavior = 'smooth') => {
        const el = document.querySelector(selector);
        if (!el) return;
        el.scrollIntoView({ behavior });
    };

    // ==========================
    // 7. Truncate string
    // ==========================
    const truncate = (str, maxLength = 50) => {
        if (str.length <= maxLength) return str;
        return str.substring(0, maxLength) + '...';
    };

    return {
        formatCurrency,
        formatDate,
        toggleVisibility,
        toggleClass,
        confirmAction,
        scrollTo,
        truncate
    };

})();
