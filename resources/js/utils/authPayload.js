export function extractAuthPayload(response) {
    if (response?.data?.access_token) {
        return response.data;
    }

    return response;
}
