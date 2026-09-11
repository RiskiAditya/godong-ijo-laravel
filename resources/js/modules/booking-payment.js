document.addEventListener('DOMContentLoaded', () => {
    const paymentButton = document.getElementById('resume-payment-button');
    if (!paymentButton) return;

    paymentButton.addEventListener('click', () => {
        if (typeof window.snap === 'undefined') {
            alert('Pembayaran sedang dimuat. Silakan coba lagi sebentar.');
            return;
        }

        paymentButton.disabled = true;
        window.snap.pay(paymentButton.dataset.snapToken, {
            onSuccess: async () => {
                try {
                    await fetch(`/midtrans/check-payment/${encodeURIComponent(paymentButton.dataset.orderId)}`);
                } finally {
                    window.location.href = `${paymentButton.dataset.redirectUrl}?from_payment=1`;
                }
            },
            onPending: () => { paymentButton.disabled = false; },
            onError: () => {
                paymentButton.disabled = false;
                alert('Pembayaran gagal. Silakan coba lagi.');
            },
            onClose: () => { paymentButton.disabled = false; },
        });
    });
});