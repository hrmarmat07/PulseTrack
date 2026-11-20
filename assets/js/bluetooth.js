const FEEA_SERVICE = '0000feea-0000-1000-8000-00805f9b34fb';
const FEE3_CHAR    = '0000fee3-0000-1000-8000-00805f9b34fb';

let lastHrSave = 0;
let lastSpo2Save = 0;

function decodeSpO2(data) {
    if (!data || data.byteLength !== 6) return null;
    const prefix = [0xFE, 0xEA, 0x20, 0x06, 0x6B];
    for (let i = 0; i < prefix.length; i++) {
        if (data.getUint8(i) !== prefix[i]) return null;
    }
    const val = data.getUint8(5);
    return (val >= 70 && val <= 100) ? val : null;
}

function nowTimestamp() {
    return new Date().toISOString().slice(0, 19).replace('T', ' ');
}

async function connectWatch() {
    const status = document.getElementById('connectionStatus');
    const dot    = document.getElementById('connectionDot');
    const nameEl = document.getElementById('connectedWatchName');
    const btn    = document.getElementById('connectBtn');

    if (!navigator.bluetooth) {
        status.textContent = 'Bluetooth not supported';
        return;
    }

    try {
        status.textContent = 'Searching...';
        btn.disabled = true;

        const device = await navigator.bluetooth.requestDevice({
            acceptAllDevices: true,
            optionalServices: ['heart_rate', FEEA_SERVICE]
        });

        device.addEventListener('gattserverdisconnected', () => {
            status.textContent = 'Watch disconnected';
            nameEl.textContent = 'No device';
            dot.classList.remove('online');
        });

        const server = await device.gatt.connect();
        status.textContent = 'Connected';
        nameEl.textContent = device.name || 'Smartwatch';
        dot.classList.add('online');

        // Heart rate notifications
        try {
            const hrService = await server.getPrimaryService('heart_rate');
            const hrChar = await hrService.getCharacteristic('heart_rate_measurement');
            await hrChar.startNotifications();
            hrChar.addEventListener('characteristicvaluechanged', async e => {
                const v = e.target.value;
                const bpm = (v.getUint8(0) & 1) ? v.getUint16(1, true) : v.getUint8(1);
                document.getElementById('heartRate').textContent = bpm;

                if (Date.now() - lastHrSave >= 10000) {
                    lastHrSave = Date.now();
                    await requestJson('api/save_vitals.php', { type: 'heart_rate', value: bpm, reading_time: nowTimestamp() });
                }
            });
        } catch (err) { console.error('HR error:', err); }

        // Fire-Boltt SpO2 notifications
        try {
            const spo2Service = await server.getPrimaryService(FEEA_SERVICE);
            const spo2Char = await spo2Service.getCharacteristic(FEE3_CHAR);
            await spo2Char.startNotifications();
            spo2Char.addEventListener('characteristicvaluechanged', async e => {
                const spo2 = decodeSpO2(e.target.value);
                if (spo2 === null) return;
                document.getElementById('spo2Value').textContent = spo2;

                if (Date.now() - lastSpo2Save >= 10000) {
                    lastSpo2Save = Date.now();
                    await requestJson('api/save_vitals.php', { type: 'spo2', value: spo2, reading_time: nowTimestamp() });
                }
            });
        } catch (err) { console.error('SpO2 error:', err); }

        } catch (err) {
        console.error('BLE Error:', err);
        status.textContent = 'Connection failed: ' + err.message;
        nameEl.textContent = 'No device';
        dot.classList.remove('online');
        }
        finally {
        btn.disabled = false;
    }
}