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
}

export default baseService;
