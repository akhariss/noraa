<!-- Reusable Atomic Modal Component v1.0 -->
<div id="atomicModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(10,31,68,0.5); z-index: 200000; align-items: center; justify-content: center;">
    <div style="background: var(--white); border-radius: 12px; padding: 32px; max-width: 400px; width: 90%; text-align: center; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: atomicModalFadeIn 0.2s ease-out;">
        <div id="atomicModalIcon" style="width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
            <!-- Icon will be injected here -->
        </div>
        <h3 id="atomicModalTitle" style="margin: 0 0 10px 0; color: var(--primary); font-weight: 800; font-size: 18px;"></h3>
        <p id="atomicModalMessage" style="margin: 0 0 24px 0; color: var(--text); line-height: 1.6; font-size: 14px; font-weight: 500;"></p>
        <button type="button" id="atomicModalBtn" onclick="closeAtomicModal()" style="background: var(--primary); color: white; padding: 12px 24px; border: none; border-radius: 6px; font-weight: 700; cursor: pointer; width: 100%; transition: all 0.2s; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">OK</button>
    </div>
</div>

<style>
@keyframes atomicModalFadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
</style>

<script>
function showAtomicModal(type, title, message, callback = null, duration = 0) {
    const modal = document.getElementById('atomicModal');
    const iconContainer = document.getElementById('atomicModalIcon');
    const titleEl = document.getElementById('atomicModalTitle');
    const msgEl = document.getElementById('atomicModalMessage');
    const btn = document.getElementById('atomicModalBtn');

    titleEl.textContent = title;
    msgEl.textContent = message;
    
    // Set theme based on type
    let iconSvg = '';
    if (type === 'success') {
        iconContainer.style.background = 'rgba(156, 124, 56, 0.1)';
        iconContainer.style.border = '2px solid rgba(156, 124, 56, 0.2)';
        iconSvg = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>';
    } else if (type === 'warning' || type === 'error') {
        iconContainer.style.background = '#fff7e6';
        iconContainer.style.border = '2px solid #ffe58f';
        iconSvg = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="3"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
    }

    iconContainer.innerHTML = iconSvg;
    modal.style.display = 'flex';
    window.atomicModalCallback = callback;

    if (duration > 0) {
        btn.style.display = 'none';
        setTimeout(closeAtomicModal, duration);
    } else {
        btn.style.display = 'block';
    }
}

function closeAtomicModal() {
    document.getElementById('atomicModal').style.display = 'none';
    if (window.atomicModalCallback) {
        window.atomicModalCallback();
        window.atomicModalCallback = null;
    }
}
</script>
