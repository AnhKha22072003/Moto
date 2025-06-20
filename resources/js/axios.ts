import axios from 'axios'

axios.defaults.baseURL = 'http://localhost:18080'
axios.defaults.headers.common['Accept'] = 'application/json'

export default axios
