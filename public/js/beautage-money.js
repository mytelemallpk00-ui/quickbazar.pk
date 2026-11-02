(function () {
    function toNumber(amount) {
        const value = Number(amount);

        return Number.isFinite(value) ? value : 0;
    }

    function format(amount) {
        const value = Math.round(toNumber(amount));

        return `Rs. ${value.toLocaleString('en-PK')}`;
    }

    function formatDecimal(amount) {
        const value = toNumber(amount);

        return `Rs. ${value.toLocaleString('en-PK', { minimumFractionDigits: 0, maximumFractionDigits: 2 })}`;
    }

    function lineTotal(item) {
        return toNumber(item?.price) * toNumber(item?.qty || 1);
    }

    window.BeautageMoney = {
        format,
        formatDecimal,
        toNumber,
        lineTotal,
    };
})();
