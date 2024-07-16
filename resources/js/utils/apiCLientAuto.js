import axios from 'axios';

// Detect file uploads automatically
const apiClientAuto = axios.create({
    baseURL: '/api',
});

// Request interceptor
apiClientAuto.interceptors.request.use(config => {
    const token = JSON.parse(localStorage.getItem('invoice-client-token'))?.token;
    if (token) {
        config.headers['Authorization'] = `Bearer ${token}`;
    }
    config.headers['Accept'] = 'application/json';
    return config;
});

export default apiClientAuto;
