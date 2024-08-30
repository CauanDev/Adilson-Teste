import axios from 'axios';
const axiosInstance = axios.create({
    baseURL:'http://127.0.0.1:8000/api',
    headers: {
        'Content-type': 'application/json',
        'Authorization': '2qzU0ENAOtmP5yzun4UeIrWv9HvKMhDQMPs54n7mf6498bF7dp4KLaQW3r48pLnDwZkUTYetvzKsGcJ',
        'ngrok-skip-browser-warning': true,
    }
});

export default axiosInstance;