const handleObjectErrors = (error, errors) => {
    if (error.response) {
        console.log(error.response);

        if (Object.keys(error.response?.data?.errors || {}).length > 0) {
            errors.value = error.response?.data?.errors;
            if (import.meta.env.VITE_APP_ENV === 'local') {
                console.log("Validation errors", errors.value);
            }
        }

        if (error.response?.data?.server_error) {
            errors.value.server_error = 'Server error. Please try again later or contact your admin.';
        }
    }
};

export default handleObjectErrors;
