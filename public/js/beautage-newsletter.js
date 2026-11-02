function beautageShowToast(message) {
    const container = document.getElementById('toast-container');
    if (! container) {
        window.alert(message);
        return;
    }

    const toast = document.createElement('div');
    toast.className =
        'pointer-events-auto bg-beautage-purpleDark text-white px-4 py-3 rounded-xl shadow-lg text-sm font-semibold border border-purple-600 transform transition-all duration-300 translate-y-2 opacity-0';
    toast.textContent = message;
    container.appendChild(toast);
    requestAnimationFrame(() => {
        toast.classList.remove('translate-y-2', 'opacity-0');
    });
    window.setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-y-2');
        window.setTimeout(() => toast.remove(), 300);
    }, 4000);
}

async function handleNewsletterSubmit(event) {
    event.preventDefault();
    const form = event.target;
    const emailInput = form.querySelector('[name="email"]');
    if (! emailInput) {
        return;
    }

    const email = emailInput.value.trim();
    if (! email) {
        return;
    }

    const list = form.dataset.newsletterList || 'store';
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';

    const submitBtn = form.querySelector('[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
    }

    try {
        const response = await fetch('/newsletter/subscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify({ email, list }),
        });

        const data = await response.json().catch(() => ({}));
        if (! response.ok) {
            throw new Error(data.message || 'Could not subscribe. Please try again.');
        }

        beautageShowToast(data.message || 'Thank you for subscribing!');
        emailInput.value = '';
    } catch (error) {
        beautageShowToast(error.message || 'Could not subscribe. Please try again.');
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
        }
    }
}

window.handleNewsletterSubmit = handleNewsletterSubmit;
window.beautageShowToast = beautageShowToast;
