async function loadDashboard() {
    try {
        const d = await requestJson('api/dashboard.php');
        document.getElementById('heartRate').textContent = formatNum(d.latest_hr);
        document.getElementById('spo2Value').textContent = formatNum(d.latest_spo2);
        document.getElementById('stepsCard').textContent = d.today_steps ?? '0';
        document.getElementById('sleepCard').textContent = d.today_sleep ?? '0';
    } catch (err) {
        console.error('Dashboard load error:', err);
    }
}

document.addEventListener('DOMContentLoaded', loadDashboard);