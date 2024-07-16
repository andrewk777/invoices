const style = getComputedStyle(document.body)

const CONF = {
    APP_URL: import.meta.env.VITE_APP_URL,
    API_URL: import.meta.env.VITE_API_URL,
    APP_DEBUG: import.meta.env.VITE_APP_DEBUG,
    APP_ENV: import.meta.env.VITE_APP_ENV,

    APP_LOGIN_URL: '/',
    APP_DEFAULT_URL: '/invoices',

    APP_NAME: import.meta.env.VITE_APP_NAME,
    PRIMARY_COLOR: style.getPropertyValue('--primary'),
    PRIMARY_COLOR_07: `${style.getPropertyValue('--primary')}70`,
    DF_MOMENTS: 'DD MMM, YYYY',
    DB_MOMENTS: 'YYYY-MM-DD',
    DB_MOMENTS_TIME: 'YYYY-MM-DD HH:mm:00',
    MOMENT_TIME_HUMAN: 'h:mmA',
    MOMENT_DATETIME_HUMAN: 'D MMM, YYYY h:mmA',
}

export default CONF
