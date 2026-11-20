function emptyRow(cols, msg = 'No records found.') {
    return `<tr><td colspan="${cols}">${msg}</td></tr>`;
}

async function loadRecords() {
    try {
        const d = await requestJson('api/dashboard.php');

        document.getElementById('heartTableBody').innerHTML = d.heart_records.length
            ? d.heart_records.map(r => `<tr><td>${escapeHtml(r.reading_time)}</td><td>${formatNum(r.heart_rate)} bpm</td></tr>`).join('')
            : emptyRow(2);

        document.getElementById('spo2TableBody').innerHTML = d.spo2_records.length
            ? d.spo2_records.map(r => `<tr><td>${escapeHtml(r.reading_time)}</td><td>${formatNum(r.spo2)}%</td></tr>`).join('')
            : emptyRow(2);

        document.getElementById('activityTableBody').innerHTML = d.activity_records.length
            ? d.activity_records.map(r => `<tr><td>${escapeHtml(r.record_date)}</td><td>${r.step_count}</td><td>${formatNum(r.active_calories)}</td><td>${formatNum(r.total_calories)}</td><td>${r.water_take_ml}</td><td>${r.active_minutes}</td></tr>`).join('')
            : emptyRow(6);

        document.getElementById('sleepTableBody').innerHTML = d.sleep_records.length
            ? d.sleep_records.map(r => `<tr><td>${escapeHtml(r.sleep_date)}</td><td>${r.total_sleep_mins} min</td><td>${r.light_sleep_mins} min</td><td>${r.deep_sleep_mins} min</td><td>${r.rem_sleep_mins} min</td><td>${r.awake_mins} min</td><td>${r.sleep_score ?? '--'}</td></tr>`).join('')
            : emptyRow(7);

        document.getElementById('alerts').innerHTML = d.alerts.length
            ? d.alerts.map(a => `<div class="alert-item"><div><strong>${escapeHtml(a.parameter)}</strong><p>${escapeHtml(a.message)}</p></div><time>${escapeHtml(a.alert_time ?? '')}</time></div>`).join('')
            : 'No alerts.';
    } catch (err) {
        console.error('Records load error:', err);
        document.getElementById('alerts').textContent = 'Could not load records.';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const today = new Date().toISOString().slice(0, 10);
    document.querySelectorAll('input[type="date"]').forEach(el => el.value = today);
    loadRecords();

    document.getElementById('dailyForm').addEventListener('submit', async e => {
        e.preventDefault();
        try {
            await requestJson('api/save_activity.php', Object.fromEntries(new FormData(e.target)));
            alert('Activity saved.');
            e.target.reset();
            e.target.querySelector('input[type="date"]').value = today;
            loadRecords();
        } catch (err) { alert(err.message); }
    });

    document.getElementById('sleepForm').addEventListener('submit', async e => {
        e.preventDefault();
        try {
            await requestJson('api/save_sleep.php', Object.fromEntries(new FormData(e.target)));
            alert('Sleep record saved.');
            e.target.reset();
            e.target.querySelector('input[type="date"]').value = today;
            loadRecords();
        } catch (err) { alert(err.message); }
    });
});