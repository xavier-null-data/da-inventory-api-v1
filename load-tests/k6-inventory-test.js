import http from 'k6/http';
import { check } from 'k6';

export let options = {
    // 500 requests por segundo
    stages: [
        { duration: '5s', target: 500 },   // subir a 500 rps
        { duration: '20s', target: 500 },  // mantener 500 rps
        { duration: '5s', target: 0 },     // bajar carga
    ],
    thresholds: {
        http_req_failed: ['rate<0.01'], // menos del 1% debe fallar
        http_req_duration: ['p(95)<300'], // latencia aceptable < 300ms
    }
};

export default function () {
    let res = http.get('http://inventory_app:8000/api/products');

    check(res, {
        "status != 500": (r) => r.status !== 500,
        "status is 200": (r) => r.status === 200,
    });
}
