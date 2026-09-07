{{-- Floating WhatsApp Button --}}
<a 
    href="https://wa.me/{{ config('app.whatsapp.number') }}" 
    class="whatsapp-float" 
    target="_blank" 
    rel="noopener noreferrer"
    aria-label="Chat WhatsApp"
>
    <svg width="32" height="32" fill="currentColor" viewBox="0 0 24 24">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
    </svg>
    <span class="whatsapp-text">Chat</span>
</a>

<style>
.whatsapp-float {
    position: fixed;
    bottom: 24px;
    right: 24px;
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
    color: #FFFFFF;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 2px;
    box-shadow: 0 4px 16px rgba(37, 211, 102, 0.4);
    z-index: 1000;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    cursor: pointer;
}

.whatsapp-float:hover {
    transform: translateY(-4px) scale(1.05);
    box-shadow: 0 8px 24px rgba(37, 211, 102, 0.6);
    background: linear-gradient(135deg, #128C7E 0%, #075E54 100%);
}

.whatsapp-float:active {
    transform: translateY(-2px) scale(1.02);
}

.whatsapp-text {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.3px;
    margin-top: -2px;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .whatsapp-float {
        bottom: 16px;
        right: 16px;
        width: 56px;
        height: 56px;
    }
    
    .whatsapp-float svg {
        width: 28px;
        height: 28px;
    }
    
    .whatsapp-text {
        font-size: 10px;
    }
}

/* Animation entrance */
@keyframes whatsappBounce {
    0% {
        transform: scale(0) translateY(100px);
        opacity: 0;
    }
    50% {
        transform: scale(1.1) translateY(0);
    }
    100% {
        transform: scale(1) translateY(0);
        opacity: 1;
    }
}

.whatsapp-float {
    animation: whatsappBounce 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55) 0.5s both;
}

/* Pulse effect */
@keyframes whatsappPulse {
    0%, 100% {
        box-shadow: 0 4px 16px rgba(37, 211, 102, 0.4);
    }
    50% {
        box-shadow: 0 4px 20px rgba(37, 211, 102, 0.7);
    }
}

.whatsapp-float {
    animation: whatsappBounce 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55) 0.5s both,
               whatsappPulse 2s ease-in-out 2s infinite;
}
</style>
