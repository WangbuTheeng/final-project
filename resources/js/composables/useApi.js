import { ref } from 'vue';
import axios from 'axios';

/**
 * API composable for handling HTTP requests with loading states
 */
export function useApi() {
    const loading = ref(false);
    const error = ref(null);

    /**
     * Make an API request with loading and error handling
     * @param {Function} apiCall 
     * @returns {Promise}
     */
    async function request(apiCall) {
        loading.value = true;
        error.value = null;

        try {
            const response = await apiCall();
            return response;
        } catch (err) {
            error.value = err.response?.data?.message || err.message || 'An error occurred';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    /**
     * GET request
     * @param {string} url 
     * @param {object} config 
     * @returns {Promise}
     */
    async function get(url, config = {}) {
        return request(() => axios.get(url, config));
    }

    /**
     * POST request
     * @param {string} url 
     * @param {object} data 
     * @param {object} config 
     * @returns {Promise}
     */
    async function post(url, data = {}, config = {}) {
        return request(() => axios.post(url, data, config));
    }

    /**
     * PUT request
     * @param {string} url 
     * @param {object} data 
     * @param {object} config 
     * @returns {Promise}
     */
    async function put(url, data = {}, config = {}) {
        return request(() => axios.put(url, data, config));
    }

    /**
     * PATCH request
     * @param {string} url 
     * @param {object} data 
     * @param {object} config 
     * @returns {Promise}
     */
    async function patch(url, data = {}, config = {}) {
        return request(() => axios.patch(url, data, config));
    }

    /**
     * DELETE request
     * @param {string} url 
     * @param {object} config 
     * @returns {Promise}
     */
    async function del(url, config = {}) {
        return request(() => axios.delete(url, config));
    }

    return {
        loading,
        error,
        request,
        get,
        post,
        put,
        patch,
        delete: del
    };
}