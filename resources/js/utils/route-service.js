import axios from "axios"

const RouteService = {

    // Authenticate each api request
    authenticateUser(url, next, logout){

        console.log("Before auth", baseService.getTokenFromLocalStorage());

        if(!baseService.getTokenFromLocalStorage()){
            window.location.href = logout;
        }

        axios.get(url, {
            headers: {
                "Authorization" : "Bearer " + baseService.getTokenFromLocalStorage(),
                'Accept' : 'application/json',
            },
            params: {
                token: baseService.getTokenFromLocalStorage()
            }

        }).then((response) => {
            if(response.data.success === true && response.status === 200){
                next();
            }else{
                window.location.href = logout;
            }
        }).catch((error) => {
            if(error.response){
                window.location.href = logout;
            }
        });
    },

}

export default RouteService;
