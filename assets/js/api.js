async function requestJson(url, data = null) {
    const options = data ? {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    } : {};

    const res = await fetch(url, options);
    const json = await res.json();
    if (!res.ok || !json.success) {
        throw new Error(json.message || 'Request failed');
    }
    return json;
}

function escapeHtml(val) {
    return String(val ?? '').replace(/[&<>"']/g, c => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    }[c]));
}

function formatNum(val) {
    const n = parseFloat(val);
    return Number.isFinite(n) ? String(Math.round(n * 10) / 10) : '--';
}