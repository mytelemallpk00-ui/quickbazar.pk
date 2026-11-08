(function () {
    'use strict';

    let quantity = 1;

    function unitPrice() {
        return Number(window.currentProduct?.price || 0);
    }

    function lineTotal() {
        return unitPrice() * quantity;
    }

    function syncQtyDisplay() {
        const qtyEl = document.getElementById('product-qty');
        if (qtyEl) {
            qtyEl.textContent = String(quantity);
        }
        const stickyQty = document.getElementById('sticky-product-qty');
        if (stickyQty) {
            stickyQty.textContent = String(quantity);
        }
    }

    function formatLineTotal() {
        if (window.BeautageMoney) {
            return window.BeautageMoney.formatDecimal(lineTotal());
        }
        return `Rs. ${lineTotal().toFixed(0)}`;
    }

    function updateLinePricing() {
        const formatted = formatLineTotal();
        const btnTotal = document.getElementById('btn-total-price');
        if (btnTotal) {
            btnTotal.textContent = formatted;
        }
        const stickyTotal = document.getElementById('sticky-btn-total-price');
        if (stickyTotal) {
            stickyTotal.textContent = formatted;
        }
    }

    function adjustQty(delta) {
        quantity = Math.max(1, quantity + delta);
        syncQtyDisplay();
        updateLinePricing();
    }

    function cartPayload() {
        return {
            name: window.currentProduct?.name || 'Product',
            price: unitPrice(),
            qty: quantity,
        };
    }

    function init() {
        updateLinePricing();
    }

    window.adjustQty = adjustQty;

    window.BeautagePdpPricing = {
        init,
        adjustQty,
        cartPayload,
        getQuantity: () => quantity,
    };
})();
