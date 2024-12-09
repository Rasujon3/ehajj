import axios from 'axios';
export const checkGuideApplicationExistLastDate = async () => {
    try {
        const response = await axios.get(`/guides/is-guide-application-exist`);
        if (response.data?.responseCode === 1) {
            return true;
        } else {
            return false;
        }
    } catch (error) {
        console.error('checkGuideApplicationLastDate API error:', error);
        return false;
    }
}
