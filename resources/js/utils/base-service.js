let baseService = {

    getUserFromLocalStorage(){
        let user = JSON.parse(localStorage.getItem('invoice-client-token'));
        return user ? user : null;
    },

    getTokenFromLocalStorage(){
        let user = JSON.parse(localStorage.getItem('invoice-client-token'));
        if(user === null || !user || !user.token){
            return null;
        }
        return user.token;
    },

    handleObjectErrors(error, errors){
        if (error.response) {
            console.log(error.response);

            if (Object.keys(error.response?.data?.errors).length > 0) {
                errors.value = error.response?.data?.errors;
                if (import.meta.env.VITE_APP_ENV === 'local') {
                    console.log("Validation errors", errors.value);
                }
            }

            if (error.response?.data?.server_error) {
                errors.value.server_error = 'Server error. Please try again later or contact your admin.';
            }
        }
    }
}

export default baseService;
