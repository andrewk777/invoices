const handleObjectErrors = (error, errors) => {
    if (error.response) {

       if(config.APP_ENV === 'local'){
           console.log("Error response", error.response);
       }

        if (Object.keys(error.response?.data?.errors || {}).length > 0) {
            errors.value = error.response?.data?.errors;
            if(config.APP_ENV === 'local'){
                console.log("Validation Errors", error.value);
            }
        }

        if (error.response?.data?.server_error) {
            errors.value.server_error = 'Server error. Please try again later or contact your admin.';
        }
    }
};

export default handleObjectErrors;
