(function () {
    'use strict';

    let quantity = 1;
    let bulkDiscountRate = 0;

    function unitPrice() {
        const base = Number(window.currentProduct?.price || 0);
        return base * (1 - bulkDiscountRate);
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

    function updateBulkCards() {
        document.querySelectorAll('[data-bulk-tier]').forEach((btn) => {
            const tier = Number(btn.dataset.bulkTier || 0);
            let active = tier === 1;
            if (quantity === 6 && bulkDiscountRate > 0) {
                active = tier === 6;
            } else if (quantity === 12 && bulkDiscountRate > 0) {
                active = tier === 12;
            }
            btn.classList.toggle('is-active', active);
            btn.setAttribute('aria-pressed', active ? 'true' : 'false');
        });
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
        updateBulkCards();
    }

    function applyQtyRules(nextQty) {
        quantity = Math.max(1, nextQty);
        if (quantity === 6) {
            bulkDiscountRate = 0.1;
        } else if (quantity === 12) {
            bulkDiscountRate = 0.2;
        } else {
            bulkDiscountRate = 0;
        }
    }

    function cartLineLabel() {
        const name = window.currentProduct?.name || 'Product';
        if (bulkDiscountRate === 0.1 && quantity === 6) {
            return `${name} (Bulk 6 · 10% off)`;
        }
        if (bulkDiscountRate === 0.2 && quantity === 12) {
            return `${name} (Bulk 12 · 20% off)`;
        }
        return name;
    }

    function selectBulkTier(pieces, discountRate) {
        bulkDiscountRate = discountRate;
        quantity = pieces;
        syncQtyDisplay();
        updateLinePricing();
    }

    function adjustQty(delta) {
        applyQtyRules(quantity + delta);
        syncQtyDisplay();
        updateLinePricing();
    }

    function cartPayload() {
        return {
            name: cartLineLabel(),
            price: unitPrice(),
            qty: quantity,
        };
    }

    function init() {
        updateLinePricing();
    }

    window.selectBulkTier = selectBulkTier;
    window.adjustQty = adjustQty;

    window.BeautagePdpPricing = {
        init,
        adjustQty,
        selectBulkTier,
        cartPayload,
        getQuantity: () => quantity,
    };
})();
