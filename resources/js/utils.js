export function formatCurrency(value) {
    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
        minimumFractionDigits: 2,
    }).format(value);
}

export function formatDate(date) {
    if (!date) return "N/A";
    return new Date(date).toLocaleDateString();
}
