import axios from "axios"

const RouteService = {

    getUserFromLocalStorage(){
        let user = JSON.parse(localStorage.getItem('invoice-client-token'));
        return user ? user : null;
    },

    getTokenFromLocalStorage(){
        let user = JSON.parse(localStorage.getItem('invoice-client-token'));
        return user ? user.token : null;
    },

    // Authenticate each api request
    authenticateUser(url, next, logout){

        console.log("Before auth", this.getTokenFromLocalStorage());

        if(!this.getTokenFromLocalStorage()){
            window.location.href = logout;
        }

        axios.get(url, {
            headers: {
                "Authorization" : "Bearer " + this.getTokenFromLocalStorage(),
                'Accept' : 'application/json',
            },
            params: {
                token: this.getTokenFromLocalStorage()
            }

        }).then((response) => {
            if(response.data.success){
                console.log("Authenticated user", this.getTokenFromLocalStorage());
                next();
            }else{
                window.location.href = logout;
            }
        });
    },

}

export default RouteService;
