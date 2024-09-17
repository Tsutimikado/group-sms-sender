import axios from "axios";
// import generateDataPoints from './randomData.js';

// export const BASE_URL = "http://test_devs.ibt.tj:5000/card-dep-info";
// export const BASE_URL = "https://localhost:5000/card-dep-info";
export const BASE_URL = "https://localhost:7211/card-dep-info";
// export const BASE_URL = "http://172.16.121.22:6843/card-dep-info";
// export const BASE_URL = "http://10.11.12.192:8282/card-dep-info";






const $api = axios.create({
    // withCredentials: true,
    baseURL: BASE_URL,
    // insecure: true
})

export default class PostService {
    static async getCurrencyPositionsData() {
        const response = await $api.get('/get-currency-positions-data');
        // console.log(JSON.parse(response.data));
        return JSON.parse(response.data); 
    }

    static async getCurrencyBalance() {
        const response = await $api.get('/get-currency-balance');
        return JSON.parse(response.data);
    }
    
    static async getRubBalance() {
        const response = await $api.get('/get-rub-balance');
        return JSON.parse(response.data);
    }

    static async getChartData(fromDate, toDate) {
        const response = await $api.get('/get-rub-balance-chart-data',{
                params: {
                    from_date: fromDate,
                    to_date: toDate
                }
            }
        );
        // console.log(JSON.parse(response.data));
        return JSON.parse(response.data)
    }

}

