import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
    stages: [
        { duration: '30s', target: 10 }, 
        { duration: '1m', target: 30 },  // 30 Siswa submit serentak di 1 detik yang sama
        { duration: '2m', target: 30 },  // (Ini setara ~500 siswa aktif ujian)
        { duration: '30s', target: 0 },
    ],
};

export default function () {
    // Tembak jalur rahasia databasemu lagi
    const res = http.get('http://cbt-app.test/k6-test-ujian');

    check(res, {
        'status is 200': (r) => r.status === 200,
    });

    sleep(1); 
}