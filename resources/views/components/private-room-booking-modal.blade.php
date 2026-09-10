<style>
.private-room-modal { display: none; position: fixed; inset: 0; z-index: 10000; align-items: center; justify-content: center; padding: 1rem; background: rgba(15, 23, 42, .62); }
.private-room-modal.active { display: flex; }
.private-room-dialog { width: min(620px, 100%); max-height: 92vh; overflow-y: auto; background: #fffdfb; border-radius: 20px; box-shadow: 0 24px 70px rgba(15,23,42,.24); }
.private-room-head { padding: 1.25rem 1.4rem; color: white; background: linear-gradient(135deg, #14532d, #0f766e); border-radius: 20px 20px 0 0; }
.private-room-head-row { display: flex; justify-content: space-between; gap: 1rem; align-items: flex-start; }
.private-room-close { border: 0; color: white; background: rgba(255,255,255,.16); border-radius: 50%; width: 34px; height: 34px; cursor: pointer; font-size: 1.25rem; }
.private-room-body { padding: 1.4rem; }
.private-room-summary { display: grid; grid-template-columns: 130px 1fr; gap: 1rem; align-items: center; margin-bottom: 1.2rem; padding: .8rem; border: 1px solid #e3ece5; border-radius: 14px; background: #f7faf7; }
.private-room-summary img { width: 130px; height: 84px; object-fit: cover; border-radius: 10px; }
.private-room-price { color: #0f766e; font-size: 1.15rem; font-weight: 800; }
.private-room-note { color: #64748b; font-size: .82rem; margin-top: .25rem; }
.private-room-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .85rem; }
.private-room-field { margin-bottom: .9rem; }
.private-room-field label { display: block; margin-bottom: .35rem; color: #334155; font-size: .82rem; font-weight: 700; }
.private-room-field input, .private-room-field select { width: 100%; padding: .72rem .8rem; border: 1px solid #d8e2dc; border-radius: 10px; background: white; }
.private-room-total { display: flex; justify-content: space-between; gap: 1rem; margin: 1rem 0; padding: 1rem; border-radius: 12px; background: #ecfdf5; color: #14532d; }
.private-room-submit { width: 100%; border: 0; border-radius: 11px; padding: .85rem 1rem; color: white; background: #0f766e; font-weight: 800; cursor: pointer; }
.private-room-submit:disabled { opacity: .65; cursor: wait; }
.private-room-error { display: none; margin-bottom: .8rem; padding: .7rem; border-radius: 9px; color: #991b1b; background: #fee2e2; font-size: .88rem; }
@media (max-width: 768px) {
    .private-room-dialog { width: min(100%, 620px); }
    .private-room-head { padding: 1rem; }
    .private-room-body { padding: 1rem; }
    .private-room-grid { grid-template-columns: 1fr; }
    .private-room-summary { grid-template-columns: 1fr; align-items: stretch; }
    .private-room-summary img { width: 100%; height: 160px; }
    .private-room-total { flex-wrap: wrap; }
}
</style>

<div id="privateRoomBookingModal" class="private-room-modal" role="dialog" aria-modal="true" aria-labelledby="privateRoomModalTitle">
    <div class="private-room-dialog">
        <div class="private-room-head">
            <div class="private-room-head-row">
                <div><h2 id="privateRoomModalTitle">Reservasi Private Room</h2><p>Pilih paket acara sesuai kebutuhan Anda.</p></div>
                <button type="button" class="private-room-close" data-private-room-close aria-label="Tutup">&times;</button>
            </div>
        </div>
        <div class="private-room-body">
            <div class="private-room-summary">
                <img id="privateRoomImage" src="" alt="Pilihan paket Private Room">
                <div><strong id="privateRoomName"></strong><div class="private-room-price" id="privateRoomUnitPrice"></div><div class="private-room-note" id="privateRoomNote"></div></div>
            </div>
            <form id="privateRoomBookingForm">
                <input type="hidden" id="privateRoomPackageId">
                <div class="private-room-field">
                    <label for="privateRoomOption">Pilihan Paket *</label>
                    <select id="privateRoomOption" required></select>
                </div>
                <div class="private-room-grid">
                    <div class="private-room-field"><label for="privateRoomNameInput">Nama Lengkap *</label><input id="privateRoomNameInput" required minlength="3"></div>
                    <div class="private-room-field"><label for="privateRoomEmail">Email *</label><input id="privateRoomEmail" type="email" required></div>
                    <div class="private-room-field"><label for="privateRoomPhone">No. WhatsApp *</label><input id="privateRoomPhone" required minlength="10" maxlength="15"></div>
                    <div class="private-room-field"><label for="privateRoomDate">Tanggal Acara *</label><input id="privateRoomDate" type="date" required></div>
                    <div class="private-room-field"><label for="privateRoomEvent">Jenis Acara *</label><select id="privateRoomEvent" required><option value="">Pilih jenis acara</option><option value="gathering">Gathering</option><option value="meeting">Meeting</option><option value="wedding">Wedding</option><option value="engagement">Engagement</option></select></div>
                    <div class="private-room-field"><label for="privateRoomDuration">Durasi</label><select id="privateRoomDuration"><option value="package">Sesuai paket</option><option value="half_day">Half Day</option><option value="full_day">Full Day</option><option value="vip">VIP</option></select></div>
                    <div class="private-room-field"><label for="privateRoomPax">Jumlah Peserta *</label><input id="privateRoomPax" type="number" min="1" required></div>
                    <div class="private-room-field"><label for="privateRoomSetup">Layout Ruangan</label><select id="privateRoomSetup"><option value="banquet">Banquet</option><option value="classroom">Classroom</option><option value="u_shape">U-Shape</option><option value="theater">Theater</option></select></div>
                </div>
                <div id="privateRoomError" class="private-room-error"></div>
                <div class="private-room-total"><strong>Estimasi Total</strong><strong id="privateRoomTotal"></strong></div>
                <button class="private-room-submit" type="submit">Lanjut ke Pembayaran</button>
            </form>
        </div>
    </div>
</div>

