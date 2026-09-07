let privateRoomBooking = { id: null, price: 0, type: 'per_person', minimum: 1, config: {} };

const privateRoomMoney = value => 'Rp ' + Number(value).toLocaleString('id-ID');

function closePrivateRoomModal() {
    document.getElementById('privateRoomBookingModal')?.classList.remove('active');
    document.body.style.overflow = '';
}

function updatePrivateRoomTotal() {
    const pax = Number(document.getElementById('privateRoomPax')?.value || 0);
    const total = privateRoomBooking.type === 'package' ? privateRoomBooking.price : privateRoomBooking.price * pax;
    const totalElement = document.getElementById('privateRoomTotal');
    if (totalElement) totalElement.textContent = privateRoomMoney(total);
}

function openPrivateRoomModal(data) {
    privateRoomBooking = { ...data, price: Number(data.price), minimum: Number(data.minimum), config: data.config || {} };
    document.getElementById('privateRoomPackageId').value = data.id;
    document.getElementById('privateRoomName').textContent = data.name;
    document.getElementById('privateRoomImage').src = data.image;
    document.getElementById('privateRoomUnitPrice').textContent = privateRoomBooking.type === 'package'
        ? privateRoomMoney(data.price)
        : privateRoomMoney(data.price) + ' / pax';
    document.getElementById('privateRoomNote').textContent = privateRoomBooking.type === 'package'
        ? `Paket total, minimum ${data.minimum} pax`
        : `Minimum ${data.minimum} pax. Harga belum termasuk pajak & service.`;
    document.getElementById('privateRoomPax').min = data.minimum;
    document.getElementById('privateRoomPax').value = data.minimum;
    document.getElementById('privateRoomEvent').value = data.event || '';
    document.getElementById('privateRoomDuration').value = data.duration || 'package';
    document.getElementById('privateRoomDate').min = new Date().toISOString().split('T')[0];
    document.getElementById('privateRoomBookingModal').classList.add('active');
    document.body.style.overflow = 'hidden';
    updatePrivateRoomTotal();
}

function parsePrivateRoomConfig(button) {
    return JSON.parse(atob(button.dataset.packageConfig));
}

document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('privateRoomBookingModal');
    const form = document.getElementById('privateRoomBookingForm');
    if (!modal || !form) return;

    document.querySelectorAll('[data-booking-type="private-room"]').forEach(button => {
        button.addEventListener('click', () => {
            const config = parsePrivateRoomConfig(button);
            openPrivateRoomModal({
                id: button.dataset.packageId,
                name: button.dataset.packageName,
                price: button.dataset.packagePrice,
                image: button.dataset.packageImage,
                type: config.price_type,
                minimum: config.minimum_pax,
                event: config.event_type,
                duration: config.duration,
                config,
            });
        });
    });

    modal.addEventListener('click', event => {
        if (event.target === modal || event.target.closest('[data-private-room-close]')) closePrivateRoomModal();
    });
    document.getElementById('privateRoomPax')?.addEventListener('input', updatePrivateRoomTotal);

    form.addEventListener('submit', async event => {
        event.preventDefault();
        const pax = Number(document.getElementById('privateRoomPax').value);
        const error = document.getElementById('privateRoomError');
        if (pax < privateRoomBooking.minimum) {
            error.textContent = `Minimal peserta adalah ${privateRoomBooking.minimum} orang.`;
            error.style.display = 'block';
            return;
        }
        error.style.display = 'none';
        const button = form.querySelector('button[type="submit"]');
        button.disabled = true;
        const payload = {
            paket_wisata_id: privateRoomBooking.id,
            nama_lengkap: document.getElementById('privateRoomNameInput').value,
            email: document.getElementById('privateRoomEmail').value,
            no_hp: document.getElementById('privateRoomPhone').value,
            tanggal_kunjungan: document.getElementById('privateRoomDate').value,
            jumlah_orang: pax,
            package_specific_data: {
                event_type: document.getElementById('privateRoomEvent').value,
                expected_attendees: pax,
                event_duration: document.getElementById('privateRoomDuration').value,
                setup_preference: document.getElementById('privateRoomSetup').value,
            },
        };

        try {
            const response = await fetch('/api/booking/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify(payload),
            });
            const result = await response.json();
            if (!response.ok || !result.success) throw new Error(result.message || 'Booking gagal dibuat');
            closePrivateRoomModal();
            if (result.data?.snap_token && typeof window.snap !== 'undefined') {
                window.snap.pay(result.data.snap_token, {
                    onSuccess: () => { window.location.href = result.data.redirect_url; },
                    onPending: () => alert('Pembayaran masih menunggu konfirmasi.'),
                    onError: () => alert('Pembayaran gagal.'),
                });
            } else {
                window.location.href = result.data?.redirect_url || result.redirect_url;
            }
        } catch (submitError) {
            error.textContent = submitError.message;
            error.style.display = 'block';
            button.disabled = false;
        }
    });
});
