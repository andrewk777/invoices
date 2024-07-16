import axios from 'axios';

// For normal requests
const apiClient = axios.create({
    baseURL: '/api',
    headers: {
        'Accept': 'application/json',
    }
});

// For file uploads requests
const apiClientMultipart = axios.create({
    baseURL: '/api',
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'multipart/form-data',
    }
});

// Add a request interceptor
const addAuthorizationHeader = (config) => {
    const token = JSON.parse(localStorage.getItem('invoice-client-token'))?.token;
    if (token) {
        config.headers['Authorization'] = `Bearer ${token}`;
    }
    return config;
};

apiClient.interceptors.request.use(addAuthorizationHeader);
apiClientMultipart.interceptors.request.use(addAuthorizationHeader);

export { apiClient, apiClientMultipart };
